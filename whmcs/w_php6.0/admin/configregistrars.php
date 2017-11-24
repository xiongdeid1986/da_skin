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
$aInt = new WHMCS_Admin("Configure Domain Registrars");
$aInt->title = $aInt->lang('domainregistrars', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'domains';
$aInt->helplink = "Domain Registrars";
$aInt->requiredFiles(array( 'registrarfunctions', 'modulefunctions' ));
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $module = $_GET['module'];
    unset($_POST['token']);
    unset($_POST['save']);
    if( $module )
    {
        delete_query('tblregistrars', array( 'registrar' => $module ));
        $registrarModule = new WHMCS_Module_Registrar();
        $registrarModule->load($module);
        $registrarConfig = $registrarModule->call('getConfigArray');
        foreach( $registrarConfig as $key => $value )
        {
            if( isset($_POST[$key]) )
            {
                $valueToStore = $_POST[$key];
            }
            else
            {
                if( $value['Type'] == 'yesno' )
                {
                    $valueToStore = '';
                }
                else
                {
                    if( isset($value['Default']) )
                    {
                        $valueToStore = $value['Default'];
                    }
                }
            }
            if( $value['Type'] == 'yesno' )
            {
                $valueToStore = !empty($valueToStore) && $valueToStore != 'off' && $valueToStore != 'disabled' ? 'on' : '';
            }
            insert_query('tblregistrars', array( 'registrar' => $module, 'setting' => $key, 'value' => encrypt(WHMCS_Input_Sanitize::decode(trim($valueToStore))) ));
        }
    }
    RebuildRegistrarModuleHookCache();
    redir("saved=true#" . $module);
}
if( $action == 'activate' )
{
    check_token("WHMCS.admin.default");
    $module = $_GET['module'];
    if( $module )
    {
        delete_query('tblregistrars', array( 'registrar' => $module ));
        insert_query('tblregistrars', array( 'registrar' => $module, 'setting' => 'Username', 'value' => '' ));
    }
    RebuildRegistrarModuleHookCache();
    redir("activated=true#" . $module);
}
if( $action == 'deactivate' )
{
    check_token("WHMCS.admin.default");
    $module = $_GET['module'];
    if( $module )
    {
        delete_query('tblregistrars', array( 'registrar' => $module ));
    }
    RebuildRegistrarModuleHookCache();
    redir("deactivated=true");
}
ob_start();
if( $saved )
{
    infoBox($aInt->lang('domainregistrars', 'changesuccess'), $aInt->lang('domainregistrars', 'changesuccessinfo'));
}
if( $activated )
{
    infoBox($aInt->lang('domainregistrars', 'moduleactivated'), $aInt->lang('domainregistrars', 'moduleactivatedinfo'), 'success');
}
if( $deactivated )
{
    infoBox($aInt->lang('domainregistrars', 'moduledeactivated'), $aInt->lang('domainregistrars', 'moduledeactivatedinfo'), 'success');
}
echo $infobox;
$aInt->deleteJSConfirm('deactivateMod', 'domainregistrars', 'deactivatesure', $_SERVER['PHP_SELF'] . "?action=deactivate&module=");
$jscode .= "function showConfig(module) {\n    \$(\"#\"+module+\"config\").fadeToggle();\n}\n";
echo "<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n<tr><th width=\"140\"></th><th>" . $aInt->lang('addonmodules', 'module') . "</th><th width=\"350\"></th></tr>";
$registrar = new WHMCS_Module_Registrar();
$modulesarray = $registrar->getList();
foreach( $modulesarray as $module )
{
    if( !isValidforPath($module) )
    {
        exit( "Invalid Registrar Module Name" );
    }
    if( file_exists("../modules/registrars/" . $module . "/logo.gif") )
    {
        $registrarlogourl = "../modules/registrars/" . $module . "/logo.gif";
    }
    else
    {
        if( file_exists("../modules/registrars/" . $module . "/logo.jpg") )
        {
            $registrarlogourl = "../modules/registrars/" . $module . "/logo.jpg";
        }
        else
        {
            if( file_exists("../modules/registrars/" . $module . "/logo.png") )
            {
                $registrarlogourl = "../modules/registrars/" . $module . "/logo.png";
            }
            else
            {
                $registrarlogourl = "./images/spacer.gif";
            }
        }
    }
    $moduleactive = false;
    $registrar->load($module);
    $moduleconfigdata = $registrar->getSettings();
    if( is_array($moduleconfigdata) && !empty($moduleconfigdata) )
    {
        $moduleactive = true;
        $moduleaction = "<input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'activate') . "\" disabled=\"disabled\" class=\"btn disabled\" /> <input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'deactivate') . "\" onclick=\"deactivateMod('" . $module . "');return false\" class=\"btn-danger\" />  <input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'config') . "\" class=\"btn\" onclick=\"showConfig('" . $module . "')\" />";
    }
    else
    {
        $moduleaction = "<input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'activate') . "\" onclick=\"window.location='" . $_SERVER['PHP_SELF'] . "?action=activate&module=" . $module . generate_token('link') . "'\" class=\"btn-success\" /> <input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'deactivate') . "\" disabled=\"disabled\" class=\"btn disabled\" /> <input type=\"button\" value=\"" . $aInt->lang('addonmodules', 'config') . "\" disabled=\"disabled\" class=\"btn disabled\" />";
    }
    $configarray = $registrar->call('getConfigArray', $params);
    echo "    <tr id=\"formholder_";
    echo $module;
    echo "\" ";
    if( $moduleactive )
    {
        echo "class=\"active\" style=\"background-color:#EBFEE2;\"";
    }
    echo ">\n        <td align=\"center\" ";
    if( $moduleactive )
    {
        echo "style=\"background-color:#EBFEE2;\"";
    }
    echo "><a name=\"";
    echo $module;
    echo "\"></a><img src=\"";
    echo $registrarlogourl;
    echo "\" width=\"125\" height=\"40\" style=\"border:1px solid #ccc;\" /></td>\n        <td class=\"title\" ";
    if( $moduleactive )
    {
        echo "style=\"background-color:#EBFEE2;\"";
    }
    echo "><strong>&nbsp;&raquo; ";
    echo $configarray['FriendlyName']['Value'] ? $configarray['FriendlyName']['Value'] : ucfirst($module);
    echo "</strong>";
    if( $configarray['Description']['Value'] )
    {
        echo "<br />" . $configarray['Description']['Value'];
    }
    echo "</td>\n        <td width=\"200\" align=\"center\" ";
    if( $moduleactive )
    {
        echo "style=\"background-color:#EBFEE2;\"";
    }
    echo ">";
    echo $moduleaction;
    echo "</td>\n    </tr>\n    <tr><td id=\"";
    echo $module;
    echo "config\" class=\"config\" style=\"display:none;padding:15px;\" colspan=\"3\"><form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=save&module=";
    echo $module . generate_token('link');
    echo "\">\n        <table class=\"form\" width=\"100%\">\n        ";
    foreach( $configarray as $key => $values )
    {
        if( $values['Type'] != 'System' )
        {
            if( !$values['FriendlyName'] )
            {
                $values['FriendlyName'] = $key;
            }
            $values['Name'] = $key;
            $values['Value'] = $moduleconfigdata[$key];
            echo "<tr><td class=\"fieldlabel\">" . $values['FriendlyName'] . "</td><td class=\"fieldarea\">" . moduleConfigFieldOutput($values) . "</td></tr>";
        }
    }
    echo "        </table><br /><div align=\"center\"><input type=\"submit\" name=\"save\" value=\"";
    echo $aInt->lang('global', 'savechanges');
    echo "\" class=\"btn primary\" /></form></div><br />\n    </td></tr>\n";
}
echo "</table>\n</div>\n\n<script language=\"javascript\">\n\$(document).ready(function(){\n    var modpass = window.location.hash;\n    if (modpass) \$(modpass+\"config\").show();\n});\n</script>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();