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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
function google_analytics_config()
{
    $configarray = array( 'name' => "Google Analytics", 'description' => "This module provides a quick and easy way to integrate full Google Analytics tracking into your WHMCS installation", 'version' => "2.0", 'author' => 'WHMCS', 'fields' => array( 'analytics_version' => array( 'FriendlyName' => "Analytics Version", 'Type' => 'radio', 'Options' => "Google Analytics,Universal Analytics", 'Description' => "<a href='https://support.google.com/analytics/answer/2790010' target='_blank'>More Info</a>" ), 'code' => array( 'FriendlyName' => "Tracking Code", 'Type' => 'text', 'Size' => '25', 'Description' => "Format: UA-XXXXXXXX-X" ), 'domain' => array( 'FriendlyName' => "Tracking Domain", 'Type' => 'text', 'Size' => '25', 'Description' => "(Optional) Format: yourdomain.com" ) ) );
    return $configarray;
}
function google_analytics_output($vars)
{
    echo "<br /><br />\n<p align=\"center\"><input type=\"button\" value=\"Launch Google Analytics Website\" onclick=\"window.open('http://www.google.com/analytics/','ganalytics');\" style=\"padding:20px 50px;font-size:20px;\" /></p>\n<br /><br />\n<p>Configuration of the Google Analytics Addon is done via <a href=\"configaddonmods.php\"><b>Setup > Addon Modules</b></a>. Please also ensure your active client area footer.tpl template file includes the {\$footeroutput} template tag.</p>";
}