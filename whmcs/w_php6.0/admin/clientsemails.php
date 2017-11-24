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
$aInt = new WHMCS_Admin("View Email Message Log", false);
$aInt->inClientsProfile = true;
$whmcs = WHMCS_Application::getinstance();
$userid = $whmcs->get_req_var('userid');
$aInt->assertClientBoundary($userid);
if( $displaymessage == 'true' )
{
    $aInt->title = $aInt->lang('emails', 'viewemail');
    $result = select_query('tblemails', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $date = $data['date'];
    $to = is_null($data['to']) ? $aInt->lang('emails', 'registeredemail') : $data['to'];
    $cc = $data['cc'];
    $bcc = $data['bcc'];
    $subject = $data['subject'];
    $message = $data['message'];
    $content = "<p><b>" . $aInt->lang('emails', 'to') . ":</b> " . WHMCS_Input_Sanitize::makesafeforoutput($to) . "<br />";
    if( $cc )
    {
        $content .= "<b>" . $aInt->lang('emails', 'cc') . ":</b> " . WHMCS_Input_Sanitize::makesafeforoutput($cc) . "<br />";
    }
    if( $bcc )
    {
        $content .= "<b>" . $aInt->lang('emails', 'bcc') . ":</b> " . WHMCS_Input_Sanitize::makesafeforoutput($bcc) . "<br />";
    }
    $content .= "<b>" . $aInt->lang('emails', 'subject') . ":</b> <span id=\"subject\">" . WHMCS_Input_Sanitize::makesafeforoutput($subject) . "</span></p>\n    " . $message;
    $aInt->title = $aInt->lang('emails', 'viewemailmessage');
    $aInt->content = $content;
    $aInt->displayPopUp();
    exit();
}
if( $action == 'send' && $messagename == 'newmessage' )
{
    redir("type=" . $type . "&id=" . $id, "sendmessage.php");
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblemails', array( 'id' => $id ));
    redir("userid=" . $userid);
}
$aInt->valUserID($userid);
ob_start();
$jscode = '';
if( $action == 'send' )
{
    check_token("WHMCS.admin.default");
    $result = sendMessage($messagename, $id, '', true);
    $queryStr = "userid=" . $userid;
    if( $result === true )
    {
        $queryStr .= "&success=1";
    }
    else
    {
        if( $result === false )
        {
            $queryStr .= "&error=1";
        }
        else
        {
            if( 0 < strlen($result) )
            {
                $queryStr .= "&error=1";
                WHMCS_Session::set('EmailError', $result);
            }
        }
    }
    redir($queryStr);
}
$aInt->deleteJSConfirm('doDelete', 'emails', 'suredelete', "clientsemails.php?userid=" . $userid . "&action=delete&id=");
$success = $whmcs->get_req_var('success');
$error = $whmcs->get_req_var('error');
if( $success )
{
    infoBox($aInt->lang('global', 'success'), $aInt->lang('email', 'sentSuccessfully'), 'success');
}
else
{
    if( $error )
    {
        $result = WHMCS_Session::get('EmailError');
        WHMCS_Session::delete('EmailError');
        if( $result )
        {
            infoBox($aInt->lang('global', 'erroroccurred'), $result, 'error');
        }
        else
        {
            infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('email', 'emailAborted'), 'warning');
        }
    }
}
if( $infobox )
{
    echo $infobox;
}
$aInt->sortableTableInit('date', 'DESC');
$result = select_query('tblemails', "COUNT(*)", array( 'userid' => $userid ));
$data = mysql_fetch_array($result);
$numrows = $data[0];
$result = select_query('tblemails', '', array( 'userid' => $userid ), $orderby, $order, $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $id = (int) $data['id'];
    $date = $data['date'];
    $date = fromMySQLDate($date, 'time');
    $subject = $data['subject'];
    if( $subject == '' )
    {
        $subject = $aInt->lang('emails', 'nosubject');
    }
    $tabledata[] = array( WHMCS_Input_Sanitize::makesafeforoutput($date), "<a href=\"#\" onClick=\"window.open('clientsemails.php?&displaymessage=true&id=" . $id . "','','width=650,height=400,scrollbars=yes');return false\">" . WHMCS_Input_Sanitize::makesafeforoutput($subject) . "</a>", "<a href=\"sendmessage.php?resend=true&emailid=" . $id . "\"><img src=\"images/icons/resendemail.png\" border=\"0\" alt=\"" . $aInt->lang('emails', 'resendemail') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "')\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\" /></a>" );
}
echo $aInt->sortableTable(array( array( 'date', $aInt->lang('fields', 'date') ), array( 'subject', $aInt->lang('emails', 'subject') ), '', '' ), $tabledata);
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();