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
/**
 * Describes the currently accessing user
 *
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2014
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 */
class WHMCS_Utility_Environment_CurrentUser {
	/**
	 * Returns the IP address for the current visitor
	 *
	 * NOTE: This method will look at the configuration file key
	 * $use_legacy_client_ip_logic to know if pre 5.3.9-release.1 IP logic
	 * should be used.  This is only provided as a safeguard for the initial
	 * release and needs to be removed was all use cases have been confirmed to
	 * work properly.
	 *
	 * @TODO remove usage of configuration file value for 'use_legacy_client_ip_logic'
	 *
	 * @return string Client IP or blank string
	 */
	public static function getIP() {
		$config = WHMCS_Config_Application::factory();
		$useLegacyIpLogic = (!empty($config['use_legacy_client_ip_logic']) ? true : false);
		if($useLegacyIpLogic) {
			return self::getForwardedIpWithoutTrust();
		} else {
			$request = new WHMCS_Http_Request($_SERVER);
			$ip = $request->getClientIp();
			/* Team ECHO : This check will return always bool(false) in PHP 5.2 */
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
				return $ip;
			}
			/* Team ECHO : For the above reason we have to perform each check individually. */
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				return $ip;
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				return $ip;
			}
		}
		return '';
	}
	/**
	 * Get the IP address of the request, possibly from untrusted proxy
	 *
	 * This is 90% of the IP determination logic prior to the introduction of
	 * a proxy trust list (5.3.9-release.1).  In short, if X_FORWARDED_FOR was
	 * provided, then it was used over REMOTE_ADDR.  REMOTE_ADDR is set by the
	 * webserver based on the TCP stack, so it's very trustworthy.  The problem
	 * is if there is a proxy (that you trust/control) setting in front of the
	 * application server, REMOTE_ADDR will be the proxy's IP & the proxy
	 * populates X_FORWARDED_FOR (with the originating IP).  The method assumes
	 * the X_FORWARDED_FOR is not tainted, which is bad.
	 * WHMCS_Http_Request::getClientIp() is much safer because it, in combination
	 * with application settings defined by an admin, will filter out the admin's
	 * known proxies and provide the latest IP in the chain (be it from
	 * REMOTE_ADDR or X_FORWARDED_FOR)
	 *
	 * @return string
	 */
	public static function getForwardedIpWithoutTrust() {
		if(function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if(array_key_exists('X-Forwarded-For', $headers)) {
				$userip = explode(',', $headers['X-Forwarded-For']);
				$ip     = trim($userip[0]);
				if(self::isIpv4AndPublic($ip)) {
					return $ip;
				}
			}
		}
		if((isset($_SERVER['HTTP_CLIENT_IP']) && self::isIpv4AndPublic($_SERVER['HTTP_CLIENT_IP']))) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		$ip_array = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) : array());
		if(count($ip_array)) {
			$ip = trim($ip_array[count($ip_array)-1]);
			if(self::isIpv4AndPublic($ip)) {
				return $ip;
			}
		}
		if((isset($_SERVER['HTTP_X_FORWARDED']) && self::isIpv4AndPublic($_SERVER['HTTP_X_FORWARDED']))) {
			return $_SERVER['HTTP_X_FORWARDED'];
		}
		if((isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::isIpv4AndPublic($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))) {
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		}
		if((isset($_SERVER['HTTP_FORWARDED_FOR']) && self::isIpv4AndPublic($_SERVER['HTTP_FORWARDED_FOR']))) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		}
		if((isset($_SERVER['HTTP_FORWARDED']) && self::isIpv4AndPublic($_SERVER['HTTP_FORWARDED']))) {
			return $_SERVER['HTTP_FORWARDED'];
		}
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			/* Team ECHO : This check will return always bool(false) in PHP 5.2 */
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
				return $ip;
			}
			/* Team ECHO : For the above reason we have to perform each check individually. */
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				return $ip;
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				return $ip;
			}
		}
		return '';
	}
	/**
	 * Returns the IP Hostname for the current visitor
	 *
	 * @return string
	 */
	public static function getIPHost() {
		$usersIP  = self::getip();
		$fullhost = gethostbyaddr($usersIP);
		return ($fullhost ? $fullhost : 'Unable to resolve hostname');
	}
	/**
	 * Validates an ip address
	 *
	 * Returns false for private ips
	 *
	 * @param string $ip
	 *
	 * @return bool
	 */
	public static function isIpv4AndPublic($ip) {
		if(!empty($ip) && ip2long($ip) != 0 - 1 && ip2long($ip) != false) {
			$private_ips = array(
				array('0.0.0.0', '2.255.255.255'),
				array('10.0.0.0', '10.255.255.255'),
				array('127.0.0.0', '127.255.255.255'),
				array('169.254.0.0', '169.254.255.255'),
				array('172.16.0.0', '172.31.255.255'),
				array('192.0.2.0', '192.0.2.255'),
				array('192.168.0.0', '192.168.255.255'),
				array('255.255.255.0', '255.255.255.255')
			);
			foreach($private_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if($min <= ip2long($ip) && ip2long($ip) <= $max) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
}