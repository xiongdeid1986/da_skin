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
$aInt = new WHMCS_Admin("View Email Templates");
$aInt->title = $aInt->lang('emailtpls', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'massmail';
$aInt->helplink = "Email Templates";
$activelanguages = array(  );
$result = select_query('tblemailtemplates', "DISTINCT language", '', 'type', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $activelanguage = $data['language'];
    if( $activelanguage )
    {
        $activelanguages[] = $activelanguage;
    }
}
if( $action == 'new' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create/Edit Email Templates");
    $emailid = insert_query('tblemailtemplates', array( 'type' => $type, 'name' => $name, 'language' => '', 'custom' => '1' ));
    redir("action=edit&id=" . $emailid);
}
if( $action == 'delatt' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create/Edit Email Templates");
    $result = select_query('tblemailtemplates', 'attachments', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $attachments = $data['attachments'];
    $attachments = explode(',', $attachments);
    $i = (int) $_GET['i'];
    $attachment = $attachments[$i];
    try
    {
        $file = new WHMCS_File($whmcs->getDownloadsDir() . $attachment);
        $file->delete();
    }
    catch( WHMCS_Exception_File_NotFound $e )
    {
    }
    unset($attachments[$i]);
    update_query('tblemailtemplates', array( 'attachments' => implode(',', $attachments) ), array( 'id' => $id ));
    redir("action=edit&id=" . $id);
}
ob_start();
if( $action == '' )
{
    if( $addlanguage )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Email Template Languages");
        $result = select_query('tblemailtemplates', '', array( 'language' => '' ));
        while( $data = mysql_fetch_array($result) )
        {
            $type = $data['type'];
            $name = $data['name'];
            $subject = $data['subject'];
            $message = $data['message'];
            $fromname = $data['fromname'];
            $fromemail = $data['fromemail'];
            $disabled = $data['disabled'];
            $custom = $data['custom'];
            insert_query('tblemailtemplates', array( 'type' => $type, 'name' => $name, 'subject' => $subject, 'message' => $message, 'language' => $addlang ));
        }
        $activelanguages = '';
        $activelanguages = array(  );
        $result = select_query('tblemailtemplates', "DISTINCT language", '', 'type', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $activelanguage = $data['language'];
            if( $activelanguage )
            {
                $activelanguages[] = $activelanguage;
            }
        }
        redir();
    }
    if( $disablelanguage && $dislang )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Email Template Languages");
        delete_query('tblemailtemplates', array( 'language' => $dislang ));
        $activelanguages = '';
        $activelanguages = array(  );
        $result = select_query('tblemailtemplates', "DISTINCT language", '', 'type', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $activelanguage = $data['language'];
            if( $activelanguage )
            {
                $activelanguages[] = $activelanguage;
            }
        }
        redir();
    }
    if( $savemessage )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Create/Edit Email Templates");
        if( $fromname == $CONFIG['CompanyName'] )
        {
            $fromname = '';
        }
        if( $fromemail == $CONFIG['Email'] )
        {
            $fromemail = '';
        }
        $result = select_query('tblemailtemplates', 'attachments', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $attachments = $data['attachments'];
        $attachments = $attachments ? explode(',', $attachments) : array(  );
        if( isset($_FILES['attachments']) )
        {
            foreach( $_FILES['attachments']['name'] as $num => $filename )
            {
                try
                {
                    $file = new WHMCS_File_Upload('attachments', $num);
                    $prefix = "{RAND}_";
                    $attachments[] = $file->move($whmcs->getDownloadsDir(), $prefix);
                }
                catch( WHMCS_Exception_File_NotUploaded $e )
                {
                }
            }
        }
        update_query('tblemailtemplates', array( 'fromname' => $fromname, 'fromemail' => $fromemail, 'attachments' => implode(',', $attachments), 'disabled' => $disabled, 'copyto' => $copyto, 'plaintext' => $plaintext ), array( 'id' => $id ));
        foreach( $subject as $key => $value )
        {
            update_query('tblemailtemplates', array( 'subject' => $value, 'message' => $message[$key] ), array( 'id' => $key ));
        }
        if( $toggleeditor )
        {
            if( $editorstate )
            {
                redir("action=edit&id=" . $id);
            }
            else
            {
                redir("action=edit&id=" . $id . "&noeditor=1");
            }
        }
        redir("success=true");
    }
    if( $delete == 'true' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Delete Email Templates");
        delete_query('tblemailtemplates', array( 'id' => $id ));
        redir("deleted=true");
    }
    if( $success )
    {
        infoBox($aInt->lang('emailtpls', 'updatesuccess'), $aInt->lang('emailtpls', 'updatesuccessinfo'));
    }
    else
    {
        if( $deleted )
        {
            infoBox($aInt->lang('emailtpls', 'delsuccess'), $aInt->lang('emailtpls', 'delsuccessinfo'));
        }
    }
    echo $infobox;
    $aInt->deleteJSConfirm('doDelete', 'emailtpls', 'delsure', "?delete=true&id=");
    echo "\n<p>";
    echo $aInt->lang('emailtpls', 'info');
    echo "</p>\n\n";
    if( checkPermission("Create/Edit Email Templates", true) )
    {
        echo "<div class=\"contextbar\">\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=new\">\n<b>";
        echo $aInt->lang('emailtpls', 'createnew');
        echo "</b> &nbsp;&nbsp;&nbsp; Type: <select name=\"type\"><option value=\"general\">";
        echo $aInt->lang('emailtpls', 'typegeneral');
        echo "</option><option value=\"product\">";
        echo $aInt->lang('emailtpls', 'typeproduct');
        echo "</option><option value=\"domain\">";
        echo $aInt->lang('emailtpls', 'typedomain');
        echo "</option><option value=\"invoice\">";
        echo $aInt->lang('emailtpls', 'typeinvoice');
        echo "</option></select> &nbsp;&nbsp;&nbsp; ";
        echo $aInt->lang('emailtpls', 'uniquename');
        echo ": <input type=\"text\" name=\"name\" size=\"30\"> &nbsp;&nbsp;&nbsp; <input type=\"submit\" value=\"";
        echo $aInt->lang('emailtpls', 'create');
        echo "\" class=\"button\">\n</form>\n</div>\n";
    }
    echo "\n";
function outputEmailTpls($type)
{
    global $aInt;
    global $tabledata;
    $tickets = new WHMCS_Tickets();
    $aInt->sortableTableInit('nopagination');
    $result2 = select_query('tblemailtemplates', '', array( 'type' => $type, 'language' => '' ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result2) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $message = $data['message'];
        $disabled = $data['disabled'];
        $custom = $data['custom'];
        $messageSummary = $tickets->getSummary($message, 250);
        $statusIcon = $disabled ? 'disabled' : 'tick';
        $linkStyle = $disabled ? " style=\"color:#666;\"" : '';
        $customText = $custom ? "<span class=\"label pending\">" . $aInt->lang('global', 'yes') . "</a>" : '-';
        $editLink = "<a href=\"?action=edit&id=" . $id . "\"><img src=\"images/icons/massmail.png\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\" />";
        $deleteLink = $custom ? "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\" /></a>" : '';
        $tabledata[] = array( "<img src=\"images/icons/" . $statusIcon . ".png\" />", "<a href=\"?action=edit&id=" . $id . "\" title=\"" . $messageSummary . "\"" . $linkStyle . ">" . $name . "</a>", $customText, $editLink, $deleteLink );
    }
    echo "<div id=\"" . $type . "EmailTemplates\">";
    echo $aInt->sortableTable(array( '', $aInt->lang('emailtpls', 'tplname'), $aInt->lang('emailtpls', 'custom'), '', '' ), $tabledata);
    echo "</div>";
}
    echo "<div style=\"float:left;width:50%;\"><div style=\"padding-right:10px;\">";
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typegeneral')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('general');
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typeinvoice')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('invoice');
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typesupport')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('support');
    echo "</div></div><div style=\"float:left;width:50%;\"><div style=\"padding-left:10px;\">";
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typeproduct')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('product');
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typedomain')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('domain');
    echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'typeadmin')) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
    outputEmailTpls('admin');
    $result = select_query('tblemailtemplates', "DISTINCT type", "type NOT IN ('general','product','domain','invoice','support','admin')", 'type', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $type = $data['type'];
        echo "<h2>" . ucfirst($aInt->lang('emailtpls', 'type' . $type)) . " " . $aInt->lang('emailtpls', 'messages') . "</h2>";
        outputEmailTpls($type);
    }
    echo "</div></div>\n<div style=\"clear:both;\"></div>\n\n<br />\n\n";
    if( checkPermission("Manage Email Template Languages", true) )
    {
        echo "<div class=\"contextbar\">\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<div style=\"display:inline;padding-right:20px;\">\n<b>";
        echo $aInt->lang('emailtpls', 'activelang');
        echo ":</b> ";
        echo $aInt->lang('global', 'default');
        foreach( $activelanguages as $language )
        {
            echo ", " . ucfirst($language);
        }
        echo "</div>\n<div style=\"display:inline;padding-right:20px;\">\n<b>";
        echo $aInt->lang('global', 'add');
        echo ":</b> <select name=\"addlang\">";
        $availlangs = $whmcs->getValidLanguages();
        foreach( $availlangs as $lang )
        {
            echo "<option value=\"" . $lang . "\">" . ucfirst($lang) . "</option>";
        }
        echo "</select> <input type=\"submit\" name=\"addlanguage\" value=\"";
        echo $aInt->lang('global', 'submit');
        echo "\" class=\"button\" />\n</div>\n<div style=\"display:inline;\">\n<b>";
        echo $aInt->lang('global', 'disable');
        echo ":</b> <select name=\"dislang\"><option value=\"xxx\">";
        echo $aInt->lang('emailtpls', 'chooseone');
        echo "</option>\n";
        foreach( $activelanguages as $lang )
        {
            echo "<option value=\"" . $lang . "\">" . ucfirst($lang) . "</option>";
        }
        echo "</select> <input type=\"submit\" name=\"disablelanguage\" value=\"";
        echo $aInt->lang('global', 'submit');
        echo "\" class=\"button\" />\n</div>\n</form>\n</div>\n";
    }
    echo "\n";
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblemailtemplates', '', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $type = $data['type'];
        $name = $data['name'];
        $subject = $data['subject'];
        $message = $data['message'];
        $attachments = $data['attachments'];
        $fromname = $data['fromname'] ? $data['fromname'] : $whmcs->get_config('CompanyName');
        $fromemail = $data['fromemail'] ? $data['fromemail'] : $whmcs->get_config('Email');
        $disabled = $data['disabled'] ? " checked" : '';
        $copyto = $data['copyto'];
        $plaintext = $data['plaintext'] ? " checked" : '';
        if( $plaintextchange )
        {
            if( $plaintext )
            {
                $message = str_replace("\n\n", "</p><p>", $message);
                $message = str_replace("\n", "<br>", $message);
                update_query('tblemailtemplates', array( 'message' => $message, 'plaintext' => '' ), array( 'id' => $id ));
                $plaintext = '';
            }
            else
            {
                $message = str_replace("<p>", '', $message);
                $message = str_replace("</p>", "\n\n", $message);
                $message = str_replace("<br>", "\n", $message);
                $message = str_replace("<br />", "\n", $message);
                $message = strip_tags($message);
                update_query('tblemailtemplates', array( 'message' => $message, 'plaintext' => '1' ), array( 'id' => $id ));
                $plaintext = " checked";
            }
        }
        $jquerycode = "\$(\"#addfileupload\").click(function () {\n    \$(\"#fileuploads\").append(\"<input type=\\\"file\\\" name=\\\"attachments[]\\\" style=\\\"width:70%;\\\" /><br />\");\n    return false;\n});";
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?savemessage=true&id=";
        echo $id;
        echo "\" enctype=\"multipart/form-data\">\n<input type=\"hidden\" name=\"editorstate\" value=\"";
        echo $noeditor;
        echo "\" />\n<p><b>";
        echo $name;
        echo "</b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr>\n    <td class=\"fieldlabel\">\n        ";
        echo $aInt->lang('emails', 'from');
        echo "    </td>\n    <td class=\"fieldarea\">\n        <input type=\"text\" name=\"fromname\" size=\"25\" value=\"";
        echo $fromname;
        echo "\" data-enter-submit=\"true\" />\n        <input type=\"text\" name=\"fromemail\" size=\"40\" value=\"";
        echo $fromemail;
        echo "\" data-enter-submit=\"true\" />\n    </td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
        echo $aInt->lang('emailtpls', 'copyto');
        echo "    </td>\n    <td class=\"fieldarea\">\n        <input type=\"text\" name=\"copyto\" size=\"50\" value=\"";
        echo $copyto;
        echo "\" data-enter-submit=\"true\" />\n        ";
        echo $aInt->lang('emailtpls', 'commasep');
        echo "    </td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
        echo $aInt->lang('support', 'attachments');
        echo "    </td>\n    <td class=\"fieldarea\">";
        if( $attachments )
        {
            $attachments = explode(',', $attachments);
            foreach( $attachments as $i => $attachment )
            {
                $filename = substr($attachment, 7);
                echo ($i + 1) . ". " . $filename . " <a href=\"configemailtemplates.php?action=delatt&id=" . $id . "&i=" . $i . generate_token('link') . "\"><img src=\"images/icons/delete.png\" border=\"0\" align=\"middle\" /> " . $aInt->lang('global', 'delete') . "</a><br />";
            }
        }
        echo "<img src=\"images/spacer.gif\" width=\"1\" height=\"2\" /><br /><input type=\"file\" name=\"attachments[]\" style=\"width:70%;\" /> <a href=\"#\" id=\"addfileupload\"><img src=\"images/icons/add.png\" align=\"absmiddle\" border=\"0\" /> ";
        echo $aInt->lang('support', 'addmore');
        echo "</a><br /><div id=\"fileuploads\"></div></td></tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
        echo $aInt->lang('emailtpls', 'plaintext');
        echo "    </td>\n    <td class=\"fieldarea\">\n        <label class=\"checkbox-inline\">\n            <input type=\"checkbox\" name=\"plaintext\" value=\"1\"";
        echo $plaintext;
        echo " onClick=\"window.location='configemailtemplates.php?action=edit&id=";
        echo $id;
        echo "&plaintextchange=true'\" data-enter-submit=\"true\" />\n            ";
        echo $aInt->lang('emailtpls', 'plaintextinfo');
        echo "        </label>\n    </td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
        echo $aInt->lang('global', 'disable');
        echo "    </td>\n    <td class=\"fieldarea\">\n        <label class=\"checkbox-inline\">\n            <input type=\"checkbox\" name=\"disabled\"";
        echo $disabled;
        echo " data-enter-submit=\"true\" />\n            ";
        echo $aInt->lang('emailtpls', 'disableinfo');
        echo "        </label>\n    </td>\n</tr>\n</table>\n<br>\n";
        $activelanguages = array(  );
        $result = select_query('tblemailtemplates', "DISTINCT language", '', 'type', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $activelanguage = $data['language'];
            if( $activelanguage )
            {
                $activelanguages[] = $activelanguage;
            }
        }
        $result = select_query('tblemailtemplates', '', array( 'type' => $type, 'name' => $name, 'language' => '' ));
        $data = mysql_fetch_array($result);
        $id = $data['id'];
        $default_subject = WHMCS_Input_Sanitize::makesafeforoutput($data['subject']);
        $default_message = WHMCS_Input_Sanitize::makesafeforoutput($data['message']);
        $defaultVersionExp = sprintf($aInt->lang('emailtpls', 'defaultversionexp'), ucfirst($CONFIG['Language']));
        $jquerycode .= "\$(\"input[data-enter-submit]\").keypress(function(event) {\n    if ( event.which == 13 ) {\n        event.preventDefault();\n        \$(\"#savechanges\").click();\n    }\n});\n";
        $templateTop = "<div style=\"float:right;\">\n    <input type=\"submit\" name=\"toggleeditor\" value=\"" . $aInt->lang('emailtpls', 'rteditor') . "\" class=\"btn\" />\n</div>\n<b>" . $aInt->lang('emailtpls', 'defaultversion') . "</b> - " . $defaultVersionExp . "<br />\n<br />\nSubject: <input type=\"text\" name=\"subject[" . $id . "]\" size=80 value=\"" . $default_subject . "\" data-enter-submit=\"true\" /><br />\n<br />";
        echo $templateTop;
        echo "<textarea name=\"message[";
        echo $id;
        echo "]\" id=\"email_msg1\" rows=\"25\" style=\"width:100%\" class=\"tinymce\">";
        echo $default_message;
        echo "</textarea><br>\n";
        $i = 2;
        foreach( $activelanguages as $language )
        {
            $result = select_query('tblemailtemplates', '', array( 'type' => $type, 'name' => $name, 'language' => $language ));
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $subject = WHMCS_Input_Sanitize::makesafeforoutput($data['subject']);
            $message = WHMCS_Input_Sanitize::makesafeforoutput($data['message']);
            if( !$id )
            {
                $subject = $default_subject;
                $message = $default_message;
                $id = insert_query('tblemailtemplates', array( 'type' => $type, 'name' => $name, 'language' => $language, 'subject' => $subject, 'message' => $message ));
            }
            echo "<b>" . ucfirst($language) . " " . $aInt->lang('emailtpls', 'version') . "</b><br><br>Subject: <input type=\"text\" name=\"subject[" . $id . "]" . "\" size=80 value=\"" . $subject . "\"><br><br>";
            echo "<textarea name=\"message[";
            echo $id;
            echo "]\" id=\"email_msg";
            echo $i;
            echo "\" rows=\"25\" style=\"width:100%\" class=\"tinymce\">";
            echo $message;
            echo "</textarea><br>\n";
            $i++;
        }
        $saveChanges = $aInt->lang('global', 'savechanges');
        echo "<p align=\"center\">\n    <input type=\"submit\" id=\"savechanges\" value=\"";
        echo $saveChanges;
        echo "\" class=\"btn btn-primary\" />\n</p>\n</form>\n\n";
        if( !$plaintext && !$noeditor )
        {
            $aInt->richTextEditor();
        }
        include("mergefields.php");
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->jquerycode = $jquerycode;
$aInt->display();