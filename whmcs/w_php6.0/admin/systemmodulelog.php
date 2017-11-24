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
$aInt = new WHMCS_Admin("View Module Debug Log");
$aInt->title = $aInt->lang('system', 'moduledebuglog');
$aInt->sidebar = 'utilities';
$aInt->icon = 'logs';
$aInt->helplink = "Troubleshooting Module Problems";
if( $enable )
{
    check_token("WHMCS.admin.default");
    if( isset($CONFIG['ModuleDebugMode']) )
    {
        update_query('tblconfiguration', array( 'value' => 'on' ), array( 'setting' => 'ModuleDebugMode' ));
    }
    else
    {
        insert_query('tblconfiguration', array( 'setting' => 'ModuleDebugMode', 'value' => 'on' ));
    }
    redir();
}
if( $disable )
{
    check_token("WHMCS.admin.default");
    update_query('tblconfiguration', array( 'value' => '' ), array( 'setting' => 'ModuleDebugMode' ));
    redir();
}
if( $reset )
{
    check_token("WHMCS.admin.default");
    delete_query('tblmodulelog', "id!=''");
    redir();
}
if( !$id )
{
    $aInt->sortableTableInit('id');
    $numrows = get_query_val('tblmodulelog', "COUNT(*)", '', 'id', 'DESC');
    $result = select_query('tblmodulelog', '', '', 'id', 'DESC', $page * $limit . ',' . $limit);
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $date = $data['date'];
        $module = $data['module'];
        $action = $data['action'];
        $request = $data['request'];
        $response = $data['response'];
        $arrdata = $data['arrdata'];
        if( $arrdata )
        {
            $response = $arrdata;
        }
        $date = fromMySQLDate($date, 'time');
        $tabledata[] = array( "<a href=\"?id=" . $id . "\">" . $date . "</a>", $module, $action, "<textarea rows=\"5\" style=\"width:100%;\">" . htmlentities($request) . "</textarea>", "<textarea rows=\"5\" style=\"width:100%;\">" . htmlentities($response) . "</textarea>" );
    }
    $content = "<p>" . $aInt->lang('system', 'moduledebuglogdesc') . "</p>\n<form method=\"post\" action=\"\">\n<p align=\"center\">";
    if( $CONFIG['ModuleDebugMode'] )
    {
        $content .= "<input type=\"submit\" name=\"disable\" value=\"" . $aInt->lang('system', 'disabledebuglogging') . "\" />";
    }
    else
    {
        $content .= "<input type=\"submit\" name=\"enable\" value=\"" . $aInt->lang('system', 'enabledebuglogging') . "\" />";
    }
    $content .= " <input type=\"submit\" name=\"reset\" value=\"" . $aInt->lang('system', 'resetdebuglogging') . "\" /></p>\n</form>\n" . $aInt->sortableTable(array( array( '', $aInt->lang('fields', 'date'), 120 ), array( '', $aInt->lang('fields', 'module'), 120 ), array( '', $aInt->lang('fields', 'action'), 150 ), $aInt->lang('fields', 'request'), $aInt->lang('fields', 'response') ), $tabledata);
}
else
{
    $result = select_query('tblmodulelog', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $id = $data['id'];
    $date = $data['date'];
    $module = $data['module'];
    $action = $data['action'];
    $request = $data['request'];
    $response = $data['response'];
    $arrdata = $data['arrdata'];
    $date = fromMySQLDate($date, 'time');
    $content = $aInt->lang('fields', 'date') . ": " . $date . " - " . $aInt->lang('fields', 'module') . ": " . $module . " - " . $aInt->lang('fields', 'action') . ": " . $action . "<br /><br />\n<b>" . $aInt->lang('fields', 'request') . "</b><br />\n<textarea rows=\"10\" style=\"width:100%;\">" . htmlentities($request) . "</textarea><br /><br />\n<b>" . $aInt->lang('fields', 'response') . "</b><br />\n<textarea rows=\"20\" style=\"width:100%;\">" . htmlentities($response) . "</textarea><br /><br />";
    if( $arrdata )
    {
        $content .= "<b>" . $aInt->lang('fields', 'interpretedresponse') . "</b><br />\n<textarea rows=\"20\" style=\"width:100%;\">" . htmlentities($arrdata) . "</textarea><br /><br />";
    }
    $content .= "<a href=\"?\">&laquo; Back</a>";
}
$aInt->content = $content;
$aInt->display();