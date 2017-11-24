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
$aInt = new WHMCS_Admin("List Support Tickets");
$aInt->title = $aInt->lang('support', 'insertkblink');
ob_start();
echo "\n<script language=\"JavaScript\">\nfunction insertKBLink(id) {\n    window.opener.insertKBLink('";
echo $CONFIG['SystemURL'];
echo "/knowledgebase.php?action=displayarticle&catid=";
echo $cat;
echo "&id='+id);\n    window.close();\n}\n</script>\n\n<p><b>Categories</b></p>\n";
if( $cat == '' )
{
    $cat = 0;
}
$result = select_query('tblknowledgebasecats', '', array( 'parentid' => $cat, 'language' => '' ), 'name', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $name = $data['name'];
    $description = $data['description'];
    echo "<a href=\"?cat=" . $id . "\"><b>" . $name . "</b></a> - " . $description . "<br>";
    $catDone = true;
}
if( !$catDone )
{
    echo $aInt->lang('support', 'nocatsfound') . "<br>";
}
echo "<p><b>Articles</b></p>\n";
$result = select_query('tblknowledgebase', '', array( 'categoryid' => $cat ), 'title', 'ASC', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $title = $data['title'];
    $article = $data['article'];
    $views = $data['views'];
    $article = strip_tags($article);
    $article = trim($article);
    $article = substr($article, 0, 100) . "...";
    echo "<a href=\"#\" onClick=\"insertKBLink('" . $id . "');\"><b>" . $title . "</b></a><br>" . $article . "<br>";
    $articleDone = true;
}
if( !$articleDone )
{
    echo $aInt->lang('support', 'noarticlesfound') . "<br>";
}
echo "\n<p><a href=\"javascript:history.go(-1)\"><< ";
echo $aInt->lang('global', 'back');
echo "</a></p>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->displayPopUp();