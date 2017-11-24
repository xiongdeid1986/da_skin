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
class WHMCS_ClientArea {
	private $pagetitle = '';
	private $breadcrumb = array();
	private $templatefile = '';
	private $templatevars = array();
	private $nowrapper = false;
	private $inorderform = false;
	private $insupportmodule = false;
	private $smarty = '';
	public function __construct() {
		if(defined('PERFORMANCE_DEBUG')) {
			define('PERFORMANCE_STARTTIME', microtime());
		}
		global $smartyvalues;
		$smartyvalues = array();
	}
	public function setPageTitle($text) {
		global $whmcs;
		$this->pagetitle = $text;
	}
	public function addToBreadCrumb($link, $text) {
		$this->breadcrumb[] = array(
			$link,
			$text
		);
	}
	public function getUserID() {
		return (int) WHMCS_SESSION::get('uid');
	}
	public function isLoggedIn() {
		return $this->getUserID() ? true : false;
	}
	public function requireLogin() {
		global $whmcs;
		if($this->isLoggedIn()) {
			if(WHMCS_Session::get('2fabackupcodenew')) {
				$this->setTemplate('logintwofa');
				$twofa = new WHMCS_2FA();
				if($twofa->setClientID($this->getUserID())) {
					$backupcode = $twofa->generateNewBackupCode();
					$this->assign('newbackupcode', $backupcode);
					WHMCS_Session::delete('2fabackupcodenew');
				} else {
					$this->assign('newbackupcodeerror', true);
				}
				$this->output();
				exit( dirname(__FILE__) . " | line".__LINE__ );
			}
			return true;
		}
		$_SESSION['loginurlredirect'] = html_entity_decode($_SERVER['REQUEST_URI']);
		if(WHMCS_Session::get('2faverifyc')) {
			$this->setTemplate('logintwofa');
			if(WHMCS_Session::get('2fabackupcodenew')) {
				$this->assign('newbackupcode', true);
			} else {
				if($whmcs->get_req_var('incorrect')) {
					$this->assign('incorrect', true);
				}
			}
			$twofa = new WHMCS_2FA();
			if($twofa->setClientID(WHMCS_Session::get('2faclientid'))) {
				if(!$twofa->isActiveClients() || !$twofa->isEnabled()) {
					WHMCS_Session::destroy();
					redir();
				}
				if($whmcs->get_req_var('backupcode')) {
					$this->assign('backupcode', true);
				} else {
					$challenge = $twofa->moduleCall('challenge');
					if($challenge) {
						$this->assign('challenge', $challenge);
					} else {
						$this->assign('error', "Bad 2 Factor Auth Module. Please contact support.");
					}
				}
			} else {
				$this->assign('error', "An error occurred. Please try again.");
			}
		} else {
			$this->setTemplate('login');
			$this->assign('loginpage', true);
			$this->assign('formaction', "dologin.php");
			if($whmcs->get_req_var('incorrect')) {
				$this->assign('incorrect', true);
			}
		}
		$this->output();
		exit( dirname(__FILE__) . " | line".__LINE__ );
	}
	public function setTemplate($filename) {
		$this->templatefile = $filename;
	}
	public function assign($key, $value) {
		$this->templatevars[$key] = $value;
		$this->smarty->assign($key, $value);
	}
	public static function getRawStatus($val) {
		$val = strtolower($val);
		$val = str_replace(" ", '', $val);
		$val = str_replace('-', '', $val);
		return $val;
	}
	public function startSmartyIfNotStarted() {
		if(is_object($this->smarty)) {
			return true;
		}
		return $this->startSmarty();
	}
	public function startSmarty() {
		global $smarty;
		if(!$smarty) {
			$smarty = new WHMCS_Smarty();
		}
		$this->smarty =& $smarty;
		return true;
	}
	public function getCurrentPageName() {
		$filename = $_SERVER['PHP_SELF'];
		$filename = substr($filename, strrpos($filename, '/'));
		$filename = str_replace('/', '', $filename);
		$filename = explode(".", $filename);
		$filename = $filename[0];
		return $filename;
	}
	public function registerDefaultTPLVars() {
		global $whmcs;
		global $_LANG;
		$this->assign('template', $whmcs->get_config('Template'));
		$this->assign('language', $whmcs->get_client_language());
		$this->assign('LANG', $_LANG);
		$this->assign('companyname', $whmcs->get_config('CompanyName'));
		$this->assign('logo', $whmcs->get_config('LogoURL'));
		$this->assign('charset', $whmcs->get_config('Charset'));
		$this->assign('pagetitle', $this->pagetitle);
		$this->assign('filename', $this->getCurrentPageName());
		$this->assign('token', generate_token('plain'));
		if($whmcs->in_ssl() && $whmcs->isSSLAvailable()) {
			$this->assign('systemurl', $whmcs->getSystemSSLURL());
		} else {
			if($whmcs->getSystemURL() != "http://www.yourdomain.com/whmcs/") {
				$this->assign('systemurl', $whmcs->getSystemURL());
			}
		}
		if($whmcs->isSSLAvailable()) {
			$this->assign('systemsslurl', $whmcs->getSystemSSLURL());
		}
		$this->assign('todaysdate', date("l, jS F Y"));
		$this->assign('date_day', date('d'));
		$this->assign('date_month', date('m'));
		$this->assign('date_year', date('Y'));
	}
	public function getCurrencyOptions() {
		$currenciesarray = array();
		$result          = select_query('tblcurrencies', "id,code,`default`", '', 'code', 'ASC');
		while($data = mysqli_fetch_array($result)) {
			$currenciesarray[] = array(
				'id' => $data['id'],
				'code' => $data['code'],
				'default' => $data['default']
			);
		}
		if(count($currenciesarray) == 1) {
			$currenciesarray = '';
		}
		return $currenciesarray;
	}
	public function getLanguageSwitcherHTML() {
		global $whmcs;
		if(!$whmcs->get_config('AllowLanguageChange')) {
			return false;
		}
		$setlanguage = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'];
		$count       = 0;
		foreach($_GET as $k => $v) {
			$prefix = $count == 0 ? "?" : "&amp;";
			$setlanguage .= $prefix . htmlentities($k) . "=" . htmlentities($v);
			$count++;
		}
		$setlanguage .= "\" name=\"languagefrm\" id=\"languagefrm\"><strong>" . $whmcs->get_lang('language') . ":</strong> <select name=\"language\" onchange=\"languagefrm.submit()\">";
		foreach($whmcs->getValidLanguages() as $lang) {
			$setlanguage .= "<option";
			if($lang == $whmcs->get_client_language()) {
				$setlanguage .= " selected=\"selected\"";
			}
			$setlanguage .= ">" . ucfirst($lang) . "</option>";
		}
		$setlanguage .= "</select></form>";
		return $setlanguage;
	}
	public function initPage() {
		global $whmcs;
		global $_LANG;
		global $clientsdetails;
		$this->startSmartyIfNotStarted();
		if($this->isLoggedIn()) {
			$this->assign('loggedin', true);
			if(!function_exists('getClientsDetails')) {
				require(ROOTDIR . "/includes/clientfunctions.php");
			}
			$clientsdetails = getClientsDetails();
			$this->assign('clientsdetails', $clientsdetails);
			$this->assign('clientsstats', getClientsStats($_SESSION['uid']));
			if(isset($_SESSION['cid'])) {
				$result             = select_query('tblcontacts', 'id,firstname,lastname,email,permissions', array(
					'id' => $_SESSION['cid'],
					'userid' => $_SESSION['uid']
				));
				$data               = mysqli_fetch_array($result);
				$loggedinuser       = array(
					'contactid' => $data['id'],
					'firstname' => $data['firstname'],
					'lastname' => $data['lastname'],
					'email' => $data['email']
				);
				$contactpermissions = explode(',', $data[4]);
			} else {
				$loggedinuser       = array(
					'userid' => $_SESSION['uid'],
					'firstname' => $clientsdetails['firstname'],
					'lastname' => $clientsdetails['lastname'],
					'email' => $clientsdetails['email']
				);
				$contactpermissions = array(
					'profile',
					'contacts',
					'products',
					'manageproducts',
					'domains',
					'managedomains',
					'invoices',
					'tickets',
					'affiliates',
					'emails',
					'orders'
				);
			}
			$this->assign('loggedinuser', $loggedinuser);
			$this->assign('contactpermissions', $contactpermissions);
		} else {
			$this->assign('loggedin', false);
		}
	}
	public function getSingleTPLOutput($templatepath, $templatevars) {
		global $whmcs;
		global $smartyvalues;
		$this->startSmartyIfNotStarted();
		$this->registerDefaultTPLVars();
		if(is_array($smartyvalues)) {
			foreach($smartyvalues as $key => $value) {
				$this->assign($key, $value);
			}
		}
		foreach($this->templatevars as $key => $value) {
			$this->smarty->assign($key, $value);
		}
		foreach($templatevars as $key => $value) {
			$this->smarty->assign($key, $value);
		}
		$templatecode = $this->smarty->fetch(ROOTDIR . $templatepath);
		$this->smarty->clear_all_assign();
		return $templatecode;
	}
	public function runClientAreaOutputHook($hookname) {
		$hookres = run_hook($hookname, $this->templatevars);
		$output  = '';
		foreach($hookres as $data) {
			if($data) {
				$output .= $data . "\n";
			}
		}
		return $output;
	}
	public static function getConditionalLinks() {
		global $whmcs;
		$calinkupdatecc = isset($_SESSION['calinkupdatecc']) ? $_SESSION['calinkupdatecc'] : CALinkUpdateCC();
		$security       = isset($_SESSION['calinkupdatesq']) ? $_SESSION['calinkupdatesq'] : CALinkUpdateSQ();
		if(!$security) {
			$twofa = new WHMCS_2FA();
			if($twofa->isActiveClients()) {
				$security = true;
			}
		}
		return array(
			'updatecc' => $calinkupdatecc,
			'updatesq' => $security,
			'security' => $security,
			'addfunds' => $whmcs->get_config('AddFundsEnabled'),
			'masspay' => $whmcs->get_config('EnableMassPay'),
			'affiliates' => $whmcs->get_config('AffiliateEnabled'),
			'domainreg' => $whmcs->get_config('AllowRegister'),
			'domaintrans' => $whmcs->get_config('AllowTransfer'),
			'domainown' => $whmcs->get_config('AllowOwnDomain'),
			'pmaddon' => get_query_val('tbladdonmodules', 'value', array(
				'module' => 'project_management',
				'setting' => 'clientenable'
			))
		);
	}
	public function buildBreadCrumb() {
		$breadcrumb = array();
		foreach($this->breadcrumb as $vals) {
			$breadcrumb[] = "<a href=\"" . $vals[0] . "\">" . $vals[1] . "</a>";
		}
		return implode(" > ", $breadcrumb);
	}
	public function output() {
		global $whmcs;
		global $licensing;
		global $smartyvalues;
		if(!$this->templatefile) {
			exit("Missing Template File '" . $this->templatefile . "'");
		}
		$this->registerDefaultTPLVars();
		$this->assign('breadcrumbnav', $this->buildBreadCrumb());
		$this->assign('langchange', $whmcs->get_config('AllowLanguageChange') ? true : false);
		$this->assign('setlanguage', $this->getLanguageSwitcherHTML());
		$this->assign('currencies', $this->getCurrencyOptions());
		$this->assign('twitterusername', $whmcs->get_config('TwitterUsername'));
		$this->assign('condlinks', $this->getConditionalLinks());
		if(is_array($smartyvalues)) {
			foreach($smartyvalues as $key => $value) {
				$this->assign($key, $value);
			}
		}
		foreach($this->templatevars as $key => $value) {
			$this->smarty->assign($key, $value);
		}
		if(isset($GLOBALS['pagelimit'])) {
			$smartyvalues['itemlimit'] = $GLOBALS['pagelimit'];
		}
		$hookvars = $this->templatevars;
		unset($hookvars['LANG']);
		$hookres = run_hook('ClientAreaPage', $hookvars);
		foreach($hookres as $arr) {
			foreach($arr as $k => $v) {
				$hookvars[$k] = $v;
				$this->assign($k, $v);
			}
		}
		$this->assign('headoutput', $this->runClientAreaOutputHook('ClientAreaHeadOutput'));
		$this->assign('headeroutput', $this->runClientAreaOutputHook('ClientAreaHeaderOutput'));
		$this->assign('footeroutput', $this->runClientAreaOutputHook('ClientAreaFooterOutput'));
		$licenseBannerHtml = $this->getLicenseBannerHtml();
		if(!$this->nowrapper) {
			$header_file = $this->smarty->fetch($whmcs->get_config('Template') . "/header.tpl");
			$footer_file = $this->smarty->fetch($whmcs->get_config('Template') . "/footer.tpl");
		}
		if($this->inorderform) {
			global $orderfrm;
			$body_file = $this->smarty->fetch(ROOTDIR . '/templates/orderforms/' . $orderfrm->getTemplate() . '/' . $this->templatefile . ".tpl");
		} else {
			if($this->insupportmodule) {
				$body_file = $this->smarty->fetch(ROOTDIR . '/templates/' . $whmcs->get_config('SupportModule') . '/' . $this->templatefile . ".tpl");
			} else {
				if(substr($this->templatefile, 0, 1) == '/') {
					$body_file = $this->smarty->fetch(ROOTDIR . $this->templatefile);
				} else {
					$body_file = $this->smarty->fetch(ROOTDIR . '/templates/' . $whmcs->getClientAreaTplName() . '/' . $this->templatefile . ".tpl");
				}
			}
		}
		$this->smarty->clear_all_assign();
		$copyrighttext = $licensing->getBrandingRemoval() ? '' : "<p style=\"text-align:center;\">Powered by <a href=\"http://nullrefer.com/?https://www.whmcs.com/\" target=\"_blank\">WHMCompleteSolution</a></p>";
		if(isset($_SESSION['adminid'])) {
			$adminloginlink = "<div style=\"position:absolute;top:0px;right:0px;padding:5px;background-color:#000066;font-family:Tahoma;font-size:11px;color:#ffffff\" class=\"adminreturndiv\">您已登陆管理员 | <a href=\"" . $whmcs->get_admin_folder_name() . '/';
			if(isset($_SESSION['uid'])) {
				$adminloginlink .= "clientssummary.php?userid=" . $_SESSION['uid'] . "&return=1";
			}
			$adminloginlink .= "\" style=\"color:#6699ff\">返回管理账户</a></div>

    ";
		} else {
			$adminloginlink = '';
		}
		if($this->nowrapper) {
			$template_output = $body_file;
		} else {
			$template_output = $header_file . PHP_EOL . $licenseBannerHtml . PHP_EOL . $body_file . PHP_EOL . $copyrighttext . PHP_EOL . $adminloginlink . PHP_EOL . $footer_file;
		}
		if(!in_array($this->templatefile, array(
			'3dsecure',
			'forwardpage',
			'viewinvoice'
		))) {
			$template_output = preg_replace("/(<form\\W[^>]*\\bmethod=('|\"|)POST('|\"|)\\b[^>]*>)/i", "\\1" . "\n" . generate_token(), $template_output);
		}
		echo $template_output;
		if(defined('PERFORMANCE_DEBUG')) {
			global $query_count;
			$exectime = microtime() - PERFORMANCE_STARTTIME;
			echo "<p>Performance Debug: " . $exectime . " Queries: " . $query_count . "</p>";
		}
		exit( dirname(__FILE__) . " | line".__LINE__ );
	}
	/**
	 * Determine license banner messaging based on license key prefix
	 *
	 * Returns an empty string if no banner is required.
	 *
	 * @return string
	 */
	public function getLicenseBannerMessage() {
		$whmcs            = WHMCS_Application::getinstance();
		$licensekey       = $whmcs->get_license_key();
		$licensekeyparts  = explode('-', $licensekey);
		$licensekeyprefix = $licensekeyparts[0];
		if(in_array($licensekeyprefix, array(
			'Dev',
			'Beta',
			'Security',
			'Trial'
		))) {
			if($licensekeyprefix == 'Beta') {
				$devBannerTitle = "Beta License";
				$devBannerMsg   = "This license is intended for beta testing only and should not be used in a production environment. Please report any cases of abuse to abuse@whmcs.com";
			} else {
				if($licensekeyprefix == 'Trial') {
					$devBannerTitle = "Trial License";
					$devBannerMsg   = "This is a free trial and is not intended for production use. Please <a href=\"http://nullrefer.com/?https://www.whmcs.com/order/\" target=\"_blank\">purchase a license</a> to remove this notice.";
				} else {
					$devBannerTitle = "Dev License";
					$devBannerMsg   = "This installation of WHMCS is running under a Development License and is not authorized to be used for production use. Please report any cases of abuse to abuse@whmcs.com";
				}
			}
			return "<strong>" . $devBannerTitle . ":</strong> " . $devBannerMsg;
		}
		return '';
	}
	/**
	 * Generate HTML for license banner display notice (client side)
	 *
	 * Builds the HTML output for the license banner message if current
	 * license key type/prefix requires one.
	 *
	 * @return string
	 */
	public function getLicenseBannerHtml() {
		$licenseBannerMsg = $this->getLicenseBannerMessage();
		return $licenseBannerMsg ? "<div style=\"margin:0 0 10px 0;padding:10px 35px;background-color:#ffffd2;color:#555;font-size:16px;text-align:center;\">" . $licenseBannerMsg . "</div>" : '';
	}
}