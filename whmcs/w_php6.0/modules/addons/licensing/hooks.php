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
add_hook('DailyCronJob', 0, 'hook_licensing_addon_log_prune');
add_hook('AdminIntelliSearch', 0, 'hook_licensing_addon_search');
function hook_licensing_addon_log_prune($vars)
{
    $logprune = get_query_val('tbladdonmodules', 'value', array( 'module' => 'licensing', 'setting' => 'logprune' ));
    if( is_numeric($logprune) )
    {
        full_query("DELETE FROM mod_licensinglog WHERE datetime<='" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $logprune, date('Y'))) . "'");
    }
    full_query("DELETE FROM mod_licensing WHERE serviceid NOT IN (SELECT id FROM tblhosting)");
    full_query("OPTIMIZE TABLE mod_licensinglog");
}
function hook_licensing_addon_search($vars)
{
    $keyword = $vars['keyword'];
    $matches = array(  );
    $result = select_query('mod_licensing', '', "licensekey LIKE '%" . db_escape_string($keyword) . "%' OR validdomain LIKE '%" . db_escape_string($keyword) . "%'");
    while( $data = mysql_fetch_array($result) )
    {
        $serviceid = $data['serviceid'];
        $licensekey = $data['licensekey'];
        $validdomain = $data['validdomain'];
        $status = $data['status'];
        $validdomain = explode(',', $validdomain);
        $validdomain = $validdomain[0];
        if( !$validdomain )
        {
            $validdomain = "Not Yet Accessed";
        }
        $matches[] = array( 'link' => "clientshosting.php?id=" . $serviceid, 'status' => $status, 'title' => $licensekey, 'desc' => $validdomain );
    }
    return array( 'Licenses' => $matches );
}