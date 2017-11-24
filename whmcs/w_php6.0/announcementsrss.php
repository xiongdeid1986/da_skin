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
define('CLIENTAREA', true);
require("init.php");
$language = isset($_REQUEST['language']) && in_array($_REQUEST['language'], $whmcs->getValidLanguages()) ? $_REQUEST['language'] : '';
header("Content-Type: application/rss+xml");
echo "<?xml version=\"1.0\" encoding=\"" . $CONFIG['Charset'] . "\"?>\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n<channel>\n<atom:link href=\"" . $CONFIG['SystemURL'] . "/announcementsrss.php\" rel=\"self\" type=\"application/rss+xml\" />\n<title><![CDATA[" . $CONFIG['CompanyName'] . "]]></title>\n<description><![CDATA[" . $CONFIG['CompanyName'] . " " . $_LANG['announcementstitle'] . " " . $_LANG['rssfeed'] . "]]></description>\n<link>" . $CONFIG['SystemURL'] . "/announcements.php</link>";
$result = select_query('tblannouncements', "*", array( 'published' => 'on' ), 'date', 'DESC');
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $date = $data['date'];
    $title = $data['title'];
    $announcement = $data['announcement'];
    $result2 = select_query('tblannouncements', '', array( 'parentid' => $id, 'language' => $language ));
    $data = mysql_fetch_array($result2);
    if( $data['title'] )
    {
        $title = $data['title'];
    }
    if( $data['announcement'] )
    {
        $announcement = $data['announcement'];
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $hours = substr($date, 11, 2);
    $minutes = substr($date, 14, 2);
    $seconds = substr($date, 17, 2);
    echo "\n<item>\n    <title><![CDATA[" . $title . "]]></title>\n    <link>" . $CONFIG['SystemURL'] . "/announcements.php?id=" . $id . "</link>\n    <guid>" . $CONFIG['SystemURL'] . "/announcements.php?id=" . $id . "</guid>\n    <pubDate>" . date('r', mktime($hours, $minutes, $seconds, $month, $day, $year)) . "</pubDate>\n    <description><![CDATA[" . $announcement . "]]></description>\n</item>";
}
echo "\n</channel>\n</rss>";