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
$aInt = new WHMCS_Admin("Manage Knowledgebase");
$aInt->title = $aInt->lang('support', 'knowledgebase');
$aInt->sidebar = 'support';
$aInt->icon = 'knowledgebase';
$catid = (int) $catid;
$categorieslist = '';
if( $addarticle )
{
    check_token("WHMCS.admin.default");
    $newarticleid = insert_query('tblknowledgebase', array( 'title' => $articlename ));
    insert_query('tblknowledgebaselinks', array( 'categoryid' => $catid, 'articleid' => $newarticleid ));
    logActivity("Added New Knowledgebase Article - " . $articlename);
    redir("action=edit&id=" . $newarticleid);
}
if( $addcategory )
{
    check_token("WHMCS.admin.default");
    $newcatid = insert_query('tblknowledgebasecats', array( 'parentid' => $catid, 'name' => $catname, 'description' => $description, 'hidden' => $hidden ));
    logActivity("Added New Knowledgebase Category - " . $catname);
    redir("catid=" . $newcatid);
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    update_query('tblknowledgebase', array( 'title' => $title, 'article' => WHMCS_Input_Sanitize::decode($article), 'views' => $views, 'useful' => $useful, 'votes' => $votes, 'private' => $private, 'order' => $order ), array( 'id' => $id ));
    delete_query('tblknowledgebaselinks', array( 'articleid' => $id ));
    foreach( $categories as $category )
    {
        insert_query('tblknowledgebaselinks', array( 'categoryid' => $category, 'articleid' => $id ));
    }
    foreach( $multilang_title as $language => $title )
    {
        delete_query('tblknowledgebase', array( 'parentid' => $id, 'language' => $language ));
        if( $title )
        {
            insert_query('tblknowledgebase', array( 'parentid' => $id, 'title' => $title, 'article' => WHMCS_Input_Sanitize::decode($multilang_article[$language]), 'language' => $language, 'order' => $order ));
        }
    }
    if( $toggleeditor )
    {
        if( $editorstate )
        {
            redir("action=edit&id=" . $id);
        }
        else
        {
            redir("action=edit&id=" . $id . "&noeditor=1");
        }
    }
    logActivity("Modified Knowledgebase Article ID: " . $id);
    redir("catid=" . $categories[0]);
}
if( $action == 'savecat' )
{
    check_token("WHMCS.admin.default");
    update_query('tblknowledgebasecats', array( 'name' => $name, 'description' => $description, 'hidden' => $hidden, 'parentid' => $parentcategory ), array( 'id' => $id ));
    foreach( $multilang_name as $language => $name )
    {
        delete_query('tblknowledgebasecats', array( 'catid' => $id, 'language' => $language ));
        if( $name )
        {
            insert_query('tblknowledgebasecats', array( 'catid' => $id, 'name' => $name, 'description' => $multilang_desc[$language], 'language' => $language ));
        }
    }
    logActivity("Modified Knowledgebase Category (ID: " . $id . ")");
    redir("catid=" . $parentcategory);
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblknowledgebase', array( 'id' => $id ));
    delete_query('tblknowledgebaselinks', array( 'articleid' => $id ));
    logActivity("Deleted Knowledgebase Article (ID: " . $id . ")");
    redir("catid=" . $catid);
}
if( $action == 'deletecategory' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblknowledgebaselinks', array( 'categoryid' => $id ));
    delete_query('tblknowledgebasecats', array( 'id' => $id ));
    delete_query('tblknowledgebasecats', array( 'parentid' => $id ));
    full_query("DELETE FROM tblknowledgebase WHERE parentid=0 AND id NOT IN (SELECT articleid FROM tblknowledgebaselinks)");
    logActivity("Deleted Knowledgebase Category (ID: " . $id . ")");
    redir("catid=" . $catid);
}
ob_start();
if( $action == '' )
{
    if( !$catid )
    {
        $catid = 0;
    }
    $breadcrumbnav = '';
    if( $catid != '0' )
    {
        $result = select_query('tblknowledgebasecats', '', array( 'id' => $catid ));
        $data = mysql_fetch_array($result);
        $catid = $data['id'];
        if( !$catid )
        {
            $aInt->gracefulExit("Category ID Not Found");
        }
        $catparentid = $data['parentid'];
        $catname = $data['name'];
        $catbreadcrumbnav = " > <a href=\"?catid=" . $catid . "\">" . $catname . "</a>";
        while( $catparentid != '0' )
        {
            $result = select_query('tblknowledgebasecats', '', array( 'id' => $catparentid ));
            $data = mysql_fetch_array($result);
            $cattempid = $data['id'];
            $catparentid = $data['parentid'];
            $catname = $data['name'];
            $catbreadcrumbnav = " > <a href=\"?catid=" . $cattempid . "\">" . $catname . "</a>" . $catbreadcrumbnav;
        }
        $breadcrumbnav .= $catbreadcrumbnav;
    }
    $aInt->deleteJSConfirm('doDelete', 'support', 'kbdelsure', $_SERVER['PHP_SELF'] . "?catid=" . $catid . "&action=delete&id=");
    $aInt->deleteJSConfirm('doDeleteCat', 'support', 'kbcatdelsure', $_SERVER['PHP_SELF'] . "?catid=" . $catid . "&action=deletecategory&id=");
    echo $aInt->Tabs(array( $aInt->lang('support', 'addcategory'), $aInt->lang('support', 'addarticle') ), true);
    echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?catid=";
    echo $catid;
    echo "&addcategory=true\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('support', 'catname');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"catname\" size=\"40\"> <input type=\"checkbox\" name=\"hidden\"> ";
    echo $aInt->lang('support', 'ticktohide');
    echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'description');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=\"100\"></td></tr>\n\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('support', 'addcategory');
    echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
    if( $catid != '' )
    {
        echo "<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?catid=";
        echo $catid;
        echo "&addarticle=true\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('support', 'articlename');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"articlename\" size=\"60\"></td></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('support', 'addarticle');
        echo "\" class=\"button\"></div>\n</form>\n";
    }
    else
    {
        echo $aInt->lang('support', 'kbnotoplevel');
    }
    echo "\n  </div>\n</div>\n\n";
    echo "<p>" . $aInt->lang('support', 'youarehere') . ": <a href=\"" . $whmcs->getPhpSelf() . "\">" . $aInt->lang('support', 'kbhome') . "</a> " . $breadcrumbnav . "</p>";
    $result = select_query('tblknowledgebasecats', '', array( 'parentid' => $catid ), 'name', 'ASC');
    $numcats = mysql_num_rows($result);
    echo "\n";
    if( $numcats != '0' )
    {
        echo "\n<p><b>";
        echo $aInt->lang('support', 'categories');
        echo "</b></p>\n\n<table width=100%><tr>\n";
        if( $catid == '' )
        {
            $catid = '0';
        }
        $result = select_query('tblknowledgebasecats', '', array( 'parentid' => $catid, 'catid' => 0 ), 'name', 'ASC');
        $i = 0;
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            $description = $data['description'];
            $hidden = $data['hidden'];
            $idnumbers = array(  );
            $idnumbers[] = $id;
            kbgetcatids($id);
            $queryreport = '';
            foreach( $idnumbers as $idnumber )
            {
                $queryreport .= " OR categoryid='" . $idnumber . "'";
            }
            $queryreport = substr($queryreport, 4);
            $result2 = select_query('tblknowledgebase', "COUNT(*)", "parentid=0 AND (" . $queryreport . ")", '', '', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
            $data2 = mysql_fetch_array($result2);
            $numarticles = $data2[0];
            echo "<td width=33%><img src=\"../images/folder.gif\" align=\"absmiddle\"> <a href=\"?catid=" . $id . "\"><b>" . $name . "</b></a> (" . $numarticles . ") <a href=\"?action=editcat&id=" . $id . "\"><img src=\"images/edit.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\" /></a> <a href=\"#\" onClick=\"doDeleteCat(" . $id . ")\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\" /></a>";
            if( $hidden == 'on' )
            {
                echo " <font color=#cccccc>(" . strtoupper($aInt->lang('fields', 'hidden')) . ")</font>";
            }
            echo "<br>" . $description . "</td>";
            $i++;
            if( $i % 3 == 0 )
            {
                echo "</tr><tr><td><br></td></tr><tr>";
                $i = 0;
            }
        }
        echo "</tr></table>\n\n";
    }
    $result = select_query('tblknowledgebase', '', array( 'categoryid' => $catid ), "order` ASC,`title", 'ASC', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
    $numarticles = mysql_num_rows($result);
    if( $numarticles != '0' )
    {
        echo "\n<p><b>";
        echo $aInt->lang('support', 'articles');
        echo "</b></p>\n\n<table width=100%><tr>\n";
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $category = $data['category'];
            $title = $data['title'];
            $article = strip_tags($data['article']);
            $views = $data['views'];
            $private = $data['private'];
            $article = substr($article, 0, 150) . "...";
            echo "<p><img src=\"../images/article.gif\" align=\"absmiddle\"> <a href=\"?action=edit&id=" . $id . "\"><b>" . $title . "</b></a> <a href=\"#\" onClick=\"doDelete(" . $id . ")\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a></font>";
            if( $private == 'on' )
            {
                echo " <font color=#cccccc>(" . strtoupper($aInt->lang('support', 'clientsonly')) . ")</font>";
            }
            echo "<br>" . $article . "<br><font color=#cccccc>" . $aInt->lang('support', 'views') . ": " . $views . "</p>";
        }
        echo "</tr></table>\n\n";
    }
    else
    {
        echo "<p><b>" . $aInt->lang('support', 'noarticlesfound') . "</b></p>";
    }
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblknowledgebase', '', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $title = WHMCS_Input_Sanitize::makesafeforoutput($data['title']);
        $article = WHMCS_Input_Sanitize::encode($data['article']);
        $views = (int) $data['views'];
        $useful = (int) $data['useful'];
        $votes = (int) $data['votes'];
        $private = $data['private'];
        $order = (int) $data['order'];
        $multilang_title = array(  );
        $multilang_article = array(  );
        $result = select_query('tblknowledgebase', '', array( 'parentid' => $id ));
        while( $data = mysql_fetch_array($result) )
        {
            $language = $data['language'];
            $multilang_title[$language] = WHMCS_Input_Sanitize::makesafeforoutput($data['title']);
            $multilang_article[$language] = WHMCS_Input_Sanitize::encode($data['article']);
        }
        $categories = array(  );
        $result = select_query('tblknowledgebaselinks', '', array( 'articleid' => $id ));
        while( $data = mysql_fetch_array($result) )
        {
            $categories[] = $data['categoryid'];
        }
        $jscode = "function showtranslation(language) {\n    \$(\"#translation_\"+language).slideToggle();\n}";
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?catid=";
        echo $category;
        echo "&action=save&id=";
        echo $id;
        echo "\">\n<input type=\"hidden\" name=\"editorstate\" value=\"";
        echo $noeditor;
        echo "\" />\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('support', 'categories');
        echo "</td><td class=\"fieldarea\"><select name=\"categories[]\" size=\"8\" multiple style=\"width:80%;\">";
        buildcategorieslist(0, 0);
        echo $categorieslist;
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'title');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" value=\"";
        echo $title;
        echo "\" size=\"70\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'views');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"views\" value=\"";
        echo $views;
        echo "\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'votes');
        echo "</td><td class=\"fieldarea\">For <input type=\"text\" name=\"useful\" value=\"";
        echo $useful;
        echo "\" size=\"10\"> Total <input type=\"text\" name=\"votes\" value=\"";
        echo $votes;
        echo "\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'private');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"private\"";
        if( $private == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('support', 'privateinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'order');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"order\" value=\"";
        echo $order;
        echo "\" size=\"10\"></td></tr>\n</table>\n\n<br />\n\n<textarea name=\"article\" rows=\"20\" style=\"width:100%\" class=\"tinymce\">";
        echo $article;
        echo "</textarea>\n\n<p align=\"center\">\n    <input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"btn btn-primary\" />\n    <input type=\"submit\" name=\"toggleeditor\" value=\"";
        echo $aInt->lang('emailtpls', 'rteditor');
        echo "\" class=\"btn\" />\n</p>\n\n<h2>";
        echo $aInt->lang('support', 'announcemultiling');
        echo "</h2>\n\n";
        foreach( $whmcs->getValidLanguages() as $language )
        {
            if( $language != $CONFIG['Language'] )
            {
                echo "<p><b><a href=\"#\" onClick=\"showtranslation('" . $language . "');return false;\">" . ucfirst($language) . "</a></b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\" id=\"translation_" . $language . "\"";
                if( !$multilang_title[$language] )
                {
                    echo " style=\"display:none;\"";
                }
                echo ">\n<tr><td width=\"15%\" class=\"fieldlabel\">" . $aInt->lang('fields', 'title') . "</td><td class=\"fieldarea\"><input type=\"text\" name=\"multilang_title[" . $language . "]\" value=\"" . $multilang_title[$language] . "\" size=\"70\"></td></tr>\n<tr><td class=\"fieldlabel\">" . $aInt->lang('support', 'article') . "</td><td class=\"fieldarea\"><textarea name=\"multilang_article[" . $language . "]\" rows=\"20\" style=\"width:100%\" class=\"tinymce\">" . $multilang_article[$language] . "</textarea></td></tr>\n</table>";
            }
        }
        closedir($dh);
        echo "\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"btn\" /></p>\n\n</form>\n\n";
        if( !$noeditor )
        {
            $aInt->richTextEditor();
        }
    }
    else
    {
        if( $action == 'editcat' )
        {
            $result = select_query('tblknowledgebasecats', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $id = (int) $data['id'];
            $parentid = $data['parentid'];
            $name = WHMCS_Input_Sanitize::makesafeforoutput($data['name']);
            $description = WHMCS_Input_Sanitize::makesafeforoutput($data['description']);
            $hidden = $data['hidden'];
            $categories = array(  );
            $categories[] = $parentid;
            $multilang_name = array(  );
            $multilang_desc = array(  );
            $result = select_query('tblknowledgebasecats', '', array( 'catid' => $id ));
            while( $data = mysql_fetch_array($result) )
            {
                $language = $data['language'];
                $multilang_name[$language] = WHMCS_Input_Sanitize::makesafeforoutput($data['name']);
                $multilang_desc[$language] = WHMCS_Input_Sanitize::makesafeforoutput($data['description']);
            }
            echo "\n<form method=\"post\" action=\"";
            echo $whmcs->getPhpSelf();
            echo "?action=savecat&id=";
            echo $id;
            echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
            echo $aInt->lang('support', 'parentcat');
            echo "</td><td class=\"fieldarea\"><select name=\"parentcategory\">\n<option value=\"\">";
            echo $aInt->lang('support', 'toplevel');
            echo "</option>\n";
            buildcategorieslist(0, 0, $id);
            echo $categorieslist;
            echo "?></select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('support', 'catname');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" value=\"";
            echo $name;
            echo "\" size=\"40\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'description');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" value=\"";
            echo $description;
            echo "\" size=\"100\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'hidden');
            echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"hidden\"";
            if( $hidden == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('fields', 'hiddeninfo');
            echo "</td></tr>\n</table>\n\n<h2>";
            echo $aInt->lang('support', 'announcemultiling');
            echo "</h2>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n";
            foreach( $whmcs->getValidLanguages() as $language )
            {
                echo "<tr><td width=\"15%\" class=\"fieldlabel\">" . ucfirst($language) . "</td><td class=\"fieldarea\">" . $aInt->lang('fields', 'name') . ": <input type=\"text\" name=\"multilang_name[" . $language . "]\" value=\"" . $multilang_name[$language] . "\" size=\"40\"> " . $aInt->lang('fields', 'description') . ": <input type=\"text\" name=\"multilang_desc[" . $language . "]\" value=\"" . $multilang_desc[$language] . "\" size=\"60\"></td></tr>\n";
            }
            echo "</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'savechanges');
            echo "\" class=\"btn\" /></p>\n\n</form>\n\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();
function kbGetCatIds($catid)
{
    global $idnumbers;
    $result = select_query('tblknowledgebasecats', 'id', array( 'parentid' => $catid, 'hidden' => '' ));
    while( $data = mysql_fetch_array($result) )
    {
        $cid = $data[0];
        $idnumbers[] = $cid;
        kbGetCatIds($cid);
    }
}
function buildCategoriesList($level, $parentlevel, $exclude = '')
{
    global $categorieslist;
    global $categories;
    $result = select_query('tblknowledgebasecats', '', array( 'parentid' => $level, 'catid' => 0 ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $parentid = $data['parentid'];
        $category = $data['name'];
        if( $id != $exclude )
        {
            $categorieslist .= "<option value=\"" . $id . "\"";
            if( in_array($id, $categories) )
            {
                $categorieslist .= " selected";
            }
            $categorieslist .= ">";
            for( $i = 1; $i <= $parentlevel; $i++ )
            {
                $categorieslist .= "- ";
            }
            $categorieslist .= $category . "</option>";
        }
        buildCategoriesList($id, $parentlevel + 1, $exclude);
    }
}