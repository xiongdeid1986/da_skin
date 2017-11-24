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
$aInt = new WHMCS_Admin("List Clients");
$aInt->title = $aInt->lang('clients', 'viewsearch');
$aInt->sidebar = 'clients';
$aInt->icon = 'clients';
$limitClientId = 0;
$licensing = WHMCS_License::getinstance();
if( $licensing->isClientLimitsEnabled() )
{
    $limitClientId = $licensing->getClientBoundaryId();
}
$name = 'clients';
$orderby = 'id';
$sort = 'DESC';
$pageObj = new WHMCS_Pagination($name, $orderby, $sort);
$pageObj->digestCookieData();
$tbl = new WHMCS_ListTable($pageObj);
$tbl->setColumns(array( 'checkall', array( 'id', $aInt->lang('fields', 'id') ), array( 'firstname', $aInt->lang('fields', 'firstname') ), array( 'lastname', $aInt->lang('fields', 'lastname') ), array( 'companyname', $aInt->lang('fields', 'companyname') ), array( 'email', $aInt->lang('fields', 'email') ), $aInt->lang('fields', 'services'), array( 'datecreated', $aInt->lang('fields', 'created') ), array( 'status', $aInt->lang('fields', 'status') ) ));
$clientsModel = new WHMCS_Clients($pageObj);
$filters = new WHMCS_Filter();
ob_start();
echo $aInt->Tabs(array( $aInt->lang('global', 'searchfilter') ), true);
$userid = $filters->get('userid');
$country = $filters->get('country');
echo "<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form action=\"clients.php\" method=\"post\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'clientname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"clientname\" size=\"30\" value=\"";
echo $clientname = $filters->get('clientname');
echo "\" /></td><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'companyname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"companyname\" size=\"30\" value=\"";
echo $companyname = $filters->get('companyname');
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'email');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"40\" value=\"";
echo $email = $filters->get('email');
echo "\" /></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'address');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"address\" size=\"30\" value=\"";
echo $address = $filters->get('address');
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'status');
echo "</td><td class=\"fieldarea\"><select name=\"status\"><option value=\"\">";
echo $aInt->lang('global', 'any');
echo "</option><option value=\"Active\"";
$status = $filters->get('status');
if( $status == 'Active' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'active');
echo "</option><option value=\"Inactive\"";
if( $status == 'Inactive' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'inactive');
echo "</option><option value=\"Closed\"";
if( $status == 'Closed' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'closed');
echo "</option></select></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'state');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"state\" size=\"30\" value=\"";
echo $state = $filters->get('state');
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'clientgroup');
echo "</td><td class=\"fieldarea\"><select name=\"clientgroup\"><option value=\"\">";
echo $aInt->lang('global', 'any');
echo "</option>";
$clientgroup = $filters->get('clientgroup');
foreach( $clientsModel->getGroups() as $id => $values )
{
    echo "<option value=\"" . $id . "\"";
    if( $id == $clientgroup )
    {
        echo " selected";
    }
    echo ">" . $values['name'] . "</option>";
}
echo "</select></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'phonenumber');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"phonenumber\" size=\"30\" value=\"";
echo $phonenumber = $filters->get('phonenumber');
echo "\" /></td></tr>\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('currencies', 'currency');
echo "</td><td class=\"fieldarea\"><select name=\"currency\"><option value=\"\">";
echo $aInt->lang('global', 'any');
echo "</option>";
$currency = $filters->get('currency');
$result = select_query('tblcurrencies', 'id,code', '', 'code', 'ASC');
while( $data = mysql_fetch_assoc($result) )
{
    echo "<option value=\"" . $data['id'] . "\"";
    if( $currency == $data['id'] )
    {
        echo " selected";
    }
    echo ">" . $data['code'] . "</option>";
}
echo "</select></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'cardlast4');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"cardlastfour\" size=\"15\" value=\"";
echo $cardlastfour = $filters->get('cardlastfour');
echo "\" /></td></tr>\n";
$customfields = $filters->get('customfields');
$result = select_query('tblcustomfields', 'id,fieldname', array( 'type' => 'client' ));
while( $data = mysql_fetch_array($result) )
{
    $fieldid = $data['id'];
    $fieldname = $data['fieldname'];
    echo "<tr><td class=\"fieldlabel\">" . $fieldname . "</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"customfields[" . $fieldid . "]\" size=\"30\" value=\"" . $customfields[$fieldid] . "\" /></td></tr>";
}
echo "</table>\n<p align=\"center\"><input type=\"submit\" id=\"search-clients\" value=\"";
echo $aInt->lang('global', 'search');
echo "\" class=\"button\"></p>\n</form>\n\n  </div>\n</div>\n\n<br />\n\n";
$filters->store();
$criteria = array( 'userid' => $userid, 'clientname' => $clientname, 'companyname' => $companyname, 'email' => $email, 'address' => $address, 'country' => $country, 'status' => $status, 'state' => $state, 'clientgroup' => $clientgroup, 'phonenumber' => $phonenumber, 'currency' => $currency, 'cardlastfour' => $cardlastfour, 'customfields' => $customfields );
$clientsModel->execute($criteria);
$numresults = $pageObj->getNumResults();
if( $filters->isActive() && $numresults == 1 )
{
    $client = $pageObj->getOne();
    redir("userid=" . $client['id'], "clientssummary.php");
}
else
{
    $clientlist = $pageObj->getData();
    foreach( $clientlist as $client )
    {
        $clientId = $client['id'];
        $linkopen = "<a href=\"clientssummary.php?userid=" . $client['id'] . "\"" . ($client['groupcolor'] ? " style=\"background-color:" . $client['groupcolor'] . "\"" : '') . ">";
        $linkclose = "</a>";
        $checkbox = "<input type=\"checkbox\" name=\"selectedclients[]\" value=\"" . $client['id'] . "\" class=\"checkall\" />";
        if( 0 < $limitClientId && $limitClientId <= $clientId )
        {
            $checkbox = array( 'trAttributes' => array( 'class' => 'grey-out' ), 'output' => $checkbox );
        }
        $tbl->addRow(array( $checkbox, $linkopen . $client['id'] . $linkclose, $linkopen . $client['firstname'] . $linkclose, $linkopen . $client['lastname'] . $linkclose, $client['companyname'], "<a href=\"mailto:" . $client['email'] . "\">" . $client['email'] . "</a>", $client['services'] . " (" . $client['totalservices'] . ")", $client['datecreated'], "<span class=\"label " . strtolower($client['status']) . "\">" . $client['status'] . "</span>" ));
    }
    $tbl->setMassActionURL("sendmessage.php?type=general&multiple=true");
    $tbl->setMassActionBtns("<input type=\"submit\" value=\"" . $aInt->lang('global', 'sendmessage') . "\" class=\"btn\" />");
    echo $tbl->output();
    unset($clientlist);
    unset($clientsModel);
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();