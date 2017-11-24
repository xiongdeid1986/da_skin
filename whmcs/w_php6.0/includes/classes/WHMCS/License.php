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
if(!(defined('ROOTDIR') || defined('WHMCS') || defined('WHMCSDBCONNECT')))
	exit('Terminating script execution due to security concerns.');
if(!class_exists('WHMCS_License')) {
	/**
	 * WHMCS License Class
	 *
	 * @package    WHMCS
	 * @author     WHMCS Limited <development@whmcs.com>
	 * @copyright  Copyright (c) WHMCS Limited 2005-2013
	 * @license    http://www.whmcs.com/license/ WHMCS Eula
	 * @version    $Id$
	 * @link       http://www.whmcs.com/
	 */
	class WHMCS_License {
		public $secretkey = '';
		public $licensekey = '';
		public $localkey = '';
		public $keydata = array();
		public $salt = '';
		public $date = '';
		public $localkeydecoded = false;
		public $responsedata = '';
		public $forceremote = false;
		public $postmd5hash = '';
		public $releasedate = '20130423';
		public $localkeydays = '10';
		public $allowcheckfaildays = '5';
		public $debuglog = array();
		public $version = '';
		static $instance = null;
		/**
		 * The WHMCS_License singleton
		 *
		 * @var WHMCS_License
		 */
		public function __construct() {
			$this->licensekey = self::get_license_key();
			$this->localkey   = self::get_config('License');
			$this->salt       = sha1('WHMCS' . self::get_config('Version') . 'TFB' . self::get_hash());
			$this->date       = date('Ymd');
			$this->keygen_version_hash();
			$this->decodeLocalOnce();
		}
		/**
		 * Set the WHMCS_License singleton.
		 *
		 * @param WHMCS_License $license
		 * @return WHMCS_License
		 */
		public static function setInstance($license) {
			self::$instance = $license;
			return $license;
		}
		/**
		 * Remove the WHMCS_License singleton.
		 */
		public static function destroyInstance() {
			self::$instance = null;
		}
		/**
		 * Retrieve a WHMCS_License object via singleton.
		 *
		 * @return WHMCS_License
		 */
		public static function getInstance() {
			if(is_null(self::$instance)) {
				self::setInstance(new WHMCS_License);
			}
			return self::$instance;
		}
		public static function get_hash() {
			return $GLOBALS['cc_encryption_hash'];
		}
		public static function get_license_key() {
			return $GLOBALS['license'];
		}
		public static function get_config($k) {
			return $GLOBALS['CONFIG'][$k];
		}
		public static function set_config($k, $v) {
			if(!isset($GLOBALS['CONFIG'][$k])) {
				insert_query('tblconfiguration', array('setting' => $k, 'value' => trim($v)));
			} else {
				update_query('tblconfiguration', array('value' => trim($v)), array('setting' => $k));
			}
			$GLOBALS['CONFIG'][$k] = $v;
		}
		public static function init() {
			return self::getInstance();
		}
		public function keygen_version_hash() {
			switch(substr(self::get_config('Version'), 0, 3)) {
				case '4.3':
					$this->secretkey = $this->version = 'uJ74FkT6aCVhTnf92kErFamdKxwnTqIdC54';
					break;
				case '4.4':
					$this->secretkey = 'cAe8d81S16daY90b5bF2905bbW057ea0cN30a0';
					$this->version   = '9b4bffa460105781f82b1d463bde8200';
					break;
				case '4.5':
					$this->secretkey = '5c9db67bb5dad8f7962cb61cd8ecf0ae1734ed9c';
					if(version_compare(self::get_config('Version'), '4.5.2') >= 0)
						$this->version = 'b6d966d0c7c5777237e6b9f8871dbbf7890e4cf8';
					else
						$this->version = '0466cbb5679eb882c93bf46f1a1e79e1';
					break;
				case '5.0':
				case '5.1':
					$this->secretkey = 'feF245f7D1ddA4ba4cC503Ff60419fc0Cc38315G';
					if(version_compare(self::get_config('Version'), '5.1.2') >= 0)
						$this->version = '8cf4ae2f054b9bb3ee4c327bdfef14bd0124afb8';
					else
						$this->version = '7baB82d4z1aE90bT496SBecEC0cD7bbK';
					break;
				case '5.2':
					$this->secretkey = $this->version = '9eb7da5f081b3fc7ae1e460afdcb89ea8239eca1';
					break;
				case '5.3':
					if(version_compare(self::get_config('Version'), '5.3.2') >= 0)
						$this->secretkey = $this->version = '7a1bbff560de83ab800c4d1d2f215b91006be8e6';
					else
						$this->secretkey = $this->version = '51eff84b535acaed345c7c228dfbc1bedbdf649c';
					break;
			}
			/*
			if(isset($_GET['revokelocal'])) {
			$this->revokeLocal();
			}
			if(isset($_GET['forceremote'])) {
			$this->decodeLocalOnce();
			$this->forceRemoteCheck();
			$_GET['licensedebug'] = true;
			}
			if(isset($_GET['licensedebug'])) {
			$this->decodeLocalOnce();
			echo "<b>General license information:</b><pre>Status: ".$this->getKeyData('status')."\nLicense Key: ".$this->getLicenseKey()."\nVersion: ".self::get_config('Version')."\nSystem URLs: ".self::get_config('SystemURL')." | ".self::get_config('SystemSSLURL')."\nProduct ID: ".$this->getKeyData('productid')."\nReg Date: ".$this->getKeyData('regdate')."\nValid Domain: ".implode(', ', $this->getKeyData('validdomains'))."\nValid IP: ".implode(', ', $this->getKeyData('validips'))."\nChkd: ".$this->getKeyData('checkdate')."\n</pre>";
			echo "<b>License manager operation log:</b><pre>".print_r($this->debuglog, true)."</pre>";
			echo "<b>Detailed license information:</b><pre>".print_r($this->keydata, true)."</pre>";
			exit();
			}
			*/
		}
		public function getHosts() {
			$hosts = gethostbynamel('licensing28.whmcs.com');
			if($hosts === false) $hosts = array('127.0.0.1');
			return $hosts;
		}
		public function getLicenseKey() {
			return $this->licensekey;
		}
		public function getHostIP() {
			return (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : (isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : getenv('LOCAL_ADDR')));
		}
		public function getHostDomain() {
			return (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
		}
		public function getHostDir() {
			return ROOTDIR;
		}
		public function getSalt() {
			return $this->salt;
		}
		public function getDate() {
			return $this->date;
		}
		public function checkLocalKeyExpiry() {
			$originalcheckdate = $this->getKeyData('checkdate');
			$localexpirymax    = date('Ymd', mktime(0, 0, 0, date('m'), date('d') - $this->localkeydays, date('Y')));
			if($originalcheckdate < $localexpirymax) {
				return false;
			}
			$localmax = date('Ymd', mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')));
			if($localmax < $originalcheckdate) {
				return false;
			}
			return true;
		}
		public function remoteCheck($forceRemote = false) {
			$localkeyvalid = $this->decodeLocalOnce();
			$this->debug('Local Key Valid: ' . $localkeyvalid);
			if($localkeyvalid) {
				$localkeyvalid = $this->checkLocalKeyExpiry();
				$this->debug('Local Key Expiry: ' . $localkeyvalid);
				if($localkeyvalid) {
					$localkeyvalid = $this->validateLocalKey();
					$this->debug('Local Key Validation: ' . $localkeyvalid);
				}
			}
			if(!$localkeyvalid || $this->forceremote || $forceRemote) {
				$postfields                = array();
				$postfields['licensekey']  = $this->getLicenseKey();
				$postfields['domain']      = $this->getHostDomain();
				$postfields['ip']          = $this->getHostIP();
				$postfields['dir']         = $this->getHostDir();
				$postfields['check_token'] = sha1(time() . $this->getLicenseKey() . mt_rand(100000000, 999999999));
				$postfields['version']     = self::get_config('Version');
				$postfields['phpversion']  = PHP_VERSION;
				$this->debug('Performing Remote Check: ' . print_r($postfields, true));
				$data = $this->callHome($postfields);
				if(!$data) {
					$this->debug('Remote check not returned ok');
					if($this->getLocalMaxExpiryDate()<$this->getKeyData('checkdate')) {
						$this->setKeyData(array('status' => 'Active'));
					} else {
						$this->setInvalid('noconnection');
					}
				} else {
					$results = $this->processResponse($data);
					if($this->posthash != sha1('WHMCSV5.2SYH' . $postfields['check_token'])) {
						$this->setInvalid();
						return false;
					}
					$this->setKeyData($results);
					$this->updateLocalKey();
				}
			}
			$this->debug('Remote Check Done');
			return true;
		}
		public function getLocalMaxExpiryDate() {
			return date('Ymd', mktime(0, 0, 0, date('m'), date('d') - ($this->localkeydays + $this->allowcheckfaildays), date('Y')));
		}
		public function buildQuery($postfields) {
			$query_string = '';
			foreach($postfields as $k => $v) {
				$query_string .= $k . '=' . urlencode($v) . '&';
			}
			return $query_string;
		}
		public function callHome($postfields) {
			$query_string = $this->buildQuery($postfields);
			$res          = $this->callHomeLoop($query_string, 5);
			if($res) {
				return $res;
			}
			return $this->callHomeLoop($query_string, 30);
		}
		public function callHomeLoop($query_string, $timeout = 5) {
			$hostips = $this->getHosts();
			foreach($hostips as $hostip) {
				$responsecode = $this->makeCall($hostip, $query_string, $timeout);
				if($responsecode == 200) {
					return $this->responsedata;
				}
			}
			return false;
		}
		public function makeCall($ip, $query_string, $timeout = 5) {
			/*
			$url = 'https://'.$ip.'/license/verify53.php';
			$this->debug('Request URL '.$url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$this->responsedata = curl_exec($ch);
			$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->debug('Response Code: '.$responsecode.' Data: '.$this->responsedata);
			if(curl_error($ch)) {
			$this->debug('Curl Error: '.curl_error($ch).' - '.curl_errno($ch));
			}
			curl_close($ch);
			return $responsecode;
			*/
			parse_str($query_string, $postfields);
			$year = date('Y') + 1;
			$data = "<key>{$postfields['licensekey']}</key>\n";
			$data .= "<status>Active</status>\n";
			$data .= "<registeredname>" . self::get_config('CompanyName') . "</registeredname>\n";
			$data .= "<productid>5</productid>\n";
			$data .= "<productname>Owned License No Branding</productname>\n";
			$data .= "<requiresupdates>0</requiresupdates>\n";
			$data .= "<supportaccess>1</supportaccess>\n";
			$data .= "<reseller></reseller>\n";
			$data .= "<regdate>2009-12-31</regdate>\n";
			$data .= "<nextduedate>0000-00-00</nextduedate>\n";
			$data .= "<billingcycle>One Time</billingcycle>\n";
			$data .= "<validdomains>{$postfields['domain']}|www.{$postfields['domain']}</validdomains>\n";
			$data .= "<validips>{$postfields['ip']}</validips>\n";
			$data .= "<validdirs>{$postfields['dir']}</validdirs>\n";
			$data .= "<configoptions>Branding Removal=Active</configoptions>\n";
			$data .= "<customfields>Reseller=LicensePal</customfields>\n";
			$data .= "<addons>"
				. "name=Android App;nextduedate={$year}-12-31;status=Active|"
				. "name=Branding Removal;status=Active|"
				. "name=Configurable Package Addon;nextduedate={$year}-12-31;status=Active|"
				. "name=iPhone App;nextduedate={$year}-12-31;status=Active|"
				. "name=Licensing Addon;nextduedate={$year}-12-31;status=Active|"
				. "name=Live Chat;nextduedate={$year}-12-31;status=Active|"
				. "name=Mobile Edition;nextduedate={$year}-12-31;status=Active|"
				. "name=Project Management Addon;nextduedate={$year}-12-31;status=Active|"
				. "name=Support and Updates;nextduedate={$year}-12-31;status=Active"
				. "</addons>\n";
			$data .= "<hash>" . sha1('WHMCSV5.2SYH' . $postfields['check_token']) . "</hash>";
			$data .= "<latestpublicversion>" . self::get_config('Version') . "</latestpublicversion>";
			preg_match_all("/<(.*?)>([^<]+)<\\/\\1>/i", $data, $matches);
			$results = array();
			foreach($matches[1] as $k=>$v) {
				$results[$v] = $matches[2][$k];
			}
			if($results['nextduedate']=='0000-00-00')
				$results['nextduedate'] = 'Never';
			foreach(array('requiresupdates', 'supportaccess') as $k)
				$results[$k] = (bool) $results[$k];
			foreach(array('validdomains', 'validips', 'validdirs') as $k)
				$results[$k] = explode('|', $results[$k]);
			$configoptions = array();
			$tempresults   = explode('|', $results['configoptions']);
			foreach($tempresults as $tempresult) {
				$values                    = explode('=', $tempresult);
				$configoptions[$values[0]] = $values[1];
			}
			$results['configoptions'] = $configoptions;
			if(version_compare(self::get_config('Version'), '5.2.0')>=0) {
				$addons      = array();
				$tempresults = explode('|', html_entity_decode($results['addons']));
				foreach($tempresults as $tempresult) {
					$tempresults2 = explode(';', $tempresult);
					$temparr      = array();
					foreach($tempresults2 as $tempresult) {
						$tempresults3              = explode('=', $tempresult);
						$temparr[$tempresults3[0]] = $tempresults3[1];
					}
					$addons[] = $temparr;
				}
				$results['addons'] = $addons;
			}
			$this->responsedata = strrev(base64_encode(serialize($results)));
			return 200;
		}
		public function processResponse($data) {
			$data           = base64_decode(strrev($data));
			$results        = unserialize($data);
			$this->posthash = $results['hash'];
			unset($results['hash']);
			$results['checkdate'] = $this->getDate();
			return $results;
		}
		public function updateLocalKey() {
			$data_encoded = base64_encode(serialize($this->keydata));
			$data_encoded = sha1($this->getDate() . $this->getSalt()) . $data_encoded;
			$data_encoded = strrev($data_encoded);
			$splpt        = strlen($data_encoded) / 2;
			$data_encoded = substr($data_encoded, $splpt) . substr($data_encoded, 0, $splpt);
			$data_encoded = sha1($data_encoded . $this->getSalt()) . $data_encoded . sha1($data_encoded . $this->getSalt() . time());
			$data_encoded = base64_encode($data_encoded);
			$data_encoded = wordwrap($data_encoded, 80, "\n", true);
			self::set_config('License', $data_encoded);
			$this->debug('Updated Local Key');
		}
		public function forceRemoteCheck() {
			$this->forceremote = true;
			$this->remoteCheck(true);
		}
		public function setInvalid($reason = 'Invalid') {
			$this->keydata = array('status' => $reason);
		}
		public function decodeLocal() {
			$this->debug('Decoding local key');
			$localkey = $this->localkey;
			if(!$localkey) {
				return false;
			}
			$localkey  = str_replace("\n", '', $localkey);
			$localkey  = base64_decode($localkey);
			$localdata = substr($localkey, 40, 0 - 40);
			$md5hash   = substr($localkey, 0, 40);
			if($md5hash == sha1($localdata . $this->getSalt())) {
				$splpt           = strlen($localdata) / 2;
				$localdata       = substr($localdata, $splpt) . substr($localdata, 0, $splpt);
				$localdata       = strrev($localdata);
				$md5hash         = substr($localdata, 0, 40);
				$localdata       = substr($localdata, 40);
				$localdata       = base64_decode($localdata);
				$localkeyresults = unserialize($localdata);
				if(version_compare(self::get_config('Version'), '5.2.0') < 0) {
					$addons      = array();
					$tempresults = explode('|', html_entity_decode($localkeyresults['addons']));
					foreach($tempresults as $tempresult) {
						$tempresults2 = explode(';', $tempresult);
						$temparr      = array();
						foreach($tempresults2 as $tempresult) {
							$tempresults3              = explode('=', $tempresult);
							$temparr[$tempresults3[0]] = $tempresults3[1];
						}
						$addons[] = $temparr;
					}
					$localkeyresults['addons'] = $addons;
				}
				$originalcheckdate = $localkeyresults['checkdate'];
				if($md5hash == sha1($originalcheckdate . $this->getSalt())) {
					if($localkeyresults['key'] == self::get_license_key()) {
						$this->debug('Local Key Decode Successful');
						$this->setKeyData($localkeyresults);
					} else {
						$this->debug('License Key Invalid');
					}
				} else {
					$this->debug('Local Key MD5 Hash 2 Invalid');
				}
			} else {
				$this->debug('Local Key MD5 Hash Invalid');
			}
			$this->localkeydecoded = true;
			return ($this->getKeyData('status') == 'Active' ? true : false);
		}
		public function decodeLocalOnce() {
			if($this->localkeydecoded) {
				return true;
			}
			return $this->decodeLocal();
		}
		public function isRunningInCLI() {
			return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']));
		}
		public function validateLocalKey() {
			if($this->getKeyData('status') != 'Active') {
				$this->debug('Local Key Status Check Failure');
				return false;
			}
			if($this->isRunningInCLI()) {
				$this->debug('Running in CLI Mode');
			} else {
				$this->debug('Running in Browser Mode');
				if($this->isValidDomain($this->getHostDomain())) {
					$this->debug('Domain Validated Successfully');
				} else {
					$this->debug('Local Key Domain Check Failure');
					return false;
				}
				if($this->isValidIP($this->getHostIP())) {
					$this->debug('IP Validated Successfully');
				} else {
					$this->debug('Local Key IP Check Failure');
					return false;
				}
			}
			if($this->isValidDir($this->getHostDir())) {
				$this->debug('Directory Validated Successfully');
			} else {
				$this->debug('Local Key Directory Check Failure');
				return false;
			}
			return true;
		}
		public function isValidDomain($domain) {
			$validdomains = $this->getArrayKeyData('validdomains');
			return in_array($domain, $validdomains);
		}
		public function isValidIP($ip) {
			$validips = $this->getArrayKeyData('validips');
			if(!$ip) {
				$this->debug('IP Could Not Be Determined - Skipping Local Validation of IP');
				return true;
			} else if(empty($validips)) {
				$this->debug('No Valid IPs returned by license check - Cloud Based License - Skipping Local Validation of IP');
				return true;
			}
			return in_array($ip, $validips);
		}
		public function isValidDir($dir) {
			$validdirs = $this->getArrayKeyData('validdirs');
			return in_array($dir, $validdirs);
		}
		public function revokeLocal() {
			self::set_config('License', '');
		}
		public function getKeyData($var) {
			return (isset($this->keydata[$var]) ? $this->keydata[$var] : '');
		}
		public function setKeyData($data) {
			$this->keydata = $data;
		}
		/**
		 * Retrieve a license element as an array, that would otherwise be a
		 * delimited string
		 *
		 * NOTE: use of this method should be very limited. New license elements
		 * added to the license data should strongly consider not depending on the
		 * use of this function, but instead structure the data and let the
		 * transmission layer do the serialize/unserialize
		 *
		 * @param string $var License data element whose value is a comma delimited string
		 *
		 * @return array
		 * @throws WHMCS_Exception when internal license key data structure is not
		 * as expected
		 */
		public function getArrayKeyData($var) {
			$data = $this->getKeyData($var);
			if(!is_array($data)) {
				$data = explode(',', $data);
				$data = array_map('trim', $data);
			}
			return $data;
		}
		public function getRegisteredName() {
			return $this->getKeyData('registeredname');
		}
		public function getProductName() {
			return $this->getKeyData('productname');
		}
		public function getStatus() {
			return $this->getKeyData('status');
		}
		public function getSupportAccess() {
			return $this->getKeyData('supportaccess');
		}
		public function getReleaseDate() {
			return str_replace('-', '', $this->releasedate);
		}
		/**
		 * Retrieve a list of Addons as known by the license
		 *
		 * @return array
		 */
		public function getLicensedAddons() {
			$licensedAddons = $this->getKeyData('addons');
			if(!is_array($licensedAddons)) {
				$licensedAddons = array();
			}
			return $licensedAddons;
		}
		public function getActiveAddons() {
			$licensedAddons = $this->getLicensedAddons();
			$activeAddons   = array();
			foreach($licensedAddons as $addon) {
				if($addon['status'] == 'Active') {
					$activeAddons[] = $addon['name'];
				}
			}
			return $activeAddons;
		}
		public function isActiveAddon($addon) {
			return (in_array($addon, $this->getActiveAddons()) ? true : false);
		}
		public function getExpiryDate($showday = false) {
			$expiry = $this->getKeyData('nextduedate');
			if(!$expiry || $expiry == 'Never') {
				$expiry = 'Never';
			} else {
				if($showday) {
					$expiry = date('l, jS F Y', strtotime($expiry));
				} else {
					$expiry = date('jS F Y', strtotime($expiry));
				}
			}
			return $expiry;
		}
		/**
		 * Get a version object that will represent the latest publicly available version
		 *
		 * If the licensing API does not return a valid version number for
		 * whatever reason, it assumes latest version = installed version
		 * to allow application to continue un-affected
		 *
		 * @return WHMCS_Version_SemanticVersion
		 */
		public function getLatestPublicVersion() {
			if(version_compare(self::get_config('Version'), '5.3.8') >= 0 && class_exists('WHMCS_Application') && class_exists('WHMCS_Version_SemanticVersion')) {
				try {
					$latestVersion = new WHMCS_Version_SemanticVersion($this->getKeyData('latestpublicversion'));
				}
				catch(WHMCS_Exception_Version_BadVersionNumber $e) {
					$whmcs         = WHMCS_Application::getinstance();
					$latestVersion = $whmcs->getVersion();
				}
				return $latestVersion;
			} else {
				return $this->getKeyData('latestpublicversion');
			}
		}
		/**
		 * Get a version object that will represent the latest available pre-release version
		 *
		 * If the licensing API does not return a valid version number for
		 * whatever reason, it assumes latest version = installed version
		 * to allow application to continue un-affected
		 *
		 * @return WHMCS_Version_SemanticVersion
		 */
		public function getLatestPreReleaseVersion() {
			if(version_compare(self::get_config('Version'), '5.3.8') >= 0 && class_exists('WHMCS_Application') && class_exists('WHMCS_Version_SemanticVersion')) {
				try {
					$latestVersion = new WHMCS_Version_SemanticVersion($this->getKeyData('latestprereleaseversion'));
				}
				catch(WHMCS_Exception_Version_BadVersionNumber $e) {
					$whmcs         = WHMCS_Application::getinstance();
					$latestVersion = $whmcs->getVersion();
				}
				return $latestVersion;
			} else {
				return $this->getKeyData('latestprereleaseversion');
			}
		}
		/**
		 * Get a version object that will represent the latest appropriate version based on current installation
		 *
		 * If running a pre-release (beta/rc) it returns the latest pre-release version
		 * Otherwise it returns the latest publicly available version
		 *
		 * @return WHMCS_Version_SemanticVersion
		 */
		public function getLatestVersion() {
			if(version_compare(self::get_config('Version'), '5.3.8') >= 0 && class_exists('WHMCS_Application') && class_exists('WHMCS_Version_SemanticVersion')) {
				$whmcs            = WHMCS_Application::getinstance();
				$installedVersion = $whmcs->getVersion();
				if(in_array($installedVersion->getPreReleaseIdentifier(), array(
					'beta',
					'rc'
				))) {
					$latestVersion = $this->getLatestPreReleaseVersion();
				} else {
					$latestVersion = $this->getLatestPublicVersion();
				}
				return $latestVersion;
			} else {
				return $this->getLatestPublicVersion();
			}
		}
		/**
		 * Determines if an update is available for the currently installed files
		 *
		 * @throws WHMCS_Exception_Version_BadVersionNumber If version number invalid
		 *
		 * @return bool
		 */
		public function isUpdateAvailable() {
			if(version_compare(self::get_config('Version'), '5.3.8') >= 0 && class_exists('WHMCS_Application') && class_exists('WHMCS_Version_SemanticVersion')) {
				$whmcs            = WHMCS_Application::getinstance();
				$installedVersion = $whmcs->getVersion();
				$latestVersion    = $this->getLatestVersion();
				return WHMCS_Version_SemanticVersion::compare($latestVersion, $installedVersion, ">");
			} else {
				$installedversion = self::get_config('Version');
				$latestversion    = $this->getLatestVersion();
				return version_compare($latestversion, $installedversion, '>');
			}
		}
		public function getRequiresUpdates() {
			return ($this->getKeyData('requiresupdates') ? true : false);
		}
		public function checkOwnedUpdates() {
			if(!$this->getRequiresUpdates()) {
				return true;
			}
			$licensedAddons = $this->getLicensedAddons();
			foreach($licensedAddons as $addon) {
				if($addon['name'] == 'Support and Updates' && str_replace('-', '', $this->getReleaseDate()) <= str_replace('-', '', $addon['nextduedate'])) {
					return true;
				}
			}
			return false;
		}
		public function getBrandingRemoval() {
			if(in_array($this->getProductName(), array(
				'Owned License No Branding',
				'Monthly Lease No Branding'
			))) {
				return true;
			}
			$licensedAddons = $this->getLicensedAddons();
			foreach($licensedAddons as $addon) {
				if($addon['name'] == 'Branding Removal' && $addon['status'] == 'Active') {
					return true;
				}
			}
			return false;
		}
		public function getVersionHash() {
			return $this->version;
		}
		public function debug($msg) {
			$this->debuglog[] = $msg;
		}
		/**
		 * Retrieve all errors
		 *
		 * @return array
		 */
		public function getDebugLog() {
			return $this->debuglog;
		}
		/**
		 * Get if client limits should be enforced from the license response.
		 *
		 * @return bool
		 */
		public function isClientLimitsEnabled() {
			return (string) $this->getKeyData('ClientLimitsEnabled');
		}
		/**
		 * Get the client limit as defined by the license.
		 *
		 * @return int
		 */
		public function getClientLimit() {
			$clientLimit = $this->getKeyData('ClientLimit');
			if($clientLimit == '') {
				return 0 - 1;
			}
			if(!is_numeric($clientLimit)) {
				$this->debug('Invalid client limit value in license');
				return 0;
			}
			return (int) $clientLimit;
		}
		/**
		 * Format the client limit for display in a human friendly way.
		 *
		 *  Expect a formatted number or the text 'None' for 0.
		 *
		 * NOTE: If an admin instance is not provided or the key has no translation,
		 * an English value would be returned.
		 *
		 * @param WHMCS_Admin $admin Admin instance for contextual language.
		 *
		 * @return string
		 */
		public function getTextClientLimit($admin = null) {
			$clientLimit = $this->getClientLimit();
			$result      = 'Unlimited';
			if(0 < $clientLimit) {
				$result = number_format($clientLimit, 0, '', ',');
			} else {
				if($admin && ($text = $admin->lang('global', 'unlimited'))) {
					$result = $text;
				}
			}
			return $result;
		}
		/**
		 * Get the number of active clients in the installation.
		 *
		 * @return int
		 */
		public function getNumberOfActiveClients() {
			return (int) get_query_val('tblclients', "count(id)", "status='Active'");
		}
		/**
		 * Format the number of active clients for display in a human friendly way.
		 *
		 * Expect a formatted number or the text 'None' for 0.
		 *
		 * NOTE: If an admin instance is not provided or the key has no translation,
		 * an English value would be returned.
		 *
		 * @param WHMCS_Admin $admin Admin instance for contextual language.
		 *
		 * @return string
		 */
		public function getTextNumberOfActiveClients($admin = null) {
			$clientLimit = $this->getNumberOfActiveClients();
			$result      = 'None';
			if(0 < $clientLimit) {
				$result = number_format($clientLimit, 0, '', ',');
			} else {
				if($admin && ($text = $admin->lang('global', 'none'))) {
					$result = $text;
				}
			}
			return $result;
		}
		/**
		 * Get the first client ID that is outside the client limit
		 *
		 * Given that client limits are meant to be enforced for the active clients
		 * in ascending order, this routine determines the first client who is
		 * outside the pool of active/inactive clients that the admin is permitted
		 * to manage.  i.e., callers should deny management rights of this id or any
		 * id higher than it.
		 *
		 * @return int
		 */
		public function getClientBoundaryId() {
			return (int) get_query_val('tblclients', 'id', "status='Active'", 'id', 'ASC', (int) $this->getClientLimit() . ',1');
		}
		/**
		 * Determine if installation's active client count is 'close' or at client limit
		 *
		 * If true, the caller is expected to show an appropriate warning.
		 *
		 * 'Close' is within 10% for a client boundary of 250; for boundaries above
		 * 250, the 'close' margin is only 5%.
		 *
		 * If there are absolutely no clients active, one can never by near or at
		 * the limit. Likewise, if by chance there's an evaluated limit of 0 from
		 * the license key data, then one can never by near or at the limit. This
		 * logic might need refinement if every there was such a thing as a 0 client
		 * seat limit.
		 *
		 * @return bool
		 */
		public function isNearClientLimit() {
			$clientLimit = $this->getClientLimit();
			$numClients  = $this->getNumberOfActiveClients();
			if($numClients < 1 || $clientLimit < 1) {
				return false;
			}
			$percentageBound = 250 < $clientLimit ? 0.05 : 0.1;
			return $clientLimit * (1 - $percentageBound) <= $numClients;
		}
		/**
		 * Public RSA key for asymmetric encryption
		 *
		 * @return string
		 */
		public function getMemberPublicKey() {
			return "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7OMhxWvu3FOqMblJGXjh
vZQLhQa2wRQoetxAM7j/c+SzFVHmLteAZrn06FeoU1RhjQz9TE0kD6BzoBBuE1bm
JkybOuhVJGVlI8QqLnl2F/jDFP3xshm2brRUt9vNBWXhGDRvOLOgmxaFtVjCiNAT
9n4dtG+344xN7w568Rw3hnnGApypGFtypaKHSeNV6waeFgHeePXSPFMUpe9evZJa
pyc9ENEWvi6nK9hWm1uZ+CfoeRjIKqW2QlgazGDqQtQev05LbDihK0Nc8LBqmVQS
NB/N2CueyYKrzVUmNqbrkJaBVm6N3EnSNBOR7WXOPf1VOjGDu79kYrbhT1MUlKpp
LQIDAQAB
-----END PUBLIC KEY-----";
		}
		/**
		 * Encrypt data for WHMCS Member Area and License system
		 *
		 * The return value will be blank if anything goes wrong, otherwise it is a
		 * base64 encoded value.
		 *
		 * NOTE: Crypt_RSA traditionally will emit warnings; the are not suppressed
		 * here.
		 *
		 * @param array $data Key/value pairs to bundle into the encrypted string
		 * @param string $publicKey RSA public key to use for the asymmetric encryption
		 *
		 * @return string
		 */
		public function encryptMemberData($data = array(), $publicKey = '') {
			if(!$publicKey) {
				$publicKey = $this->getMemberPublicKey();
			}
			$publicKey  = str_replace(array(
				"\n",
				"\r",
				' '
			), array(
				'',
				'',
				''
			), $publicKey);
			$cipherText = '';
			if(is_array($data)) {
				try {
					$rsa = new Crypt_RSA();
					$rsa->loadKey($publicKey);
					$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
					$cipherText = $rsa->encrypt(json_encode($data));
					if(!$cipherText) {
						throw new WHMCS_Exception('Could not perform rsa encryption');
					}
					$cipherText = base64_encode($cipherText);
				}
				catch(Exception $e) {
					$this->debug('Failed to encrypt member data');
				}
			}
			return $cipherText;
		}
	}
}
if(!class_exists('Licensing')) {
	final class Licensing extends WHMCS_License {}
	final class WHMCSLicense193 extends WHMCS_License {}
	final class WHMCSLicense581 extends WHMCS_License {}
	final class WHMCSLicense827 extends WHMCS_License {}
}