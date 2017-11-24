<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
class twitterIntents
{
    private $url = NULL;
    private $username = NULL;
    private $version = NULL;
    private $dbPull = FALSE;
    private $db = NULL;
    /**
     * Twitter Intent Integration
     *
     * Grabs tweets using the Twitter Intent system, which is the only way to
     * programmatically grab tweets without Twitter Developer API Keys.
     *
     * This object essentially performs an Intent request and then parses the
     * HTML response.
     *
     * @param string $username
     * @param WHMCS_Version_SemanticVersion $version
     */
    public function __construct($username = '', $version)
    {
        $this->username = trim($username);
        $this->url = "https://twitter.com/intent/user?screen_name=" . $this->username;
        $this->version = $version;
        $this->db = new WHMCS_TransientData();
    }
    protected function doDependencyCheck()
    {
        if( !class_exists('DOMDocument') )
        {
            logModuleCall('Twitter', "Accessing DOMDocument", 'DOMDocument', "DOMDocument class does not exist.");
            return FALSE;
        }
        if( !class_exists('DOMXPath') )
        {
            logModuleCall('Twitter', "Accessing DOMXPath", 'DOMXPath', "DOMXPath class does not exist.");
            return FALSE;
        }
        return TRUE;
    }
    public function getTweets()
    {
        if( !$this->doDependencyCheck() )
        {
            return FALSE;
        }
        if( $tweets = $this->getCachedTweets() )
        {
            return $tweets;
        }
        $tweets = $this->scrapePage();
        return $tweets;
    }
    protected function tidyHTML($html)
    {
        $tidy = new tidy();
        $tidy->ParseString($html);
        $tidy->cleanRepair();
        return (bool) $tidy;
    }
    protected function scrapePage()
    {
        $twitterLink = curlCall($this->url, '');
        if( function_exists('mb_convert_encoding') )
        {
            $twitterLink = mb_convert_encoding($twitterLink, 'HTML-ENTITIES', 'UTF-8');
        }
        else
        {
            $twitterLink = "<?xml encoding=\"UTF-8\">" . $twitterLink;
        }
        if( class_exists('tidy') )
        {
            $twitterLink = $this->tidyHTML($twitterLink);
        }
        $doc = new DOMDocument();
        $doc->loadHTML($twitterLink);
        $xpath = new DOMXpath($doc);
        $tweetComments = $this->findClassNodes($xpath, 'div', 'tweet-text');
        $tweetDates = $this->findClassNodes($xpath, 'span', '_timestamp');
        $tweetLinks = $this->findClassNodes($xpath, 'div', 'tweet-text', '/a');
        $tweetAbsLinks = $this->findClassNodes($xpath, 'div', 'tweet-text', "/a/@href");
        $tweets = array(  );
        foreach( $tweetComments as $key => $value )
        {
            foreach( $tweetLinks as $k => $link )
            {
                if( strpos($value, $link) !== FALSE )
                {
                    if( preg_match("%^/%", $tweetAbsLinks[$k]) )
                    {
                        continue;
                    }
                    $replace = "<a href = \"" . $tweetAbsLinks[$k] . "\" target = \"_blank\">" . $link . "</a>";
                    $value = str_replace($link, $replace, $value);
                }
            }
            $twitterdate = strtotime($tweetDates[$key]);
            if( $twitterdate === FALSE )
            {
                $twitterdate = $tweetDates[$key];
            }
            else
            {
                $twitterdate = fromMySQLDate(date("Y-m-d H:i", $twitterdate), TRUE);
            }
            $tweets[] = array( 'date' => $twitterdate, 'tweet' => $value );
        }
        $this->cacheTweets($tweets);
        return $tweets;
    }
    protected function findClassNodes($xpath, $tag, $classname = '', $attributes = '')
    {
        $classname = trim($classname);
        if( strlen($classname) < 1 )
        {
            return FALSE;
        }
        $arr = array(  );
        $result = $xpath->query('//' . $tag . "[@class='" . $classname . "']" . $attributes);
        foreach( $result as $value )
        {
            $arr[] = trim($value->textContent);
        }
        return $arr;
    }
    protected function getCachedTweets()
    {
        $minVersion = "5.3.0";
        if( isset($this->version) )
        {
            if( WHMCS_Version_SemanticVersion::compare($this->version, new WHMCS_Version_SemanticVersion($minVersion), "<") )
            {
                return FALSE;
            }
            $tweets = $this->db->retrieve('twitter');
            if( strlen(trim($tweets)) < 1 )
            {
                return false;
            }
            $tweets = json_decode($tweets, true);
            $this->dbPull = TRUE;
            return $tweets;
        }
        return FALSE;
    }
    protected function cacheTweets($tweets = array(  ))
    {
        if( count($tweets) < 1 )
        {
            return FALSE;
        }
        if( $this->dbPull )
        {
            return FALSE;
        }
        $name = 'twitter';
        $data = json_encode($tweets);
        $this->db->store($name, $data, 300);
    }
}