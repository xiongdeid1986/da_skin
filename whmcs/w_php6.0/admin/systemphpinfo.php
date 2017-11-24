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
$aInt = new WHMCS_Admin('View PHP Info');
$aInt->title = $aInt->lang('system', 'phpinfo');
$aInt->sidebar = 'utilities';
$aInt->icon = 'phpinfo';
ob_start();
phpinfo();
$info = ob_get_contents();
ob_end_clean();
ini_set('pcre.backtrack_limit', 1000000);
ini_set('pcre.recursion_limit', 1000000);
$info = preg_replace("%^.*<body>(.*)</body>.*\$%ms", "\$1", $info);
ob_start();
echo "<style type=\"text/css\">\n.e {background-color: #EFF2F9; font-weight: bold; color: #000000;}\n.v {background-color: #efefef; color: #000000;}\n.vr {background-color: #efefef; text-align: right; color: #000000;}\nhr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}\n</style>\n";
echo $info;
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();