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
$aInt = new WHMCS_Admin("Database Status");
$aInt->title = $aInt->lang('utilities', 'dbstatus');
$aInt->sidebar = 'utilities';
$aInt->icon = 'dbbackups';
$aInt->requiredFiles(array( 'backupfunctions' ));
if( $optimize )
{
    check_token("WHMCS.admin.default");
    $alltables = full_query("SHOW TABLES");
    while( $table = mysql_fetch_assoc($alltables) )
    {
        foreach( $table as $db => $tablename )
        {
            full_query("OPTIMIZE TABLE '" . $tablename . "'");
        }
    }
    redir("optimized=1");
}
if( $dlbackup )
{
    check_token("WHMCS.admin.default");
    $db_name = '';
    require(ROOTDIR . "/configuration.php");
    set_time_limit(0);
    header("Content-type: application/octet-stream");
    header("Content-disposition: attachment; filename=" . $db_name . '_backup_' . date('Ymd_His') . ".zip");
    echo generateBackup();
}
ob_start();
if( $optimized )
{
    infoBox($aInt->lang('system', 'optcomplete'), $aInt->lang('system', 'optcompleteinfo'));
}
echo $infobox;
echo "\n<table width=760 align=center cellspacing=0 cellpadding=0><tr><td width=380 valign=top>\n\n<table bgcolor=#cccccc cellspacing=1 width=370 align=center>\n<tr style=\"text-align:center;font-weight:bold;background-color:#efefef\"><td>";
echo $aInt->lang('fields', 'name');
echo "</td><td>";
echo $aInt->lang('fields', 'rows');
echo "</td><td>";
echo $aInt->lang('fields', 'size');
echo "</td></tr>\n";
$query = "SHOW TABLE STATUS";
$result = full_query($query);
for( $i = 0; $data = mysql_fetch_array($result); $i++ )
{
    $name = $data['Name'];
    $rows = $data['Rows'];
    $datalen = $data['Data_length'];
    $indexlen = $data['Index_length'];
    $totalsize = $datalen + $indexlen;
    $totalrows += $rows;
    $size += $totalsize;
    $reportarray[] = array( 'name' => $name, 'rows' => $rows, 'size' => round($totalsize / 1024, 2) );
}
foreach( $reportarray as $key => $value )
{
    if( $key < $i / 2 )
    {
        echo "<tr bgcolor=#ffffff style=\"text-align:center\"><td>" . $value['name'] . "</td><td>" . $value['rows'] . "</td><td>" . $value['size'] . " " . $aInt->lang('fields', 'kb') . "</td></tr>";
    }
}
echo "</table>\n\n</td><td align=\"center\" width=370 valign=top>\n\n<table bgcolor=#cccccc cellspacing=1 width=370>\n<tr style=\"text-align:center;font-weight:bold;background-color:#efefef\"><td>";
echo $aInt->lang('fields', 'name');
echo "</td><td>";
echo $aInt->lang('fields', 'rows');
echo "</td><td>";
echo $aInt->lang('fields', 'size');
echo "</td></tr>\n";
foreach( $reportarray as $key => $value )
{
    if( $i / 2 <= $key )
    {
        echo "<tr bgcolor=#ffffff style=\"text-align:center\"><td>" . $value['name'] . "</td><td>" . $value['rows'] . "</td><td>" . $value['size'] . " " . $aInt->lang('fields', 'kb') . "</td></tr>";
    }
}
echo "</table>\n\n</td></tr></table>\n\n<p align=center><b>";
echo $aInt->lang('system', 'totaltables');
echo ":</b> ";
echo $i;
echo " - <b>";
echo $aInt->lang('system', 'totalrows');
echo ":</b> ";
echo $totalrows;
echo " - <B>";
echo $aInt->lang('system', 'totalsize');
echo ":</B> ";
echo round($size / 1024, 2);
echo " ";
echo $aInt->lang('fields', 'kb');
echo "</p>\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\">\n<p align=center><input type=\"submit\" name=\"optimize\" value=\"";
echo $aInt->lang('system', 'opttables');
echo "\" class=\"button\" /> <input type=\"submit\" name=\"dlbackup\" value=\"";
echo $aInt->lang('system', 'dldbbackup');
echo "\" class=\"button\" /></p>\n</form>\n\n</td></tr></table>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();