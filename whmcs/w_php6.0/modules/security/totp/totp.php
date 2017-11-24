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
class MyOauth {
	private $tokendata = '';
	public function setTokenData($token) {
		$this->tokendata = $token;
	}
	public function getData($username) {
		global $twofa;
		$tokendata = $this->tokendata ? $this->tokendata : $twofa->getUserSetting('tokendata');
		return $tokendata;
	}
	public function putData($username, $data) {
		global $twofa;
		$twofa->saveUserSettings(array(
			'tokendata' => $data
		));
		return true;
	}
	public function getUsers() {
		return false;
	}
}
function totp_config() {
	$licensing   = WHMCS_License::getinstance();
	$licensedata = $licensing->getKeyData('configoptions');
	$totpenabled = array_key_exists('TOTP', $licensedata) ? $licensedata['TOTP'] : 0;
	$configarray = array(
		'FriendlyName' => array(
			'Type' => 'System',
			'Value' => "Time-based HMAC One-Time Password (TOTP)"
		),
		'Description' => array(
			'Type' => 'System',
			'Value' => "TOTP requires that a user enter a 6 digit code that changes every 30 seconds to complete login. This works with mobile apps such as OATH Token and Google Authenticator.<br /><br />For more information about Time Based Tokens, please <a href=\"http://nullrefer.com/?http://go.whmcs.com/114/totp\" target=\"_blank\">click here</a>." . ($totpenabled ? '' : "<br /><br /><strong>Just \$1.50 per month (unlimited users)</strong>")
		),
		'Licensed' => array(
			'Type' => 'System',
			'Value' => $totpenabled ? true : false
		),
		'SubscribeLink' => array(
			'Type' => 'System',
			'Value' => "http://nullrefer.com/?http://go.whmcs.com/114/totp"
		)
	);
	return $configarray;
}
function totp_activate($params) {
	$whmcs = WHMCS_Application::getinstance();
	if($whmcs->get_req_var('showqrimage')) {
		if(!isset($_SESSION['totpqrurl'])) {
			exit();
		}
		include(ROOTDIR . "/modules/security/totp/phpqrcode.php");
		QRcode::png($_SESSION['totpqrurl'], false, 6, 6);
		exit();
	}
	$username  = $params['user_info']['username'];
	$tokendata = isset($params['user_settings']['tokendata']) ? $params['user_settings']['tokendata'] : '';
	totp_loadgaclass();
	$gaotp    = new MyOauth();
	$username = $whmcs->sanitize('a-z', $whmcs->get_config('CompanyName')) . ":" . $username;
	if($whmcs->get_req_var('step') == 'verify') {
		$verifyfail = false;
		if($whmcs->get_req_var('verifykey')) {
			$ans = $gaotp->authenticateUser($username, $whmcs->get_req_var('verifykey'));
			if($ans) {
				$output              = array();
				$output['completed'] = true;
				$output['msg']       = "Key Verified Successfully!";
				$output['settings']  = array(
					'tokendata' => $tokendata
				);
				return $output;
			}
			$verifyfail = true;
		}
		$output = "<h2>" . $whmcs->get_lang('twoipverificationstep') . "</h2><p>" . $whmcs->get_lang('twoipverificationstepmsg') . "</p>";
		if($verifyfail) {
			$output .= "<div class=\"errorbox\"><strong>" . $whmcs->get_lang('twoipverificationerror') . "</strong><br />" . $whmcs->get_lang('twoipcodemissmatch') . "</div>";
		}
		$output .= "<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"totp\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<p align=\"center\"><input type=\"text\" name=\"verifykey\" size=\"10\" maxlength=\"6\" style=\"font-size:18px;\" /></p>
<p align=\"center\"><input type=\"button\" value=\"" . $whmcs->get_lang('confirm') . " &raquo;\" class=\"btn btn-primary large\" onclick=\"dialogSubmit()\" /></p>
</form>";
	} else {
		$key                   = $gaotp->setUser($username, 'TOTP');
		$url                   = $gaotp->createUrl($username);
		$_SESSION['totpqrurl'] = $url;
		$output                = "<h2>" . $whmcs->get_lang('twoiptimebasedpassword') . "</h2>
<p>" . $whmcs->get_lang('twoiptimebasedexplain') . "</p>
<p>" . $whmcs->get_lang('twoipconfigureapp') . "</p>
<ul>
<li>" . $whmcs->get_lang('twoipconfigurestep1') . "</li>
<li>" . $whmcs->get_lang('twoipconfigurestep2') . "\"" . $gaotp->getKey($username) . "\"</li>
</ul>

<div align=\"center\">" . (function_exists('imagecreate') ? "<img src=\"" . $_SERVER['PHP_SELF'] . "?2fasetup=1&module=totp&showqrimage=1\" />" : "<em>" . ${$whmcs}->get_lang('twoipgdmissing') . "</em>") . "</div>

<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"totp\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<p align=\"center\"><input type=\"button\" value=\"" . $whmcs->get_lang('confirm') . " &raquo;\" onclick=\"dialogSubmit()\" class=\"btn btn-primary\" /></p>
</form>

";
	}
	return $output;
}
function totp_challenge($params) {
	$whmcs  = WHMCS_Application::getinstance();
	$output = "<form method=\"post\" action=\"dologin.php\">
    <div align=\"center\">
        <input type=\"text\" name=\"key\" size=\"10\" style=\"font-size:20px;\" maxlength=\"6\" /> <input type=\"submit\" value=\"" . $whmcs->get_lang('loginbutton') . " &raquo;\" class=\"btn button\" />
    </div>
</form>";
	return $output;
}
function totp_get_used_otps() {
	$whmcs    = WHMCS_Application::getinstance();
	$usedotps = $whmcs->get_config('TOTPUsedOTPs');
	$usedotps = $usedotps ? unserialize($usedotps) : array();
	if(!is_array($usedotps)) {
		$usedotps = array();
	}
	return $usedotps;
}
function totp_verify($params) {
	$whmcs     = WHMCS_Application::getinstance();
	$username  = $params['admin_info']['username'];
	$tokendata = $params['admin_settings']['tokendata'];
	$key       = $params['post_vars']['key'];
	totp_loadgaclass();
	$gaotp = new MyOauth();
	$gaotp->setTokenData($tokendata);
	$username = "WHMCS:" . $username;
	$usedotps = totp_get_used_otps();
	$hash     = md5($username . $key);
	if(array_key_exists($hash, $usedotps)) {
		return false;
	}
	$ans = false;
	$ans = $gaotp->authenticateUser($username, $key);
	if($ans) {
		$usedotps[$hash] = time();
		$expiretime      = time() - 5 * 60;
		foreach($usedotps as $k => $time) {
			if($time < $expiretime) {
				unset($usedotps[$k]);
			} else {
				break;
			}
		}
		$whmcs->set_config('TOTPUsedOTPs', serialize($usedotps));
	}
	return $ans;
}
function totp_loadgaclass() {
	if(!class_exists('GoogleAuthenticator')) {
		include(ROOTDIR . "/modules/security/totp/ga4php.php");
		class MyOauth extends GoogleAuthenticator {
			private $tokendata = '';
			public function setTokenData($token) {
				$this->tokendata = $token;
			}
			public function getData($username) {
				global $twofa;
				$tokendata = $this->tokendata ? $this->tokendata : $twofa->getUserSetting('tokendata');
				return $tokendata;
			}
			public function putData($username, $data) {
				global $twofa;
				$twofa->saveUserSettings(array(
					'tokendata' => $data
				));
				return true;
			}
			public function getUsers() {
				return false;
			}
		}
	}
}