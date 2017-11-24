#!/usr/local/bin/php
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
$silent = 'true';
$_emailoutput = array(  );
$fd = fopen("php://stdin", 'r');
$input = '';
while( !feof($fd) )
{
    $input .= fread($fd, 1024);
}
fclose($fd);
$decode_params['input'] = $input;
$decode_params['include_bodies'] = true;
$decode_params['decode_bodies'] = true;
$decode_params['decode_headers'] = true;
$decode = new Mail_mimeDecode($input, "\r\n");
$structure = $decode->decode($decode_params);
$_emailoutput['headers'] = $structure->headers;
interpret_structure($structure);
if( $_emailoutput['body']['text/plain'] )
{
    $body = $_emailoutput['body']['text/plain'];
}
else
{
    if( $_emailoutput['body']['text/html'] )
    {
        $body = strip_tags($_emailoutput['body']['text/html']);
    }
    else
    {
        $body = "No message found.";
    }
}
$attachments = '';
if( !empty($_emailoutput['attachments']) )
{
    foreach( $_emailoutput['attachments'] as $attachment )
    {
        $filename = $attachment['filename'];
        if( checkTicketAttachmentExtension($filename) )
        {
            $filenameparts = explode(".", $filename);
            $extension = end($filenameparts);
            $filename = implode(array_slice($filenameparts, 0, 0 - 1));
            $filename = trim(preg_replace("/[^a-zA-Z0-9-_ ]/", '', $filename));
            if( !$filename )
            {
                $filename = 'attachment';
            }
            mt_srand(time());
            $rand = mt_rand(100000, 999999);
            $attachmentfilename = $rand . '_' . $filename . "." . $extension;
            $attachments .= $attachmentfilename . "|";
            $fp = fopen($attachments_dir . $attachmentfilename, 'w');
            fwrite($fp, $attachment['data']);
            fclose($fp);
        }
        else
        {
            $body .= "\n" . "\nAttachment " . $filename . " blocked - file type not allowed.";
        }
    }
}
$attachments = substr($attachments, 0, 0 - 1);
$from = $_emailoutput['headers']['from'];
$to = $_emailoutput['headers']['to'];
$cc = $_emailoutput['headers']['cc'];
$bcc = $_emailoutput['headers']['bcc'];
if( !$to )
{
    $to = $_emailoutput['headers']['resent-to'];
}
$subject = $_emailoutput['headers']['subject'];
$fromname = preg_replace("/(.*)<(.*)>/", "\\1", $from);
$fromname = str_replace("\"", '', $fromname);
if( $_emailoutput['headers']['reply-to'] )
{
    $fromemail = $_emailoutput['headers']['reply-to'];
    if( !filter_var($fromemail, FILTER_VALIDATE_EMAIL) )
    {
        $fromemail = preg_replace("/(.*)<(.*)>/", "\\2", $fromemail);
    }
}
else
{
    $fromemail = preg_replace("/(.*)<(.*)>/", "\\2", $from);
}
$to = explode(',', $to);
foreach( $to as $toemail )
{
    if( strpos("." . $toemail, "<") )
    {
        $toemails[] = preg_replace("/(.*)<(.*)>/", "\\2", $toemail);
    }
    else
    {
        $toemails[] = $toemail;
    }
}
$to = explode(',', $cc);
foreach( $to as $toemail )
{
    if( strpos("." . $toemail, "<") )
    {
        $toemails[] = preg_replace("/(.*)<(.*)>/", "\\2", $toemail);
    }
    else
    {
        $toemails[] = $toemail;
    }
}
$to = explode(',', $bcc);
foreach( $to as $toemail )
{
    if( strpos("." . $toemail, "<") )
    {
        $toemails[] = preg_replace("/(.*)<(.*)>/", "\\2", $toemail);
    }
    else
    {
        $toemails[] = $toemail;
    }
}
$to = implode(',', $toemails);
processPipedTicket($to, $fromname, $fromemail, $subject, $body, $attachments);
/**
*  +----------------------------- IMPORTANT ------------------------------+
*  | Usage of this class compared to native php extensions such as        |
*  | mailparse or imap, is slow and may be feature deficient. If available|
*  | you are STRONGLY recommended to use the php extensions.              |
*  +----------------------------------------------------------------------+
*
* Mime Decoding class
*
* This class will parse a raw mime email and return
* the structure. Returned structure is similar to
* that returned by imap_fetchstructure().
*
* USAGE: (assume $input is your raw email)
*
* $decode = new Mail_mimeDecode($input, "\r\n");
* $structure = $decode->decode();
* print_r($structure);
*
* Or statically:
*
* $params['input'] = $input;
* $structure = Mail_mimeDecode::decode($params);
* print_r($structure);
*
* TODO:
*  o Implement multipart/appledouble
*  o UTF8: ???
        > 4. We have also found a solution for decoding the UTF-8
        > headers. Therefore I made the following function:
        >
        > function decode_utf8($txt) {
        > $trans=array("æ#8216;"=>"õ","u"=>"û","O"=>"f#8226;","U"
        =>"f#8250;");
        > $txt=strtr($txt,$trans);
        > return(utf8_decode($txt));
        > }
        >
        > And I have inserted the following line to the class:
        >
        > if (strtolower($charset)=="utf-8") $text=decode_utf8($text);
        >
        > ... before the following one in the '_decodeHeader' function:
        >
        > $input = str_replace($encoded, $text, $input);
        >
        > This way from now on it can easily decode the UTF-8 headers too.
*
* @author  Richard Heyes <richard@phpguru.org>
* @version $Revision: 1.4 $
* @package Mail
*/
class Mail_mimeDecode
{
    public $_input = NULL;
    public $_header = NULL;
    public $_body = NULL;
    public $_error = NULL;
    public $_include_bodies = NULL;
    public $_decode_bodies = NULL;
    public $_decode_headers = NULL;
    /**
     * Constructor.
     *
     * Sets up the object, initialise the variables, and splits and
     * stores the header and body of the input.
     *
     * @param string The input to decode
     * @access public
     */
    public function Mail_mimeDecode($input)
    {
        list($header, $body) = $this->_splitBodyHeader($input);
        $this->_input = $input;
        $this->_header = $header;
        $this->_body = $body;
        $this->_decode_bodies = true;
        $this->_include_bodies = true;
    }
    /**
     * Begins the decoding process. If called statically
     * it will create an object and call the decode() method
     * of it.
     *
     * @param array An array of various parameters that determine
     *              various things:
     *              include_bodies - Whether to include the body in the returned
     *                               object.
     *              decode_bodies  - Whether to decode the bodies
     *                               of the parts. (Transfer encoding)
     *              decode_headers - Whether to decode headers
     *              input          - If called statically, this will be treated
     *                               as the input
     * @return object Decoded results
     * @access public
     */
    public function decode($params = null)
    {
        $isStatic = !(isset($this) && get_class($this) == 'Mail_mimeDecode');
        if( $isStatic && isset($params['input']) )
        {
            $obj = new Mail_mimeDecode($params['input']);
            $structure = $obj->decode($params);
        }
        else
        {
            if( $isStatic )
            {
                return false;
            }
            $this->_include_bodies = isset($params['include_bodies']) ? $params['include_bodies'] : false;
            $this->_decode_bodies = isset($params['decode_bodies']) ? $params['decode_bodies'] : false;
            $this->_decode_headers = isset($params['decode_headers']) ? $params['decode_headers'] : false;
            $structure = $this->_decode($this->_header, $this->_body);
        }
        return $structure;
    }
    /**
     * Performs the decoding. Decodes the body string passed to it
     * If it finds certain content-types it will call itself in a
     * recursive fashion
     *
     * @param string Header section
     * @param string Body section
     * @return object Results of decoding process
     * @access private
     */
    public function _decode($headers, $body, $default_ctype = 'text/plain')
    {
        $return = new stdClass();
        $return->headers = array(  );
        $headers = $this->_parseHeaders($headers);
        foreach( $headers as $value )
        {
            if( isset($return->headers[strtolower($value['name'])]) && !is_array($return->headers[strtolower($value['name'])]) )
            {
                $return->headers[strtolower($value['name'])] = array( $return->headers[strtolower($value['name'])] );
                $return->headers[strtolower($value['name'])][] = $value['value'];
            }
            else
            {
                if( isset($return->headers[strtolower($value['name'])]) )
                {
                    $return->headers[strtolower($value['name'])][] = $value['value'];
                }
                else
                {
                    $return->headers[strtolower($value['name'])] = $value['value'];
                }
            }
        }
        reset($headers);
        while( list($key, $value) = each($headers) )
        {
            $headers[$key]['name'] = strtolower($headers[$key]['name']);
            switch( $headers[$key]['name'] )
            {
                case 'content-type':
                    $content_type = $this->_parseHeaderValue($headers[$key]['value']);
                    if( preg_match("/([0-9a-z+.-]+)\\/([0-9a-z+.-]+)/i", $content_type['value'], $regs) )
                    {
                        $return->ctype_primary = $regs[1];
                        $return->ctype_secondary = $regs[2];
                    }
                    if( isset($content_type['other']) )
                    {
                        while( list($p_name, $p_value) = each($content_type['other']) )
                        {
                            $return->ctype_parameters[$p_name] = $p_value;
                        }
                    }
                    break;
                case 'content-disposition':
                    $content_disposition = $this->_parseHeaderValue($headers[$key]['value']);
                    $return->disposition = $content_disposition['value'];
                    if( isset($content_disposition['other']) )
                    {
                        while( list($p_name, $p_value) = each($content_disposition['other']) )
                        {
                            $return->d_parameters[$p_name] = $p_value;
                        }
                    }
                    break;
                case 'content-transfer-encoding':
                    $content_transfer_encoding = $this->_parseHeaderValue($headers[$key]['value']);
            }
            break;
        }
        if( isset($content_type) )
        {
            switch( strtolower($content_type['value']) )
            {
                case 'text/plain':
                    $encoding = isset($content_transfer_encoding) ? $content_transfer_encoding['value'] : '7bit';
                    $this->_include_bodies ? ($return->body = $this->_decode_bodies ? $this->_decodeBody($body, $encoding) : $body) : null;
                    break;
                case 'text/html':
                    $encoding = isset($content_transfer_encoding) ? $content_transfer_encoding['value'] : '7bit';
                    $this->_include_bodies ? ($return->body = $this->_decode_bodies ? $this->_decodeBody($body, $encoding) : $body) : null;
                    break;
                case 'multipart/parallel':
                    break;
                case 'multipart/report':
                    break;
                case 'multipart/signed':
                    break;
                case 'multipart/digest':
                    break;
                case 'multipart/alternative':
                    break;
                case 'multipart/related':
                    break;
                case 'multipart/mixed':
                    if( !isset($content_type['other']['boundary']) )
                    {
                        $this->_error = "No boundary found for " . $content_type['value'] . " part";
                        return false;
                    }
                    $default_ctype = strtolower($content_type['value']) === 'multipart/digest' ? 'message/rfc822' : 'text/plain';
                    $parts = $this->_boundarySplit($body, $content_type['other']['boundary']);
                    for( $i = 0; $i < count($parts); $i++ )
                    {
                        list($part_header, $part_body) = $this->_splitBodyHeader($parts[$i]);
                        $part = $this->_decode($part_header, $part_body, $default_ctype);
                        $return->parts[] = $part;
                    }
                    break;
                case 'message/rfc822':
                    $obj = new Mail_mimeDecode($body);
                    $return->parts[] = $obj->decode(array( 'include_bodies' => $this->_include_bodies, 'decode_bodies' => $this->_decode_bodies, 'decode_headers' => $this->_decode_headers ));
                    unset($obj);
                    break;
                default:
                    if( !isset($content_transfer_encoding['value']) )
                    {
                        $content_transfer_encoding['value'] = '7bit';
                    }
                    $this->_include_bodies ? ($return->body = $this->_decode_bodies ? $this->_decodeBody($body, $content_transfer_encoding['value']) : $body) : null;
                    break;
            }
        }
        else
        {
            $ctype = explode('/', $default_ctype);
            $return->ctype_primary = $ctype[0];
            $return->ctype_secondary = $ctype[1];
            $this->_include_bodies ? ($return->body = $this->_decode_bodies ? $this->_decodeBody($body) : $body) : null;
        }
        return $return;
    }
    /**
     * Given the output of the above function, this will return an
     * array of references to the parts, indexed by mime number.
     *
     * @param  object $structure   The structure to go through
     * @param  string $mime_number Internal use only.
     * @return array               Mime numbers
     */
    public function &getMimeNumbers(&$structure, $no_refs = false, $mime_number = '', $prepend = '')
    {
        $return = array(  );
        if( !empty($structure->parts) )
        {
            if( $mime_number != '' )
            {
                $structure->mime_id = $prepend . $mime_number;
                $return[$prepend . $mime_number] =& $structure;
            }
            for( $i = 0; $i < count($structure->parts); $i++ )
            {
                if( !empty($structure->headers['content-type']) && substr(strtolower($structure->headers['content-type']), 0, 8) == 'message/' )
                {
                    $prepend = $prepend . $mime_number . ".";
                    $_mime_number = '';
                }
                else
                {
                    $_mime_number = $mime_number == '' ? $i + 1 : sprintf("%s.%s", $mime_number, $i + 1);
                }
                $arr =& Mail_mimeDecode::getmimenumbers($structure->parts[$i], $no_refs, $_mime_number, $prepend);
                foreach( $arr as $key => $val )
                {
                    $no_refs ? $return[$key] : ($return[$key] =& $arr[$key]);
                }
            }
        }
        else
        {
            if( $mime_number == '' )
            {
                $mime_number = '1';
            }
            $structure->mime_id = $prepend . $mime_number;
            $no_refs ? $return[$prepend . $mime_number] : ($return[$prepend . $mime_number] =& $structure);
        }
        return $return;
    }
    /**
     * Given a string containing a header and body
     * section, this function will split them (at the first
     * blank line) and return them.
     *
     * @param string Input to split apart
     * @return array Contains header and body section
     * @access private
     */
    public function _splitBodyHeader($input)
    {
        if( preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $input, $match) )
        {
            return array( $match[1], $match[2] );
        }
        $this->_error = "Could not split header and body";
        return false;
    }
    /**
     * Parse headers given in $input and return
     * as assoc array.
     *
     * @param string Headers to parse
     * @return array Contains parsed headers
     * @access private
     */
    public function _parseHeaders($input)
    {
        if( $input !== '' )
        {
            $input = preg_replace("/\r?\n/", "\r\n", $input);
            $input = preg_replace("/\r\n(\t| )+/", " ", $input);
            $headers = explode("\r\n", trim($input));
            foreach( $headers as $value )
            {
                $hdr_name = substr($value, 0, $pos = strpos($value, ":"));
                $hdr_value = substr($value, $pos + 1);
                if( $hdr_value[0] == " " )
                {
                    $hdr_value = substr($hdr_value, 1);
                }
                $return[] = array( 'name' => $hdr_name, 'value' => $this->_decode_headers ? $this->_decodeHeader($hdr_value) : $hdr_value );
            }
        }
        else
        {
            $return = array(  );
        }
        return $return;
    }
    /**
     * Function to parse a header value,
     * extract first part, and any secondary
     * parts (after ;) This function is not as
     * robust as it could be. Eg. header comments
     * in the wrong place will probably break it.
     *
     * @param string Header value to parse
     * @return array Contains parsed result
     * @access private
     */
    public function _parseHeaderValue($input)
    {
        if( ($pos = strpos($input, ';')) !== false )
        {
            $return['value'] = trim(substr($input, 0, $pos));
            $input = trim(substr($input, $pos + 1));
            if( 0 < strlen($input) )
            {
                $splitRegex = "/([^;'\"]*['\"]([^'\"]*([^'\"]*)*)['\"][^;'\"]*|([^;]+))(;|\$)/";
                preg_match_all($splitRegex, $input, $matches);
                $parameters = array(  );
                for( $i = 0; $i < count($matches[0]); $i++ )
                {
                    $param = $matches[0][$i];
                    while( substr($param, 0 - 2) == "\\;" )
                    {
                        $param .= $matches[0][++$i];
                    }
                    $parameters[] = $param;
                }
                for( $i = 0; $i < count($parameters); $i++ )
                {
                    $param_name = trim(substr($parameters[$i], 0, $pos = strpos($parameters[$i], "=")), "'\";\t\\ ");
                    $param_value = trim(str_replace("\\;", ';', substr($parameters[$i], $pos + 1)), "'\";\t\\ ");
                    if( $param_value[0] == "\"" )
                    {
                        $param_value = substr($param_value, 1, 0 - 1);
                    }
                    $return['other'][$param_name] = $param_value;
                    $return['other'][strtolower($param_name)] = $param_value;
                }
            }
        }
        else
        {
            $return['value'] = trim($input);
        }
        return $return;
    }
    /**
     * This function splits the input based
     * on the given boundary
     *
     * @param string Input to parse
     * @return array Contains array of resulting mime parts
     * @access private
     */
    public function _boundarySplit($input, $boundary)
    {
        $parts = array(  );
        $bs_possible = substr($boundary, 2, 0 - 2);
        $bs_check = "\\\"" . $bs_possible . "\\\"";
        if( $boundary == $bs_check )
        {
            $boundary = $bs_possible;
        }
        $tmp = explode('--' . $boundary, $input);
        for( $i = 1; $i < count($tmp) - 1; $i++ )
        {
            $parts[] = $tmp[$i];
        }
        return $parts;
    }
    /**
     * Given a header, this function will decode it
     * according to RFC2047. Probably not *exactly*
     * conformant, but it does pass all the given
     * examples (in RFC2047).
     *
     * @param string Input header value to decode
     * @return string Decoded header value
     * @access private
     */
    public function _decodeHeader($input)
    {
        $input = preg_replace("/(=\\?[^?]+\\?(q|b)\\?[^?]*\\?=)(\\s)+=\\?/i", "\\1=?", $input);
        while( preg_match("/(=\\?([^?]+)\\?(q|b)\\?([^?]*)\\?=)/i", $input, $matches) )
        {
            $encoded = $matches[1];
            $charset = $matches[2];
            $encoding = $matches[3];
            $text = $matches[4];
            switch( strtolower($encoding) )
            {
                case 'b':
                    $text = base64_decode($text);
                    break;
                case 'q':
                    $text = str_replace('_', " ", $text);
                    preg_match_all("/=([a-f0-9]{2})/i", $text, $matches);
                    foreach( $matches[1] as $value )
                    {
                        $text = str_replace("=" . $value, chr(hexdec($value)), $text);
                    }
            }
            $input = str_replace($encoded, $text, $input);
            break;
        }
        return $input;
    }
    /**
     * Given a body string and an encoding type,
     * this function will decode and return it.
     *
     * @param  string Input body to decode
     * @param  string Encoding type to use.
     * @return string Decoded body
     * @access private
     */
    public function _decodeBody($input, $encoding = '7bit')
    {
        switch( strtolower($encoding) )
        {
            case '7bit':
                return $input;
                break;
            case 'quoted-printable':
                return $this->_quotedPrintableDecode($input);
                break;
            case 'base64':
                return base64_decode($input);
                break;
            default:
                return $input;
                break;
        }
    }
    /**
     * Given a quoted-printable string, this
     * function will decode and return it.
     *
     * @param  string Input body to decode
     * @return string Decoded body
     * @access private
     */
    public function _quotedPrintableDecode($input)
    {
        $input = preg_replace("/=\r?\n/", '', $input);
        $input = preg_replace("/=([a-f0-9]{2})/ie", "chr(hexdec('\\1'))", $input);
        return $input;
    }
    /**
     * Checks the input for uuencoded files and returns
     * an array of them. Can be called statically, eg:
     *
     * $files =& Mail_mimeDecode::uudecode($some_text);
     *
     * It will check for the begin 666 ... end syntax
     * however and won't just blindly decode whatever you
     * pass it.
     *
     * @param  string Input body to look for attahcments in
     * @return array  Decoded bodies, filenames and permissions
     * @access public
     * @author Unknown
     */
    public function &uudecode($input)
    {
        preg_match_all("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $input, $matches);
        for( $j = 0; $j < count($matches[3]); $j++ )
        {
            $str = $matches[3][$j];
            $filename = $matches[2][$j];
            $fileperm = $matches[1][$j];
            $file = '';
            $str = preg_split("/\r?\n/", trim($str));
            $strlen = count($str);
            for( $i = 0; $i < $strlen; $i++ )
            {
                $pos = 1;
                $d = 0;
                $len = (int) (ord(substr($str[$i], 0, 1)) - 32 - " " & 63);
                while( $d + 3 <= $len && $pos + 4 <= strlen($str[$i]) )
                {
                    $c0 = ord(substr($str[$i], $pos, 1)) ^ 32;
                    $c1 = ord(substr($str[$i], $pos + 1, 1)) ^ 32;
                    $c2 = ord(substr($str[$i], $pos + 2, 1)) ^ 32;
                    $c3 = ord(substr($str[$i], $pos + 3, 1)) ^ 32;
                    $file .= chr(($c0 - " " & 63) << 2 | ($c1 - " " & 63) >> 4);
                    $file .= chr(($c1 - " " & 63) << 4 | ($c2 - " " & 63) >> 2);
                    $file .= chr(($c2 - " " & 63) << 6 | $c3 - " " & 63);
                    $pos += 4;
                    $d += 3;
                }
                if( $d + 2 <= $len && $pos + 3 <= strlen($str[$i]) )
                {
                    $c0 = ord(substr($str[$i], $pos, 1)) ^ 32;
                    $c1 = ord(substr($str[$i], $pos + 1, 1)) ^ 32;
                    $c2 = ord(substr($str[$i], $pos + 2, 1)) ^ 32;
                    $file .= chr(($c0 - " " & 63) << 2 | ($c1 - " " & 63) >> 4);
                    $file .= chr(($c1 - " " & 63) << 4 | ($c2 - " " & 63) >> 2);
                    $pos += 3;
                    $d += 2;
                }
                if( $d + 1 <= $len && $pos + 2 <= strlen($str[$i]) )
                {
                    $c0 = ord(substr($str[$i], $pos, 1)) ^ 32;
                    $c1 = ord(substr($str[$i], $pos + 1, 1)) ^ 32;
                    $file .= chr(($c0 - " " & 63) << 2 | ($c1 - " " & 63) >> 4);
                }
            }
            $files[] = array( 'filename' => $filename, 'fileperm' => $fileperm, 'filedata' => $file );
        }
        return $files;
    }
    /**
     * getSendArray() returns the arguments required for Mail::send()
     * used to build the arguments for a mail::send() call
     *
     * Usage:
     * $mailtext = Full email (for example generated by a template)
     * $decoder = new Mail_mimeDecode($mailtext);
     * $parts =  $decoder->getSendArray();
     * if (!PEAR::isError($parts) {
     *     list($recipents,$headers,$body) = $parts;
     *     $mail = Mail::factory('smtp');
     *     $mail->send($recipents,$headers,$body);
     * } else {
     *     echo $parts->message;
     * }
     * @return mixed   array of recipeint, headers,body or Pear_Error
     * @access public
     * @author Alan Knowles <alan@akbkhome.com>
     */
    public function getSendArray()
    {
        $this->_decode_headers = FALSE;
        $headerlist = $this->_parseHeaders($this->_header);
        $to = '';
        if( !$headerlist )
        {
            return false;
        }
        foreach( $headerlist as $item )
        {
            $header[$item['name']] = $item['value'];
            switch( strtolower($item['name']) )
            {
                case 'to':
                case 'cc':
                    break;
                case 'bcc':
                    $to = ',' . $item['value'];
                    break;
                default:
                    break;
            }
        }
        if( $to == '' )
        {
            return false;
        }
        $to = substr($to, 1);
        return array( $to, $header, $this->_body );
    }
    /**
     * Returns a xml copy of the output of
     * Mail_mimeDecode::decode. Pass the output in as the
     * argument. This function can be called statically. Eg:
     *
     * $output = $obj->decode();
     * $xml    = Mail_mimeDecode::getXML($output);
     *
     * The DTD used for this should have been in the package. Or
     * alternatively you can get it from cvs, or here:
     * http://www.phpguru.org/xmail/xmail.dtd.
     *
     * @param  object Input to convert to xml. This should be the
     *                output of the Mail_mimeDecode::decode function
     * @return string XML version of input
     * @access public
     */
    public function getXML($input)
    {
        $crlf = "\r\n";
        $output = "<?xml version='1.0'?>" . $crlf . "<!DOCTYPE email SYSTEM \"http://www.phpguru.org/xmail/xmail.dtd\">" . $crlf . "<email>" . $crlf . Mail_mimeDecode::_getxml($input) . "</email>";
        return $output;
    }
    /**
     * Function that does the actual conversion to xml. Does a single
     * mimepart at a time.
     *
     * @param  object  Input to convert to xml. This is a mimepart object.
     *                 It may or may not contain subparts.
     * @param  integer Number of tabs to indent
     * @return string  XML version of input
     * @access private
     */
    public function _getXML($input, $indent = 1)
    {
        $htab = "\t";
        $crlf = "\r\n";
        $output = '';
        $headers = (array) $input->headers;
        foreach( $headers as $hdr_name => $hdr_value )
        {
            if( is_array($headers[$hdr_name]) )
            {
                for( $i = 0; $i < count($hdr_value); $i++ )
                {
                    $output .= Mail_mimeDecode::_getxml_helper($hdr_name, $hdr_value[$i], $indent);
                }
            }
            else
            {
                $output .= Mail_mimeDecode::_getxml_helper($hdr_name, $hdr_value, $indent);
            }
        }
        if( !empty($input->parts) )
        {
            for( $i = 0; $i < count($input->parts); $i++ )
            {
                $output .= $crlf . str_repeat($htab, $indent) . "<mimepart>" . $crlf . Mail_mimeDecode::_getxml($input->parts[$i], $indent + 1) . str_repeat($htab, $indent) . "</mimepart>" . $crlf;
            }
        }
        else
        {
            if( isset($input->body) )
            {
                $output .= $crlf . str_repeat($htab, $indent) . "<body><![CDATA[" . $input->body . "]]></body>" . $crlf;
            }
        }
        return $output;
    }
    /**
     * Helper function to _getXML(). Returns xml of a header.
     *
     * @param  string  Name of header
     * @param  string  Value of header
     * @param  integer Number of tabs to indent
     * @return string  XML version of input
     * @access private
     */
    public function _getXML_helper($hdr_name, $hdr_value, $indent)
    {
        $htab = "\t";
        $crlf = "\r\n";
        $return = '';
        $new_hdr_value = $hdr_name != 'received' ? Mail_mimeDecode::_parseheadervalue($hdr_value) : array( 'value' => $hdr_value );
        $new_hdr_name = str_replace(" ", '-', ucwords(str_replace('-', " ", $hdr_name)));
        if( !empty($new_hdr_value['other']) )
        {
            foreach( $new_hdr_value['other'] as $paramname => $paramvalue )
            {
                $params[] = str_repeat($htab, $indent) . $htab . "<parameter>" . $crlf . str_repeat($htab, $indent) . $htab . $htab . "<paramname>" . htmlspecialchars($paramname) . "</paramname>" . $crlf . str_repeat($htab, $indent) . $htab . $htab . "<paramvalue>" . htmlspecialchars($paramvalue) . "</paramvalue>" . $crlf . str_repeat($htab, $indent) . $htab . "</parameter>" . $crlf;
            }
            $params = implode('', $params);
        }
        else
        {
            $params = '';
        }
        $return = str_repeat($htab, $indent) . "<header>" . $crlf . str_repeat($htab, $indent) . $htab . "<headername>" . htmlspecialchars($new_hdr_name) . "</headername>" . $crlf . str_repeat($htab, $indent) . $htab . "<headervalue>" . htmlspecialchars($new_hdr_value['value']) . "</headervalue>" . $crlf . $params . str_repeat($htab, $indent) . "</header>" . $crlf;
        return $return;
    }
}
function interpret_structure($structure)
{
    global $_emailoutput;
    global $disable_iconv;
    $ctype = strtolower($structure->ctype_primary) . '/' . strtolower($structure->ctype_secondary);
    if( !$ctype )
    {
        $ctype = 'text/plain';
    }
    if( $ctype == 'text/html' || $ctype == 'text/plain' )
    {
        $charset = 'us-ascii';
        if( !empty($structure->ctype_parameters) && isset($structure->ctype_parameters['charset']) )
        {
            $charset = $structure->ctype_parameters['charset'];
        }
        if( !empty($structure->disposition) && $structure->disposition == 'attachment' )
        {
            handle_attachment($structure);
        }
        else
        {
            $var = $ctype == 'text/html' ? 'html' : 'text';
            $bodyUtf8 = $structure->body;
            if( $charset == 'UTF-8' )
            {
                $charset = '';
            }
            if( $charset && function_exists('iconv') && !$disable_iconv )
            {
                $bodyUtf8 = iconv($charset, 'utf-8', $bodyUtf8);
                if( !$_emailoutput['headers']['convertedcharset'] )
                {
                    $_emailoutput['headers']['subject'] = iconv($charset, 'utf-8', $_emailoutput['headers']['subject']);
                    $_emailoutput['headers']['convertedcharset'] = true;
                }
            }
            $_emailoutput['body'][$ctype] = trim($bodyUtf8);
        }
    }
    else
    {
        if( strtolower($structure->ctype_primary) == 'multipart' )
        {
            if( !empty($structure->parts) )
            {
                for( $i = 0; $i < count($structure->parts); $i++ )
                {
                    interpret_structure($structure->parts[$i]);
                }
            }
        }
        else
        {
            handle_attachment($structure);
        }
    }
}
function handle_attachment($structure)
{
    global $_emailoutput;
    if( !empty($structure->d_parameters['filename']) )
    {
        $filename = $structure->d_parameters['filename'];
    }
    else
    {
        if( !empty($structure->ctype_parameters['name']) )
        {
            $filename = $structure->ctype_parameters['name'];
        }
        else
        {
            return NULL;
        }
    }
    $ctype = strtolower($structure->ctype_primary) . '/' . strtolower($structure->ctype_secondary);
    $_emailoutput['attachments'][] = array( 'data' => $structure->body, 'size' => strlen($structure->body), 'filename' => $filename, 'contenttype' => $ctype );
}