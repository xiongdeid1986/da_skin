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
require("../init.php");
$aInt = new WHMCS_Admin("View Integration Code");
$aInt->title = $aInt->lang('system', 'integrationcode');
$aInt->sidebar = 'utilities';
$aInt->icon = 'integrationcode';
$aInt->requiredFiles(array( 'domainfunctions' ));
$currency = getCurrency();
$tlds = getTLDList();
$systemurl = $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
ob_start();
echo "\n<p>";
echo $aInt->lang('system', 'integrationinfo');
echo "</p>\n\n<p>";
echo $aInt->lang('system', 'widgetsinfo');
echo " <a href=\"http://nullrefer.com/?http://docs.whmcs.com/Widgets\" target=\"_blank\">docs.whmcs.com/Widgets</a></p>\n\n<br />\n\n<h2>";
echo $aInt->lang('system', 'intclientlogin');
echo "</h2>\n<p>";
echo $aInt->lang('system', 'intclientlogininfo');
echo "</p>\n<textarea rows=\"6\" style=\"width:100%;\"><form method=\"post\" action=\"";
echo $systemurl;
echo "/dologin.php\">\nEmail Address: <input type=\"text\" name=\"username\" size=\"50\" /><br />\nPassword: <input type=\"password\" name=\"password\" size=\"20\" /><br />\n<input type=\"submit\" value=\"Login\" />\n</form></textarea>\n<br /><br />\n\n<h2>";
echo $aInt->lang('system', 'intdalookup');
echo "</h2>\n<p>";
echo $aInt->lang('system', 'intdalookupinfo');
echo "</p>\n<textarea rows=\"10\" style=\"width:100%;\"><form action=\"";
echo $systemurl;
echo "/domainchecker.php\" method=\"post\">\n<input type=\"hidden\" name=\"direct\" value=\"true\" />\nDomain: <input type=\"text\" name=\"domain\" size=\"20\" /> <select name=\"ext\">\n";
foreach( $tlds as $tld )
{
    echo "<option>" . $tld . "</option>\n";
}
echo "</select>\n<input type=\"submit\" value=\"Go\" />\n</form>\n</textarea>\n<br /><br />\n\n<h2>";
echo $aInt->lang('system', 'intdo');
echo "</h2>\n<p>";
echo $aInt->lang('system', 'intdoinfo');
echo "</p>\n<textarea rows=\"10\" style=\"width:100%;\"><form action=\"";
echo $systemurl;
echo "/cart.php?a=add&domain=register\" method=\"post\">\nDomain: <input type=\"text\" name=\"sld\" size=\"20\" /> <select name=\"tld\">\n";
foreach( $tlds as $tld )
{
    echo "<option>" . $tld . "</option>\n";
}
echo "</select>\n<input type=\"submit\" value=\"Go\" />\n</form>\n</textarea>\n<br /><br />\n\n<h2>";
echo $aInt->lang('system', 'intuserreg');
echo "</h2>\n<p>";
echo $aInt->lang('system', 'intuserreginfo');
echo "</p>\n<textarea rows=\"2\" style=\"width:100%;\"><a href=\"";
echo $systemurl;
echo "/register.php\">Click here to register with us</a></textarea>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();