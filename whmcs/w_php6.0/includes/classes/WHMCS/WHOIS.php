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
class WHMCS_WHOIS
{
    private $whoisserverdefs = '';
    private $whoistlds = array(  );
    private $whoisservers = array(  );
    private $whoisvalues = array(  );
    private $whoisreqprefix = array(  );
    public function __construct()
    {
    }
    public function init()
    {
        if( $this->loadWHOISDefinitions() )
        {
            $this->processWHOISDefinitions();
        }
    }
    public function getWHOISDefinitionsPath()
    {
        return ROOTDIR . "/includes/whoisservers.php";
    }
    public function loadWHOISDefinitions()
    {
        $path = $this->getWHOISDefinitionsPath();
        if( file_exists($path) )
        {
            $this->whoisserverdefs = file_get_contents($path);
            return true;
        }
        return false;
    }
    public function processWHOISDefinitions()
    {
        $whoisdefs = $this->whoisserverdefs;
        $whoisdefs = explode("\n", $whoisdefs);
        foreach( $whoisdefs as $line )
        {
            $values = explode("|", $line);
            $tld = trim(strip_tags($values[0]));
            $this->whoistlds[] = $tld;
            $this->whoisservers[$tld] = trim(strip_tags($values[1]));
            $this->whoisvalues[$tld] = trim(strip_tags($values[2]));
            $this->whoisreqprefix[$tld] = isset($values[3]) ? strip_tags($values[3]) : '';
        }
    }
    public function getTLDs()
    {
        return $this->whoistlds;
    }
    public function canLookupTLD($tld)
    {
        return in_array($tld, $this->getTLDS());
    }
    public function getServer($tld)
    {
        return $this->whoisservers[$tld];
    }
    public function getAvailableMatchString($tld)
    {
        return $this->whoisvalues[$tld];
    }
    public function getReqPrefix($tld)
    {
        return $this->whoisreqprefix[$tld];
    }
    public function lookup($parts)
    {
        $whmcs = WHMCS_Application::getinstance();
        $sld = $parts['sld'];
        $tld = $parts['tld'];
        $server = $this->getServer($tld);
        $return = $this->getAvailableMatchString($tld);
        $reqprefix = $this->getReqPrefix($tld);
        $results = array(  );
        if( $server == '' )
        {
            return false;
        }
        $fulldomain = $domain = $sld . $tld;
        if( substr($return, 0, 12) == 'HTTPREQUEST-' )
        {
            $ch = curl_init();
            $url = $server . $domain;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data = curl_exec($ch);
            $data2 = " ---" . $data;
            if( curl_error($ch) )
            {
                $results['result'] = 'error';
                if( $_SESSION['adminid'] )
                {
                    $results['errordetail'] = "Error: " . curl_errno($ch) . " - " . curl_error($ch);
                }
            }
            else
            {
                if( strpos($data2, substr($return, 12)) == true )
                {
                    $results['result'] = 'available';
                }
                else
                {
                    $results['result'] = 'unavailable';
                    $results['whois'] = nl2br(strip_tags($data));
                }
            }
            curl_close($ch);
        }
        else
        {
            $port = '43';
            if( strpos($server, ":") )
            {
                $port = explode(":", $server, 2);
                $server = $port[0];
                $port = $port[1];
            }
            if( substr($return, 0, 6) == 'NOTLD-' )
            {
                $domain = $sld;
                $return = substr($return, 6);
            }
            $fp = @fsockopen($server, $port, $errno, $errstr, 10);
            if( $fp )
            {
                @fputs($fp, $reqprefix . $domain . "\r\n");
                @socket_set_timeout($fp, 10);
                while( !@feof($fp) )
                {
                    $data .= @fread($fp, 4096);
                }
                @fclose($fp);
                $data2 = " ---" . $data;
                if( strpos($data2, $return) == true )
                {
                    $results['result'] = 'available';
                }
                else
                {
                    $results['result'] = 'unavailable';
                    $results['whois'] = nl2br(htmlentities($data));
                }
            }
            else
            {
                $results['result'] = 'error';
                if( $_SESSION['adminid'] )
                {
                    $results['errordetail'] = "Error: " . $errno . " - " . $errstr;
                }
            }
        }
        insert_query('tblwhoislog', array( 'date' => "now()", 'domain' => $fulldomain, 'ip' => WHMCS_Utility_Environment_CurrentUser::getip() ));
        return $results;
    }
}