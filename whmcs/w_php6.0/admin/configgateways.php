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
function defineGatewayField($gateway, $type, $name, $defaultvalue, $friendlyname, $size, $description) {
	global $GatewayFieldDefines;
	if($type == 'dropdown') {
		$options     = $description;
		$description = '';
	} else {
		$options = '';
	}
	$GatewayFieldDefines[$name] = array(
		'FriendlyName' => $friendlyname,
		'Type' => $type,
		'Size' => $size,
		'Description' => $description,
		'Value' => $defaultvalue,
		'Options' => $options
	);
}
$aInt           = new WHMCS_Admin("Configure Payment Gateways");
$aInt->title    = $aInt->lang('setup', 'gateways');
$aInt->sidebar  = 'config';
$aInt->icon     = 'offlinecc';
$aInt->helplink = "Payment Gateways";
$aInt->requiredFiles(array(
	'gatewayfunctions',
	'modulefunctions'
));
$GatewayValues = $GatewayConfig = $ActiveGateways = $DisabledGateways = array();
$result        = select_query('tblpaymentgateways', '', '', 'setting', 'ASC');
while($data = mysql_fetch_array($result)) {
	$gwv_gateway                               = $data['gateway'];
	$gwv_setting                               = $data['setting'];
	$gwv_value                                 = $data['value'];
	$GatewayValues[$gwv_gateway][$gwv_setting] = $gwv_value;
}
$includedmodules = array();
$dh              = opendir("../modules/gateways/");
while(false !== ($file = readdir($dh))) {
	$fileext = explode(".", $file, 2);
	if(trim($file) && $file != "index.php" && $fileext[1] == 'php' && !in_array($fileext[0], $includedmodules)) {
		$includedmodules[] = $fileext[0];
		$gwv_modulename    = $fileext[0];
		if(!isValidforPath($gwv_modulename)) {
			exit("Invalid Gateway Module Name");
		}
		require_once(ROOTDIR . '/modules/gateways/' . $gwv_modulename . ".php");
		if(isset($GatewayValues[$gwv_modulename]['type'])) {
			$ActiveGateways[] = $gwv_modulename;
		} else {
			$DisabledGateways[] = $gwv_modulename;
		}
		if(function_exists($gwv_modulename . '_config')) {
			$GatewayConfig[$gwv_modulename] = call_user_func($gwv_modulename . '_config');
		} else {
			$GatewayFieldDefines                 = array();
			$GatewayFieldDefines['FriendlyName'] = array(
				'Type' => 'System',
				'Value' => $GATEWAYMODULE[$gwv_modulename . 'visiblename']
			);
			if($GATEWAYMODULE[$gwv_modulename . 'notes']) {
				$GatewayFieldDefines['UsageNotes'] = array(
					'Type' => 'System',
					'Value' => $GATEWAYMODULE[$gwv_modulename . 'notes']
				);
			}
			call_user_func($gwv_modulename . '_activate');
			$GatewayConfig[$gwv_modulename] = $GatewayFieldDefines;
		}
	}
}
closedir($dh);
$result    = select_query('tblpaymentgateways', '', '', 'order', 'DESC');
$data      = mysql_fetch_array($result);
$lastorder = $data['order'];
if($action == 'activate' && in_array($gateway, $includedmodules)) {
	check_token("WHMCS.admin.default");
	delete_query('tblpaymentgateways', array(
		'gateway' => $gateway
	));
	$lastorder++;
	$type = 'Invoices';
	if(function_exists($gateway . '_capture')) {
		$type = 'CC';
	}
	insert_query('tblpaymentgateways', array(
		'gateway' => $gateway,
		'setting' => 'name',
		'value' => $GatewayConfig[$gateway]['FriendlyName']['Value'],
		'order' => $lastorder
	));
	if($GatewayConfig[$gateway]['RemoteStorage']) {
		insert_query('tblpaymentgateways', array(
			'gateway' => $gateway,
			'setting' => 'remotestorage',
			'value' => '1'
		));
	}
	insert_query('tblpaymentgateways', array(
		'gateway' => $gateway,
		'setting' => 'type',
		'value' => $type
	));
	insert_query('tblpaymentgateways', array(
		'gateway' => $gateway,
		'setting' => 'visible',
		'value' => 'on'
	));
	redir("activated=true");
}
if($action == 'deactivate' && in_array($newgateway, $includedmodules)) {
	check_token("WHMCS.admin.default");
	if($gateway != $newgateway) {
		update_query('tblhosting', array(
			'paymentmethod' => $newgateway
		), array(
			'paymentmethod' => $gateway
		));
		update_query('tblhostingaddons', array(
			'paymentmethod' => $newgateway
		), array(
			'paymentmethod' => $gateway
		));
		update_query('tbldomains', array(
			'paymentmethod' => $newgateway
		), array(
			'paymentmethod' => $gateway
		));
		update_query('tblinvoices', array(
			'paymentmethod' => $newgateway
		), array(
			'paymentmethod' => $gateway
		));
		update_query('tblorders', array(
			'paymentmethod' => $newgateway
		), array(
			'paymentmethod' => $gateway
		));
		update_query('tblaccounts', array(
			'gateway' => $newgateway
		), array(
			'gateway' => $gateway
		));
		delete_query('tblpaymentgateways', array(
			'gateway' => $gateway
		));
		redir("deactivated=true");
	} else {
		redir();
	}
	exit();
}
if($action == 'save' && in_array($module, $includedmodules)) {
	check_token("WHMCS.admin.default");
	$GatewayConfig[$module]['visible']   = array(
		'Type' => 'yesno'
	);
	$GatewayConfig[$module]['name']      = array(
		'Type' => 'text'
	);
	$GatewayConfig[$module]['convertto'] = array(
		'Type' => 'text'
	);
	foreach($GatewayConfig[$module] as $confname => $values) {
		if($values['Type'] != 'System') {
			$result = select_query('tblpaymentgateways', "COUNT(*)", array(
				'gateway' => $module,
				'setting' => $confname
			));
			$data   = mysql_fetch_array($result);
			$count  = $data[0];
			if($count) {
				update_query('tblpaymentgateways', array(
					'value' => WHMCS_Input_Sanitize::decode(trim($field[$confname]))
				), array(
					'gateway' => $module,
					'setting' => $confname
				));
			} else {
				insert_query('tblpaymentgateways', array(
					'gateway' => $module,
					'setting' => $confname,
					'value' => WHMCS_Input_Sanitize::decode(trim($field[$confname]))
				));
			}
		}
	}
	redir("updated=true");
}
if($action == 'moveup') {
	check_token("WHMCS.admin.default");
	$result  = select_query('tblpaymentgateways', '', array(
		"`order`" => $order
	));
	$data    = mysql_fetch_array($result);
	$gateway = $data['gateway'];
	$order1  = $order - 1;
	update_query('tblpaymentgateways', array(
		'order' => $order
	), array(
		"`order`" => $order1
	));
	update_query('tblpaymentgateways', array(
		'order' => $order1
	), array(
		'gateway' => $gateway
	));
	redir();
}
if($action == 'movedown') {
	check_token("WHMCS.admin.default");
	$result  = select_query('tblpaymentgateways', '', array(
		"`order`" => $order
	));
	$data    = mysql_fetch_array($result);
	$gateway = $data['gateway'];
	$order1  = $order + 1;
	update_query('tblpaymentgateways', array(
		'order' => $order
	), array(
		"`order`" => $order1
	));
	update_query('tblpaymentgateways', array(
		'order' => $order1
	), array(
		'gateway' => $gateway
	));
	redir();
}
$result = select_query('tblcurrencies', 'id,code', '', 'code', 'ASC');
for($i = 0; $currenciesarray[$i] = mysql_fetch_assoc($result); $i++);
array_pop($currenciesarray);
ob_start();
if($activated) {
	infoBox($aInt->lang('global', 'success'), $aInt->lang('gateways', 'activatesuccess'));
}
if($deactivated) {
	infoBox($aInt->lang('global', 'success'), $aInt->lang('gateways', 'deactivatesuccess'));
}
if($updated) {
	infoBox($aInt->lang('global', 'success'), $aInt->lang('gateways', 'savesuccess'));
}
echo $infobox;
echo "<p>" . $aInt->lang('gateways', 'intro') . " <a href=\"http://nullrefer.com/?http://docs.whmcs.com/Creating_Modules\" target=\"_blank\">docs.whmcs.com/Creating_Modules</a></p>";
echo "
<p>";
echo "<form id=\"frmActivateGatway\" method=\"post\" action=\"" . $whmcs->getPhpSelf() . "\"><input type=\"hidden\" name=\"action\" value=\"activate\"><b>" . $aInt->lang('gateways', 'activatemodule') . ":</b> ";
if(0 < count($DisabledGateways)) {
	$AlphaDisabled = array();
	foreach($DisabledGateways as $modulename) {
		$AlphaDisabled[$GatewayConfig[$modulename]['FriendlyName']['Value']] = $modulename;
	}
	ksort($AlphaDisabled);
	echo "<select name=\"gateway\">";
	foreach($AlphaDisabled as $displayname => $modulename) {
		echo "<option value=\"" . $modulename . "\">" . $displayname . "</option>";
	}
	echo "</select> <input type=\"submit\" value=\"" . $aInt->lang('gateways', 'activate') . "\">";
} else {
	echo $aInt->lang('gateways', 'nodisabledgateways');
}
echo "</form></p>

";
$count       = 1;
$newgateways = '';
$data        = get_query_vals('tblpaymentgateways', "COUNT(gateway)", array(
	'setting' => 'name'
));
$numgateways = $data[0];
$result3     = select_query('tblpaymentgateways', '', array(
	'setting' => 'name'
), 'order', 'ASC');
while($data = mysql_fetch_array($result3)) {
	$module = $data['gateway'];
	$order  = $data['order'];
	echo "
<form id=\"frmActivateGatway\" method=\"post\" action=\"";
	echo $whmcs->getPhpSelf();
	echo "?action=save\">
<input type=\"hidden\" name=\"module\" value=\"";
	echo $module;
	echo "\">

<p align=\"left\"><b>";
	$isModuleDisabled = false;
	if(isset($GatewayConfig[$module])) {
		$modName = $GatewayConfig[$module]['FriendlyName']['Value'];
	} else {
		$modName          = $module;
		$isModuleDisabled = true;
	}
	echo $count . ". " . $modName;
	if($numgateways != '1') {
		echo " <a href=\"#\" onclick=\"deactivateGW('" . $module . "','" . $GatewayConfig[$module]['FriendlyName']['Value'] . "');return false\" style=\"color:#cc0000\">(" . $aInt->lang('gateways', 'deactivate') . ")</a> ";
	}
	echo "</b>";
	if($order != '1') {
		echo "<a href=\"?action=moveup&order=" . $order . generate_token('link') . "\"><img src=\"images/moveup.gif\" align=\"absmiddle\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"></a> ";
	}
	if($order != $lastorder) {
		echo "<a href=\"?action=movedown&order=" . $order . generate_token('link') . "\"><img src=\"images/movedown.gif\" align=\"absmiddle\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"></a>";
	}
	if($isModuleDisabled === true) {
		echo "\t<p style=\"border: 2px solid red; padding: 10px\"><strong>";
		echo $aInt->lang('gateways', 'moduleunavailable');
		echo "</strong></p>
";
	} else {
		echo "</p>
<table class=\"form\" id=\"Payment-Gateway-Config-";
		echo $module;
		echo "\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
<tr><td width=\"200\" class=\"fieldlabel\">";
		echo $aInt->lang('gateways', 'showonorderform');
		echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"field[visible]\"";
		if($GatewayValues[$module]['visible']) {
			echo " checked";
		}
		echo " /></td></tr>
<tr><td class=\"fieldlabel\">";
		echo $aInt->lang('gateways', 'displayname');
		echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"field[name]\" size=\"30\" value=\"";
		echo htmlspecialchars($GatewayValues[$module]['name']);
		echo "\"></td></tr>
";
		foreach($GatewayConfig[$module] as $confname => $values) {
			if($values['Type'] != 'System') {
				$values['Name'] = "field[" . $confname . "]";
				if(isset($GatewayValues[$module][$confname])) {
					$values['Value'] = $GatewayValues[$module][$confname];
				}
				echo "<tr><td class=\"fieldlabel\">" . $values['FriendlyName'] . "</td><td class=\"fieldarea\">" . moduleConfigFieldOutput($values) . "</td></tr>";
			}
		}
		if(1 < count($currenciesarray)) {
			echo "<tr><td class=\"fieldlabel\">" . $aInt->lang('gateways', 'currencyconvert') . "</td><td class=\"fieldarea\"><select name=\"field[convertto]\"><option value=\"\">" . $aInt->lang('global', 'none') . "</option>";
			foreach($currenciesarray as $currencydata) {
				echo "<option value=\"" . $currencydata['id'] . "\"";
				if($currencydata['id'] == $GatewayValues[$module]['convertto']) {
					echo " selected";
				}
				echo ">" . $currencydata['code'] . "</option>";
			}
			echo "</select></td></tr>";
		}
		echo "<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\"><input type=\"submit\" value=\"";
		echo $aInt->lang('global', 'savechanges');
		echo "\">";
		if($GatewayConfig[$module]['UsageNotes']['Value']) {
			echo " (" . $GatewayConfig[$module]['UsageNotes']['Value'] . ")";
		}
		echo "</td></tr>
</table>
";
	}
	echo "<br />

</form>

";
	if($count != $order) {
		update_query('tblpaymentgateways', array(
			'order' => $count
		), array(
			'setting' => 'name',
			'gateway' => $module
		));
	}
	$count++;
	$newgateways .= "<option value=\"" . $module . "\">" . $GatewayConfig[$module]['FriendlyName']['Value'] . "</option>";
}
echo $aInt->jqueryDialog('deactivategw', $aInt->lang('gateways', 'deactivatemodule'), "<p>" . $aInt->lang('gateways', 'deactivatemoduleinfo') . "</p><form method=\"post\" action=\"configgateways.php?action=deactivate\" id=\"deactivategwfrm\"><input type=\"hidden\" name=\"gateway\" value=\"\" id=\"deactivategwfield\"><input type=\"hidden\" name=\"friendlygateway\" value=\"\" id=\"friendlygatewayname\"><div align=\"center\"><select id=\"newgateway\" name=\"newgateway\">" . $newgateways . "</select></div></form>", array(
	$aInt->lang('gateways', 'deactivate') => "\$('#deactivategwfrm').submit();",
	$aInt->lang('supportreq', 'cancel') => "\$('#newgateway').append(\"<option value='\"+\$(\"#deactivategwfield\").val()+\"'>\"+\$(\"#friendlygatewayname\").val()+\"</option>\"); \$('#deactivategw').dialog('close');"
));
$jscode .= "
function deactivateGW(module,friendlyname) {
    \$(\"#deactivategwfield\").val(module);
    \$(\"#friendlygatewayname\").val(friendlyname);
    \$(\"#newgateway option[value='\"+module+\"']\").remove();
    showDialog(\"deactivategw\");
}";
$content = ob_get_contents();
ob_end_clean();
$aInt->content    = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode     = $jscode;
$aInt->display();