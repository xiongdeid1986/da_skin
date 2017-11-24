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
function lookupDomain($sld, $ext)
{
    global $remote_ip;
    $sld = str_replace(array( "\r\n", "\n", "\r" ), '', $sld);
    $ext = str_replace(array( "\r\n", "\n", "\r" ), '', $ext);
    $idnconv = new WHMCS_Domains_Idna();
    $sld = $idnconv->encode($sld);
    $whoisservers = file_get_contents(dirname(__FILE__) . "/whoisservers.php");
    $whoisservers = explode("\n", $whoisservers);
    foreach( $whoisservers as $value )
    {
        $value = explode("|", $value);
        $tld = trim(strip_tags($value[0]));
        $whoisserver[$tld] = trim(strip_tags($value[1]));
        $whoisvalue[$tld] = trim(strip_tags($value[2]));
        $whoisreqprefix[$tld] = isset($value[3]) ? strip_tags($value[3]) : '';
    }
    $port = '43';
    $server = $whoisserver[$ext];
    $return = $whoisvalue[$ext];
    $reqprefix = $whoisreqprefix[$ext];
    if( $server == '' )
    {
        $result['result'] = 'available';
    }
    else
    {
        $fulldomain = $domain = $sld . $ext;
        if( substr($return, 0, 12) == 'HTTPREQUEST-' )
        {
            $ch = curl_init();
            $url = $server . urlencode($domain);
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
                $result['result'] = 'error';
                if( $_SESSION['adminid'] )
                {
                    $result['errordetail'] = "Error: " . curl_errno($ch) . " - " . curl_error($ch);
                }
            }
            else
            {
                if( strpos($data2, substr($return, 12)) == true )
                {
                    $result['result'] = 'available';
                }
                else
                {
                    $result['result'] = 'unavailable';
                    $result['whois'] = nl2br(strip_tags($data));
                }
            }
            curl_close($ch);
        }
        else
        {
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
                    $result['result'] = 'available';
                }
                else
                {
                    $result['result'] = 'unavailable';
                    $result['whois'] = nl2br(htmlentities($data));
                }
            }
            else
            {
                $result['result'] = 'error';
                if( $_SESSION['adminid'] )
                {
                    $result['errordetail'] = "Error: " . $errno . " - " . $errstr;
                }
            }
        }
        insert_query('tblwhoislog', array( 'date' => "now()", 'domain' => $fulldomain, 'ip' => $remote_ip ));
    }
    return $result;
}
function getWHOISServers()
{
    $whoisservers = file_get_contents(dirname(__FILE__) . "/whoisservers.php");
    $whoisservers = explode("\n", $whoisservers);
    foreach( $whoisservers as $value )
    {
        $value = explode("|", $value);
        $whoisserver[trim(strip_tags($value[0]))] = trim(strip_tags($value[1]));
    }
    return $whoisserver;
}
function getWHOISServerVars()
{
    $whoisservers = file_get_contents(dirname(__FILE__) . "/whoisservers.php");
    $whoisservers = explode("\n", $whoisservers);
    foreach( $whoisservers as $value )
    {
        $value = explode("|", $value);
        $whoisvalue[trim(strip_tags($value[0]))] = trim(strip_tags($value[2]));
    }
    return $whoisvalue;
}