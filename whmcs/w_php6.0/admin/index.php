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
define('ADMINAREA', true);
require('../init.php');
function load_admin_home_widgets() {
	global $aInt;
	global $hooks;
	global $allowedwidgets;
	global $jquerycode;
	global $jscode;
	if(!is_array($hooks)) {
		if(defined('HOOKSLOGGING')) {
			logActivity(sprintf("Hooks Debug: Hook File: the hooks list has been mutated to %s", ucfirst(gettype($hooks))));
		}
		$hooks = array();
	}
	$hookjquerycode = '';
	$hook_name      = 'AdminHomeWidgets';
	$allowedwidgets = explode(',', $allowedwidgets);
	$args           = array(
		'adminid' => $_SESSION['adminid'],
		'loading' => "<img src=\"images/loading.gif\" align=\"absmiddle\" /> " . $aInt->lang('global', 'loading')
	);
	if(!array_key_exists($hook_name, $hooks)) {
		return array();
	}
	reset($hooks[$hook_name]);
	$results = array();
	while(list($key, $hook) = each($hooks[$hook_name])) {
		$widgetname = substr($hook['hook_function'], 7);
		if(in_array($widgetname, $allowedwidgets) && function_exists($hook['hook_function'])) {
			$res = call_user_func($hook['hook_function'], $args);
			if(is_array($res)) {
				if(array_key_exists('jquerycode', $res)) {
					$hookjquerycode .= $res['jquerycode'] . "\n";
				}
				if(array_key_exists('jscode', $res)) {
					$jscode .= $res['jscode'] . "\n";
				}
				$results[] = array_merge(array(
					'name' => $widgetname
				), $res);
			}
		}
	}
	$jquerycode .= "setTimeout(function(){
        " . $hookjquerycode . "
    }, 4000);";
	return $results;
}
if(!function_exists('curl_init')) {
	echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>Critical Error</strong><br>CURL is not installed or is disabled on your server and it is required for WHMCS to run</div>";
	exit();
}
if(!$licensing->checkOwnedUpdates()) {
	redir("licenseerror=version", "licenseerror.php");
}
if(!checkPermission("Main Homepage", true) && checkPermission("Support Center Overview", true)) {
	redir('', "supportcenter.php");
}
$aInt          = new WHMCS_Admin("Main Homepage");
$aInt->title   = $aInt->lang('global', 'hometitle');
$aInt->sidebar = 'home';
$aInt->icon    = 'home';
$aInt->requiredFiles(array(
	'clientfunctions',
	'invoicefunctions',
	'gatewayfunctions',
	'ccfunctions',
	'processinvoices',
	'reportfunctions'
));
$aInt->template = 'homepage';
$chart          = new WHMCSChart();
$action         = $whmcs->get_req_var('action');
if($whmcs->get_req_var('createinvoices') || $whmcs->get_req_var('generateinvoices')) {
	check_token("WHMCS.admin.default");
	checkPermission("Generate Due Invoices");
	createInvoices('', $noemails);
	redir("generatedinvoices=1&count=" . $invoicecount);
}
if($whmcs->get_req_var('generatedinvoices')) {
	infoBox($aInt->lang('invoices', 'gencomplete'), (int) $whmcs->get_req_var('count') . " Invoices Created");
}
if($whmcs->get_req_var('attemptccpayments')) {
	check_token("WHMCS.admin.default");
	checkPermission("Attempts CC Captures");
	$session = new WHMCS_Session();
	$session->create();
	WHMCS_Session::set('AdminHomeCCCaptureResultMsg', ccProcessing());
	redir("attemptedccpayments=1");
}
if($whmcs->get_req_var('attemptedccpayments') && WHMCS_Session::get('AdminHomeCCCaptureResultMsg')) {
	infoBox($aInt->lang('invoices', 'attemptcccapturessuccess'), WHMCS_Session::get('AdminHomeCCCaptureResultMsg'));
	$session = new WHMCS_Session();
	$session->create();
	WHMCS_Session::delete('AdminHomeCCCaptureResultMsg');
}
if($action == 'savenotes') {
	check_token("WHMCS.admin.default");
	update_query('tbladmins', array(
		'notes' => $notes
	), array(
		'id' => $_SESSION['adminid']
	));
	redir();
}
/*
if($whmcs->get_req_var('infopopup')) {
	$valuesToSend = array(
		'licensekey' => $whmcs->get_license_key(),
		'version' => $whmcs->getVersion()->getCanonical(),
		'ssl' => $whmcs->in_ssl(),
		'phpversion' => PHP_VERSION,
		'extension_pdo' => extension_loaded('pdo'),
		'extension_pdomysql' => extension_loaded('pdo_mysql'),
		'memory_limit' => ini_get('memory_limit'),
		'config_fileperms' => fileperms(ROOTDIR . DIRECTORY_SEPARATOR . "configuration.php"),
		'writeabledirs_moved' => $whmcs->getApplicationConfig()->hasCustomWritableDirectories()
	);
	$data         = curlCall("https://updates.whmcs.com/feeds/homepopup.php", $valuesToSend);
	if(substr($data, 0, 2) != 'ok') {
		exit("<div class=\"content\" style=\"text-align:center;padding-top:80px;\">A connection error occurred. Please try again later.</div>");
	}
	echo substr($data, 2);
	throw new WHMCS_Exception_Exit();
}
if($whmcs->get_req_var('toggleinfopopup')) {
	check_token("WHMCS.admin.default");
	$infotoggle = unserialize($whmcs->get_config('ToggleInfoPopup'));
	if(!is_array($infotoggle)) {
		$infotoggle = array();
	}
	if($showhide == 'true') {
		$infotoggle[$_SESSION['adminid']] = curlCall("http://updates.whmcs.com/feeds/homepopup.php", "lastupdate=1", array(
			'CURLOPT_TIMEOUT' => '5'
		));
	} else {
		if($showhide == 'false') {
			unset($infotoggle[$_SESSION['adminid']]);
		}
	}
	$whmcs->set_config('ToggleInfoPopup', serialize($infotoggle));
	exit();
}
*/
if($whmcs->get_req_var('saveorder')) {
	check_token("WHMCS.admin.default");
	update_query('tbladmins', array(
		'homewidgets' => $widgetdata
	), array(
		'id' => $_SESSION['adminid']
	));
	exit();
}
if($whmcs->get_req_var('dismissgs')) {
	$roleid  = get_query_val('tbladmins', 'roleid', array(
		'id' => $_SESSION['adminid']
	));
	$result  = select_query('tbladminroles', 'widgets', array(
		'id' => $roleid
	));
	$data    = mysql_fetch_array($result);
	$widgets = $data['widgets'];
	$widgets = explode(',', $widgets);
	foreach($widgets as $k => $v) {
		if($v == 'getting_started') {
			unset($widgets[$k]);
		}
	}
	update_query('tbladminroles', array(
		'widgets' => implode(',', $widgets)
	), array(
		'id' => $roleid
	));
	exit();
}
if($whmcs->get_req_var('getincome')) {
	check_token("WHMCS.admin.default");
	if(!checkPermission("View Income Totals", true)) {
		return false;
	}
	$stats = getAdminHomeStats('income');
	echo "<a href=\"transactions.php\"><img src=\"images/icons/transactions.png\" align=\"absmiddle\" border=\"0\"> <b>" . $aInt->lang('billing', 'income') . "</b></a> " . $aInt->lang('billing', 'incometoday') . ": <span class=\"textgreen\"><b>" . $stats['income']['today'] . "</b></span> " . $aInt->lang('billing', 'incomethismonth') . ": <span class=\"textred\"><b>" . $stats['income']['thismonth'] . "</b></span> " . $aInt->lang('billing', 'incomethisyear') . ": <span class=\"textblack\"><b>" . $stats['income']['thisyear'] . "</b></span>";
	exit();
}
$templatevars['licenseinfo'] = array(
	'registeredname' => $licensing->getRegisteredName(),
	'productname' => $licensing->getProductName(),
	'expires' => $licensing->getExpiryDate(),
	'currentversion' => $whmcs->getVersion()->getCasual(),
	'latestversion' => $licensing->getLatestVersion()->getCasual(),
	'updateavailable' => $licensing->isUpdateAvailable()
);
if($licensing->getKeyData('productname') == "15 Day Free Trial") {
	$templatevars['freetrial'] = true;
}
$templatevars['infobox'] = $infobox;
$query                   = "SELECT COUNT(*) FROM tblpaymentgateways WHERE setting='type' AND value='CC'";
$result                  = full_query($query);
$data                    = mysql_fetch_array($result);
if($data[0]) {
	$templatevars['showattemptccbutton'] = true;
}
if($CONFIG['MaintenanceMode']) {
	$templatevars['maintenancemode'] = true;
}
$jquerycode     = "\$( \".homecolumn\" ).sortable({
    handle : '.widget-header',
    connectWith: ['.homecolumn'],
    stop: function() { saveHomeWidgets(); }
});
\$( \".homewidget\" ).find( \".widget-header\" ).prepend( \"<span class='ui-icon ui-icon-minusthick'></span>\");
resHomeWidgets();
\$( \".widget-header .ui-icon\" ).click(function() {
    \$( this ).toggleClass( \"ui-icon-minusthick\" ).toggleClass( \"ui-icon-plusthick\" );
    \$( this ).parents( \".homewidget:first\" ).find( \".widget-content\" ).toggle();
    saveHomeWidgets();
});
";
$data           = get_query_vals('tbladmins', "tbladmins.homewidgets,tbladminroles.widgets", array(
	"tbladmins.id" => $_SESSION['adminid']
), '', '', '', "tbladminroles ON tbladminroles.id=tbladmins.roleid");
$homewidgets    = $data['homewidgets'];
$allowedwidgets = $data['widgets'];
if(!$homewidgets) {
	$homewidgets = "getting_started:true,system_overview:true,income_overview:true,client_activity:true,admin_activity:true,activity_log:true|my_notes:true,orders_overview:true,sysinfo:true,whmcs_news:true,network_status:true,todo_list:true,income_forecast:true,open_invoices:true";
}
$homewidgets     = explode("|", $homewidgets);
$homewidgetscol1 = explode(',', $homewidgets[0]);
foreach($homewidgetscol1 as $k => $v) {
	$v = explode(":", $v);
	if(!$v[0]) {
		unset($homewidgetscol1[$k]);
	}
}
$homewidgetscol1 = implode(',', $homewidgetscol1);
$homewidgetscol2 = explode(',', $homewidgets[1]);
foreach($homewidgetscol2 as $k => $v) {
	$v = explode(":", $v);
	if(!$v[0]) {
		unset($homewidgetscol2[$k]);
	}
}
$homewidgetscol2 = implode(',', $homewidgetscol2);
$jscode          = "var savedOrders = new Array();
savedOrders[1] = \"" . $homewidgetscol1 . "\";
savedOrders[2] = \"" . $homewidgetscol2 . "\";
function saveHomeWidgets() {
    var orderdata = '';
    \$(\".homecolumn\").each(function(index, value){
        var colid = value.id;
        var order = \$(\"#\"+colid).sortable(\"toArray\");
        for ( var i = 0, n = order.length; i < n; i++ ) {
            var v = \$('#' + order[i] ).find('.widget-content').is(':visible');
            order[i] = order[i] + \":\" + v;
        }
        orderdata = orderdata + order + \"|\";
    });";
if($aInt->chartFunctions) {
	$jscode .= "redrawCharts()";
}
$csrfToken = generate_token('plain');
$jscode .= "    \$.post(\"index.php\", { saveorder: \"1\", widgetdata: orderdata, token: \"" . $csrfToken . "\" });
}
function resHomeWidgets() {
    var IDs = '';
    var IDsp = '';
    var widgetID = '';
    var visible = '';
    var widget = '';
    for (var z = 1, y = 2; z <= y; z++ ) {
        if (savedOrders[z]) {
            IDs = savedOrders[z].split(',');
            for (var i = 0, n = IDs.length; i < n; i++ ) {
                IDsp = (IDs[i].split(':'));
                widgetID = IDsp[0];
                visible = IDsp[1];
                widget = \$(\".homecolumn\").find('#' + widgetID).appendTo(\$('#homecol'+z));
                if (visible === 'false') {
                    widget.find(\".ui-icon\").toggleClass( \"ui-icon-minusthick\" ).toggleClass( \"ui-icon-plusthick\" );
                    widget.find(\".widget-content\").hide();
                }
            }
        }
    }
}";
$hooksdir = ROOTDIR . '/modules/widgets/';
if(is_dir($hooksdir)) {
	$dh = opendir($hooksdir);
	while(false !== ($hookfile = readdir($dh))) {
		if(is_file($hooksdir . $hookfile) && $hookfile != "index.php") {
			$extension = explode(".", $hookfile);
			$extension = end($extension);
			if($extension == 'php') {
				include($hooksdir . $hookfile);
			}
		}
	}
}
closedir($dh);
$templatevars['widgets'] = load_admin_home_widgets();
if(checkPermission("View Income Totals", true)) {
	$templatevars['viewincometotals'] = true;
	$jquerycode .= "jQuery.post(\"index.php\", { getincome: 1, token: \"" . generate_token('plain') . "\" },
    function(data){
        jQuery(\"#incometotals\").html(data);
    });";
}
$invoicedialog               = $aInt->jqueryDialog('geninvoices', $aInt->lang('invoices', 'geninvoices'), $aInt->lang('invoices', 'geninvoicessendemails'), array(
	$aInt->lang('global', 'yes') => "window.location='index.php?generateinvoices=true" . generate_token('link') . "'",
	$aInt->lang('global', 'no') => "window.location='index.php?generateinvoices=true&noemails=true" . generate_token('link') . "'"
));
$cccapturedialog             = $aInt->jqueryDialog('cccapture', $aInt->lang('invoices', 'attemptcccaptures'), $aInt->lang('invoices', 'attemptcccapturessure'), array(
	$aInt->lang('global', 'yes') => "window.location='index.php?attemptccpayments=true" . generate_token('link') . "'",
	$aInt->lang('global', 'no') => ''
));
$addons_html                 = run_hook('AdminHomepage', array());
$templatevars['addons_html'] = $addons_html;
/*
if(get_query_val('tbladmins', 'roleid', array(
	'id' => (int) $_SESSION['adminid']
)) == 1) {
	$infotoggle = unserialize($whmcs->get_config('ToggleInfoPopup'));
	if(!is_array($infotoggle)) {
		$infotoggle = array();
	}
	$showdialog = true;
	if(!empty($infotoggle[$_SESSION['adminid']])) {
		$dismissdate = $infotoggle[$_SESSION['adminid']];
		$lastupdate  = curlCall("http://updates.whmcs.com/feeds/homepopup.php", "lastupdate=1", array(
			'CURLOPT_TIMEOUT' => '5'
		));
		if($dismissdate < $lastupdate) {
			unset($infotoggle[$_SESSION['adminid']]);
			$whmcs->set_config('ToggleInfoPopup', serialize($infotoggle));
		} else {
			$showdialog = false;
		}
	}
	if($showdialog) {
		$aInt->dialog('infopopup');
		$jquerycode .= "dialogOpen();";
		$jscode .= "function toggleInfoPopup() { jQuery.post(\"index.php\", \"toggleinfopopup=1" . generate_token('link') . "&showhide=\"+\$(\"#toggleinfocb\").is(\":checked\")); }";
	}
}
*/
$licensing = WHMCS_License::getinstance();
if($licensing->isClientLimitsEnabled()) {
	$templatevars['licenseinfo']['productname'] .= " (" . $licensing->getTextClientLimit($aInt) . ")";
	if($licensing->isNearClientLimit()) {
		$clientLimit = $licensing->getClientLimit();
		if($licensing->getNumberOfActiveClients() < $clientLimit) {
			$warningMsg = "You currently have " . $licensing->getNumberOfActiveClients() . " Active clients of a total " . $licensing->getTextClientLimit($aInt) . " Active clients permitted by your current license. <a href=\"systemlicense.php\">Upgrade now as your business grows!</a>";
		} else {
			if($clientLimit == $licensing->getNumberOfActiveClients()) {
				$warningMsg = "You are at " . $licensing->getNumberOfActiveClients() . " Active clients which is the maximum permitted by your current license. <a href=\"systemlicense.php\">Upgrade now as your business grows!</a>";
			} else {
				$warningMsg = "You are " . ($licensing->getNumberOfActiveClients() - $clientLimit) . " Active clients over the " . $licensing->getTextClientLimit($aInt) . " maximum Active clients permitted by your current license. <a href=\"systemlicense.php\">Upgrade now for your business needs!</a>";
			}
		}
		if(!is_array($addons_html)) {
			$addons_html = array();
		}
		$templatevars['addons_html'] = array_merge(array(
			"<div style=\"background-color:#FFBFBF;padding:10px;margin:0 0 10px;text-align:center;color:#7F0000;\">" . $warningMsg . "</div>"
		), $addons_html);
	}
}
$aInt->jscode       = $jscode;
$aInt->jquerycode   = $jquerycode;
$aInt->templatevars = $templatevars;
$aInt->display();