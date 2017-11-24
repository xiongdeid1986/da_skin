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
require("../../../init.php");
$whmcs->load_function('client');
if( $CONFIG['SupportModule'] != 'kayako' )
{
    exit( "Kayako Module not Enabled in General Settings > Support" );
}
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$remote_ip = $_REQUEST['ipaddress'];
if( validateClientLogin($username, $password, true) )
{
    $result = select_query('tblclients', '', array( 'id' => $_SESSION['uid'] ));
    $data = mysql_fetch_array($result);
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $email = $data['email'];
    $phonenumber = $data['phonenumber'];
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<loginshare>\n    <result>1</result>\n    <user>\n        <usergroup>Registered</usergroup>\n        <fullname><![CDATA[" . $firstname . " " . $lastname . "]]></fullname>\n        <emails>\n            <email>" . $email . "</email>\n        </emails>\n        <phone>" . $phonenumber . "</phone>\n    </user>\n</loginshare>";
}
else
{
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<loginshare>\n    <result>0</result>\n    <message>Invalid Username or Password</message>\n</loginshare>";
}
echo $xml;