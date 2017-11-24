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
$aInt = new WHMCS_Admin("Manage Predefined Replies");
$aInt->title = $aInt->lang('support', 'predefreplies');
$aInt->sidebar = 'support';
$aInt->icon = 'ticketspredefined';
if( $addreply == 'true' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create Predefined Replies");
    $lastid = insert_query('tblticketpredefinedreplies', array( 'catid' => $catid, 'name' => $name ));
    logActivity("Added New Predefined Reply - " . $title);
    redir("action=edit&id=" . $lastid);
}
if( $sub == 'save' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Predefined Replies");
    $table = 'tblticketpredefinedreplies';
    $array = array( 'catid' => $catid, 'name' => $name, 'reply' => $reply );
    $where = array( 'id' => $id );
    update_query($table, $array, $where);
    logActivity("Modified Predefined Reply (ID: " . $id . ")");
    redir("catid=" . $catid . "&save=true");
}
if( $sub == 'savecat' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Predefined Replies");
    $table = 'tblticketpredefinedcats';
    $array = array( 'parentid' => $parentid, 'name' => $name );
    $where = array( 'id' => $id );
    update_query($table, $array, $where);
    logActivity("Modified Predefined Reply Category (ID: " . $id . ")");
    redir("catid=" . $parentid . "&savecat=true");
}
if( $addcategory == 'true' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create Predefined Replies");
    insert_query('tblticketpredefinedcats', array( 'parentid' => $catid, 'name' => $catname ));
    logActivity("Added New Predefined Reply Category - " . $catname);
    redir("catid=" . $catid . "&addedcat=true");
    exit();
}
if( $sub == 'delete' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Predefined Replies");
    delete_query('tblticketpredefinedreplies', array( 'id' => $id ));
    logActivity("Deleted Predefined Reply (ID: " . $id . ")");
    redir("catid=" . $catid . "&delete=true");
}
if( $sub == 'deletecategory' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Predefined Replies");
    delete_query('tblticketpredefinedreplies', array( 'catid' => $id ));
    delete_query('tblticketpredefinedcats', array( 'id' => $id ));
    deletepredefcat($id);
    logActivity("Deleted Predefined Reply Category (ID: " . $id . ")");
    redir("catid=" . $catid . "&deletecat=true");
}
ob_start();
if( $action == '' )
{
    if( $addedcat )
    {
        infoBox($aInt->lang('global', 'success'), $aInt->lang('support', 'predefaddedcat'));
    }
    if( $save )
    {
        infoBox($aInt->lang('global', 'success'), $aInt->lang('support', 'predefsave'));
    }
    if( $savecat )
    {
        infoBox($aInt->lang('global', 'success'), $aInt->lang('support', 'predefsavecat'));
    }
    if( $delete )
    {
        infoBox($aInt->lang('global', 'success'), $aInt->lang('support', 'predefdelete'));
    }
    if( $deletecat )
    {
        infoBox($aInt->lang('global', 'success'), $aInt->lang('support', 'predefdeletecat'));
    }
    echo $infobox;
    if( $catid )
    {
        $catid = get_query_val('tblticketpredefinedcats', 'id', array( 'id' => $catid ));
    }
    $aInt->deleteJSConfirm('doDelete', 'support', 'predefdelsure', $_SERVER['PHP_SELF'] . "?catid=" . $catid . "&sub=delete&id=");
    $aInt->deleteJSConfirm('doDeleteCat', 'support', 'predefdelcatsure', $_SERVER['PHP_SELF'] . "?catid=" . $catid . "&sub=deletecategory&id=");
    echo $aInt->Tabs(array( $aInt->lang('support', 'addcategory'), $aInt->lang('support', 'addpredef'), $aInt->lang('global', 'searchfilter') ), true);
    echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?catid=";
    echo $catid;
    echo "&addcategory=true\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('support', 'catname');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"catname\" size=\"40\"></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('support', 'addcategory');
    echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
    if( $catid != '' )
    {
        echo "<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?catid=";
        echo $catid;
        echo "&addreply=true\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('support', 'articlename');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"60\"></td></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('support', 'addarticle');
        echo "\" class=\"button\"></div>\n</form>\n";
    }
    else
    {
        echo $aInt->lang('support', 'pdnotoplevel');
    }
    echo "\n  </div>\n</div>\n<div id=\"tab2box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" method=\"post\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('support', 'articlename');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" size=\"40\" value=\"";
    echo $title;
    echo "\" /></td><td class=\"fieldlabel\">";
    echo $aInt->lang('mergefields', 'message');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"message\" size=\"60\" value=\"";
    echo $message;
    echo "\" /></td></tr>\n</table>\n<input type=\"hidden\" name=\"search\" value=\"search\" />\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'searchfilter');
    echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n\n";
    if( $catid == '' )
    {
        $catid = '0';
    }
    if( $catid != '0' )
    {
        $result = select_query('tblticketpredefinedcats', '', array( 'id' => $catid ));
        $data = mysql_fetch_array($result);
        $catparentid = $data['parentid'];
        $catname = $data['name'];
        $catbreadcrumbnav = " > <a href=\"?catid=" . $catid . "\">" . $catname . "</a>";
        while( $catparentid != '0' )
        {
            $result = select_query('tblticketpredefinedcats', '', array( 'id' => $catparentid ));
            $data = mysql_fetch_array($result);
            $cattempid = $data['id'];
            $catparentid = $data['parentid'];
            $catname = $data['name'];
            $catbreadcrumbnav = " > <a href=\"?catid=" . $cattempid . "\">" . $catname . "</a>" . $catbreadcrumbnav;
        }
        $breadcrumbnav .= $catbreadcrumbnav;
        echo "<p>" . $aInt->lang('support', 'youarehere') . ": <a href=\"" . $whmcs->getPhpSelf() . "\">" . $aInt->lang('support', 'toplevel') . "</a> " . $breadcrumbnav . "</p>";
    }
    $result = select_query('tblticketpredefinedcats', '', array( 'parentid' => $catid ), 'name', 'ASC');
    $numcats = mysql_num_rows($result);
    echo "\n";
    if( $numcats != '0' && !$search )
    {
        echo "\n<p><b>";
        echo $aInt->lang('support', 'categories');
        echo "</b></p>\n\n<table width=100%><tr>\n";
        if( $catid == '' )
        {
            $catid = '0';
        }
        $result = select_query('tblticketpredefinedcats', '', array( 'parentid' => $catid ), 'name', 'ASC');
        $i = 0;
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            $result3 = select_query('tblticketpredefinedreplies', 'id', array( 'catid' => $id ));
            $numarticles = mysql_num_rows($result3);
            echo "<td width=33%><img src=\"../images/folder.gif\" align=\"absmiddle\"> <a href=\"?catid=" . $id . "\"><b>" . $name . "</b></a> (" . $numarticles . ") <a href=\"?action=editcat&id=" . $id . "\"><img src=\"images/edit.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\" /></a> <a href=\"#\" onClick=\"doDeleteCat(" . $id . ");return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\"alt=\"" . $aInt->lang('global', 'delete') . "\" /></a><br>" . $description . "</td>";
            $i++;
            if( $i % 3 == 0 )
            {
                echo "</tr><tr><td><br></td></tr><tr>";
                $i = 0;
            }
        }
        echo "</tr></table>\n\n";
    }
    else
    {
        if( $catid == '0' && !$search )
        {
            echo "<p><b>" . $aInt->lang('support', 'nocatsfound') . "</b></p>";
        }
    }
    $where = '';
    if( !$search )
    {
        $where .= " AND catid='" . db_escape_string($catid) . "'";
    }
    if( $title )
    {
        $where .= " AND name LIKE '%" . db_escape_string($title) . "%'";
    }
    if( $message )
    {
        $where .= " AND reply LIKE '%" . db_escape_string($message) . "%'";
    }
    if( $where )
    {
        $where = substr($where, 5);
    }
    $result = select_query('tblticketpredefinedreplies', '', $where, 'name', 'ASC');
    $numarticles = mysql_num_rows($result);
    if( $search )
    {
        echo "<p>" . $aInt->lang('support', 'youarehere') . ": <a href=\"" . $whmcs->getPhpSelf() . "\">" . $aInt->lang('support', 'toplevel') . "</a>  > <a href=\"" . $whmcs->getPhpSelf() . "\">" . $aInt->lang('global', 'search') . "</a></p>";
    }
    if( $numarticles != '0' )
    {
        echo "\n<p><b>";
        echo $aInt->lang('support', 'replies');
        echo "</b></p>\n\n<table width=100%><tr>\n";
        $result = select_query('tblticketpredefinedreplies', '', $where, 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            $reply = strip_tags(stripslashes($data['reply']));
            $reply = substr($reply, 0, 150) . "...";
            echo "<p><img src=\"../images/article.gif\" align=\"absmiddle\"> <a href=\"?action=edit&id=" . $id . "\"><b>" . $name . "</b></a> <a href=\"#\" onClick=\"doDelete(" . $id . ");return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\" /></a><br>" . $reply . "</p>";
        }
        echo "</tr></table>\n\n";
    }
    else
    {
        if( $catid != '0' || $search )
        {
            echo "<p><b>" . $aInt->lang('support', 'norepliesfound') . "</b></p>";
        }
    }
    echo "\n";
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblticketpredefinedreplies', '', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $catid = $data['catid'];
        $name = $data['name'];
        $reply = $data['reply'];
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?sub=save&id=";
        echo $id;
        echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('support', 'category');
        echo "</td><td class=\"fieldarea\"><select name=\"catid\">";
        buildcategorieslist(0, 0);
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'replyname');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" value=\"";
        echo $name;
        echo "\" size=70></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('mergefields', 'title');
        echo "</td><td class=\"fieldarea\">[NAME] - ";
        echo $aInt->lang('mergefields', 'ticketname');
        echo "<br />[FIRSTNAME] - ";
        echo $aInt->lang('fields', 'firstname');
        echo "<br />[EMAIL] - ";
        echo $aInt->lang('mergefields', 'ticketemail');
        echo "</td></tr>\n</table>\n<br>\n<textarea name=\"reply\" rows=18 style=\"width:100%\">";
        echo $reply;
        echo "</textarea>\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n</form>\n\n";
    }
    else
    {
        if( $action == 'editcat' )
        {
            $result = select_query('tblticketpredefinedcats', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $parentid = $catid = $data['parentid'];
            $name = stripslashes($data['name']);
            echo "\n<form method=\"post\" action=\"";
            echo $whmcs->getPhpSelf();
            echo "?catid=";
            echo $parentid;
            echo "&sub=savecat&id=";
            echo $id;
            echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
            echo $aInt->lang('support', 'parentcat');
            echo "</td><td class=\"fieldarea\"><select name=\"parentid\"><option value=\"\">";
            echo $aInt->lang('support', 'toplevel');
            buildcategorieslist(0, 0, $id);
            echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('support', 'catname');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" value=\"";
            echo $name;
            echo "\" size=40></td></tr>\n</table>\n<p align=\"center\"><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'savechanges');
            echo "\" class=\"button\"></p>\n</form>\n\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();
function buildCategoriesList($level, $parentlevel, $exclude = '')
{
    global $catid;
    $result = select_query('tblticketpredefinedcats', '', array( 'parentid' => $level ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $parentid = $data['parentid'];
        $category = $data['name'];
        if( $id == $exclude )
        {
            continue;
        }
        echo "<option value=\"" . $id . "\"";
        if( $id == $catid )
        {
            echo " selected";
        }
        echo ">";
        for( $i = 1; $i <= $parentlevel; $i++ )
        {
            echo "- ";
        }
        echo $category . "</option>";
        buildCategoriesList($id, $parentlevel + 1);
    }
}
function deletePreDefCat($catid)
{
    $result = select_query('tblticketpredefinedcats', '', array( 'parentid' => $catid ));
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        delete_query('tblticketpredefinedreplies', array( 'catid' => $id ));
        delete_query('tblticketpredefinedcats', array( 'id' => $id ));
        deletePreDefCat($id);
    }
}