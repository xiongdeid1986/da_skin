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
 * WHMCS Core Application Class
 *
 * @copyright Copyright (c) WHMCS Limited 2005-2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
class WHMCS_Application extends WHMCS_Init {
	protected $applicationConfig = NULL;
	private $databaseObj = NULL;
	protected static $instance = NULL;
	protected $dangerousVariables = array('_GET', '_POST', '_REQUEST', '_SERVER', '_COOKIE', '_FILES', '_ENV', 'GLOBALS');
	protected $forced_ssl_filenames = array('aff', 'affiliates', 'cart', 'clientarea', 'configuressl', 'contact', 'creditcard', 'domainchecker', 'login', 'logout', 'networkissues', 'pwreset', 'register', 'serverstatus', 'submitticket', 'supporttickets', 'upgrade', 'viewemail', 'viewinvoice', 'viewquote', 'viewticket');
	private $forced_non_ssl_filenames = array('announcements', 'banned', 'contact', 'downloads', 'index', 'knowledgebase', 'whois');
	const FILES_VERSION = "5.3.14-release.1";
	const RELEASE_DATE = '2014-02-05';
	/**
	 * List of filenames to always be served over Non-SSL
	 *
	 * @var array
	 */
	public function __construct($config = null, $database = null) {
		global $CONFIG;
		parent::__construct();
		$this->setInstance($this);
		$this->initInputs();
		$this->loadApplicationConfig($config);
		$this->loadDatabase($database);
		$this->loadAdminDefinedConfigurations();
		WHMCS_Http_Request::defineproxytrustfromapplication($this);
		$this->setRemoteIp(WHMCS_Utility_Environment_CurrentUser::getip());
		$this->setClientLanguage($this->validateLanguage($this->get_config('Language')));
		$this->setPhpSelf($_SERVER['SCRIPT_NAME']);
		if($this->shouldRedirectForIPBan() && $this->isVisitorIPBanned()) {
			$this->redirect($CONFIG['SystemURL'] . "/banned.php");
		}
		$instanceid = $this->getWHMCSInstanceID();
		if(!$instanceid) {
			$instanceid = $this->createWHMCSInstanceID();
		}
		$session = new WHMCS_Session();
		$session->create($instanceid);
		$token_manager =& getTokenManager($this);
		$token_manager->conditionallySetToken();
		if(isset($_SESSION['Language'])) {
			$this->set_client_language($_SESSION['Language'], 1);
		}
		if(isset($_REQUEST['systpl'])) {
			$_SESSION['Template'] = $_REQUEST['systpl'];
		}
		if(isset($_REQUEST['carttpl'])) {
			$_SESSION['OrderFormTemplate'] = $_REQUEST['carttpl'];
		}
		$this->validate_templates();
		$this->validateAdminAuth();
		$this->validateClientAuth();
		if(!defined('DoNotForceNonSSLonDLFile')) {
			$this->forced_non_ssl_filenames[] = 'dl';
		}
	}
	/**
	 * Set the WHMCS_Application singleton.
	 *
	 * @param WHMCS_Application $whmcs
	 * @return WHMCS_Application
	 */
	protected static function setInstance($whmcs) {
		self::$instance = $whmcs;
		return $whmcs;
	}
	/**
	 * Remove the WHMCS_Application singleton.
	 */
	protected static function destroyInstance() {
		self::$instance = null;
	}
	/**
	 * Retrieve a WHMCS_Application object via singleton.
	 *
	 * @return WHMCS_Application
	 */
	public static function getInstance() {
		if(is_null(self::$instance)) {
			self::setinstance(new WHMCS_Application());
		}
		return self::$instance;
	}
	/**
	 * WHMCS' class autoloader.
	 *
	 * @TODO make PSR-4 compliant, notably are namespace & path mapping and
	 * by not returning anything!
	 *
	 * @link http://www.php.net/spl_autoload_register
	 * @param string $className
	 *
	 * @return bool
	 */
	public static function loadClass($className) {
		$className = preg_replace("/[^0-9a-z_]/i", '', $className);
		switch($className) {
			case 'Smarty':
				$namespacePath = ROOTDIR . "/includes/classes/Smarty/Smarty.class.php";
				break;
			case 'PHPMailer':
				$namespacePath = ROOTDIR . "/includes/classes/PHPMailer/class.phpmailer.php";
				break;
			case 'TCPDF':
				$namespacePath = ROOTDIR . "/includes/classes/TCPDF/tcpdf.php";
				break;
			default:
				$namespacePath = ROOTDIR . '/includes/classes/' . str_replace('_', DIRECTORY_SEPARATOR, $className) . ".php";
				break;
		}
		if(file_exists($namespacePath)) {
			include_once($namespacePath);
			return true;
		}
		return false;
	}
	/**
	 * Load the WHMCS_Application's config during application startup.
	 *
	 * @param WHMCS_Config_AbstractConfig $config
	 * @return WHMCS_Application
	 */
	protected function loadApplicationConfig($config = null) {
		if(!$config) {
			$config = WHMCS_Config_Application::factory();
		}
		$this->importConfigObj($config);
		if(!$this->getLicenseClientKey()) {
			$msg = "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\">" . "<strong>Welcome to WHMCS!</strong><br>Before you can begin using WHMCS you need to perform the installation procedure. <a href=\"" . (file_exists("install/install.php") ? '' : "../") . "install/install.php\" style=\"color:#000;\">Click here to begin...</a>" . "</div>";
			throw new WHMCS_Exception_Fatal($msg);
		}
		return $this;
	}
	/**
	 * Load a database connection into the WHMCS_Application during startup.
	 *
	 * @param WHMCS_Database $database
	 * @return WHMCS_Application
	 */
	protected function loadDatabase($database = null) {
		$config = $this->getApplicationConfig();
		if(!$database) {
			$database = new WHMCS_Database();
			$database->loadConfig($config);
		}
		$this->setDatabaseObj($database);
		try {
			$database->connect();
		}
		catch(Exception $e) {
			$msg = "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\">" . "<strong>Critical Error</strong><br>Could not connect to the database";
			if($config['display_errors']) {
				$msg .= "</br>" . $e->getMessage() . "</div>";
			} else {
				$msg .= "</div>";
			}
			throw new WHMCS_Exception_Fatal($msg);
		}
		$this->setDatabaseObj($database);
		return $this;
	}
	public function importConfigObj($config) {
		$vars = $config->validConfigVariables();
		foreach($vars as $varToGlobal) {
			$this->registerGlobalVariable($varToGlobal, $config[$varToGlobal]);
		}
		if($config['display_errors']) {
			$this->display_errors($config['display_errors']);
		}
		if(!$config['templates_compiledir'] || $config['templates_compiledir'] == 'templates_c/' || $config['templates_compiledir'] == 'templates_c') {
			$config['templates_compiledir'] = ROOTDIR . '/templates_c/';
		}
		if(!$config['attachments_dir']) {
			$config['attachments_dir'] = ROOTDIR . '/attachments/';
		}
		if(!$config['downloads_dir']) {
			$config['downloads_dir'] = ROOTDIR . '/downloads/';
		}
		if(!$config['customadminpath']) {
			$config['customadminpath'] = 'admin';
		}
		if(!$config['overidephptimelimit']) {
			$overidephptimelimit = 300;
		}
		$config['attachments_dir']      = preg_replace("/([^\\/])\$/", "\$1/", $config['attachments_dir']);
		$config['downloads_dir']        = preg_replace("/([^\\/])\$/", "\$1/", $config['downloads_dir']);
		$config['templates_compiledir'] = preg_replace("/([^\\/])\$/", "\$1/", $config['templates_compiledir']);
		@set_time_limit($overidephptimelimit);
		$this->license              = $config['license'];
		$this->cc_hash              = $config['cc_encryption_hash'];
		$this->templates_compiledir = $config['templates_compiledir'];
		$this->customadminpath      = $config['customadminpath'];
		$this->setApplicationConfig($config);
		return $this;
	}
	public function getLicenseClientKey() {
		return $this->license;
	}
	public function setPhpSelf($script) {
		global $PHP_SELF;
		$_SERVER['PHP_SELF'] = $script;
		$PHP_SELF            = $this->phpSelf = $_SERVER['PHP_SELF'];
		return $this;
	}
	public function getPhpSelf() {
		return $this->phpSelf;
	}
	public function setRemoteIp($ip) {
		global $remote_ip;
		$remote_ip = $this->remote_ip = $ip;
		return $this;
	}
	public function getRemoteIp() {
		return $this->remote_ip;
	}
	public function setClientLanguage($lang) {
		$this->clientlang = $lang;
	}
	public function getClientLanguage() {
		return $this->clientlang;
	}
	public function setDatabaseObj($database) {
		$this->databaseObj = $database;
		return $this;
	}
	public function getDatabaseObj() {
		return $this->databaseObj;
	}
	public function setApplicationConfig($config) {
		$this->applicationConfig = $config;
		return $this;
	}
	public function getApplicationConfig() {
		return $this->applicationConfig;
	}
	public function getAttachmentsDir() {
		return $this->applicationConfig['attachments_dir'];
	}
	public function getDownloadsDir() {
		return $this->applicationConfig['downloads_dir'];
	}
	public function getTemplatesCacheDir() {
		return $this->applicationConfig['templates_compiledir'];
	}
	public function redirect($path = null, $vars = array(), $prefix = '') {
		if(!$path) {
			$path = $this->getPhpSelf();
		}
		$filenamePattern = "/^[a-zA-Z0-9~\\._\\/\\:\\-]*\$/";
		if(preg_match($filenamePattern, $path) !== 1) {
			throw new WHMCS_Exception_Fatal(sprintf("Invalid filename for redirect: %s", htmlspecialchars($path, ENT_QUOTES)));
		}
		$AnyMultipleSlashNotPrecededByColonPattern = "/([^:]|^)\\/\\/+/";
		$precedingCharacterIfAnyWithOneSlash       = "\${1}/";
		$prefix                                    = preg_replace($AnyMultipleSlashNotPrecededByColonPattern, $precedingCharacterIfAnyWithOneSlash, $prefix);
		if(is_array($vars)) {
			$vars = http_build_query($vars);
		}
		if(is_string($vars) && strpos($vars, "=") !== false) {
			$urlEncodedNewline        = urlencode("\n");
			$urlEncodedCarriageReturn = urlencode("\r");
			$newlinePattern           = "/[\n\r]|(" . $urlEncodedNewline . ")|(" . $urlEncodedCarriageReturn . ")/i";
			$vars                     = sprintf("?%s", preg_replace($newlinePattern, '', trim($vars)));
		} else {
			if($vars) {
				throw new WHMCS_Exception_Fatal(sprintf("URL parameter variables must be in the form of an array or HTTP build query string"));
			}
		}
		header(sprintf("Location: %s%s%s", $prefix, $path, $vars));
		WHMCS_Terminus::getinstance()->doExit();
	}
	public function redirectSystemURL($path = '', $vars = '') {
		$this->redirect($path, $vars, $this->getSystemURL());
	}
	public function redirectSystemSSLURL($path = '', $vars = '') {
		$this->redirect($path, $vars, $this->getSystemSSLURL());
	}
	public function initInputs() {
		$_GET     = $this->sanitize_input_vars($_GET);
		$_POST    = $this->sanitize_input_vars($_POST);
		$_REQUEST = $this->sanitize_input_vars($_REQUEST);
		$_SERVER  = $this->sanitize_input_vars($_SERVER);
		$_COOKIE  = $this->sanitize_input_vars($_COOKIE);
		if(isset($_SERVER['REQUEST_METHOD'])) {
			switch($_SERVER['REQUEST_METHOD']) {
				case 'GET':
				case 'POST':
					break;
				case 'OPTIONS':
					break;
				case 'HEAD':
					break;
				default:
					header("HTTP/ 405 Method Not Allowed");
					header("Allow: GET, POST, OPTIONS, HEAD");
					WHMCS_Terminus::getinstance()->doDie($_SERVER['REQUEST_METHOD'] . " Request Method Not Allowed");
					break;
			}
		}
		foreach($this->dangerousVariables as $var) {
			if(isset($_REQUEST[$var]) || isset($_FILES[$var])) {
				WHMCS_Terminus::getinstance()->doDie("Hacking attempt");
			}
		}
		$this->load_input();
		$this->clean_input();
		$this->register_globals();
		return $this;
	}
	protected function registerGlobalVariable($globalVariableName, $globalVariableValue) {
		global ${$globalVariableName};
		${$globalVariableName} = $globalVariableValue;
	}
	protected function register_globals() {
		foreach($this->input as $k => $v) {
			$this->registerGlobalVariable($k, $v);
		}
	}
	protected function loadAdminDefinedConfigurations() {
		global $CONFIG;
		$CONFIG    = array();
		$configObj = $this->getApplicationConfig();
		$database  = $this->getDatabaseObj();
		if($configObj) {
		}
		$CONFIG = $database->getTblConfigurationData();
		if(isset($CONFIG['DisplayErrors']) && $CONFIG['DisplayErrors']) {
			$this->display_errors($CONFIG['DisplayErrors']);
		}
		header("Content-Type: text/html; charset=" . $CONFIG['Charset']);
		foreach(array(
			'SystemURL',
			'SystemSSLURL',
			'Domain'
		) as $v) {
			if(!isset($CONFIG[$v])) {
				$CONFIG[$v] = '';
			}
			if(substr($CONFIG[$v], 0 - 1, 1) == '/') {
				$CONFIG[$v] = substr($CONFIG[$v], 0, 0 - 1);
			}
		}
		if($CONFIG['SystemURL'] == $CONFIG['SystemSSLURL'] || substr($CONFIG['SystemSSLURL'], 0, 5) != 'https') {
			$CONFIG['SystemSSLURL'] = '';
		}
		$this->config = $CONFIG;
		return $CONFIG;
	}
	public function getValidLanguages($admin = '') {
		global $customadminpath;
		static $ClientLanguages;
		static $AdminLanguages;
		$langs = array();
		if($admin) {
			if(count($AdminLanguages)) {
				return $AdminLanguages;
			}
			$admin = '/' . $customadminpath;
		} else {
			if(count($ClientLanguages)) {
				return $ClientLanguages;
			}
		}
		$dirpath = ROOTDIR . $admin . '/lang/';
		if(!is_dir($dirpath)) {
			$msg = "Language Folder Not Found";
			WHMCS_Terminus::getinstance()->doDie($msg);
		}
		$dh = '';
		if(!($dh = opendir($dirpath))) {
			$msg = "Cannot open language directory";
			WHMCS_Terminus::getinstance()->doDie($msg);
		}
		while(false !== ($file = readdir($dh))) {
			if(!is_dir(ROOTDIR . '/lang/' . $file) && $file !== "." && $file !== ".") {
				$pieces = explode(".", $file);
				if($pieces[1] == 'php') {
					$langs[] = $pieces[0];
				}
			}
		}
		closedir($dh);
		sort($langs);
		if($admin) {
			$AdminLanguages = $langs;
		} else {
			$ClientLanguages = $langs;
		}
		return $langs;
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
		if(!$lang) {
			$msg = sprintf("No Valid Language File Found", '');
			WHMCS_Terminus::getinstance()->doDie($msg);
		}
		return $lang;
	}
	/**
	 * needs work?
	 * @param unknown $lang
	 * @param string $skip
	 * @return boolean
	 */
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
			), $this->getDatabaseObj()->retrieveDatabaseConnection());
		}
		$_SESSION['Language'] = $lang;
	}
	/**
	 * Determine if the visiting IP is banned
	 *
	 * @return bool
	 */
	public function isVisitorIPBanned() {
		$handle         = $this->getDatabaseObj()->retrieveDatabaseConnection();
		$result         = full_query("DELETE FROM tblbannedips WHERE expires<now()", $handle);
		$visitorIP      = $this->getRemoteIp();
		$visitorIPParts = explode(".", $visitorIP);
		array_pop($visitorIPParts);
		$remoteIP1 = implode(".", $visitorIPParts) . ".*";
		array_pop($visitorIPParts);
		$remoteIP2 = implode(".", $visitorIPParts) . ".*.*";
		$result    = full_query("SELECT id FROM tblbannedips WHERE " . "ip='" . db_escape_string($visitorIP) . "' OR " . "ip='" . db_escape_string($remoteIP1) . "' OR " . "ip='" . db_escape_string($remoteIP2) . "' " . "ORDER BY id DESC", $handle);
		$data      = mysql_fetch_array($result);
		if($data['id']) {
			return true;
		}
		return false;
	}
	/**
	 * Determine if current page is valid for IP Ban Redirect
	 *
	 * @return bool
	 */
	protected function shouldRedirectForIPBan() {
		$excludedPages = array(
			"banned.php",
			"includes/api.php"
		);
		foreach($excludedPages as $excludedPage) {
			$currentPage = substr($this->getPhpSelf(), strlen($excludedPage) * (0 - 1));
			if($currentPage == $excludedPage) {
				return false;
			}
		}
		return true;
	}
	/**
	 * Determine if the user has a valid admin session.
	 *
	 * @return WHMCS_Application
	 */
	protected function validateAdminAuth() {
		$auth = new WHMCS_Auth();
		if($auth->isLoggedIn()) {
			$auth->getInfobyID($_SESSION['adminid'], $this->getDatabaseObj()->retrieveDatabaseConnection());
			if($auth->isSessionPWHashValid($this)) {
			} else {
				$auth->destroySession();
			}
		} else {
			if($auth->isValidRememberMeCookie($this)) {
				$auth->setSessionVars($this);
			}
		}
		return $this;
	}
	/**
	 * Determine if the user has a valid client session.
	 *
	 * @return WHMCS_Application
	 */
	protected function validateClientAuth() {
		$haship = $this->get_config('DisableSessionIPCheck') ? '' : WHMCS_Utility_Environment_CurrentUser::getip();
		$handle = $this->getDatabaseObj()->retrieveDatabaseConnection();
		if(defined('CLIENTAREA') && !isset($_SESSION['uid']) && isset($_COOKIE['WHMCSUser'])) {
			$cookiedata = explode(":", $_COOKIE['WHMCSUser']);
			if(is_numeric($cookiedata[0])) {
				$data              = get_query_vals('tblclients', 'id,password', array(
					'id' => (int) $cookiedata[0]
				), '', '', '', '', $handle);
				$loginhash         = sha1($data['id'] . $data['password'] . $haship . substr(sha1($this->get_hash()), 0, 20));
				$cookiehashcompare = sha1($loginhash . $this->get_hash());
				if($cookiedata[1] == $cookiehashcompare) {
					$_SESSION['uid']   = $data['id'];
					$_SESSION['upw']   = $loginhash;
					$_SESSION['tkval'] = substr(sha1(rand(1000, 9999) . time()), 0, 12);
				}
			}
		}
		if(isset($_SESSION['uid'])) {
			$sessionUserId     = WHMCS_Session::get('uid');
			$sessionContactId  = WHMCS_Session::get('cid');
			$sessionAdminId    = WHMCS_Session::get('adminid');
			$sessionUserPwHash = WHMCS_Session::get('upw');
			if($sessionContactId) {
				$result = select_query('tblcontacts', "tblcontacts.id, tblcontacts.password", array(
					"tblcontacts.id" => (int) $sessionContactId,
					"tblclients.status" => array(
						'sqltype' => 'IN',
						'values' => array(
							'Active',
							'Inactive'
						)
					)
				), '', '', '', "tblclients ON tblclients.id = tblcontacts.userid", $handle);
			} else {
				$result = select_query('tblclients', "id, password", array(
					'id' => (int) $sessionUserId,
					'status' => array(
						'sqltype' => 'IN',
						'values' => array(
							'Active',
							'Inactive'
						)
					)
				), '', '', '', '', $handle);
			}
			$data                 = mysql_fetch_array($result);
			$dbId                 = $data['id'];
			$dbPassword           = $data['password'];
			$validatedSessionData = false;
			if($dbId) {
				$hashSalt     = substr(sha1($this->get_hash()), 0, 20);
				$computedHash = sha1($sessionUserId . $sessionContactId . $dbPassword . $haship . $hashSalt);
				if($sessionAdminId || $sessionUserPwHash == $computedHash) {
					$validatedSessionData = true;
					WHMCS_Session::delete('currency');
				}
			}
			if(!$validatedSessionData) {
				WHMCS_Session::destroy();
			}
		}
		return $this;
	}
	/**
	 * Retrieve WHMCS's unique instance id.
	 *
	 * @return string
	 */
	public function getWHMCSInstanceID() {
		return $this->get_config('InstanceID');
	}
	/**
	 * Create a unique WHMCS application instance id.
	 *
	 * @return string
	 */
	protected function createWHMCSInstanceID() {
		$instanceid = genRandomVal(12);
		$this->set_config('InstanceID', $instanceid, $this->getDatabaseObj()->retrieveDatabaseConnection());
		return $instanceid;
	}
	/**
	 * Get current filename without extension
	 *
	 * @param boolean $stripExtension Defaults to true
	 * @return string
	 */
	public function getCurrentFilename($stripExtension = true) {
		$filename = $this->getPhpSelf();
		$filename = substr($filename, strrpos($filename, '/'));
		$filename = str_replace('/', '', $filename);
		if($stripExtension) {
			$filename = substr($filename, 0, strrpos($filename, "."));
		}
		return $filename;
	}
	/**
	 * Returns the URL for the Installation
	 *
	 * @return string
	 */
	public function getSystemURL() {
		$url = trim($this->get_config('SystemURL'));
		if($url) {
			while(substr($url, 0 - 1) == '/') {
				$url = substr($url, 0, 0 - 1);
			}
			if(substr($url, 0 - 1) != '/') {
				$url .= '/';
			}
		}
		return $url;
	}
	/**
	 * Returns the SSL URL for the Installation
	 *
	 * @return string
	 */
	public function getSystemSSLURL() {
		$url = trim($this->get_config('SystemSSLURL'));
		if($url) {
			while(substr($url, 0 - 1) == '/') {
				$url = substr($url, 0, 0 - 1);
			}
			if(substr($url, 0 - 1) != '/') {
				$url .= '/';
			}
		}
		return $url;
	}
	/**
	 * Returns true if an SSL System URL is set
	 *
	 * @return boolean
	 */
	public function isSSLAvailable() {
		return $this->getSystemSSLURL() ? true : false;
	}
	/**
	 * Returns true if the current page is in the forced ssl list
	 *
	 * @return boolean
	 */
	public function shouldSSLBeForcedForCurrentPage() {
		return in_array($this->getCurrentFilename(), $this->forced_ssl_filenames);
	}
	/**
	 * Returns true if the current page is in the forced non-ssl list
	 *
	 * @return boolean
	 */
	public function shouldNonSSLBeForcedForCurrentPage() {
		return in_array($this->getCurrentFilename(), $this->forced_non_ssl_filenames);
	}
	/**
	 * Determine whether actions are being run via the API.
	 *
	 * @return bool
	 */
	public function isApiRequest() {
		return defined('APICALL');
	}
	/**
	 * Return client area template
	 *
	 * @return string
	 */
	public function getClientAreaTplName() {
		return $this->get_config('Template');
	}
	/**
	 * Get a version object that will represent the state of the files
	 *
	 * @throws WHMCS_Exception_Version_BadVersionNumber If version number invalid
	 *
	 * @return WHMCS_Version_SemanticVersion
	 */
	public function getVersion() {
		return new WHMCS_Version_SemanticVersion(self::FILES_VERSION);
	}
	/**
	 * Get a version object that will represent the state of the database
	 *
	 * @throws WHMCS_Exception_Version_BadVersionNumber If version number invalid
	 *
	 * @return WHMCS_Version_SemanticVersion
	 */
	public function getDBVersion() {
		$DBVersion = $this->get_config('Version');
		try {
			return new WHMCS_Version_SemanticVersion($DBVersion);
		}
		catch(Exception $e) {
			throw new WHMCS_Exception_Fatal("<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Database Error</strong><br />One or more of the WHMCS database tables appear to be either missing or corrupted. Please check and repair.</div>");
		}
	}
	/**
	 * Compares file and databse version objects and returns false if not an exact match
	 *
	 * @throws WHMCS_Exception_Version_BadVersionNumber If either version number is invalid
	 *
	 * @return bool
	 */
	public function doFileAndDBVersionsNotMatch() {
		$filesVersionObj = $this->getVersion();
		$dbVersionOjb    = $this->getDBVersion();
		return !WHMCS_Version_SemanticVersion::compare($dbVersionOjb, $filesVersionObj, "==");
	}
	/**
	 * Returns the release date of the current Application core
	 *
	 * @return string
	 */
	public function getReleaseDate() {
		return self::RELEASE_DATE;
	}
}