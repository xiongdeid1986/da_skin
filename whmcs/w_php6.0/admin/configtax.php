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
$aInt = new WHMCS_Admin("Configure Tax Setup");
$aInt->title = $aInt->lang('taxconfig', 'taxrulestitle');
$aInt->sidebar = 'config';
$aInt->icon = 'taxrules';
$aInt->helplink = 'Tax/VAT';
ob_start();
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $save_arr = array( 'TaxEnabled' => $taxenabled, 'TaxType' => $taxtype, 'TaxDomains' => $taxdomains, 'TaxBillableItems' => $taxbillableitems, 'TaxLateFee' => $taxlatefee, 'TaxCustomInvoices' => $taxcustominvoices, 'TaxL2Compound' => $taxl2compound, 'TaxInclusiveDeduct' => $taxinclusivededuct );
    foreach( $save_arr as $k => $v )
    {
        if( !isset($CONFIG[$k]) )
        {
            insert_query('tblconfiguration', array( 'setting' => $k, 'value' => $v ));
        }
        else
        {
            update_query('tblconfiguration', array( 'value' => $v ), array( 'setting' => $k ));
        }
    }
    redir("saved=true");
}
if( $action == 'add' )
{
    check_token("WHMCS.admin.default");
    $name = $whmcs->get_req_var('name');
    $state = $whmcs->get_req_var('state');
    $country = $whmcs->get_req_var('country');
    $taxrate = $whmcs->get_req_var('taxrate');
    if( $countrytype == 'any' && $statetype != 'any' )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('taxconfig', 'taxvalidationerrorcountry'));
        $validationerror = true;
    }
    else
    {
        if( $countrytype == 'any' )
        {
            $country = '';
        }
        if( $statetype == 'any' )
        {
            $state = '';
        }
        insert_query('tbltax', array( 'level' => $level, 'name' => $name, 'state' => $state, 'country' => $country, 'taxrate' => $taxrate ));
        redir();
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tbltax', array( 'id' => $id ));
    redir();
}
$result = select_query('tblconfiguration', '', '');
while( $data = mysql_fetch_array($result) )
{
    $setting = $data['setting'];
    $value = $data['value'];
    $CONFIG[$setting] = $value;
}
if( $saved )
{
    infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('global', 'changesuccessdesc'));
}
echo $infobox;
$aInt->deleteJSConfirm('doDelete', 'taxconfig', 'delsuretaxrule', "?action=delete&id=");
echo "\n<script type=\"text/javascript\" src=\"../includes/jscript/statesdropdown.js\"></script>\n\n<p>";
echo $aInt->lang('taxconfig', 'taxrulesconfigheredesc');
echo "</p>\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=save\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('taxconfig', 'taxsupportenabled');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"taxenabled\" id=\"taxenabled\"";
if( $CONFIG['TaxEnabled'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxenabled\">";
echo $aInt->lang('taxconfig', 'taxsupportenableddesc');
echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('taxconfig', 'taxtype');
echo "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"taxtype\" value=\"Exclusive\" id=\"taxtypeexcl\"";
if( $CONFIG['TaxType'] == 'Exclusive' )
{
    echo " checked";
}
echo "> <label for=\"taxtypeexcl\">";
echo $aInt->lang('taxconfig', 'taxtypeexclusive');
echo "</label> <input type=\"radio\" name=\"taxtype\" value=\"Inclusive\" id=\"taxtypeincl\"";
if( $CONFIG['TaxType'] == 'Inclusive' )
{
    echo " checked";
}
echo "> <label for=\"taxtypeincl\">";
echo $aInt->lang('taxconfig', 'taxtypeinclusive');
echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('taxconfig', 'taxappliesto');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"taxdomains\" id=\"taxdomains\"";
if( $CONFIG['TaxDomains'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxdomains\">";
echo $aInt->lang('taxconfig', 'taxdomains');
echo "</label> <input type=\"checkbox\" name=\"taxbillableitems\" id=\"taxbillableitems\"";
if( $CONFIG['TaxBillableItems'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxbillableitems\">";
echo $aInt->lang('taxconfig', 'taxbillableitems');
echo "</label> <input type=\"checkbox\" name=\"taxlatefee\" id=\"taxlatefee\"";
if( $CONFIG['TaxLateFee'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxlatefee\">";
echo $aInt->lang('taxconfig', 'taxlatefee');
echo "</label> <input type=\"checkbox\" name=\"taxcustominvoices\" id=\"taxcustominvoices\"";
if( $CONFIG['TaxCustomInvoices'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxcustominvoices\">";
echo $aInt->lang('taxconfig', 'taxcustominvoices');
echo "</label> (";
echo $aInt->lang('taxconfig', 'taxproducts');
echo ")</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('taxconfig', 'compoundtax');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"taxl2compound\" id=\"taxl2compound\"";
if( $CONFIG['TaxL2Compound'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxl2compound\">";
echo $aInt->lang('taxconfig', 'compoundtaxdesc');
echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('taxconfig', 'deducttaxamount');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"taxinclusivededuct\" id=\"taxinclusivededuct\"";
if( $CONFIG['TaxInclusiveDeduct'] == 'on' )
{
    echo " checked";
}
echo "> <label for=\"taxinclusivededuct\">";
echo $aInt->lang('taxconfig', 'deducttaxamountdesc');
echo "</label></td></tr>\n</table>\n<p align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n</form>\n\n<br>\n\n";
echo $aInt->Tabs(array( $aInt->lang('taxconfig', 'level1rules'), $aInt->lang('taxconfig', 'level2rules'), $aInt->lang('taxconfig', 'addnewrule') ));
if( $validationerror )
{
    $jquerycode = "\$(\".tab\").removeClass(\"tabselected\");\$(\".tabbox\").hide();\$(\"#tab2\").addClass(\"tabselected\");\$(\"#tab2box\").show();";
}
echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
$aInt->sortableTableInit('nopagination');
$tabledata = '';
$result = select_query('tbltax', '', array( 'level' => '1' ), 'state', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $name = $data['name'];
    $state = $data['state'];
    $country = $data['country'];
    $taxrate = $data['taxrate'];
    if( $state == '' )
    {
        $state = $aInt->lang('taxconfig', 'taxappliesanystate');
    }
    if( $country == '' )
    {
        $country = $aInt->lang('taxconfig', 'taxappliesanycountry');
    }
    $tabledata[] = array( $name, $country, $state, $taxrate . "%", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" border=\"0\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'name'), $aInt->lang('fields', 'country'), $aInt->lang('fields', 'state'), $aInt->lang('fields', 'taxrate'), '' ), $tabledata);
echo "\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
$aInt->sortableTableInit('nopagination');
$tabledata = '';
$result = select_query('tbltax', '', array( 'level' => '2' ), 'state', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $name = $data['name'];
    $state = $data['state'];
    $country = $data['country'];
    $taxrate = $data['taxrate'];
    if( $state == '' )
    {
        $state = $aInt->lang('taxconfig', 'taxappliesanystate');
    }
    if( $country == '' )
    {
        $country = $aInt->lang('taxconfig', 'taxappliesanycountry');
    }
    $tabledata[] = array( $name, $country, $state, $taxrate . "%", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" border=\"0\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'name'), $aInt->lang('fields', 'country'), $aInt->lang('fields', 'state'), $aInt->lang('fields', 'taxrate'), '' ), $tabledata);
echo "\n  </div>\n</div>\n<div id=\"tab2box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=add\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">Level</td><td class=\"fieldarea\"><select name=\"level\"><option>1</option><option";
if( $_POST['level'] == 2 )
{
    echo " selected";
}
echo ">2</option></select></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'name');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"30\" value=\"";
echo $_POST['name'];
echo "\" ></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'country');
echo "</td><td class=\"fieldarea\"><label><input type=\"radio\" name=\"countrytype\" value=\"any\" checked> ";
echo $aInt->lang('taxconfig', 'taxappliesallcountry');
echo "</label><br /><label><input type=\"radio\" name=\"countrytype\" value=\"specific\"";
if( $_POST['countrytype'] == 'specific' )
{
    echo " checked";
}
echo "> ";
echo $aInt->lang('taxconfig', 'taxappliesspecificcountry');
echo ":<label> ";
include("../includes/clientfunctions.php");
include("../includes/countries.php");
echo getCountriesDropDown($_POST['country']);
echo "</td></tr>\n<tr><td class=\"fieldlabel\">State</td><td class=\"fieldarea\"><label><input type=\"radio\" name=\"statetype\" value=\"any\" checked> ";
echo $aInt->lang('taxconfig', 'taxappliesallstate');
echo "</label><br /><label><input type=\"radio\" name=\"statetype\" value=\"specific\"";
if( $_POST['statetype'] == 'specific' )
{
    echo " checked";
}
echo "> ";
echo $aInt->lang('taxconfig', 'taxappliesspecificstate');
echo ":</label> <input type=\"text\" name=\"state\" size=\"25\" value=\"";
echo $_POST['state'];
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'taxrate');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"taxrate\" size=\"10\" value=\"";
echo isset($_POST['taxrate']) ? $_POST['taxrate'] : "0.00";
echo "\" /> %</td></tr>\n</table>\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<div align=center><input type=\"submit\" value=\"";
echo $aInt->lang('taxconfig', 'addrule');
echo "\" class=\"button\"></div>\n\n</form>\n\n  </div>\n</div>\n\n<script type=\"text/javascript\" src=\"../includes/jscript/statesdropdown.js\"></script>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();