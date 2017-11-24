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
$aInt = new WHMCS_Admin("Configure Fraud Protection");
$aInt->title = $aInt->lang('fraud', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'configbans';
$aInt->helplink = "Fraud Protection";
$aInt->requiredFiles(array( 'modulefunctions' ));
ob_start();
$module = new WHMCS_Module_Fraud();
$fraudmodules = $module->getList();
if( $fraud && in_array($fraud, $fraudmodules) )
{
    $module->load($fraud);
    $configarray = $module->call('getConfigArray');
    if( $action == 'save' )
    {
        check_token("WHMCS.admin.default");
        foreach( $configarray as $regconfoption => $values )
        {
            $regconfoption2 = str_replace(" ", '_', $regconfoption);
            $result = select_query('tblfraud', '', array( 'fraud' => $fraud, 'setting' => $regconfoption ));
            $num_rows = mysql_num_rows($result);
            if( $num_rows == '0' )
            {
                insert_query('tblfraud', array( 'fraud' => $fraud, 'setting' => $regconfoption, 'value' => trim($_POST[$regconfoption2]) ));
            }
            else
            {
                update_query('tblfraud', array( 'value' => trim($_POST[$regconfoption2]) ), array( 'fraud' => $fraud, 'setting' => $regconfoption ));
            }
        }
        redir("fraud=" . $fraud . "&success=1");
    }
    if( $success )
    {
        infoBox($aInt->lang('fraud', 'changesuccess'), $aInt->lang('fraud', 'changesuccessinfo'));
    }
    echo $infobox;
}
else
{
    $fraud = '';
}
echo "<p>" . $aInt->lang('fraud', 'info') . "</p>";
echo "<form method=\"get\" action=\"" . $whmcs->getPhpSelf() . "\"><p>" . $aInt->lang('fraud', 'choose') . ": <select name=\"fraud\" onChange=\"submit();\">";
foreach( $fraudmodules as $file )
{
    echo "<option value=\"" . $file . "\"";
    if( $fraud == $file )
    {
        echo " selected";
    }
    echo ">" . TitleCase(str_replace('_', " ", $file)) . "</option>";
}
echo "</select> <input type=\"submit\" value=\" " . $aInt->lang('global', 'go') . " \" class=\"button\"></p></form>";
if( $fraud )
{
    $configarray = $module->call('getConfigArray');
    $configvalues = $module->getSettings();
    echo "\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=save\">\n<input type=\"hidden\" name=\"fraud\" value=\"";
    echo $fraud;
    echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n";
    foreach( $configarray as $regconfoption => $values )
    {
        if( !$values['FriendlyName'] )
        {
            $values['FriendlyName'] = $regconfoption;
        }
        $values['Name'] = $regconfoption;
        $values['Value'] = $configvalues[$regconfoption];
        echo "<tr><td class=\"fieldlabel\">" . $values['FriendlyName'] . "</td><td class=\"fieldarea\">" . moduleConfigFieldOutput($values) . "</td></tr>";
    }
    echo "</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'savechanges');
    echo "\" class=\"button\" /></p>\n\n</form>\n\n";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();