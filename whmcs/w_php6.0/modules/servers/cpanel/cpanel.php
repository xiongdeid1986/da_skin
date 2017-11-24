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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
global $licensing;
if( defined('CPANELCONFPACKAGEADDONLICENSE') )
{
    exit( "License Hacking Attempt Detected" );
}
define('CPANELCONFPACKAGEADDONLICENSE', $licensing->isActiveAddon("Configurable Package Addon"));
function cpanel_MetaData()
{
    return array( 'DisplayName' => 'cPanel', 'APIVersion' => "1.1" );
}
function cpanel_ConfigOptions()
{
    $configarray = array( "WHM Package Name" => array( 'Type' => 'text', 'Size' => '25' ), "Max FTP Accounts" => array( 'Type' => 'text', 'Size' => '5' ), "Web Space Quota" => array( 'Type' => 'text', 'Size' => '5', 'Description' => 'MB' ), "Max Email Accounts" => array( 'Type' => 'text', 'Size' => '5' ), "Bandwidth Limit" => array( 'Type' => 'text', 'Size' => '5', 'Description' => 'MB' ), "Dedicated IP" => array( 'Type' => 'yesno' ), "Shell Access" => array( 'Type' => 'yesno', 'Description' => "Tick to grant access" ), "Max SQL Databases" => array( 'Type' => 'text', 'Size' => '5' ), "CGI Access" => array( 'Type' => 'yesno', 'Description' => "Tick to grant access" ), "Max Subdomains" => array( 'Type' => 'text', 'Size' => '5' ), "Frontpage Extensions" => array( 'Type' => 'yesno', 'Description' => "Tick to grant access" ), "Max Parked Domains" => array( 'Type' => 'text', 'Size' => '5' ), "cPanel Theme" => array( 'Type' => 'text', 'Size' => '15' ), "Max Addon Domains" => array( 'Type' => 'text', 'Size' => '5' ), "Limit Reseller by Number" => array( 'Type' => 'text', 'Size' => '5', 'Description' => "Enter max number of allowed accounts" ), "Limit Reseller by Usage" => array( 'Type' => 'yesno', 'Description' => "Tick to limit by resource usage" ), "Reseller Disk Space" => array( 'Type' => 'text', 'Size' => '7', 'Description' => 'MB' ), "Reseller Bandwidth" => array( 'Type' => 'text', 'Size' => '7', 'Description' => 'MB' ), "Allow DS Overselling" => array( 'Type' => 'yesno', 'Description' => 'MB' ), "Allow BW Overselling" => array( 'Type' => 'yesno', 'Description' => 'MB' ), "Reseller ACL List" => array( 'Type' => 'text', 'Size' => '20' ), "Add Prefix to Package" => array( 'Type' => 'yesno', 'Description' => "Add username_ to package name" ), "Configure Nameservers" => array( 'Type' => 'yesno', 'Description' => "Setup Custom ns1/ns2 Nameservers" ), "Reseller Ownership" => array( 'Type' => 'yesno', 'Description' => "Set the reseller to own their own account" ) );
    return $configarray;
}
function cpanel_AdminLink($params)
{
    if( $params['serversecure'] )
    {
        $http = 'https';
        $whmport = '2087';
    }
    else
    {
        $http = 'http';
        $whmport = '2086';
    }
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $form = sprintf("<form action=\"%s://%s:%s/login/\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"user\" value=\"%s\" />" . "<input type=\"hidden\" name=\"pass\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $http, WHMCS_Input_Sanitize::encode($domain), $whmport, WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'WHM');
    return $form;
}
function cpanel_costrrpl($val)
{
    $val = str_replace('MB', '', $val);
    $val = str_replace('Accounts', '', $val);
    $val = trim($val);
    if( $val == 'Yes' )
    {
        $val = 'on';
    }
    else
    {
        if( $val == 'No' )
        {
            $val = '';
        }
        else
        {
            if( $val == 'Unlimited' )
            {
                $val = 'unlimited';
            }
        }
    }
    return $val;
}
function cpanel_CreateAccount($params)
{
    if( CPANELCONFPACKAGEADDONLICENSE )
    {
        if( $params['configoptions']["Disk Space"] )
        {
            $params['configoption17'] = cpanel_costrrpl($params['configoptions']["Disk Space"]);
            $params['configoption3'] = $params['configoption17'];
        }
        if( $params['configoptions']['Bandwidth'] )
        {
            $params['configoption18'] = cpanel_costrrpl($params['configoptions']['Bandwidth']);
            $params['configoption5'] = $params['configoption18'];
        }
        if( $params['configoptions']["FTP Accounts"] )
        {
            $params['configoption2'] = cpanel_costrrpl($params['configoptions']["FTP Accounts"]);
        }
        if( $params['configoptions']["Email Accounts"] )
        {
            $params['configoption4'] = cpanel_costrrpl($params['configoptions']["Email Accounts"]);
        }
        if( $params['configoptions']["MySQL Databases"] )
        {
            $params['configoption8'] = cpanel_costrrpl($params['configoptions']["MySQL Databases"]);
        }
        if( $params['configoptions']['Subdomains'] )
        {
            $params['configoption10'] = cpanel_costrrpl($params['configoptions']['Subdomains']);
        }
        if( $params['configoptions']["Parked Domains"] )
        {
            $params['configoption12'] = cpanel_costrrpl($params['configoptions']["Parked Domains"]);
        }
        if( $params['configoptions']["Addon Domains"] )
        {
            $params['configoption14'] = cpanel_costrrpl($params['configoptions']["Addon Domains"]);
        }
        if( $params['configoptions']["Dedicated IP"] )
        {
            $params['configoption6'] = cpanel_costrrpl($params['configoptions']["Dedicated IP"]);
        }
        if( $params['configoptions']["CGI Access"] )
        {
            $params['configoption9'] = cpanel_costrrpl($params['configoptions']["CGI Access"]);
        }
        if( $params['configoptions']["Shell Access"] )
        {
            $params['configoption7'] = cpanel_costrrpl($params['configoptions']["Shell Access"]);
        }
        if( $params['configoptions']["FrontPage Extensions"] )
        {
            $params['configoption11'] = cpanel_costrrpl($params['configoptions']["FrontPage Extensions"]);
        }
        if( $params['configoptions']["Mailing Lists"] )
        {
            $mailinglists = cpanel_costrrpl($params['configoptions']["Mailing Lists"]);
        }
        if( $params['configoptions']["Package Name"] )
        {
            $params['configoption1'] = $params['configoptions']["Package Name"];
        }
        if( $params['configoptions']['Language'] )
        {
            $languageco = $params['configoptions']['Language'];
        }
    }
    if( $params['configoption6'] )
    {
        $dedicatedip = 'y';
    }
    else
    {
        $dedicatedip = 'n';
    }
    if( $params['configoption9'] )
    {
        $cgiaccess = 'y';
    }
    else
    {
        $cgiaccess = 'n';
    }
    if( $params['configoption7'] )
    {
        $shellaccess = 'y';
    }
    else
    {
        $shellaccess = 'n';
    }
    if( $params['configoption11'] )
    {
        $fpextensions = 'y';
    }
    else
    {
        $fpextensions = 'n';
    }
    if( $params['configoption22'] )
    {
        $prefix = $params['serverusername'] . '_';
    }
    else
    {
        $prefix = '';
    }
    $postfields = array(  );
    $postfields['username'] = $params['username'];
    $postfields['password'] = $params['password'];
    $postfields['domain'] = $params['domain'];
    $postfields['plan'] = $prefix . $params['configoption1'];
    $postfields['savepkg'] = 0;
    $postfields['featurelist'] = 'default';
    $postfields['quota'] = $params['configoption3'];
    $postfields['bwlimit'] = $params['configoption5'];
    $postfields['ip'] = $dedicatedip;
    $postfields['cgi'] = $cgiaccess;
    $postfields['frontpage'] = $fpextensions;
    $postfields['hasshell'] = $shellaccess;
    $postfields['contactemail'] = $params['clientsdetails']['email'];
    $postfields['cpmod'] = $params['configoption13'];
    $postfields['maxftp'] = $params['configoption2'];
    $postfields['maxsql'] = $params['configoption8'];
    $postfields['maxpop'] = $params['configoption4'];
    if( $mailinglists )
    {
        $postfields['maxlst'] = $mailinglists;
    }
    $postfields['maxsub'] = $params['configoption10'];
    $postfields['maxpark'] = $params['configoption12'];
    $postfields['maxaddon'] = $params['configoption14'];
    if( $languageco )
    {
        $postfields['language'] = $languageco;
    }
    $postfields['reseller'] = 0;
    $cpanelrequest = "/xml-api/createacct?";
    foreach( $postfields as $k => $v )
    {
        $cpanelrequest .= $k . "=" . urlencode($v) . "&";
    }
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( !is_array($output) )
    {
        return $output;
    }
    if( !$output['CREATEACCT']['RESULT']['STATUS'] )
    {
        $error = $output['CREATEACCT']['RESULT']['STATUSMSG'];
        if( !$error )
        {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    if( $dedicatedip == 'y' )
    {
        $newaccountip = $output['CREATEACCT']['RESULT']['OPTIONS']['IP'];
        update_query('tblhosting', array( 'dedicatedip' => $newaccountip ), array( 'id' => $params['serviceid'] ));
    }
    if( $params['type'] == 'reselleraccount' )
    {
        $makeowner = $params['configoption24'] ? 1 : 0;
        $cpanelrequest = "/xml-api/setupreseller?user=" . $params['username'] . "&makeowner=" . $makeowner;
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['SETUPRESELLER']['RESULT']['STATUS'] )
        {
            $error = $output['SETUPRESELLER']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
        $cpanelrequest = "/xml-api/setresellerlimits?user=" . $params['username'];
        if( $params['configoption16'] )
        {
            $cpanelrequest .= "&enable_resource_limits=1&diskspace_limit=" . urlencode($params['configoption17']) . "&bandwidth_limit=" . urlencode($params['configoption18']);
            if( $params['configoption19'] )
            {
                $cpanelrequest .= "&enable_overselling_diskspace=1";
            }
            if( $params['configoption20'] )
            {
                $cpanelrequest .= "&enable_overselling_bandwidth=1";
            }
        }
        if( $params['configoption15'] )
        {
            $cpanelrequest .= "&enable_account_limit=1&account_limit=" . urlencode($params['configoption15']);
        }
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['SETRESELLERLIMITS']['RESULT']['STATUS'] )
        {
            $error = $output['SETRESELLERLIMITS']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
        $cpanelrequest = "/xml-api/setacls?reseller=" . $params['username'] . "&acllist=" . urlencode($params['configoption21']);
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['SETACLS']['RESULT']['STATUS'] )
        {
            $error = $output['SETACLS']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
        if( $params['configoption23'] )
        {
            $cpanelrequest = "/xml-api/setresellernameservers?user=" . $params['username'] . "&nameservers=ns1." . $params['domain'] . ",ns2." . $params['domain'];
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
            if( !is_array($output) )
            {
                return $output;
            }
            if( !$output['SETRESELLERNAMESERVERS']['RESULT']['STATUS'] )
            {
                $error = $output['SETRESELLERNAMESERVERS']['RESULT']['STATUSMSG'];
                if( !$error )
                {
                    $error = "An unknown error occurred";
                }
                return $error;
            }
        }
    }
    return 'success';
}
function cpanel_SuspendAccount($params)
{
    if( !$params['username'] )
    {
        return "Cannot perform action without accounts username";
    }
    if( $params['type'] == 'reselleraccount' )
    {
        $cpanelrequest = "/scripts/suspendreseller?reseller=" . $params['username'] . "&resalso=1";
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest, true);
        if( strpos($output, "Curl Error") == true )
        {
            $result = $output;
        }
        else
        {
            if( strpos($output, "<form action=\"/login/\" method=\"POST\">") == true )
            {
                $result = "Login Failed";
            }
            else
            {
                if( strpos($output, "account has been suspended") == true )
                {
                    $result = 'success';
                }
                else
                {
                    if( strpos($output, "Account Already Suspended") == true )
                    {
                        $result = "Account Already Suspended";
                    }
                    else
                    {
                        if( strpos($output, "You do not have permission to suspend that account") == true )
                        {
                            $result = "You do not have permission to suspend that account";
                        }
                        else
                        {
                            $result = "An Unknown Error Occurred";
                        }
                    }
                }
            }
        }
        return $result;
    }
    $cpanelrequest = "/xml-api/suspendacct?user=" . $params['username'] . "&reason=" . urlencode($params['suspendreason']);
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( !is_array($output) )
    {
        return $output;
    }
    if( !$output['SUSPENDACCT']['RESULT']['STATUS'] )
    {
        $error = $output['SUSPENDACCT']['RESULT']['STATUSMSG'];
        if( !$error )
        {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    return 'success';
}
function cpanel_UnsuspendAccount($params)
{
    if( !$params['username'] )
    {
        return "Cannot perform action without accounts username";
    }
    if( $params['type'] == 'reselleraccount' )
    {
        $cpanelrequest = "/scripts/suspendreseller?reseller=" . $params['username'] . "&resalso=1&un=1";
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest, true);
        if( strpos($output, "Curl Error") == true )
        {
            $result = $output;
        }
        else
        {
            if( strpos($output, "<form action=\"/login/\" method=\"POST\">") == true )
            {
                $result = "Login Failed";
            }
            else
            {
                if( strpos($output, "does not exist") == true )
                {
                    $result = "Account Does Not Exist";
                }
                else
                {
                    if( strpos($output, "Complete!") == true )
                    {
                        $result = 'success';
                    }
                    else
                    {
                        $result = "An Unknown Error Occurred";
                    }
                }
            }
        }
        return $result;
    }
    $cpanelrequest = "/xml-api/unsuspendacct?user=" . $params['username'];
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( !is_array($output) )
    {
        return $output;
    }
    if( !$output['UNSUSPENDACCT']['RESULT']['STATUS'] )
    {
        $error = $output['UNSUSPENDACCT']['RESULT']['STATUSMSG'];
        if( !$error )
        {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    return 'success';
}
function cpanel_TerminateAccount($params)
{
    if( !$params['username'] )
    {
        return "Cannot perform action without accounts username";
    }
    if( $params['type'] == 'reselleraccount' )
    {
        $cpanelrequest = "/xml-api/terminatereseller?reseller=" . $params['username'] . "&terminatereseller=1&verify=I%20understand%20this%20will%20irrevocably%20remove%20all%20the%20accounts%20owned%20by%20the%20reseller%20" . $params['username'];
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['TERMINATERESELLER']['RESULT']['STATUS'] )
        {
            $error = $output['TERMINATERESELLER']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
    }
    else
    {
        $cpanelrequest = "/xml-api/removeacct?user=" . $params['username'];
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['REMOVEACCT']['RESULT']['STATUS'] )
        {
            $error = $output['REMOVEACCT']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
    }
    return 'success';
}
function cpanel_ChangePassword($params)
{
    $cpanelrequest = "/xml-api/passwd?user=" . $params['username'] . "&pass=" . urlencode($params['password']);
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( !is_array($output) )
    {
        return $output;
    }
    if( !$output['PASSWD']['PASSWD1']['STATUS'] )
    {
        $error = $output['PASSWD']['PASSWD1']['STATUSMSG'];
        if( !$error )
        {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    return 'success';
}
function cpanel_ChangePackage($params)
{
    if( $params['configoptions']["Package Name"] )
    {
        $params['configoption1'] = $params['configoptions']["Package Name"];
    }
    if( $params['configoption22'] )
    {
        $prefix = $params['serverusername'] . '_';
    }
    else
    {
        $prefix = '';
    }
    $cpanelrequest = '/xml-api/listresellers';
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest, true);
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $output, $xml_values);
    xml_parser_free($parser);
    $rusernames = array(  );
    foreach( $xml_values as $vals )
    {
        if( $vals['tag'] == 'reseller' )
        {
            $rusernames[] = $vals['value'];
        }
    }
    if( $params['type'] == 'reselleraccount' )
    {
        if( !in_array($params['username'], $rusernames) )
        {
            $makeowner = $params['configoption24'] ? 1 : 0;
            $cpanelrequest = "/xml-api/setupreseller?user=" . $params['username'] . "&makeowner=" . $makeowner;
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
            if( !is_array($output) )
            {
                return $output;
            }
            if( !$output['SETUPRESELLER']['RESULT']['STATUS'] )
            {
                $error = $output['SETUPRESELLER']['RESULT']['STATUSMSG'];
                if( !$error )
                {
                    $error = "An unknown error occurred";
                }
                return $error;
            }
        }
        if( $params['configoption21'] )
        {
            $cpanelrequest = "/xml-api/setacls?reseller=" . $params['username'] . "&acllist=" . urlencode($params['configoption21']);
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
            if( !is_array($output) )
            {
                return $output;
            }
            if( !$output['SETACLS']['RESULT']['STATUS'] )
            {
                $error = $output['SETACLS']['RESULT']['STATUSMSG'];
                if( !$error )
                {
                    $error = "An unknown error occurred";
                }
                return $error;
            }
        }
        $cpanelrequest = "/xml-api/setresellerlimits?user=" . $params['username'];
        if( $params['configoption16'] )
        {
            $cpanelrequest .= "&enable_resource_limits=1&diskspace_limit=" . urlencode($params['configoption17']) . "&bandwidth_limit=" . urlencode($params['configoption18']);
            if( $params['configoption19'] )
            {
                $cpanelrequest .= "&enable_overselling_diskspace=1";
            }
            if( $params['configoption20'] )
            {
                $cpanelrequest .= "&enable_overselling_bandwidth=1";
            }
        }
        else
        {
            $cpanelrequest .= "&enable_resource_limits=0";
        }
        if( $params['configoption15'] )
        {
            if( $params['configoption15'] == 'unlimited' )
            {
                $cpanelrequest .= "&enable_account_limit=1&account_limit=";
            }
            else
            {
                $cpanelrequest .= "&enable_account_limit=1&account_limit=" . urlencode($params['configoption15']);
            }
        }
        else
        {
            $cpanelrequest .= "&enable_account_limit=0&account_limit=";
        }
        $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        if( !is_array($output) )
        {
            return $output;
        }
        if( !$output['SETRESELLERLIMITS']['RESULT']['STATUS'] )
        {
            $error = $output['SETRESELLERLIMITS']['RESULT']['STATUSMSG'];
            if( !$error )
            {
                $error = "An unknown error occurred";
            }
            return $error;
        }
    }
    else
    {
        if( in_array($params['username'], $rusernames) )
        {
            $cpanelrequest = "/xml-api/unsetupreseller?user=" . $params['username'];
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        }
        if( $params['configoption1'] != 'Custom' )
        {
            $cpanelrequest = "/xml-api/changepackage?user=" . $params['username'] . "&pkg=" . urlencode($prefix . $params['configoption1']);
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
            if( !is_array($output) )
            {
                return $output;
            }
            if( !$output['CHANGEPACKAGE']['RESULT']['STATUS'] )
            {
                $error = $output['CHANGEPACKAGE']['RESULT']['STATUSMSG'];
                if( !$error )
                {
                    $error = "An unknown error occurred";
                }
                return $error;
            }
        }
    }
    if( CPANELCONFPACKAGEADDONLICENSE && count($params['configoptions']) )
    {
        if( isset($params['configoptions']["Disk Space"]) )
        {
            $params['configoption3'] = cpanel_costrrpl($params['configoptions']["Disk Space"]);
            $cpanelrequest = "/scripts/editquota?user=" . $params['username'] . "&quota=" . $params['configoption3'] . '';
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest, true);
        }
        if( isset($params['configoptions']['Bandwidth']) )
        {
            $params['configoption5'] = cpanel_costrrpl($params['configoptions']['Bandwidth']);
            $cpanelrequest = "/scripts2/dolimitbw?user=" . $params['username'] . "&bwlimit=" . $params['configoption5'] . '';
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest, true);
        }
        $cpanelrequest = '';
        if( isset($params['configoptions']["FTP Accounts"]) )
        {
            $params['configoption2'] = cpanel_costrrpl($params['configoptions']["FTP Accounts"]);
            $cpanelrequest .= "MAXFTP=" . $params['configoption2'] . "&";
        }
        if( isset($params['configoptions']["Email Accounts"]) )
        {
            $params['configoption4'] = cpanel_costrrpl($params['configoptions']["Email Accounts"]);
            $cpanelrequest .= "MAXPOP=" . $params['configoption4'] . "&";
        }
        if( isset($params['configoptions']["MySQL Databases"]) )
        {
            $params['configoption8'] = cpanel_costrrpl($params['configoptions']["MySQL Databases"]);
            $cpanelrequest .= "MAXSQL=" . $params['configoption8'] . "&";
        }
        if( isset($params['configoptions']['Subdomains']) )
        {
            $params['configoption10'] = cpanel_costrrpl($params['configoptions']['Subdomains']);
            $cpanelrequest .= "MAXSUB=" . $params['configoption10'] . "&";
        }
        if( isset($params['configoptions']["Parked Domains"]) )
        {
            $params['configoption12'] = cpanel_costrrpl($params['configoptions']["Parked Domains"]);
            $cpanelrequest .= "MAXPARK=" . $params['configoption12'] . "&";
        }
        if( isset($params['configoptions']["Addon Domains"]) )
        {
            $params['configoption14'] = cpanel_costrrpl($params['configoptions']["Addon Domains"]);
            $cpanelrequest .= "MAXADDON=" . $params['configoption14'] . "&";
        }
        if( isset($params['configoptions']["CGI Access"]) )
        {
            $params['configoption9'] = cpanel_costrrpl($params['configoptions']["CGI Access"]);
            $cpanelrequest .= "HASCGI=" . $params['configoption9'] . "&";
        }
        if( isset($params['configoptions']["Shell Access"]) )
        {
            $params['configoption7'] = cpanel_costrrpl($params['configoptions']["Shell Access"]);
            $cpanelrequest .= "shell=" . $params['configoption7'] . "&";
        }
        if( $cpanelrequest )
        {
            $cpanelrequest = "/xml-api/modifyacct?user=" . $params['username'] . "&domain=" . $params['domain'] . "&" . $cpanelrequest;
            if( $params['configoption13'] )
            {
                $cpanelrequest .= "CPTHEME=" . $params['configoption13'];
            }
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
        }
        if( isset($params['configoptions']["Dedicated IP"]) )
        {
            $params['configoption6'] = cpanel_costrrpl($params['configoptions']["Dedicated IP"]);
            if( $params['configoption6'] )
            {
                $currentip = '';
                $alreadydedi = false;
                $cpanelrequest = "/xml-api/accountsummary?user=" . $params['username'];
                $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
                $currentip = $output['ACCOUNTSUMMARY']['ACCT']['IP'];
                $cpanelrequest = '/xml-api/listips';
                $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
                foreach( $output['LISTIPS'] as $result )
                {
                    if( $result['IP'] == $currentip && $result['MAINADDR'] != '1' )
                    {
                        $alreadydedi = true;
                    }
                }
                if( !$alreadydedi )
                {
                    foreach( $output['LISTIPS'] as $result )
                    {
                        $active = $result['ACTIVE'];
                        $dedicated = $result['DEDICATED'];
                        $ipaddr = $result['IP'];
                        $used = $result['USED'];
                        if( $active && $dedicated && !$used )
                        {
                            break;
                        }
                    }
                    $cpanelrequest = "/xml-api/setsiteip?user=" . $params['username'] . "&ip=" . $ipaddr;
                    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
                    if( $output['SETSITEIP']['RESULT']['STATUS'] )
                    {
                        update_query('tblhosting', array( 'dedicatedip' => $ipaddr ), array( 'id' => $params['serviceid'] ));
                    }
                }
            }
        }
    }
    return 'success';
}
function cpanel_LoginLink($params)
{
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $code = sprintf("<a href=\"%s://%s:%s/xfercpanel/%s\" target=\"_blank\" class=\"moduleloginlink\">%s</a>", $params['serversecure'] ? 'https' : 'http', WHMCS_Input_Sanitize::encode($domain), $params['serversecure'] ? '2087' : '2086', WHMCS_Input_Sanitize::encode($params['username']), "login to control panel");
    return $code;
}
function cpanel_UsageUpdate($params)
{
    $cpanelrequest = '/xml-api/listaccts';
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( is_array($output) && $output['LISTACCTS'] )
    {
        foreach( $output['LISTACCTS'] as $data )
        {
            $domain = $data['DOMAIN'];
            $diskused = $data['DISKUSED'];
            $disklimit = $data['DISKLIMIT'];
            $diskused = str_replace('M', '', $diskused);
            $disklimit = str_replace('M', '', $disklimit);
            update_query('tblhosting', array( 'diskusage' => $diskused, 'disklimit' => $disklimit, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
        }
    }
    unset($output);
    $cpanelrequest = '/xml-api/showbw';
    $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
    if( is_array($output) && $output['SHOWBW'] )
    {
        foreach( $output['SHOWBW']['BANDWIDTH'] as $data )
        {
            $domain = $data['MAINDOMAIN'];
            $bwused = $data['TOTALBYTES'];
            $bwlimit = $data['LIMIT'];
            $bwused = $bwused / (1024 * 1024);
            $bwlimit = $bwlimit / (1024 * 1024);
            update_query('tblhosting', array( 'bwusage' => $bwused, 'bwlimit' => $bwlimit, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
        }
    }
    unset($output);
    $result = select_query('tblhosting', 'domain,username', array( 'server' => $params['serverid'], 'type' => 'reselleraccount' ), '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
    while( $data = mysql_fetch_array($result) )
    {
        $domain = $data['domain'];
        $username = $data['username'];
        if( $username )
        {
            $cpanelrequest = "/xml-api/resellerstats?reseller=" . $username;
            $output = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], $cpanelrequest);
            if( is_array($output) && $output['RESELLERSTATS'] )
            {
                $diskused = $output['RESELLERSTATS']['RESULT']['DISKUSED'];
                $disklimit = $output['RESELLERSTATS']['RESULT']['DISKQUOTA'];
                if( !$disklimit )
                {
                    $disklimit = $output['RESELLERSTATS']['RESULT']['TOTALDISKALLOC'];
                }
                $bwused = $output['RESELLERSTATS']['RESULT']['TOTALBWUSED'];
                $bwlimit = $output['RESELLERSTATS']['RESULT']['BANDWIDTHLIMIT'];
                if( !$bwlimit )
                {
                    $bwlimit = $output['RESELLERSTATS']['RESULT']['TOTALBWALLOC'];
                }
                update_query('tblhosting', array( 'diskusage' => $diskused, 'disklimit' => $disklimit, 'bwusage' => $bwused, 'bwlimit' => $bwlimit, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
            }
        }
        unset($output);
        unset($username);
        unset($domain);
    }
}
function cpanel_req($usessl, $host, $user, $pass, $accesshash, $request, $notxml = '')
{
    $cleanaccesshash = preg_replace("'(\r|\n)'", '', $accesshash);
    if( $cleanaccesshash )
    {
        $authstr = "WHM " . $user . ":" . $cleanaccesshash;
    }
    else
    {
        $authstr = "Basic " . base64_encode($user . ":" . $pass);
    }
    $results = array(  );
    $ch = curl_init();
    if( $usessl )
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, "https://" . $host . ":2087" . $request);
    }
    else
    {
        curl_setopt($ch, CURLOPT_URL, "http://" . $host . ":2086" . $request);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curlheaders[0] = "Authorization: " . $authstr;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    $data = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $results = "(Curl Error) " . curl_error($ch) . " - code: " . curl_errno($ch) . '';
    }
    else
    {
        if( $notxml )
        {
            $results = $data;
        }
        else
        {
            if( strpos($data, "Brute Force Protection") == true )
            {
                $results = "WHM has imposed a Brute Force Protection Block - Contact cPanel for assistance";
            }
            else
            {
                if( strpos($data, "<form action=\"/login/\" method=\"POST\">") == true )
                {
                    $results = "Login Failed";
                }
                else
                {
                    if( strpos($data, "SSL encryption is required") == true )
                    {
                        $results = "SSL Required for Login";
                    }
                    else
                    {
                        if( strpos($data, "META HTTP-EQUIV=\"refresh\" CONTENT=") && !$usessl )
                        {
                            $results = "You must enable SSL Mode";
                        }
                        else
                        {
                            if( substr($data, 0, 1) != "<" )
                            {
                                $data = substr($data, strpos($data, "<"));
                            }
                            $results = XMLtoArray($data);
                            if( $results['CPANELRESULT']['DATA']['REASON'] == "Access denied" )
                            {
                                $results = "Login Failed";
                            }
                        }
                    }
                }
            }
        }
    }
    curl_close($ch);
    $action = explode("?", $request);
    $action = $action[0];
    $action = str_replace('/xml-api/', '', $action);
    logModuleCall('cpanel', $action, $request, $data, $results);
    unset($data);
    return $results;
}
function cpanel_TestConnection($params)
{
    $response = cpanel_req($params['serversecure'], $params['serverip'], $params['serverusername'], $params['serverpassword'], $params['serveraccesshash'], '/xml-api/version');
    if( is_array($response) && array_key_exists('VERSION', $response) )
    {
        return array( 'success' => true );
    }
    return array( 'error' => $response );
}