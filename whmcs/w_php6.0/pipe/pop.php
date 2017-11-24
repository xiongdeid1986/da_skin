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
require(dirname(__FILE__) . "/../init.php");
require(dirname(__FILE__) . "/../includes/adminfunctions.php");
require(dirname(__FILE__) . "/../includes/ticketfunctions.php");
define('IN_CRON', true);
if( !function_exists('imap_open') )
{
    exit( "IMAP needs to be compiled into PHP for this to function" );
}
$type = array( 'text', 'multipart', 'message', 'application', 'audio', 'image', 'video', 'other' );
$encoding = array( '7bit', '8bit', 'binary', 'base64', 'quoted-printable', 'other' );
$whmcs = WHMCS_Application::getinstance();
$whmcsAppConfig = $whmcs->getApplicationConfig();
$attachments_dir = $whmcsAppConfig['attachments_dir'];
$sapiType = php_sapi_name();
echo formatoutput("<b>POP Import Log</b><br>Date: " . date("d/m/Y H:i:s") . "<hr>", $sapiType);
$result = select_query('tblticketdepartments', '', array( 'host' => array( 'sqltype' => 'NEQ', 'value' => '' ), 'port' => array( 'sqltype' => 'NEQ', 'value' => '' ), 'login' => array( 'sqltype' => 'NEQ', 'value' => '' ) ), 'order', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    ob_start();
    $host = $data['host'];
    $port = $data['port'];
    $login = $data['login'];
    $password = decrypt($data['password']);
    echo "Host: " . $host . "<br>Email: " . $login . "<br>";
    $imapLastError = $connectSuccess = '';
    if( $port == '995' )
    {
        $mBox = imap_open("{" . $host . ":" . $port . "/pop3/ssl/novalidate-cert}INBOX", $login, $password);
        if( $mBox == false )
        {
            $imapLastError = imap_last_error();
        }
        else
        {
            $connectSuccess = true;
        }
    }
    else
    {
        $mBox = imap_open("{" . $host . ":" . $port . "/pop3/notls}INBOX", $login, $password);
        if( $mBox == false )
        {
            if( !$imapLastError )
            {
                $imapLastError = imap_last_error();
            }
        }
        else
        {
            $connectSuccess = true;
        }
        if( !$connectSuccess )
        {
            $mBox = imap_open("{" . $host . ":" . $port . "/pop3/novalidate-cert}INBOX", $login, $password);
            if( $mBox == false )
            {
                if( !$imapLastError )
                {
                    $imapLastError = imap_last_error();
                }
            }
            else
            {
                $connectSuccess = true;
            }
        }
    }
    if( !$connectSuccess )
    {
        echo "An Error Occurred: " . $imapLastError . "<hr>";
    }
    else
    {
        $headers = imap_headers($mBox);
        $emailCount = count($headers);
        echo "Email Count: " . $emailCount . "<hr>";
        if( $emailCount )
        {
            $msgNo = 1;
            while( $msgNo <= $emailCount )
            {
                $sections = $attachments = $header_info = array(  );
                $header_info = getheaders($mBox, $msgNo);
                $structure = imap_fetchstructure($mBox, $msgNo);
                if( isset($structure) && is_object($structure) && isset($structure->parts) && (is_array($structure->parts) || is_object($structure->parts)) && 1 < sizeof($structure->parts) )
                {
                    $sections = parse($structure);
                    $attachments = get_attachments($sections);
                }
                $msgBody = get_part($mBox, $msgNo, 'TEXT/PLAIN');
                if( !$msgBody )
                {
                    $msgBody = get_part($mBox, $msgNo, 'TEXT/HTML');
                    $msgBody = strip_tags($msgBody);
                }
                if( !$msgBody )
                {
                    $msgBody = "No message found.";
                }
                $msgBody = str_replace("&nbsp;", " ", $msgBody);
                $attachmentsList = '';
                if( 0 < count($attachments) )
                {
                    foreach( $attachments as $attachment )
                    {
                        $pid = $attachment['pid'];
                        $attachmentEncoding = $attachment['encoding'];
                        $filename = $attachment['name'] ? $attachment['name'] : $attachment['filename'];
                        if( checkTicketAttachmentExtension($filename) )
                        {
                            $filenameParts = explode(".", $filename);
                            $extension = end($filenameParts);
                            $filename = implode(array_slice($filenameParts, 0, 0 - 1));
                            $filename = preg_replace("/[^a-zA-Z0-9-_ ]/", '', $filename);
                            if( !$filename )
                            {
                                $filename = 'filename';
                            }
                            mt_srand(time());
                            $rand = mt_rand(100000, 999999);
                            $attachmentFilename = $rand . '_' . $filename . "." . $extension;
                            $attachmentsList .= $attachmentFilename . "|";
                            $attachmentData = imap_fetchbody($mBox, $msgNo, $pid);
                            if( $attachmentEncoding == 'base64' )
                            {
                                $attachmentData = imap_base64($attachmentData);
                            }
                            $fp = fopen($attachments_dir . $attachmentFilename, 'w');
                            fwrite($fp, $attachmentData);
                            fclose($fp);
                        }
                        else
                        {
                            $msgBody .= "\n" . "\nAttachment " . $filename . " blocked - file type not allowed.";
                        }
                    }
                }
                $attachmentsList = substr($attachmentsList, 0, 0 - 1);
                $fromEmail = $header_info['fromAddr'];
                if( isset($header_info['replyTo']) )
                {
                    $fromEmail = $header_info['replyTo'];
                }
                $header_info['subject'] = str_replace("{", "[", $header_info['subject']);
                $header_info['subject'] = str_replace("}", "]", $header_info['subject']);
                processPipedTicket($header_info['to'] . ',' . $login, $header_info['fromName'], $fromEmail, $header_info['subject'], $msgBody, $attachmentsList);
                $sections = $attachments = $header_info = array(  );
                $fromEmail = $attachmentsList = $attachmentData = $attachmentFilename = '';
                imap_delete($mBox, $msgNo);
                $msgNo += 1;
            }
        }
        imap_expunge($mBox);
        imap_close($mBox);
    }
    $content = ob_get_contents();
    ob_end_clean();
    echo formatoutput($content, $sapiType);
}
function get_mime_type(&$structure)
{
    $primary_mime_type = array( 'TEXT', 'MULTIPART', 'MESSAGE', 'APPLICATION', 'AUDIO', 'IMAGE', 'VIDEO', 'OTHER' );
    if( $structure->subtype )
    {
        return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
    }
    return 'TEXT/PLAIN';
}
/**
 * Obtain the part of the email with the specific mime_type.
 *
 * @param resource $stream the imap_open resource
 * @param integer $msg_number the message number to obtain
 * @param string $mime_type the mime_type to obtain the part for
 * @param bool|object $structure the structure of the email
 * @param bool|integer $part_number the part number to obtain
 * @return bool|string the part of the email matching the mime_type or false.
 */
function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false)
{
    global $CONFIG;
    global $disable_iconv;
    if( !$structure )
    {
        $structure = imap_fetchstructure($stream, $msg_number);
    }
    if( $structure )
    {
        $charset = '';
        if( $structure->ifparameters == true )
        {
            foreach( $structure->parameters as $param )
            {
                if( $param->attribute == 'CHARSET' )
                {
                    $charset = $param->value;
                    if( $charset == 'UTF-8' )
                    {
                        $charset = '';
                    }
                }
            }
        }
        if( $mime_type == get_mime_type($structure) )
        {
            if( !$part_number )
            {
                $part_number = '1';
            }
            $text = imap_fetchbody($stream, $msg_number, $part_number);
            if( $structure->encoding == 3 )
            {
                $text = imap_base64($text);
            }
            else
            {
                if( $structure->encoding == 4 )
                {
                    $text = imap_qprint($text);
                }
            }
            if( $charset && function_exists('iconv') && !$disable_iconv )
            {
                $text = iconv($charset, $CONFIG['Charset'], $text);
            }
            if( $charset && !isset($GLOBALS['mailcharset']) )
            {
                $GLOBALS['mailcharset'] = $charset;
            }
            return $text;
        }
        if( $structure->type == 1 )
        {
            while( list($index, $sub_structure) = each($structure->parts) )
            {
                $prefix = '';
                if( $part_number )
                {
                    $prefix = $part_number . ".";
                }
                $data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
                if( $data )
                {
                    return $data;
                }
            }
        }
    }
    return false;
}
/**
 * Parse the structure of the email for possible attachment data.
 *
 * @param object $structure The structure of the email from imap_fetchstructure
 * @return array an array of data containing the potential attachment information
 */
function parse($structure)
{
    global $type;
    global $encoding;
    $ret = array(  );
    $parts = $structure->parts;
    for( $x = 0; $x < sizeof($parts); $x++ )
    {
        $ret[$x]['pid'] = $x + 1;
        $thisPart = $parts[$x];
        if( $thisPart->type == '' )
        {
            $thisPart->type = 0;
        }
        $ret[$x]['type'] = $type[$thisPart->type];
        if( $thisPart->ifsubtype )
        {
            $ret[$x]['type'] .= '/' . strtolower($thisPart->subtype);
        }
        if( $thisPart->encoding == '' )
        {
            $thisPart->encoding = 0;
        }
        $ret[$x]['encoding'] = $encoding[$thisPart->encoding];
        $ret[$x]['size'] = strtolower($thisPart->bytes);
        if( $thisPart->ifdisposition )
        {
            $ret[$x]['disposition'] = strtolower($thisPart->disposition);
        }
        if( $thisPart->ifparameters )
        {
            foreach( $thisPart->parameters as $p )
            {
                $ret[$x][strtolower($p->attribute)] = $p->value;
            }
        }
        if( $thisPart->ifdparameters )
        {
            foreach( $thisPart->dparameters as $p )
            {
                $ret[$x][strtolower($p->attribute)] = $p->value;
            }
        }
    }
    return $ret;
}
/**
 * Check the array of possible attachments for file names.
 *
 * No filename means it is not possible to import.
 *
 * @param array $arr an array of potential attachments
 * @return array an array of confirmed attachments with file names.
 */
function get_attachments($arr = array(  ))
{
    $ret = array(  );
    for( $x = 0; $x < sizeof($arr); $x++ )
    {
        if( isset($arr[$x]['filename']) || isset($arr[$x]['name']) )
        {
            $ret[] = $arr[$x];
        }
    }
    return $ret;
}
/**
 * Obtain an array of headers to be used when importing the email
 *
 * @param resource $mBox the mailbox resource from imap_open
 * @param int $msgNo the id of the message number being read
 * @return array the headers of the email in array format
 */
function getHeaders($mBox, $msgNo)
{
    global $CONFIG;
    global $disable_iconv;
    $header_info = array(  );
    if( $headers = imap_headerinfo($mBox, $msgNo) )
    {
        $header_info['msgID'] = $headers->message_id;
        if( $headersFrom = $headers->from )
        {
            $header_info['fromAddr'] = $headersFrom[0]->mailbox . "@" . $headersFrom[0]->host;
            if( $headersFrom[0]->personal )
            {
                $fromName = $headersFrom[0]->personal;
            }
            else
            {
                $fromName = $headersFrom[0]->mailbox . "@" . $headersFrom[0]->host;
            }
            $elements = imap_mime_header_decode($fromName);
            $fromName = $elements[0]->text;
            $charset = $elements[0]->charset;
            if( $charset && function_exists('iconv') && !$disable_iconv && $charset != 'default' )
            {
                $fromName = iconv($charset, $CONFIG['Charset'], $fromName);
            }
            $fromName = str_replace(array( "<", ">", "\"", "'" ), '', $fromName);
            $header_info['fromName'] = $fromName;
        }
        $to = '';
        if( isset($headers->to) && ($headersTo = $headers->to) )
        {
            if( 1 < sizeof($headersTo) )
            {
                $toMailbox = $headersTo[0]->mailbox . "@" . $headersTo[0]->host;
                if( !strstr($toMailbox, 'UNEXPECTED_DATA') )
                {
                    $to .= $toMailbox;
                }
                for( $i = 1; $i < sizeof($headersTo); $i++ )
                {
                    $toMailbox = $headersTo[$i]->mailbox . "@" . $headersTo[$i]->host;
                    if( !strstr($toMailbox, 'UNEXPECTED_DATA') )
                    {
                        $to .= ", " . $toMailbox;
                    }
                }
                $header_info['to'] = $to;
            }
            else
            {
                $header_info['to'] = $headersTo[0]->mailbox . "@" . $headersTo[0]->host;
            }
        }
        else
        {
            $header_info['to'] = "&nbsp;";
        }
        $cc = '';
        if( isset($headers->cc) && ($headersCc = $headers->cc) )
        {
            if( 1 < sizeof($headersCc) )
            {
                for( $i = 0; $i < sizeof($headersCc) - 1; $i++ )
                {
                    $ccMailbox = $headersCc[$i]->mailbox . "@" . $headersCc[$i]->host;
                    $cc .= $ccMailbox . ", ";
                }
                $ccMailbox = $headersCc[sizeof($headersCc) - 1]->mailbox . "@" . $headersCc[sizeof($headersCc) - 1]->host;
                $cc .= $ccMailbox;
                $header_info['cc'] = $cc;
            }
            else
            {
                $header_info['cc'] = $headersCc[0]->mailbox . "@" . $headersCc[0]->host;
            }
        }
        if( isset($headers->Date) )
        {
            $header_info['date'] = htmlspecialchars($headers->Date);
        }
        else
        {
            $header_info['date'] = "&nbsp;";
        }
        if( isset($headers->subject) )
        {
            $subject = $headers->subject;
            $elements = imap_mime_header_decode($subject);
            $subject = '';
            foreach( $elements as $values )
            {
                $subjectPart = $values->text;
                $charset = $values->charset != 'default' ? $values->charset : 'default';
                if( $charset && function_exists('iconv') && !$disable_iconv && $charset != 'default' )
                {
                    $subjectPart = iconv($charset, $CONFIG['Charset'], $subjectPart);
                }
                $subject .= $subjectPart;
            }
            $header_info['subject'] = $subject;
        }
        else
        {
            $header_info['subject'] = "No Subject";
        }
        $headersReplyTo = array(  );
        if( isset($headers->reply_to) )
        {
            $headersReplyTo = $headers->reply_to;
        }
        if( 0 < count($headersReplyTo) )
        {
            $header_info['replyTo'] = $headersReplyTo[0]->mailbox . "@" . $headersReplyTo[0]->host;
        }
    }
    return $header_info;
}
/**
 * Parse the text to be output depending on if CLI or not
 *
 * @param string $output the text to format
 * @param string $sapiType the out from php_sapi_name()
 * @return string the formatted text
 */
function formatOutput($output, $sapiType)
{
    if( substr($sapiType, 0, 3) == 'cli' )
    {
        $output = strip_tags(str_replace(array( "<br>", "<hr>" ), array( "\n", "\n---\n" ), $output));
    }
    return $output;
}