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
if(!defined('ROOTDIR')) {
	define('ROOTDIR', realpath(dirname(__FILE__)));
}
if(!defined('WHMCS')) {
	define('WHMCS', true);
}
function gracefulCoreRequiredFileInclude($path) {
	$fullpath = ROOTDIR . $path;
	if(file_exists($fullpath)) {
		include_once($fullpath);
	} else {
		echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Down for Maintenance</strong><br>One or more required files are missing. If an install or upgrade is not currently in progress, please contact the system administrator.</div>";
		exit();
	}
}
function getValidLanguages($admin = '') {
	$langs = WHMCS_Application::getinstance()->getValidLanguages($admin);
	return $langs;
}
function htmlspecialchars_array($arr) {
	return WHMCS_Application::getinstance()->sanitize_input_vars($arr);
}
gracefulCoreRequiredFileInclude("/includes/classes/WHMCS/Init.php");
gracefulCoreRequiredFileInclude("/includes/classes/WHMCS/Application.php");
gracefulCoreRequiredFileInclude("/includes/classes/WHMCS/Terminus.php");
spl_autoload_register(array(
	'WHMCS_Application',
	'loadClass'
));
$terminus = WHMCS_Terminus::getinstance();
gracefulCoreRequiredFileInclude("/includes/dbfunctions.php");
gracefulCoreRequiredFileInclude("/includes/functions.php");
if(!defined('WHMCSDBCONNECT')) {
	if(defined('CLIENTAREA')) {
		gracefulCoreRequiredFileInclude("/includes/clientareafunctions.php");
	}
	if(defined('ADMINAREA') || defined('MOBILEEDITION')) {
		gracefulCoreRequiredFileInclude("/includes/adminfunctions.php");
	}
}
$whmcs                = WHMCS_Application::getinstance();
$whmcsAppConfig       = $whmcs->getApplicationConfig();
$templates_compiledir = $whmcsAppConfig['templates_compiledir'];
$downloads_dir        = $whmcsAppConfig['downloads_dir'];
$attachments_dir      = $whmcsAppConfig['attachments_dir'];
$customadminpath      = $whmcsAppConfig['customadminpath'];
if(function_exists('mb_internal_encoding')) {
	$characterSet = $whmcs->get_config('Charset') == '' ? 'UTF-8' : $whmcs->get_config('Charset');
	mb_internal_encoding($characterSet);
}
$previousVersion = new WHMCS_Version_SemanticVersion('5.3.12-release.1');
if(WHMCS_Version_SemanticVersion::compare($whmcs->getDBVersion(), $previousVersion, "==")) {
	$whmcs->set_config('Version', $whmcs->getVersion()->getCanonical());
	$messageHash   = '6dd1a70917ebbed0ed5681f1c9fe7e5a';
	$query         = "SELECT md5(`message`) as message FROM tblemailtemplates WHERE `name` = 'Expired Domain Notice' AND `language` = '';";
	$result        = mysql_query($query);
	$data          = mysql_fetch_assoc($result);
	$storedMessage = get_query_val('tblemailtemplates', "md5(`message`)", array(
		'name' => 'Expired Domain Notice',
		'language' => ''
	));
	if($storedMessage == $messageHash) {
		$message = "&lt;p&gt;Dear {\$client_name},&lt;/p&gt;
&lt;p&gt;The domain name listed below expired {\$domain_days_after_expiry} days ago.&lt;/p&gt;
&lt;p&gt;{\$domain_name}&lt;/p&gt;
&lt;p&gt;To ensure that the domain isn&#39;t registered by someone else, you should renew it now. To renew the domain, please visit the following page and follow the steps shown: &lt;a title=&quot;{\$whmcs_url}/cart.php?gid=renewals&quot; href=&quot;{\$whmcs_url}/cart.php?gid=renewals&quot;&gt;{\$whmcs_url}/cart.php?gid=renewals&lt;/a&gt;&lt;/p&gt;
&lt;p&gt;Due to the domain expiring, the domain will not be accessible so any web site or email services associated with it will stop working. You may be able to renew it for up to 30 days after the renewal date.&lt;/p&gt;
&lt;p&gt;{\$signature}&lt;/p&gt;";
		$query   = "UPDATE tblemailtemplates SET message = '" . $message . "' WHERE `name` = 'Expired Domain Notice' AND language = '';";
		full_query($query);
	}
}
$previousVersion = new WHMCS_Version_SemanticVersion("5.3.13-release.1");
if(WHMCS_Version_SemanticVersion::compare($whmcs->getDBVersion(), $previousVersion, "==")) {
	$whmcs->set_config('Version', $whmcs->getVersion()->getCanonical());
}
if($whmcs->doFileAndDBVersionsNotMatch()) {
	if(file_exists("../install/install.php")) {
		header("Location: ../install/install.php");
		exit();
	}
	echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Down for Maintenance (Err 2)</strong><br>An upgrade is currently in progress... Please come back soon...</div>";
	exit();
}
$licensing = WHMCS_License::getinstance();
if($licensing->getVersionHash() != '7a1bbff560de83ab800c4d1d2f215b91006be8e6') {
	$terminus->doDie("License Checking Error");
} elseif(empty($CONFIG['License'])) {
	/* Team ECHO : Ensure we are brandfree. */
	$licensing->forceRemoteCheck();
}
if(file_exists(ROOTDIR . "/install/install.php")) {
	echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Security Warning</strong><br>The install folder needs to be deleted for security reasons before using WHMCS</div>";
	exit();
}
if(defined('ADMINAREA') && !defined('MOBILEEDITION')) {
	$currentDirectoryPath      = dirname($whmcs->getPhpSelf());
	$currentDirectoryPathParts = explode('/', $currentDirectoryPath);
	$currentDir                = array_pop($currentDirectoryPathParts);
	$AppConfig                 = $whmcs->getApplicationConfig();
	$configuredAdminDir        = $AppConfig['customadminpath'];
	$adminDirErrorMsg          = '';
	if($configuredAdminDir == 'admin' && $currentDir != $configuredAdminDir) {
		$adminDirErrorMsg = "You are attempting to access the admin area via a directory that is not configured. Please either revert to the default admin directory name, or see our documentation for <a href=\"http://nullrefer.com/?http://docs.whmcs.com/Customising_the_Admin_Directory\" target=\"_blank\">Customising the Admin Directory</a>.";
	} else {
		if($currentDir != $configuredAdminDir) {
			$adminDirErrorMsg = "You are attempting to access the admin area via a directory that is different from the one configured. Please refer to the <a href=\"http://nullrefer.com/?http://docs.whmcs.com/Customising_the_Admin_Directory\" target=\"_blank\">Customising the Admin Directory</a> documentation for instructions on how to update it.";
		} else {
			if($configuredAdminDir != 'admin' && is_dir(ROOTDIR . DIRECTORY_SEPARATOR . 'admin')) {
				$adminDirErrorMsg = "You are attempting to access the admin area via a custom directory, but we have detected the presence of a default \"admin\" directory too. This could indicate files from a recent update have been uploaded to the default admin path location instead of the custom one, resulting in these files being out of date. Please ensure your custom admin folder contains all the latest files, and delete the default admin directory to continue.";
			}
		}
	}
	if($adminDirErrorMsg) {
		throw new WHMCS_Exception_Fatal("<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Admin Directory Conflict</strong><br />" . $adminDirErrorMsg . "</div>");
	}
}
if(!$whmcs->check_template_cache_writeable()) {
	exit("<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Permissions Error</strong><br>The templates compiling directory '" . $whmcs->get_template_compiledir_name() . "' must be writeable (CHMOD 777) before you can continue.<br>If the path shown is incorrect, you can update it in the configuration.php file.</div>");
}
if(defined('CLIENTAREA') && $CONFIG['MaintenanceMode'] && !$_SESSION['adminid']) {
	if($CONFIG['MaintenanceModeURL']) {
		header("Location: " . $CONFIG['MaintenanceModeURL']);
		exit();
	}
	echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Down for Maintenance (Err 3)</strong><br>" . $CONFIG['MaintenanceModeMessage'] . "</div>";
	exit();
}
if(defined('CLIENTAREA') && isset($_SESSION['uid']) && !isset($_SESSION['adminid'])) {
	$twofa = new WHMCS_2FA();
	$twofa->setClientID($_SESSION['uid']);
	if($twofa->isForced() && !$twofa->isEnabled() && $twofa->isActiveClients()) {
		if($whmcs->get_filename() == 'clientarea' && ($whmcs->get_req_var('action') == 'security' || $whmcs->get_req_var('2fasetup'))) {
		} else {
			redir("action=security&2fasetup=1&enforce=1", "clientarea.php");
		}
	}
}
if(isset($_SESSION['currency']) && is_array($_SESSION['currency'])) {
	$_SESSION['currency'] = $_SESSION['currency']['id'];
}
if(!isset($_SESSION['uid']) && isset($_REQUEST['currency'])) {
	$result = select_query('tblcurrencies', 'id', array(
		'id' => (int) $_REQUEST['currency']
	));
	$data   = mysql_fetch_array($result);
	if($data['id']) {
		$_SESSION['currency'] = $data['id'];
	}
}
if(defined('CLIENTAREA') && isset($_REQUEST['language'])) {
	$whmcs->set_client_language($_REQUEST['language']);
}
$whmcs->loadLanguage();
if(defined('CLIENTAREA') && $whmcs->isSSLAvailable()) {
	if(WHMCS_Session::get('FORCESSL') && $whmcs->getCurrentFilename() == 'index') {
		define('FORCESSL', true);
	}
	$reqvars = $_REQUEST;
	if(array_key_exists('token', $reqvars)) {
		unset($reqvars['token']);
	}
	if($whmcs->shouldSSLBeForcedForCurrentPage() || defined('FORCESSL')) {
		if(!$whmcs->in_ssl()) {
			$whmcs->redirectSystemSSLURL($whmcs->getCurrentFilename(false), $reqvars);
		}
	} else {
		if($whmcs->shouldNonSSLBeForcedForCurrentPage() && $whmcs->in_ssl()) {
			$whmcs->redirectSystemURL($whmcs->getCurrentFilename(false), $reqvars);
		}
	}
}
load_hooks();