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
$adminpermsarray = getAdminPermsArray();
if( !$adminpermsarray[$permid] )
{
    exit();
}
$adminfolder = $whmcs->get_admin_folder_name();
$result = select_query('tbladmins', 'language', array( 'id' => $_SESSION['adminid'] ));
$data = mysql_fetch_array($result);
$language = $data['language'];
$_ADMINLANG = array(  );
if( $_SESSION['adminlang'] )
{
    $language = $_SESSION['adminlang'];
}
if( !isValidforPath($language) )
{
    exit( "Invalid Admin Language Name" );
}
$langfilepath = ROOTDIR . '/' . $adminfolder . '/lang/' . $language . ".php";
if( file_exists($langfilepath) )
{
    include($langfilepath);
}
else
{
    include(ROOTDIR . '/' . $adminfolder . "/lang/english.php");
}
logActivity("Access Denied to " . $adminpermsarray[$permid]);
echo "\n<html>\n<head>\n<title>WHMCS - ";
echo $_ADMINLANG['permissions']['accessdenied'];
echo "</title>\n<link href=\"templates/original/style.css\" rel=\"stylesheet\" type=\"text/css\" />\n</head>\n<body>\n\n<br /><br /><br /><br /><br />\n<p align=\"center\" style=\"font-size:24px;\">";
echo $_ADMINLANG['permissions']['accessdenied'];
echo "</p>\n<p align=\"center\" style=\"font-size:18px;color:#FF0000;\">";
echo $_ADMINLANG['permissions']['nopermission'];
echo "</p>\n<br /><br />\n<p align=\"center\" style=\"font-size:18px;\">";
echo $_ADMINLANG['permissions']['action'];
echo ": ";
echo $adminpermsarray[$permid];
echo "</p>\n<br /><br /><br />\n<p align=\"center\"><input type=\"button\" value=\" &laquo; ";
echo $_ADMINLANG['global']['goback'];
echo " \" onClick=\"javascript:history.go(-1)\"></p>\n<br /><br />\n\n</body>\n</html>";