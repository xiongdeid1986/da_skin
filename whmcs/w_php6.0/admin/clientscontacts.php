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
$aInt = new WHMCS_Admin("Edit Clients Details");
$aInt->requiredFiles(array( 'clientfunctions' ));
$aInt->inClientsProfile = true;
$aInt->valUserID($userid);
$aInt->assertClientBoundary($userid);
$whmcs = WHMCS_Application::getinstance();
$emailerr = $whmcs->get_req_var('emailerr');
$email = $whmcs->get_req_var('email');
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Clients Details");
    if( $subaccount )
    {
        $subaccount = '1';
        $result = select_query('tblclients', "COUNT(*)", array( 'email' => $email ));
        $data = mysql_fetch_array($result);
        $result = select_query('tblcontacts', "COUNT(*)", array( 'email' => $email, 'id' => array( 'sqltype' => 'NEQ', 'value' => $contactid ) ));
        $data2 = mysql_fetch_array($result);
        if( $data[0] + $data2[0] )
        {
            $querystring = '';
            foreach( $_REQUEST as $k => $v )
            {
                if( !is_array($v) && $k != 'action' )
                {
                    $querystring .= "&" . $k . "=" . urlencode($v);
                }
            }
            redir("error=" . $_LANG['ordererroruserexists'] . $querystring);
        }
    }
    else
    {
        $subaccount = '0';
    }
    if( $domainemails )
    {
        $domainemails = 1;
    }
    if( $generalemails )
    {
        $generalemails = 1;
    }
    if( $invoiceemails )
    {
        $invoiceemails = 1;
    }
    if( $productemails )
    {
        $productemails = 1;
    }
    if( $supportemails )
    {
        $supportemails = 1;
    }
    if( $affiliateemails )
    {
        $affiliateemails = 1;
    }
    $valErr = '';
    $validate = new WHMCS_Validate();
    $queryStr = "userid=" . $userid . "&contactid=" . $contactid;
    if( $validate->validate('required', 'email', 'erroremail') )
    {
        if( !$validate->validate('email', 'email', 'erroremailinvalid') )
        {
            $valErr = 'erroremailinvalid';
        }
    }
    else
    {
        $valErr = 'erroremail';
    }
    if( 0 < strlen($valErr) )
    {
        $queryStr .= "&emailerr=" . $valErr;
        redir($queryStr);
    }
    if( $contactid == 'addnew' )
    {
        if( $password && $password != $aInt->lang('fields', 'password') )
        {
            $array['password'] = generateClientPW($password);
        }
        $contactid = addContact($userid, $firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $password, $permissions, $generalemails, $productemails, $domainemails, $invoiceemails, $supportemails, $affiliateemails);
        logActivity("Added Contact - User ID: " . $userid . " - Contact ID: " . $contactid);
    }
    else
    {
        logActivity("Contact Modified - User ID: " . $userid . " - Contact ID: " . $contactid);
        $oldcontactdata = get_query_vals('tblcontacts', '', array( 'userid' => $_SESSION['uid'], 'id' => $id ));
        if( $permissions )
        {
            $permissions = implode(',', $permissions);
        }
        $table = 'tblcontacts';
        $array = array( 'firstname' => $firstname, 'lastname' => $lastname, 'companyname' => $companyname, 'email' => $email, 'address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'postcode' => $postcode, 'country' => $country, 'phonenumber' => $phonenumber, 'subaccount' => $subaccount, 'permissions' => $permissions, 'domainemails' => $domainemails, 'generalemails' => $generalemails, 'invoiceemails' => $invoiceemails, 'productemails' => $productemails, 'supportemails' => $supportemails, 'affiliateemails' => $affiliateemails );
        if( $password && $password != $aInt->lang('fields', 'entertochange') )
        {
            $array['password'] = generateClientPW($password);
        }
        $where = array( 'id' => $contactid );
        update_query($table, $array, $where);
        run_hook('ContactEdit', array_merge(array( 'userid' => $userid, 'contactid' => $contactid, 'olddata' => $oldcontactdata ), $array));
    }
    redir($queryStr);
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblcontacts', array( 'id' => $contactid ));
    update_query('tblclients', array( 'billingcid' => '' ), array( 'billingcid' => $contactid ));
    run_hook('ContactDelete', array( 'userid' => $userid, 'contactid' => $contactid ));
    redir("userid=" . $userid);
}
if( $resetpw )
{
    check_token("WHMCS.admin.default");
    sendMessage("Automated Password Reset", $userid, array( 'contactid' => $contactid ));
    redir("userid=" . $userid . "&contactid=" . $contactid . "&pwreset=1");
}
ob_start();
$infobox = '';
if( $pwreset )
{
    infoBox($aInt->lang('clients', 'resetsendpassword'), $aInt->lang('clients', 'passwordsuccess'));
}
if( $error )
{
    infoBox($aInt->lang('global', 'validationerror'), $error);
}
if( 0 < strlen($aInt->lang('clients', $emailerr)) )
{
    if( !empty($_ADMINLANG['clients'][$emailerr]) )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('clients', $emailerr), 'error');
    }
    else
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('clients', 'invalidemail'), 'error');
    }
}
echo $infobox;
echo "\n<form action=\"";
echo $_SERVER['PHP_SELF'];
echo "\" method=\"get\">\n<input type=\"hidden\" name=\"userid\" value=\"";
echo $userid;
echo "\">\n";
echo $aInt->lang('clientsummary', 'contacts');
echo ": <select name=\"contactid\" onChange=\"submit();\">\n";
$result = select_query('tblcontacts', '', array( 'userid' => $userid ), "firstname` ASC,`lastname", 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $contactlistid = $data['id'];
    if( !$contactid )
    {
        $contactid = $contactlistid;
    }
    $contactlistfirstname = $data['firstname'];
    $contactlistlastname = $data['lastname'];
    $contactlistemail = $data['email'];
    echo "<option value=\"" . $contactlistid . "\"";
    if( $contactlistid == $contactid )
    {
        echo " selected";
    }
    echo ">" . $contactlistfirstname . " " . $contactlistlastname . " - " . $contactlistemail . "</option>";
}
if( !$contactid )
{
    $contactid = 'addnew';
}
echo "<option value=\"addnew\"";
if( $contactid == 'addnew' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('global', 'addnew');
echo "</option>\n</select> <input type=\"submit\" value=\"";
echo $aInt->lang('global', 'go');
echo "\">\n</form>\n\n<br>\n\n";
$aInt->deleteJSConfirm('deleteContact', 'clients', 'deletecontactconfirm', "?action=delete&userid=" . $userid . "&contactid=");
if( $contactid && $contactid != 'addnew' )
{
    $result = select_query('tblcontacts', '', array( 'userid' => $userid, 'id' => $contactid ));
    $data = mysql_fetch_array($result);
    $contactid = $data['id'];
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $companyname = $data['companyname'];
    $email = $data['email'];
    $address1 = $data['address1'];
    $address2 = $data['address2'];
    $city = $data['city'];
    $state = $data['state'];
    $postcode = $data['postcode'];
    $country = $data['country'];
    $phonenumber = $data['phonenumber'];
    $subaccount = $data['subaccount'];
    $password = $data['password'];
    $permissions = explode(',', $data['permissions']);
    $generalemails = $data['generalemails'];
    $productemails = $data['productemails'];
    $domainemails = $data['domainemails'];
    $invoiceemails = $data['invoiceemails'];
    $supportemails = $data['supportemails'];
    $affiliateemails = $data['affiliateemails'];
    $password = $CONFIG['NOMD5'] ? decrypt($data['password']) : $aInt->lang('fields', 'entertochange');
}
if( !is_array($permissions) )
{
    $permissions = array(  );
}
echo "\n<form method=\"post\" action=\"";
echo $_SERVER['PHP_SELF'];
echo "?action=save&userid=";
echo $userid;
echo "&contactid=";
echo $contactid;
echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'firstname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"30\" name=\"firstname\" tabindex=\"1\" value=\"";
echo $firstname;
echo "\"></td><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'address');
echo " 1</td><td class=\"fieldarea\"><input type=\"text\" size=\"30\" name=\"address1\" tabindex=\"7\" value=\"";
echo $address1;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'lastname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"30\" name=\"lastname\" tabindex=\"2\" value=\"";
echo $lastname;
echo "\"></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'address');
echo " 2</td><td class=\"fieldarea\"><input type=\"text\" size=\"30\" name=\"address2\" tabindex=\"8\" value=\"";
echo $address2;
echo "\"> <font color=#cccccc><small>(";
echo $aInt->lang('global', 'optional');
echo ")</small></font></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'companyname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"30\" name=\"companyname\" tabindex=\"3\" value=\"";
echo $companyname;
echo "\"> <font color=#cccccc><small>(";
echo $aInt->lang('global', 'optional');
echo ")</small></font></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'city');
echo "</td><td class=\"fieldarea\"><input type=\"text\" tabindex=\"9\" size=\"25\" name=\"city\" value=\"";
echo $city;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'email');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"35\" name=\"email\"  tabindex=\"4\" value=\"";
echo $email;
echo "\"></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'state');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"25\" name=\"state\" tabindex=\"10\" value=\"";
echo $state;
echo "\"></font></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('clients', 'activatesubaccount');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" tabindex=\"5\" name=\"subaccount\" id=\"subaccount\" ";
if( $subaccount )
{
    echo 'checked';
}
echo "> <label for=\"subaccount\">";
echo $aInt->lang('global', 'ticktoenable');
echo "</label></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'postcode');
echo "</td><td class=\"fieldarea\"><input type=\"text\" tabindex=\"11\" size=\"14\" name=\"postcode\" value=\"";
echo $postcode;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'password');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"20\" name=\"password\" tabindex=\"6\" value=\"";
echo $password;
echo "\" onfocus=\"if(this.value == '";
echo $aInt->lang('fields', 'entertochange');
echo "') {this.value=''}\" />";
if( $contactid != 'addnew' && $subaccount == 1 )
{
    echo " <a href=\"clientscontacts.php?userid=";
    echo $userid;
    echo "&contactid=";
    echo $contactid;
    echo "&resetpw=true";
    echo generate_token('link');
    echo "\"><img src=\"images/icons/resetpw.png\" border=\"0\" align=\"absmiddle\" /> ";
    echo $aInt->lang('clients', 'resetsendpassword');
    echo "</a>";
}
echo "</td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'country');
echo "</td><td class=\"fieldarea\">";
include("../includes/countries.php");
echo getCountriesDropDown($country, '', '12');
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'emailnotifications');
echo "</td><td class=\"fieldarea\">\n<label><input type=\"checkbox\" name=\"generalemails\" tabindex=\"14\" ";
if( $generalemails )
{
    echo 'checked';
}
echo "> General</label>\n<label><input type=\"checkbox\" name=\"invoiceemails\" tabindex=\"15\" ";
if( $invoiceemails )
{
    echo 'checked';
}
echo "> Invoice</label>\n<label><input type=\"checkbox\" name=\"supportemails\" tabindex=\"16\" ";
if( $supportemails )
{
    echo 'checked';
}
echo "> Support</label><br />\n<label><input type=\"checkbox\" name=\"productemails\" tabindex=\"17\" ";
if( $productemails )
{
    echo 'checked';
}
echo "> Product</label>\n<label><input type=\"checkbox\" name=\"domainemails\" tabindex=\"18\" ";
if( $domainemails )
{
    echo 'checked';
}
echo "> Domain</label>\n<label><input type=\"checkbox\" name=\"affiliateemails\" tabindex=\"19\" ";
if( $affiliateemails )
{
    echo 'checked';
}
echo "> Affiliate</label>\n</td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'phonenumber');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"20\" name=\"phonenumber\" tabindex=\"13\" value=\"";
echo $phonenumber;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'permissions');
echo "</td><td class=\"fieldarea\" colspan=\"3\">\n<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"50%\" valign=\"top\">\n";
$taxindex = 20;
$perms = array( 'profile', 'contacts', 'products', 'manageproducts', 'domains', 'managedomains', 'invoices', 'tickets', 'affiliates', 'emails', 'orders' );
foreach( $perms as $perm )
{
    $taxindex++;
    echo "<label><input type=\"checkbox\" name=\"permissions[]\" tabindex=\"" . $taxindex . "\" value=\"" . $perm . "\"";
    if( in_array($perm, $permissions) )
    {
        echo " checked";
    }
    echo " /> " . $aInt->lang('contactpermissions', 'perm' . $perm) . "</label><br />";
    if( $perm == 'managedomains' )
    {
        echo "</td><td width=\"50%\" valign=\"top\">";
    }
}
echo "</td></tr></table>\n</td></tr>\n</table>\n\n<p align=\"center\">";
if( $contactid != 'addnew' )
{
    echo "<input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'savechanges');
    echo "\" class=\"btn btn-primary\" tabindex=\"";
    echo $taxindex++;
    echo "\" /> <input type=\"reset\" value=\"";
    echo $aInt->lang('global', 'cancelchanges');
    echo "\" class=\"button\" tabindex=\"";
    echo $taxindex++;
    echo "\" /><br />\n<a href=\"#\" onClick=\"deleteContact('";
    echo $contactid;
    echo "');return false\" style=\"color:#cc0000\"><b>";
    echo $aInt->lang('global', 'delete');
    echo "</b></a>";
}
else
{
    echo "<input type=\"submit\" value=\"";
    echo $aInt->lang('clients', 'addcontact');
    echo "\" class=\"btn btn-primary\" tabindex=\"";
    echo $taxindex++;
    echo "\" /> <input type=\"reset\" value=\"";
    echo $aInt->lang('global', 'cancelchanges');
    echo "\" class=\"button\" tabindex=\"";
    echo $taxindex++;
    echo "\" />";
}
echo "</p>\n\n</form>\n\n  </div>\n</div>\n\n<script type=\"text/javascript\" src=\"../includes/jscript/statesdropdown.js\"></script>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();