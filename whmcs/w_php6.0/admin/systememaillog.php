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
$aInt = new WHMCS_Admin("View Email Message Log");
$aInt->title = $aInt->lang('system', 'emailmessagelog');
$aInt->sidebar = 'utilities';
$aInt->icon = 'logs';
$aInt->sortableTableInit('date');
$select_keyword = 'SQL_CALC_FOUND_ROWS';
$result = select_query('tblemails,tblclients', $select_keyword . " tblemails.id,tblemails.date,tblemails.subject,tblemails.userid,tblclients.firstname,tblclients.lastname", "tblemails.userid=tblclients.id", "tblemails`.`id", 'DESC', $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $id = (int) $data['id'];
    $date = WHMCS_Input_Sanitize::makesafeforoutput($data['date']);
    $subject = WHMCS_Input_Sanitize::makesafeforoutput($data['subject']);
    $userid = (int) $data['userid'];
    $firstname = WHMCS_Input_Sanitize::makesafeforoutput($data['firstname']);
    $lastname = WHMCS_Input_Sanitize::makesafeforoutput($data['lastname']);
    $tabledata[] = array( fromMySQLDate($date, 'time'), "<a href=\"#\" onClick=\"window.open('clientsemails.php?&displaymessage=true&id=" . $id . "','','width=650,height=400,scrollbars=yes');return false\">" . $subject . "</a>", "<a href=\"clientssummary.php?userid=" . $userid . "\">" . $firstname . " " . $lastname . "</a>", "<a href=\"sendmessage.php?resend=true&emailid=" . $id . "\"><img src=\"images/icons/resendemail.png\" border=\"0\" alt=\"" . $aInt->lang('emails', 'resendemail') . "\"></a>" );
}
if( !count($tabledata) )
{
    $numrows = 0;
}
else
{
    $result = full_query("SELECT FOUND_ROWS()");
    $data = mysql_fetch_array($result);
    $numrows = $data[0];
}
$content = $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'subject'), $aInt->lang('system', 'recipient'), '' ), $tabledata);
$aInt->content = $content;
$aInt->display();