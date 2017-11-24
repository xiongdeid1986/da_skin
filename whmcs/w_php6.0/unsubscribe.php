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
define('CLIENTAREA', true);
require("init.php");
$pagetitle = $_LANG['unsubscribe'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"clientarea.php\">" . $_LANG['clientareatitle'] . "</a> > <a href=\"unsubscribe.php\">" . $_LANG['unsubscribe'] . "</a>";
initialiseClientArea($pagetitle, '', $breadcrumbnav);
$email = $whmcs->get_req_var('email');
$key = $whmcs->get_req_var('key');
if( $email )
{
    $errormessage = dounsubscribe($email, $key);
    $smartyvalues['errormessage'] = $errormessage;
    if( !$errormessage )
    {
        $smartyvalues['successful'] = true;
    }
    $templatefile = 'unsubscribe';
    outputClientArea($templatefile);
}
else
{
    redir("index.php");
}
function doUnsubscribe($email, $key)
{
    global $whmcs;
    global $_LANG;
    $whmcs->get_hash();
    if( !$email )
    {
        return $_LANG['pwresetemailrequired'];
    }
    $result = select_query('tblclients', 'id,email,emailoptout', array( 'email' => $email ));
    $data = mysql_fetch_array($result);
    $userid = $data['id'];
    $email = $data['email'];
    $emailoptout = $data['emailoptout'];
    $newkey = sha1($email . $userid . $cc_encryption_hash);
    if( $newkey == $key )
    {
        if( !$userid )
        {
            return $_LANG['unsubscribehashinvalid'];
        }
        if( $emailoptout == 1 )
        {
            return $_LANG['alreadyunsubscribed'];
        }
        update_query('tblclients', array( 'emailoptout' => '1' ), array( 'id' => $userid ));
        sendMessage("Unsubscribe Confirmation", $userid);
        logActivity("Unsubscribed From Marketing Emails - User ID:" . $userid, $userid);
    }
    else
    {
        return $_LANG['unsubscribehashinvalid'];
    }
}