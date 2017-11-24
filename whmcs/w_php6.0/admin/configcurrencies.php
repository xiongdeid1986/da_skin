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
$aInt = new WHMCS_Admin("Configure Currencies", false);
$aInt->title = $aInt->lang('currencies', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'income';
$aInt->helplink = 'Currencies';
$aInt->requiredFiles(array( 'currencyfunctions' ));
if( $action == 'add' )
{
    check_token("WHMCS.admin.default");
    $prefix = strip_tags(WHMCS_Input_Sanitize::decode($prefix));
    $suffix = strip_tags(WHMCS_Input_Sanitize::decode($suffix));
    insert_query('tblcurrencies', array( 'code' => $code, 'prefix' => $prefix, 'suffix' => $suffix, 'format' => $format, 'rate' => $rate ));
    redir();
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( $id == 1 )
    {
        $rate = 1;
    }
    $prefix = strip_tags(WHMCS_Input_Sanitize::decode($prefix));
    $suffix = strip_tags(WHMCS_Input_Sanitize::decode($suffix));
    update_query('tblcurrencies', array( 'code' => $code, 'prefix' => $prefix, 'suffix' => $suffix, 'format' => $format, 'rate' => $rate ), array( 'id' => $id ));
    if( $updatepricing )
    {
        currencyUpdatePricing($id);
    }
    redir();
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblclients', "COUNT(*)", array( 'currency' => $id ));
    $data = mysql_fetch_array($result);
    $inuse = $data[0];
    if( !$inuse )
    {
        delete_query('tblcurrencies', array( 'id' => $id ));
        delete_query('tblpricing', array( 'currency' => $id ));
    }
    redir();
}
if( $action == 'updaterates' )
{
    check_token("WHMCS.admin.default");
    $msg = currencyUpdateRates();
    WHMCS_Session::set('CurrencyUpdateMsg', $msg);
    redir("updaterates=1");
}
if( $action == 'updateprices' )
{
    check_token("WHMCS.admin.default");
    currencyUpdatePricing();
    redir("updateprices=1");
}
ob_start();
if( !$action )
{
    $aInt->deleteJSConfirm('doDelete', 'currencies', 'delsure', "?action=delete&id=");
    $infobox = '';
    if( $updaterates && WHMCS_Session::get('CurrencyUpdateMsg') )
    {
        infoBox($aInt->lang('currencies', 'exchrateupdate'), WHMCS_Session::get('CurrencyUpdateMsg'));
        WHMCS_Session::delete('CurrencyUpdateMsg');
    }
    if( $updateprices )
    {
        infoBox($aInt->lang('currencies', 'updatedpricing'), $aInt->lang('currencies', 'updatepricinginfo'));
    }
    echo $infobox;
    echo "<p>" . $aInt->lang('currencies', 'info') . "</p>";
    $aInt->sortableTableInit('nopagination');
    $totalcurrencies = 0;
    for( $result = select_query('tblcurrencies', '', '', 'code', 'ASC'); $data = mysql_fetch_array($result); $totalcurrencies++ )
    {
        $id = $data['id'];
        $code = $data['code'];
        $prefix = $data['prefix'];
        $suffix = $data['suffix'];
        $format = $data['format'];
        $rate = $data['rate'];
        if( $format == 1 )
        {
            $formatex = "1234.56";
        }
        else
        {
            if( $format == 2 )
            {
                $formatex = "1,234.56";
            }
            else
            {
                if( $format == 3 )
                {
                    $formatex = "1.234,56";
                }
                else
                {
                    if( $format == 4 )
                    {
                        $formatex = '1,234';
                    }
                }
            }
        }
        if( $id != 1 )
        {
            $result2 = select_query('tblclients', "COUNT(*)", array( 'currency' => $id ));
            $data = mysql_fetch_array($result2);
            $inuse = $data[0];
            $deletelink = "<a href=\"#\" onClick=\"";
            if( $inuse )
            {
                $deletelink .= "alert('" . addslashes($aInt->lang('currencies', 'deleteinuse')) . "')";
            }
            else
            {
                $deletelink .= "doDelete('" . $id . "')";
            }
            $deletelink .= ";return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>";
        }
        else
        {
            $deletelink = '';
        }
        $tabledata[] = array( $code, $prefix, $suffix, $formatex, $rate, "<a href=\"?action=edit&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", $deletelink );
    }
    echo $aInt->sortableTable(array( $aInt->lang('currencies', 'code'), $aInt->lang('currencies', 'prefix'), $aInt->lang('currencies', 'suffix'), $aInt->lang('currencies', 'format'), $aInt->lang('currencies', 'baserate'), '', '' ), $tabledata);
    echo "\n<p align=\"center\"><input type=\"button\" value=\"";
    echo $aInt->lang('currencies', 'updateexch');
    echo "\" class=\"button\" onclick=\"window.location='configcurrencies.php?action=updaterates";
    echo generate_token('link');
    echo "'\" /> <input type=\"button\" value=\"";
    echo $aInt->lang('currencies', 'updateprod');
    echo "\" class=\"button\" onclick=\"window.location='configcurrencies.php?action=updateprices";
    echo generate_token('link');
    echo "'\" /></p>\n\n<h2>";
    echo $aInt->lang('currencies', 'addadditional');
    echo "</h2>\n\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "\">\n<input type=\"hidden\" name=\"action\" value=\"add\" />\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('currencies', 'code');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"code\" size=\"10\"> ";
    echo $aInt->lang('currencies', 'codeinfo');
    echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('currencies', 'prefix');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"prefix\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('currencies', 'suffix');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"suffix\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('currencies', 'format');
    echo "</td><td class=\"fieldarea\"><select name=\"format\">\n<option value=\"1\">1234.56</option>\n<option value=\"2\">1,234.56</option>\n<option value=\"3\">1.234,56</option>\n<option value=\"4\">1,234</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('currencies', 'baserate');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"rate\" size=\"10\"> ";
    echo $aInt->lang('currencies', 'baserateinfo');
    echo "</td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('currencies', 'add');
    echo "\" class=\"button\"></p>\n\n</form>\n\n";
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblcurrencies', '', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $code = $data['code'];
        $prefix = $data['prefix'];
        $suffix = $data['suffix'];
        $format = $data['format'];
        $rate = $data['rate'];
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<input type=\"hidden\" name=\"action\" value=\"save\" />\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\" />\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'code');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"code\" size=\"10\" value=\"";
        echo $code;
        echo "\"> ";
        echo $aInt->lang('currencies', 'codeinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'prefix');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"prefix\" size=\"10\" value=\"";
        echo WHMCS_Input_Sanitize::encode($prefix);
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'suffix');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"suffix\" size=\"10\" value=\"";
        echo WHMCS_Input_Sanitize::encode($suffix);
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'format');
        echo "</td><td class=\"fieldarea\"><select name=\"format\">\n<option value=\"1\"";
        if( $format == 1 )
        {
            echo " selected";
        }
        echo ">1234.56</option>\n<option value=\"2\"";
        if( $format == 2 )
        {
            echo " selected";
        }
        echo ">1,234.56</option>\n<option value=\"3\"";
        if( $format == 3 )
        {
            echo " selected";
        }
        echo ">1.234,56</option>\n<option value=\"4\"";
        if( $format == 4 )
        {
            echo " selected";
        }
        echo ">1,234</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'baserate');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"rate\" size=\"10\" value=\"";
        echo $rate;
        echo "\"";
        if( $id == 1 )
        {
            echo " readonly=true disabled";
        }
        echo "> ";
        echo $aInt->lang('currencies', 'baserateinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('currencies', 'updatepricing');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"updatepricing\"> ";
        echo $aInt->lang('currencies', 'recalcpricing');
        echo "</td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->display();