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
if( !defined('RECAPTCHA_API_SERVER') )
{
    define('RECAPTCHA_API_SERVER', "http://www.google.com/recaptcha/api");
}
if( !defined('RECAPTCHA_API_SECURE_SERVER') )
{
    define('RECAPTCHA_API_SECURE_SERVER', "https://www.google.com/recaptcha/api");
}
if( !defined('RECAPTCHA_VERIFY_SERVER') )
{
    define('RECAPTCHA_VERIFY_SERVER', "www.google.com");
}

/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
class ReCaptchaResponse
{
    public $is_valid = NULL;
    public $error = NULL;
}
function _recaptcha_qsencode($data)
{
    $req = '';
    foreach( $data as $key => $value )
    {
        $req .= $key . "=" . urlencode(stripslashes($value)) . "&";
    }
    $req = substr($req, 0, strlen($req) - 1);
    return $req;
}
/**
 * Submits an HTTP POST to a reCAPTCHA server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
function _recaptcha_http_post($host, $path, $data, $port = 80)
{
    $req = _recaptcha_qsencode($data);
    $http_request = "POST " . $path . " HTTP/1.0\r\n";
    $http_request .= "Host: " . $host . "\r" . "\n";
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: " . strlen($req) . "\r\n";
    $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
    $http_request .= "\r\n";
    $http_request .= $req;
    $response = '';
    if( false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10)) )
    {
        exit( "reCAPTCHA Error: Could not open socket" );
    }
    fwrite($fs, $http_request);
    while( !feof($fs) )
    {
        $response .= fgets($fs, 1160);
    }
    fclose($fs);
    $response = explode("\r\n\r\n", $response, 2);
    return $response;
}
/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
 * @return string - The HTML to be embedded in the user's form.
 */
function recaptcha_get_html($pubkey, $error = null, $use_ssl = false)
{
    if( $pubkey == null || $pubkey == '' )
    {
        return "Required reCAPTCHA Keys missing from Setup > General Settings > Security";
    }
    if( $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' )
    {
        $use_ssl = true;
    }
    if( $use_ssl )
    {
        $server = RECAPTCHA_API_SECURE_SERVER;
    }
    else
    {
        $server = RECAPTCHA_API_SERVER;
    }
    $errorpart = '';
    if( $error )
    {
        $errorpart = "&amp;error=" . $error;
    }
    return "<script type=\"text/javascript\" src=\"" . $server . "/challenge?k=" . $pubkey . $errorpart . "\"></script>\n    <noscript>\n        <iframe src=\"" . $server . "/noscript?k=" . $pubkey . $errorpart . "\" height=\"300\" width=\"500\" frameborder=\"0\"></iframe><br/>\n        <textarea name=\"recaptcha_challenge_field\" rows=\"3\" cols=\"40\"></textarea>\n        <input type=\"hidden\" name=\"recaptcha_response_field\" value=\"manual_challenge\"/>\n    </noscript>";
}
/**
  * Calls an HTTP POST function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return ReCaptchaResponse
  */
function recaptcha_check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array(  ))
{
    if( $privkey == null || $privkey == '' )
    {
        return "Required reCAPTCHA Keys missing from Setup > General Settings > Security";
    }
    if( $remoteip == null || $remoteip == '' )
    {
        return "For security reasons, you must pass the remote ip to reCAPTCHA";
    }
    if( $challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0 )
    {
        $recaptcha_response = new ReCaptchaResponse();
        $recaptcha_response->is_valid = false;
        $recaptcha_response->error = 'incorrect-captcha-sol';
        return $recaptcha_response;
    }
    $response = _recaptcha_http_post(RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/verify', array( 'privatekey' => $privkey, 'remoteip' => $remoteip, 'challenge' => $challenge, 'response' => $response ) + $extra_params);
    $answers = explode("\n", $response[1]);
    $recaptcha_response = new ReCaptchaResponse();
    if( trim($answers[0]) == 'true' )
    {
        $recaptcha_response->is_valid = true;
    }
    else
    {
        $recaptcha_response->is_valid = false;
        $recaptcha_response->error = $answers[1];
    }
    return $recaptcha_response;
}
/**
 * gets a URL where the user can sign up for reCAPTCHA. If your application
 * has a configuration page where you enter a key, you should provide a link
 * using this function.
 * @param string $domain The domain where the page is hosted
 * @param string $appname The name of your application
 */
function recaptcha_get_signup_url($domain = null, $appname = null)
{
    return "https://www.google.com/recaptcha/admin/create?" . _recaptcha_qsencode(array( 'domains' => $domain, 'app' => $appname ));
}
function _recaptcha_aes_pad($val)
{
    $block_size = 16;
    $numpad = $block_size - strlen($val) % $block_size;
    return str_pad($val, strlen($val) + $numpad, chr($numpad));
}
function _recaptcha_aes_encrypt($val, $ky)
{
    if( !function_exists('mcrypt_encrypt') )
    {
        exit( "reCAPTCHA Error: To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed." );
    }
    $mode = MCRYPT_MODE_CBC;
    $enc = MCRYPT_RIJNDAEL_128;
    $val = _recaptcha_aes_pad($val);
    return mcrypt_encrypt($enc, $ky, $val, $mode, '');
}
function _recaptcha_mailhide_urlbase64($x)
{
    return strtr(base64_encode($x), "+/", '-_');
}
function recaptcha_mailhide_url($pubkey, $privkey, $email)
{
    if( $pubkey == '' || $pubkey == null || $privkey == '' || $privkey == null )
    {
        exit( "reCAPTCHA Error: To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " . "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>" );
    }
    $ky = pack("H*", $privkey);
    $cryptmail = _recaptcha_aes_encrypt($email, $ky);
    return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64($cryptmail);
}
/**
 * gets the parts of the email to expose to the user.
 * eg, given johndoe@example,com return ['john', "example.com"].
 * the email is then displayed as john...@example.com
 */
function _recaptcha_mailhide_email_parts($email)
{
    $arr = preg_split("/@/", $email);
    if( strlen($arr[0]) <= 4 )
    {
        $arr[0] = substr($arr[0], 0, 1);
    }
    else
    {
        if( strlen($arr[0]) <= 6 )
        {
            $arr[0] = substr($arr[0], 0, 3);
        }
        else
        {
            $arr[0] = substr($arr[0], 0, 4);
        }
    }
    return $arr;
}
/**
 * Gets html to display an email address given a public an private key.
 * to get a key, go to:
 *
 * http://www.google.com/recaptcha/mailhide/apikey
 */
function recaptcha_mailhide_html($pubkey, $privkey, $email)
{
    $emailparts = _recaptcha_mailhide_email_parts($email);
    $url = recaptcha_mailhide_url($pubkey, $privkey, $email);
    return htmlentities($emailparts[0]) . "<a href='" . htmlentities($url) . "' onclick=\"window.open('" . htmlentities($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities($emailparts[1]);
}