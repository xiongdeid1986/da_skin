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
$pagetitle = $_LANG['contacttitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"contact.php\">" . $_LANG['contacttitle'] . "</a>";
$templatefile = 'contact';
$pageicon = "images/contact_big.gif";
$sendError = '';
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
$action = $whmcs->get_req_var('action');
$name = $whmcs->get_req_var('name');
$email = $whmcs->get_req_var('email');
$subject = $whmcs->get_req_var('subject');
$message = $whmcs->get_req_var('message');
if( $CONFIG['ContactFormDept'] )
{
    redir("step=2&deptid=" . $CONFIG['ContactFormDept'], "submitticket.php");
}
$capatacha = clientAreaInitCaptcha();
$validate = new WHMCS_Validate();
$contactFormSent = false;
$sendError = '';
if( $action == 'send' )
{
    check_token();
    $validate->validate('required', 'name', 'contacterrorname');
    if( $validate->validate('required', 'email', 'clientareaerroremail') )
    {
        $validate->validate('email', 'email', 'clientareaerroremailinvalid');
    }
    $validate->validate('required', 'subject', 'contacterrorsubject');
    $validate->validate('required', 'message', 'contacterrormessage');
    $validate->validate('captcha', 'code', 'captchaverifyincorrect');
    if( !$validate->hasErrors() )
    {
        if( $CONFIG['LogoURL'] )
        {
            $sendmessage = "<p><a href=\"" . $CONFIG['Domain'] . "\" target=\"_blank\"><img src=\"" . $CONFIG['LogoURL'] . "\" alt=\"" . $CONFIG['CompanyName'] . "\" border=\"0\"></a></p>";
        }
        $sendmessage .= "<font style=\"font-family:Verdana;font-size:11px\"><p>" . nl2br($message) . "</p>";
        try
        {
            $mail = new WHMCS_Mail($name, $email);
            $mail->Subject = $_LANG['contactform'] . ": " . $subject;
            $message_text = str_replace("</p>", "\n\n", $sendmessage);
            $message_text = str_replace("<br>", "\n", $message_text);
            $message_text = str_replace("<br />", "\n", $message_text);
            $message_text = strip_tags($message_text);
            $mail->Body = $sendmessage;
            $mail->AltBody = $message_text;
            if( !$CONFIG['ContactFormTo'] )
            {
                $contactformemail = $CONFIG['Email'];
            }
            else
            {
                $contactformemail = $CONFIG['ContactFormTo'];
            }
            $mail->From = $CONFIG['SystemEmailsFromEmail'];
            $mail->FromName = $CONFIG['SystemEmailsFromName'];
            $mail->AddAddress($contactformemail);
            $mail->addReplyTo($email, $name);
            if( $smtp_debug )
            {
                $mail->SMTPDebug = true;
            }
            $mail->Send();
            $contactFormSent = true;
        }
        catch( phpmailerException $e )
        {
            $sendError = "<li>" . $_LANG['clientareaerroroccured'] . "</li>";
            logActivity("Contact form mail sending failed with a PHPMailer Exception: " . $e->getMessage() . " (Subject: " . $subject . ")");
        }
    }
}
$smarty->assign('sent', $contactFormSent);
if( $validate->hasErrors() || $sendError )
{
    $smarty->assign('errormessage', implode("\n", array( $validate->getHTMLErrorOutput(), $sendError )));
}
$smarty->assign('name', $name);
$smarty->assign('email', $email);
$smarty->assign('subject', $subject);
$smarty->assign('message', $message);
$smarty->assign('capatacha', $capatacha);
$smarty->assign('recapatchahtml', clientAreaReCaptchaHTML());
outputClientArea($templatefile);