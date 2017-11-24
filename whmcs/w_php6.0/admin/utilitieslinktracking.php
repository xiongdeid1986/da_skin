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
$aInt = new WHMCS_Admin("Link Tracking");
$aInt->title = "Link Tracking";
$aInt->sidebar = 'utilities';
$aInt->icon = 'linktracking';
$aInt->helplink = "Link Tracking";
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $streamPattern = "/^[a-zA-Z0-9]+\\s?:\\s?\\//";
    if( !preg_match($streamPattern, $url) )
    {
        redir("action=manage&id=" . $id . "&invalidurl=1");
    }
    if( $id )
    {
        $table = 'tbllinks';
        $array = array( 'name' => $name, 'link' => $url, 'clicks' => $clicks, 'conversions' => $conversions );
        $where = array( 'id' => $id );
        update_query($table, $array, $where);
    }
    else
    {
        $table = 'tbllinks';
        $array = array( 'name' => $name, 'link' => $url, 'clicks' => $clicks, 'conversions' => $conversions );
        insert_query($table, $array);
    }
    redir();
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tbllinks', array( 'id' => $id ));
    redir();
}
ob_start();
if( !$action )
{
    $aInt->deleteJSConfirm('doDelete', 'linktracking', 'delete', "?action=delete&id=");
    echo "\n<p>The Link Tracking system allows you to track how people are arriving at your site (what links they are clicking on) and then how many conversions you get from people who have clicked on that link.</p>\n\n<p><b>Options:</b> <a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=manage\">Add a New Link</a></p>\n\n";
    if( $orderby == 'conversionrate' )
    {
        $orderbysql = "(conversions/clicks)";
    }
    else
    {
        if( in_array($orderby, array( 'id', 'name', 'link', 'clicks', 'conversions' )) )
        {
            $orderbysql = $orderby;
        }
        else
        {
            $orderby = '';
            $orderbysql = 'id';
        }
    }
    $aInt->sortableTableInit('id', 'ASC');
    $result = full_query("SELECT COUNT(id) FROM tbllinks");
    $data = mysql_fetch_array($result);
    $numrows = $data[0];
    $result = full_query("SELECT * FROM tbllinks ORDER BY " . db_escape_string($orderbysql) . " " . db_escape_string($order) . " LIMIT " . (int) ($page * $limit) . ',' . (int) $limit);
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $link = $data['link'];
        $clicks = $data['clicks'];
        $conversions = $data['conversions'];
        $displaylink = $link;
        if( 40 < strlen($displaylink) )
        {
            $displaylink = substr($link, 0, 40) . "...";
        }
        $conversionrate = @round($conversions / $clicks * 100, 2);
        $tabledata[] = array( $id, $name, "<a href=\"" . $link . "\" target=\"_blank\">" . $displaylink . "</a>", $clicks, $conversions, $conversionrate . "%", "<a href=\"?action=manage&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" );
    }
    echo $aInt->sortableTable(array( array( 'id', 'ID' ), array( 'name', 'Name' ), array( 'link', 'Link' ), array( 'clicks', 'Clicks' ), array( 'conversions', 'Conversions' ), array( 'conversionrate', "Conversion Rate" ), '', '' ), $tabledata);
}
else
{
    if( $action == 'manage' )
    {
        if( $id )
        {
            $table = 'tbllinks';
            $fields = '';
            $where = array( 'id' => $id );
            $result = select_query($table, $fields, $where);
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $name = $data['name'];
            $url = $data['link'];
            $clicks = $data['clicks'];
            $conversions = $data['conversions'];
            $actiontitle = "Edit Link";
        }
        else
        {
            $clicks = 0;
            $conversions = 0;
            $actiontitle = "Add Link";
        }
        if( $whmcs->get_req_var('invalidurl') )
        {
            infoBox("Invalid Forward To URL", "Please enter a full and valid URL in a format such as http://www.domain.com/path/to/file.php");
            echo $infobox;
        }
        echo "\n<p><strong>";
        echo $actiontitle;
        echo "</strong></p>\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=save";
        if( $id )
        {
            echo "&id=" . $id;
        }
        echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">Name</td><td class=\"fieldarea\"><input type=\"text\" size=\"40\" name=\"name\" value=\"";
        echo $name;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">Forward To</td><td class=\"fieldarea\"><input type=\"text\" name=\"url\" size=100 value=\"";
        echo $url;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">Clicks</td><td class=\"fieldarea\"><input type=\"text\" name=\"clicks\" size=10 value=\"";
        echo $clicks;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">Conversions</td><td class=\"fieldarea\"><input type=\"text\" name=\"conversions\" size=10 value=\"";
        echo $conversions;
        echo "\"></td></tr>\n";
        if( $id )
        {
            echo "<tr><td class=\"fieldlabel\">Link/URL</td><td class=\"fieldarea\"><input type=\"text\" name=\"linkurl\" size=100 value=\"";
            echo $CONFIG['SystemURL'];
            echo "/link.php?id=";
            echo $id;
            echo "\"></td></tr>";
        }
        echo "</table>\n<p align=\"center\"><input type=\"submit\" value=\"Save Changes\" class=\"button\"></p>\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->display();