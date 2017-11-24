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
class WHMCS_DuoSecurity {
	const DUO_PREFIX = 'TX';
	const APP_PREFIX = 'APP';
	const AUTH_PREFIX = 'AUTH';
	const DUO_EXPIRE = 300;
	const APP_EXPIRE = 3600;
	const IKEY_LEN = 20;
	const SKEY_LEN = 40;
	const AKEY_LEN = 40;
	const ERR_USER = "ERR|The username passed to sign_request() is invalid.";
	const ERR_IKEY = "ERR|The Duo integration key passed to sign_request() is invalid.";
	const ERR_SKEY = "ERR|The Duo secret key passed to sign_request() is invalid.";
	const ERR_AKEY = "ERR|The application secret key passed to sign_request() must be at least 40 characters.";
	private static function sign_vals($key, $vals, $prefix, $expire, $time = NULL) {
		$exp    = ($time ? $time : time()) + $expire;
		$val    = $vals . "|" . $exp;
		$b64    = base64_encode($val);
		$cookie = $prefix . "|" . $b64;
		$sig    = hash_hmac('sha1', $cookie, $key);
		return $cookie . "|" . $sig;
	}
	private static function parse_vals($key, $val, $prefix, $ikey, $time = NULL) {
		$ts    = $time ? $time : time();
		$parts = explode("|", $val);
		if(count($parts) != 3) {
			return null;
		}
		list($u_prefix, $u_b64, $u_sig) = $parts;
		$sig = hash_hmac('sha1', $u_prefix . "|" . $u_b64, $key);
		if(hash_hmac('sha1', $sig, $key) != hash_hmac('sha1', $u_sig, $key)) {
			return null;
		}
		if($u_prefix != $prefix) {
			return null;
		}
		$cookie_parts = explode("|", base64_decode($u_b64));
		if(count($cookie_parts) != 3) {
			return null;
		}
		list($user, $u_ikey, $exp) = $cookie_parts;
		if($u_ikey != $ikey) {
			return null;
		}
		if(intval($exp) <= $ts) {
			return null;
		}
		return $user;
	}
	public static function signRequest($ikey, $skey, $akey, $username, $time = NULL) {
		if(!isset($username) || strlen($username) == 0) {
			return self::ERR_USER;
		}
		if(strpos($username, "|") !== FALSE) {
			return self::ERR_USER;
		}
		if(!isset($ikey) || strlen($ikey) != self::IKEY_LEN) {
			return self::ERR_IKEY;
		}
		if(!isset($skey) || strlen($skey) != self::SKEY_LEN) {
			return self::ERR_SKEY;
		}
		if(!isset($akey) || strlen($akey) < self::AKEY_LEN) {
			return self::ERR_AKEY;
		}
		$vals    = $username . "|" . $ikey;
		$duo_sig = self::sign_vals($skey, $vals, self::DUO_PREFIX, self::DUO_EXPIRE, $time);
		$app_sig = self::sign_vals($akey, $vals, self::APP_PREFIX, self::APP_EXPIRE, $time);
		return $duo_sig . ":" . $app_sig;
	}
	public static function verifyResponse($ikey, $skey, $akey, $sig_response, $time = NULL) {
		list($auth_sig, $app_sig) = explode(":", $sig_response);
		$auth_user = self::parse_vals($skey, $auth_sig, self::AUTH_PREFIX, $ikey, $time);
		$app_user  = self::parse_vals($akey, $app_sig, self::APP_PREFIX, $ikey, $time);
		if($auth_user != $app_user) {
			return null;
		}
		return $auth_user;
	}
}
function duosecurity_config() {
	global $licensing;
	$licensedata  = $licensing->getKeyData('configoptions');
	$duouserlimit = array_key_exists("Duo Security", $licensedata) ? $licensedata["Duo Security"] : 0;
	$usercount    = get_query_val('tblclients', "COUNT(id)", array(
		'authmodule' => 'duosecurity'
	)) + get_query_val('tbladmins', "COUNT(id)", array(
		'authmodule' => 'duosecurity'
	));
	$configarray  = array(
		'FriendlyName' => array(
			'Type' => 'System',
			'Value' => "Duo Security"
		),
		'Description' => array(
			'Type' => 'System',
			'Value' => "Duo Security enables your users to secure their logins using their smartphones. Authentication options include push notifications, passcodes, text messages and/or phone calls.<br /><br />For more information about Duo Security, please <a href=\"http://nullrefer.com/?http://go.whmcs.com/110/duo-security\" target=\"_blank\">click here</a>." . (0 < $duouserlimit ? '' : "<br /><br /><strong>Starts from just \$3/per user/per month</strong>")
		),
		'Licensed' => array(
			'Type' => 'System',
			'Value' => 0 < $duouserlimit ? true : false
		),
		'SubscribeLink' => array(
			'Type' => 'System',
			'Value' => "http://nullrefer.com/?http://go.whmcs.com/110/duo-security"
		),
		'UserLimit' => array(
			'Type' => 'System',
			'Value' => $duouserlimit
		),
		"User Limit" => array(
			'Type' => 'Info',
			'Description' => $usercount . '/' . $duouserlimit . " - <a href=\"http://nullrefer.com/?http://go.whmcs.com/122/buy-duo-security\" target=\"_blank\">Click here to buy more</a>"
		)
	);
	return $configarray;
}
function duosecurity_activate($params) {
	global $licensing;
	$licensedata  = $licensing->getKeyData('configoptions');
	$duouserlimit = array_key_exists("Duo Security", $licensedata) ? $licensedata["Duo Security"] : 0;
	$usercount    = get_query_val('tblclients', "COUNT(id)", array(
		'authmodule' => 'duosecurity'
	)) + get_query_val('tbladmins', "COUNT(id)", array(
		'authmodule' => 'duosecurity'
	));
	if($duouserlimit == 0) {
		if(defined('ADMINAREA')) {
			return "<h2>DuoSecurity Activation Problem</h2><p>This WHMCS license has not had Duo Security Users purchased yet. To buy more, please navigate to Setup > Staff Management > Two-Factor Authentication.</p><br /><p align=\"center\"><input type=\"button\" value=\"Close Window\" onclick=\"dialogClose()\" /></p>";
		}
		return "<h2>DuoSecurity Activation Problem</h2><p>Error Code 101. Cannot continue. Please contact support.</p><br /><p align=\"center\"><input type=\"button\" value=\"Close Window\" onclick=\"dialogClose()\" /></p>";
	}
	if($duouserlimit <= $usercount) {
		if(defined('ADMINAREA')) {
			return "<h2>DuoSecurity Activation Problem</h2><p>This WHMCS license has reached the allowed number of Duo Security users.</p><p>Please contact the system administrator.</p><br /><p align=\"center\"><input type=\"button\" value=\"Close Window\" onclick=\"dialogClose()\" /></p>";
		}
		return "<h2>DuoSecurity Activation Problem</h2><p>Error Code 102. Cannot continue. Please contact support.</p><br /><p align=\"center\"><input type=\"button\" value=\"Close Window\" onclick=\"dialogClose()\" /></p>";
	}
	return array(
		'completed' => true,
		'msg' => "You will be asked to configure your Duo Security Two-Factor Authentication the next time you login."
	);
}
function duosecurity_challenge($params) {
	global $whmcs;
	$appsecretkey   = sha1('Duo' . $whmcs->get_hash());
	$adminid        = $params['user_info']['id'];
	$username       = $params['user_info']['username'];
	$email          = $params['user_info']['email'];
	$integrationkey = 'DILXRHE92017KPRVVM4T';
	$secretkey      = 'lUQE5dQlJn69ime5PtWJ8f8A0oMjmVXZY6wA5tqT';
	$apihostname    = "api-3ce575d8.duosecurity.com";
	$uid            = $username . ":" . $email . ":" . $whmcs->get_license_key();
	$sig_request    = WHMCS_DuoSecurity::signrequest($integrationkey, $secretkey, $appsecretkey, $uid);
	if($sig_request != null) {
		$output = "<script src=\"" . (defined('ADMINAREA') ? "../" : '') . "modules/security/duosecurity/Duo-Web-v1.min.js\"></script>
<script>
  Duo.init({
    \"host\": \"" . $apihostname . "\",
    \"sig_request\": \"" . $sig_request . "\",
    \"post_action\": \"dologin.php\"
  });
</script>
<iframe id=\"duo_iframe\" width=\"100%\" height=\"500\" frameborder=\"0\"></iframe>";
	} else {
		$output = "There is an error with the DuoSecurity module configuration. Please try again.";
	}
	return $output;
}
function duosecurity_verify($params) {
	global $whmcs;
	$appsecretkey   = sha1('Duo' . $whmcs->get_hash());
	$integrationkey = 'DILXRHE92017KPRVVM4T';
	$secretkey      = 'lUQE5dQlJn69ime5PtWJ8f8A0oMjmVXZY6wA5tqT';
	$apihostname    = "api-3ce575d8.duosecurity.com";
	if(WHMCS_DuoSecurity::verifyresponse($integrationkey, $secretkey, $appsecretkey, $_POST['sig_response'])) {
		return true;
	}
	return false;
}