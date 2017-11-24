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
add_hook('AdminHomeWidgets', 1, 'widget_staffboard_overview');
function widget_staffboard_overview($vars)
{
    $title = "Staff Noticeboard";
    $lastviews = get_query_val('tbladdonmodules', 'value', array( 'module' => 'staffboard', 'setting' => 'lastviewed' ));
    if( $lastviews )
    {
        $lastviews = unserialize($lastviews);
        $new = false;
    }
    else
    {
        $lastviews = array(  );
        $new = true;
    }
    $lastviewed = $lastviews[$_SESSION['adminid']];
    $lastviews[$_SESSION['adminid']] = time();
    if( $new )
    {
        insert_query('tbladdonmodules', array( 'module' => 'staffboard', 'setting' => 'lastviewed', 'value' => serialize($lastviews) ));
    }
    else
    {
        update_query('tbladdonmodules', array( 'value' => serialize($lastviews) ), array( 'module' => 'staffboard', 'setting' => 'lastviewed' ));
    }
    $numchanged = get_query_val('mod_staffboard', "COUNT(id)", "date>='" . date("Y-m-d H:i:s", $lastviewed) . "'");
    $content = "\n<style>\n.staffboardchanges {\n    margin: 0 0 5px 0;\n    padding: 8px 25px;\n    font-size: 1.2em;\n    text-align: center;\n}\n.staffboardnotices {\n    max-height: 130px;\n    overflow: auto;\n    border-top: 1px solid #ccc;\n    border-bottom: 1px solid #ccc;\n}\n.staffboardnotices div {\n    padding: 5px 15px;\n    border-bottom: 2px solid #fff;\n}\n.staffboardnotices div.pink {\n    background-color: #F3CBF3;\n}\n.staffboardnotices div.yellow {\n    background-color: #FFFFC1;\n}\n.staffboardnotices div.purple {\n    background-color: #DCD7FE;\n}\n.staffboardnotices div.white {\n    background-color: #FAFAFA;\n}\n.staffboardnotices div.pink {\n    background-color: #F3CBF3;\n}\n.staffboardnotices div.blue {\n    background-color: #A6E3FC;\n}\n.staffboardnotices div.green {\n    background-color: #A5F88B;\n}\n</style>\n<div class=\"staffboardchanges\">There are <strong>" . $numchanged . "</strong> New or Updated Staff Notices Since your Last Visit - <a href=\"addonmodules.php?module=staffboard\">Visit Noticeboard &raquo;</a></div><div class=\"staffboardnotices\">";
    $result = select_query('mod_staffboard', '', '', 'date', 'DESC');
    while( $data = mysql_fetch_array($result) )
    {
        $content .= "<div class=\"" . $data['color'] . "\">" . fromMySQLDate($data['date'], 1) . " - " . (100 < strlen($data['note']) ? substr($data['note'], 0, 100) . "..." : $data['note']) . "</div>";
    }
    $content .= "</div>";
    return array( 'title' => $title, 'content' => $content, 'jquerycode' => null );
}