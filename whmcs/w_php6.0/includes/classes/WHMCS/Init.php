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
 * WHMCS Core Initialisation Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Init {
	protected $input = array();
	protected $last_input = null;
	protected $config = array();
	protected $clean_variables = array('int' => array('id', 'userid', 'kbcid', 'invoiceid', 'idkb', 'currency'), 'a-z' => array('systpl', 'carttpl', 'language'));
	protected $license = '';
	protected $db_host = '';
	protected $db_username = '';
	protected $db_password = '';
	protected $db_name = '';
	protected $db_sqlcharset = '';
	protected $cc_hash = '';
	protected $templates_compiledir = '';
	protected $customadminpath = '';
	public $remote_ip = '';
	protected $clientlang = '';
	protected $protected_variables = array('whmcs', 'smtp_debug', 'attachments_dir', 'downloads_dir', 'customadminpath', 'mysql_charset', 'overidephptimelimit', 'orderform', 'smartyvalues', 'usingsupportmodule', 'copyrighttext', 'adminorder', 'revokelocallicense', 'allow_idn_domains', 'templatefile', '_LANG', '_ADMINLANG', 'display_errors', 'debug_output', 'mysql_errors', 'moduleparams', 'errormessage');
	/**
	 * Protected variables ignored from user input
	 *
	 * @var array
	 */
	public function __construct() {
	}
	/**
	 * Initialisation of class.
	 *
	 * @deprecated
	 * @see WHMCS_Application::__construct()
	 * @return WHMCS_Init
	 */
	public function init() {
		return $this;
	}
	public function load_function($name) {
		$name  = $this->sanitize('a-z', $name);
		$path  = ROOTDIR . '/includes/' . $name . 'functions.php';
		$path2 = ROOTDIR . '/includes/' . $name . '.php';
		if(file_exists($path)) {
			include_once($path);
		} elseif(file_exists($path2)) {
			include_once($path2);
		}
	}
	/**
	 * Recursively sanitize an array of user input
	 *
	 * @param array $arr
	 *
	 * @throw WHMCS_Exception if there's an obviously nefarious input element
	 *
	 * @return array
	 */
	public function sanitize_input_vars($arr) {
		$cleandata = array();
		if(is_array($arr)) {
			if(isset($arr['sqltype'])) {
				throw new WHMCS_Exception('Invalid request input.');
			}
			foreach($arr as $key => $val) {
				if(ctype_alnum(str_replace(array('_', '-', '.', ' '), '', $key))) {
					if(is_array($val)) {
						$cleandata[$key] = $this->sanitize_input_vars($val);
					} else {
						$val             = str_replace(chr(0), '', $val);
						$cleandata[$key] = WHMCS_Input_Sanitize::encode($val);
						if(@get_magic_quotes_gpc()) {
							$cleandata[$key] = stripslashes($cleandata[$key]);
						}
					}
				}
			}
		} else {
			$arr       = str_replace(chr(0), '', $arr);
			$cleandata = WHMCS_Input_Sanitize::encode($arr);
			if(@get_magic_quotes_gpc()) {
				$cleandata = stripslashes($cleandata);
			}
		}
		return $cleandata;
	}
	/**
	 * The two functions below are used solely as a temporary workaround for local API compatability with $whmcs->get_req_var()
	 */
	public function replace_input($array) {
		$this->last_input = $this->input;
		$this->input      = $array;
		return true;
	}
	public function reset_input() {
		if(is_array($this->last_input)) {
			$this->input = $this->last_input;
			return true;
		}
		return false;
	}
	public function get_req_var($k, $k2 = '') {
		if($k2) {
			return isset($this->input[$k][$k2]) ? $this->input[$k][$k2] : '';
		}
		return isset($this->input[$k]) ? $this->input[$k] : '';
	}
	public function get_req_var_if($e, $key, $fallbackarray) {
		if($e) {
			$var = $this->get_req_var($key);
		} else {
			$var = array_key_exists($key, $fallbackarray) ? $fallbackarray[$key] : '';
		}
		return $var;
	}
	protected function load_input() {
		foreach($_COOKIE as $k => $v) {
			unset($_REQUEST[$k]);
		}
		foreach($_REQUEST as $k => $v) {
			$this->input[$k] = $v;
		}
	}
	protected function clean_input() {
		foreach($this->clean_variables as $type => $vars) {
			foreach($vars as $var) {
				if(isset($this->input[$var])) {
					$this->input[$var] = $this->sanitize($type, $this->input[$var]);
				}
			}
		}
		foreach($this->protected_variables as $var) {
			if(isset($this->input[$var])) {
				unset($this->input[$var]);
			}
			global ${$var};
			${$var} = '';
		}
	}
	public function sanitize($type, $var) {
		if($type == 'int') {
			$var = (int) $var;
		} else {
			if($type == 'a-z') {
				$var = preg_replace("/[^0-9a-z-]/i", '', $var);
			} else {
				$var = preg_replace("/[^" . $type . "]/i", '', $var);
			}
		}
		return $var;
	}
	protected function load_config_file($configFilePath = '/configuration.php') {
		global $license;
		global $cc_encryption_hash;
		global $templates_compiledir;
		global $attachments_dir;
		global $downloads_dir;
		global $customadminpath;
		global $disable_iconv;
		global $api_access_key;
		global $disable_admin_ticket_page_counts;
		global $disable_clients_list_services_summary;
		global $disable_auto_ticket_refresh;
		global $pleskpacketversion;
		global $smtp_debug;
		$license = $db_host = $db_name = $db_username = $db_password = $mysql_charset = $display_errors = $templates_compiledir = $attachments_dir = $downloads_dir = $customadminpath = $disable_iconv = $overidephptimelimit = $api_access_key = $disable_admin_ticket_page_counts = $disable_clients_list_services_summary = $disable_auto_ticket_refresh = $pleskpacketversion = $smtp_debug = '';
		if(file_exists(ROOTDIR . $configFilePath)) {
			ob_start();
			require(ROOTDIR . $configFilePath);
			ob_end_clean();
			if(!$db_name || !$license) {
				return false;
			}
			$this->license     = $license;
			$this->db_host     = $db_host;
			$this->db_username = $db_username;
			$this->db_password = $db_password;
			$this->db_name     = $db_name;
			$this->cc_hash     = $cc_encryption_hash;
			if($mysql_charset) {
				$this->db_sqlcharset = $mysql_charset;
			}
			if($display_errors) {
				$this->display_errors($display_errors);
			}
			if(!$templates_compiledir || $templates_compiledir == 'templates_c/') {
				$templates_compiledir = ROOTDIR . '/templates_c/';
			}
			if(!$attachments_dir) {
				$attachments_dir = ROOTDIR . '/attachments/';
			}
			if(!$downloads_dir) {
				$downloads_dir = ROOTDIR . '/downloads/';
			}
			if(!$customadminpath) {
				$customadminpath = 'admin';
			}
			if(!$overidephptimelimit) {
				$overidephptimelimit = 300;
			}
			@set_time_limit($overidephptimelimit);
			$this->templates_compiledir = $templates_compiledir;
			$this->customadminpath      = $customadminpath;
			return true;
		}
		return false;
	}
	public function get_license_key() {
		return $this->license;
	}
	/**
	 * Returns the IP address for the current visitor
	 *
	 * @return string
	 */
	public function get_user_ip() {
		return WHMCS_Utility_Environment_CurrentUser::getIP();
	}
	protected function load_config_vars() {
		$CONFIG = array();
		$result = select_query('tblconfiguration', '', '');
		while($data = @mysql_fetch_array($result)) {
			$setting          = $data['setting'];
			$value            = $data['value'];
			$CONFIG[$setting] = $value;
		}
		if(isset($CONFIG['DisplayErrors']) && $CONFIG['DisplayErrors']) {
			$this->display_errors($CONFIG['DisplayErrors']);
		}
		header("Content-Type: text/html; charset=" . $CONFIG['Charset']);
		foreach(array(
			'SystemURL',
			'SystemSSLURL',
			'Domain'
		) as $v) {
			$CONFIG[$v] = substr($CONFIG[$v], 0 - 1, 1) == '/' ? substr($CONFIG[$v], 0, 0 - 1) : $CONFIG[$v];
		}
		if($CONFIG['SystemURL'] == $CONFIG['SystemSSLURL'] || substr($CONFIG['SystemSSLURL'], 0, 5) != 'https') {
			$CONFIG['SystemSSLURL'] = '';
		}
		$this->config     = $CONFIG;
		$this->clientlang = $this->validateLanguage($CONFIG['Language']);
		return $CONFIG;
	}
	/**
	 * Set a configuration value.
	 *
	 * @param string $seting
	 * @param string $value
	 * @param resource $resource
	 */
	public function set_config($setting, $value, $resource = null) {
		global $CONFIG;
		if(!isset($this->config[$setting])) {
			insert_query('tblconfiguration', array(
				'setting' => $setting,
				'value' => trim($value)
			), $resource);
		} else {
			update_query('tblconfiguration', array(
				'value' => trim($value)
			), array(
				'setting' => $setting
			), $resource);
		}
		$CONFIG[$setting] = $this->config[$setting] = $value;
	}
	public function get_config($k) {
		return isset($this->config[$k]) ? $this->config[$k] : '';
	}
	public function get_template_compiledir_name() {
		return $this->templates_compiledir;
	}
	public function check_template_cache_writeable() {
		$dir = $this->get_template_compiledir_name();
		if(!is_writeable($dir)) {
			return false;
		}
		return true;
	}
	public function get_admin_folder_name() {
		if(isValidforPath($this->customadminpath)) {
			return $this->customadminpath;
		}
		return 'admin';
	}
	public function get_filename() {
		$filename = $_SERVER['PHP_SELF'];
		$filename = substr($filename, strrpos($filename, '/'));
		$filename = str_replace(array(
			'/',
			'.php'
		), '', $filename);
		return $filename;
	}
	public function get_hash() {
		return $this->cc_hash;
	}
	/**
	 * Control error displaying
	 * NOTE: passing a numeric valid will allow for specific error reporting.
	 * This is the recommended implementation.  The default value of true is
	 * provided (which will equal E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING
	 * for legacy behavior)
	 *
	 * @param bool|int $requestedErrorLevel if equivalent to true enable display errors otherwise disable
	 * @return $this
	 */
	protected function display_errors($requestedErrorLevel = true) {
		if(!defined('E_DEPRECATED')) {
			/* Team ECHO : For PHP 5.2 compatibility. */
			define('E_DEPRECATED', 0);
		}
		if(!$requestedErrorLevel) {
			return $this->disableErrorDisplay();
		}
		if($requestedErrorLevel === 1 || $requestedErrorLevel === true) {
			$level = E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING;
		} else {
			if(!is_numeric($requestedErrorLevel)) {
				if($requestedErrorLevel == 'on' || $requestedErrorLevel == 'true' || $requestedErrorLevel === '1') {
					$level = E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING;
				} else {
					return $this->disableErrorDisplay();
				}
			} else {
				$level = $requestedErrorLevel;
			}
		}
		return $this->enableErrorDisplay($level);
	}
	/**
	 * Enable all the settings required to display errors
	 *
	 * @param integer $requestedErrorLevel error reporting bitmask
	 *
	 * @return $this
	 */
	public function enableErrorDisplay($requestedErrorLevel = E_ALL) {
		WHMCS_Terminus::getinstance()->setErrorReportingLevel($requestedErrorLevel)->enableIniDisplayErrors();
		return $this;
	}
	/**
	 * Disable all report reporting and display
	 *
	 * @return $this
	 */
	public function disableErrorDisplay() {
		WHMCS_Terminus::getinstance()->setErrorReportingLevel(0)->disableIniDisplayErrors();
		return $this;
	}
	protected function validate_templates() {
		global $CONFIG;
		$systpl = $this->get_config('Template');
		if(isset($_SESSION['Template'])) {
			$systpl = $_SESSION['Template'];
		}
		$systpl = $this->sanitize('a-z', $systpl);
		if($systpl == '' || !is_dir(ROOTDIR . '/templates/' . $systpl . '/')) {
			$systpl = 'default';
		}
		$this->config['Template'] = $systpl;
		$CONFIG['Template']       = $this->config['Template'];
		$carttpl                  = $this->get_config('OrderFormTemplate');
		if(isset($_SESSION['OrderFormTemplate'])) {
			$carttpl = $_SESSION['OrderFormTemplate'];
		}
		$carttpl = $this->sanitize('a-z', $carttpl);
		if($carttpl == '' || !is_dir(ROOTDIR . '/templates/orderforms/' . $carttpl . '/')) {
			$carttpl = 'modern';
		}
		$this->config['OrderFormTemplate'] = $carttpl;
		$CONFIG['OrderFormTemplate']       = $this->config['OrderFormTemplate'];
	}
	public function getValidLanguages($admin = '') {
		static $clientLanguages;
		static $adminLanguages;
		$whmcsAppConfig = WHMCS_Application::getinstance()->getApplicationConfig();
		if($admin) {
			if(0 < count($adminLanguages)) {
				return $adminLanguages;
			}
		} else {
			if(0 < count($clientLanguages)) {
				return $clientLanguages;
			}
		}
		$languages    = array();
		$languagePath = ROOTDIR . ($admin ? '/' . $this->get_admin_folder_name() : '') . '/lang/';
		if(!is_dir($languagePath)) {
			WHMCS_Terminus::getinstance()->doDie('Language Folder Not Found');
		}
		$dh = opendir($languagePath);
		while(false !== ($file = readdir($dh))) {
			if(!is_dir(ROOTDIR . ($admin ? '/' . $whmcsAppConfig['customadminpath'] : '') . '/lang/' . $file)) {
				$pieces = explode('.', $file);
				if($pieces[1] == 'php') {
					$languages[] = $pieces[0];
				}
			}
		}
		closedir($dh);
		sort($languages);
		if($admin) {
			$adminLanguages = $languages;
		} else {
			$clientLanguages = $languages;
		}
		return $languages;
	}
	public function validateLanguage($lang, $admin = '') {
		$lang       = strtolower($lang);
		$lang       = $this->sanitize('a-z', $lang);
		$validlangs = $this->getValidLanguages($admin);
		if(!in_array($lang, $validlangs)) {
			if(in_array('english', $validlangs)) {
				$lang = 'english';
			} else {
				$lang = $validlangs[0];
			}
		}
		return $lang;
	}
	public function loadLanguage($lang = '', $admin = '') {
		global $_LANG;
		global $_ADMINLANG;
		if(!$lang) {
			$lang = $this->clientlang;
		}
		$lang = $this->validateLanguage($lang, $admin);
		if($admin) {
			$admin = '/' . $this->get_admin_folder_name();
		}
		$langfilepath          = ROOTDIR . $admin . '/lang/' . $lang . '.php';
		$langfileoverridespath = ROOTDIR . $admin . '/lang/overrides/' . $lang . '.php';
		if($admin) {
			$_ADMINLANG = array();
		} else {
			$_LANG = array();
		}
		ob_start();
		if(file_exists($langfilepath)) {
			include($langfilepath);
		} else {
			WHMCS_Terminus::getinstance()->doDie("Language File '" . $lang . "' Missing");
		}
		if(file_exists($langfileoverridespath)) {
			include($langfileoverridespath);
		}
		ob_end_clean();
	}
	public function set_client_language($lang, $skip = '') {
		$lang = $this->clientlang = $this->validateLanguage($lang);
		if($skip) {
			return false;
		}
		if(isset($_SESSION['uid']) && !isset($_SESSION['adminid'])) {
			update_query('tblclients', array(
				'language' => $lang
			), array(
				'id' => $_SESSION['uid']
			));
		}
		$_SESSION['Language'] = $lang;
	}
	public function get_client_language() {
		return $this->clientlang;
	}
	public function get_lang($var) {
		global $_LANG;
		return isset($_LANG[$var]) ? $_LANG[$var] : 'Missing Language Var ' . $var;
	}
	public function in_ssl() {
		return array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off';
	}
	public function getInstalledVersionNumber() {
		return $this->get_config('Version');
	}
	public function getCurrencyID() {
		global $currency;
		return (int) $currency['id'];
	}
}