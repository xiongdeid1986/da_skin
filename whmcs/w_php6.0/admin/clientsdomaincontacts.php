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
define('ADMINAREA', true);
require("../init.php");
$aInt = new WHMCS_Admin("Edit Clients Domains");
$aInt->title = $aInt->lang('domains', 'modifycontact');
$aInt->sidebar = 'clients';
$aInt->icon = 'clientsprofile';
$aInt->requiredFiles(array( 'clientfunctions', 'registrarfunctions' ));
ob_start();
$domains = new WHMCS_Domains();
$domain_data = $domains->getDomainsDatabyID($whmcs->get_req_var('domainid'));
$domainid = $domain_data['id'];
if( !$domainid )
{
    $aInt->gracefulExit("Domain ID Not Found");
}
$userid = $domain_data['userid'];
$aInt->valUserID($userid);
$domain = $domain_data['domain'];
$registrar = $domain_data['registrar'];
$registrationperiod = $domain_data['registrationperiod'];
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $contactdetails = $whmcs->get_req_var('contactdetails');
    $wc = $whmcs->get_req_var('wc');
    $sel = $whmcs->get_req_var('sel');
    foreach( $wc as $wc_key => $wc_val )
    {
        if( $wc_val == 'contact' )
        {
            $selectedContact = $sel[$wc_key];
            $selectedContactType = substr($selectedContact, 0, 1);
            $selectedContactID = substr($selectedContact, 1);
            $tmpcontactdetails = array(  );
            if( $selectedContactType == 'u' )
            {
                $client = new WHMCS_Client($userid);
                $tmpcontactdetails = $client->getDetails();
            }
            else
            {
                if( $selectedContactType == 'c' )
                {
                    $client = new WHMCS_Client($userid);
                    $tmpcontactdetails = $client->getDetails($selectedContactID);
                }
            }
            $contactdetails[$wc_key] = $domains->buildWHOISSaveArray($tmpcontactdetails);
        }
    }
    $success = $domains->moduleCall('SaveContactDetails', array( 'contactdetails' => $contactdetails ));
    $reDirVars = array(  );
    $reDirVars['domainid'] = $domainid;
    if( $success )
    {
        $reDirVars['editSuccess'] = true;
    }
    else
    {
        $reDirVars['editSuccess'] = false;
        WHMCS_Cookie::set('contactEditError', $domains->getLastError());
    }
    redir($reDirVars);
    exit();
}
if( $whmcs->get_req_var('editSuccess') == 1 )
{
    infoBox($aInt->lang('domains', 'modifySuccess'), $aInt->lang('domains', 'changesuccess'), 'success');
}
else
{
    if( $whmcs->get_req_var('editError') == 0 )
    {
        $editError = WHMCS_Input_Sanitize::makesafeforoutput(WHMCS_Cookie::get('contactEditError'));
        if( $editError )
        {
            infoBox($aInt->lang('domains', 'registrarerror'), $editError, 'error');
        }
        WHMCS_Cookie::delete('contactEditError');
    }
}
$success = $domains->moduleCall('GetContactDetails');
if( $success )
{
    $contactdetails = $domains->getModuleReturn();
}
else
{
    infoBox($aInt->lang('domains', 'registrarerror'), $domains->getLastError());
}
echo "<script language=\"javascript\">\nfunction usedefaultwhois(id) {\n    jQuery(\".\"+id.substr(0,id.length-1)+\"customwhois\").attr(\"disabled\", true);\n    jQuery(\".\"+id.substr(0,id.length-1)+\"defaultwhois\").attr(\"disabled\", false);\n    jQuery('#'+id.substr(0,id.length-1)+'1').attr(\"checked\", \"checked\");\n}\nfunction usecustomwhois(id) {\n    jQuery(\".\"+id.substr(0,id.length-1)+\"customwhois\").attr(\"disabled\", false);\n    jQuery(\".\"+id.substr(0,id.length-1)+\"defaultwhois\").attr(\"disabled\", true);\n    jQuery('#'+id.substr(0,id.length-1)+'2').attr(\"checked\", \"checked\");\n}\n</script>\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?domainid=";
echo $domainid;
echo "&action=save\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'registrar');
echo "</td><td class=\"fieldarea\">";
echo ucfirst($registrar);
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'domain');
echo "</td><td class=\"fieldarea\">";
echo $domain;
echo "</td></tr>\n</table>\n\n";
echo $infobox;
if( $success )
{
    $contactsarray = array(  );
    $result = select_query('tblcontacts', 'id,firstname,lastname', array( 'userid' => $userid, 'address1' => array( 'sqltype' => 'NEQ', 'value' => '' ) ), "firstname` ASC,`lastname", 'ASC');
    while( $data = mysql_fetch_assoc($result) )
    {
        $contactsarray[] = array( 'id' => $data['id'], 'name' => $data['firstname'] . " " . $data['lastname'] );
    }
    $i = 0;
    foreach( $contactdetails as $contactdetail => $values )
    {
        echo "\n<p><b>";
        echo $contactdetail;
        echo "</b>";
        if( $i != 0 )
        {
            echo " - <a href=\"clientsdomaincontacts.php?domainid=";
            echo $domainid;
            echo "#\">";
            echo $aInt->lang('global', 'top');
            echo "</a>";
        }
        $i++;
        echo "</p>\n\n<p><input type=\"radio\" name=\"wc[";
        echo $contactdetail;
        echo "]\" id=\"";
        echo $contactdetail;
        echo "1\" value=\"contact\" onclick=\"usedefaultwhois(id)\" /> <label for=\"";
        echo $contactdetail;
        echo "1\">";
        echo $aInt->lang('domains', 'domaincontactusexisting');
        echo "</label></p>\n    <table id=\"";
        echo $contactdetail;
        echo "defaultwhois\">\n      <tr>\n        <td width=\"150\" align=\"right\">";
        echo $aInt->lang('domains', 'domaincontactchoose');
        echo "</td>\n        <td><select name=\"sel[";
        echo $contactdetail;
        echo "]\" id=\"";
        echo $contactdetail;
        echo "3\" class=\"";
        echo $contactdetail;
        echo "defaultwhois\" onclick=\"usedefaultwhois(id)\">\n            <option value=\"u";
        echo $userid;
        echo "\">";
        echo $aInt->lang('domains', 'domaincontactprimary');
        echo "</option>\n            ";
        foreach( $contactsarray as $subcontactsarray )
        {
            echo "            <option value=\"c";
            echo $subcontactsarray['id'];
            echo "\">";
            echo $subcontactsarray['name'];
            echo "</option>\n            ";
        }
        echo "          </select></td>\n      </tr>\n  </table>\n<p><input type=\"radio\" name=\"wc[";
        echo $contactdetail;
        echo "]\" id=\"";
        echo $contactdetail;
        echo "2\" value=\"custom\" onclick=\"usecustomwhois(id)\" checked /> <label for=\"";
        echo $contactdetail;
        echo "2\">";
        echo $aInt->lang('domains', 'domaincontactusecustom');
        echo "</label></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\" id=\"";
        echo $contactdetail;
        echo "customwhois\">\n";
        foreach( $values as $name => $value )
        {
            echo "<tr><td width=\"20%\" class=\"fieldlabel\">";
            echo $name;
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"contactdetails[";
            echo $contactdetail;
            echo "][";
            echo $name;
            echo "]\" value=\"";
            echo $value;
            echo "\" size=\"30\" class=\"";
            echo $contactdetail;
            echo "customwhois\"></td></tr>\n";
        }
        echo "</table>\n\n";
    }
}
echo "\n<p align=center><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"> <input type=\"button\" value=\"";
echo $aInt->lang('global', 'goback');
echo "\" class=\"button\" onClick=\"window.location='clientsdomains.php?userid=";
echo $userid;
echo "&domainid=";
echo $domainid;
echo "'\"></p>\n\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();