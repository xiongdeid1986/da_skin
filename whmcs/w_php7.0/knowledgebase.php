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
$pagetitle = $_LANG['knowledgebasetitle'];
$breadcrumbnav = "<a href=\"" . $CONFIG['SystemURL'] . "/index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"" . $CONFIG['SystemURL'] . "/knowledgebase.php\">" . $_LANG['knowledgebasetitle'] . "</a>";
$pageicon = "images/knowledgebase_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
$action = $whmcs->get_req_var('action');
$kbcats = $kbmostviews = $kbarticles = array(  );
if( isset($catid) && !is_numeric($catid) )
{
    redirSystemURL('', "knowledgebase.php");
}
if( isset($id) && !is_numeric($id) )
{
    redirSystemURL('', "knowledgebase.php");
}
$usingsupportmodule = false;
if( $CONFIG['SupportModule'] )
{
    if( !isValidforPath($CONFIG['SupportModule']) )
    {
        exit( "Invalid Support Module" );
    }
    $supportmodulepath = 'modules/support/' . $CONFIG['SupportModule'] . "/knowledgebase.php";
    if( file_exists($supportmodulepath) )
    {
        $usingsupportmodule = true;
        $templatefile = '';
        require($supportmodulepath);
        outputClientArea($templatefile);
        exit( dirname(__FILE__) . " | line".__LINE__ );
    }
}
if( $action == 'search' && $searchin == 'Downloads' )
{
    redirSystemURL("action=search&search=" . $search, "downloads.php");
}
$smartyvalues['seofriendlyurls'] = $CONFIG['SEOFriendlyUrls'];
if( $action == 'displaycat' )
{
    $templatefile = 'knowledgebasecat';
    $result = select_query('tblknowledgebasecats', '', array( 'id' => $catid, 'hidden' => '', 'catid' => 0 ));
    $data = mysqli_fetch_array($result);
    $catid = $data['id'];
    if( !$catid )
    {
        redirSystemURL('', "knowledgebase.php");
    }
    $smartyvalues['catid'] = $catid;
    $catparentid = $data['parentid'];
    $catname = $data['name'];
    $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $catid, 'language' => $_SESSION['Language'] ));
    $data = mysqli_fetch_array($result2);
    if( $data['name'] )
    {
        $catname = $data['name'];
    }
    $smartyvalues['catname'] = $catname;
    if( $CONFIG['SEOFriendlyUrls'] )
    {
        $catbreadcrumbnav = " > <a href=\"knowledgebase/" . $catid . '/' . getModRewriteFriendlyString($catname) . "\">" . $catname . "</a>";
    }
    else
    {
        $catbreadcrumbnav = " > <a href=\"knowledgebase.php?action=displaycat&amp;catid=" . $catid . "\">" . $catname . "</a>";
    }
    $i = 0;
    while( $catparentid != '0' )
    {
        $result = select_query('tblknowledgebasecats', '', array( 'id' => $catparentid ));
        $data = mysqli_fetch_array($result);
        $cattempid = $data['id'];
        $catparentid = $data['parentid'];
        $catname = $data['name'];
        $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $cattempid, 'language' => $_SESSION['Language'] ));
        $data = mysqli_fetch_array($result2);
        if( $data['name'] )
        {
            $catname = $data['name'];
        }
        if( $CONFIG['SEOFriendlyUrls'] )
        {
            $catbreadcrumbnav = " > <a href=\"knowledgebase/" . $cattempid . '/' . getModRewriteFriendlyString($catname) . "\">" . $catname . "</a>" . $catbreadcrumbnav;
        }
        else
        {
            $catbreadcrumbnav = " > <a href=\"knowledgebase.php?action=displaycat&amp;catid=" . $cattempid . "\">" . $catname . "</a>" . $catbreadcrumbnav;
        }
        $i++;
        if( 100 < $i )
        {
            break;
        }
    }
    $breadcrumbnav .= $catbreadcrumbnav;
    $smarty->assign('breadcrumbnav', $breadcrumbnav);
    $i = 1;
    for( $result = select_query('tblknowledgebasecats', '', array( 'parentid' => $catid, 'hidden' => '', 'catid' => 0 ), 'name', 'ASC'); $data = mysqli_fetch_array($result); $i++ )
    {
        $idkb = $data['id'];
        $name = $data['name'];
        $description = $data['description'];
        $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $idkb, 'language' => $_SESSION['Language'] ));
        $data = mysqli_fetch_array($result2);
        if( $data['name'] )
        {
            $name = $data['name'];
        }
        if( $data['description'] )
        {
            $description = $data['description'];
        }
        $kbcats[$i] = array( 'id' => $idkb, 'name' => $name, 'urlfriendlyname' => getModRewriteFriendlyString($name), 'description' => $description );
        $idnumbers = array(  );
        $idnumbers[] = $idkb;
        kbgetcatids($idkb);
        $queryreport = '';
        foreach( $idnumbers as $idnumber )
        {
            $queryreport .= " OR categoryid='" . $idnumber . "'";
        }
        $queryreport = substr($queryreport, 4);
        $result2 = select_query('tblknowledgebase', "COUNT(*)", "(" . $queryreport . ")", '', '', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
        $data2 = mysqli_fetch_array($result2);
        $categorycount = $data2[0];
        $kbcats[$i]['numarticles'] = $categorycount;
    }
    $smarty->assign('kbcats', $kbcats);
    $result = select_query('tblknowledgebase', '', array( 'categoryid' => $catid ), "order` ASC,`title", 'ASC', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid", 'order', 'ASC');
    while( $data = mysqli_fetch_array($result) )
    {
        $id = $data['id'];
        $category = $data['category'];
        $title = $data['title'];
        $article = $data['article'];
        $views = $data['views'];
        $result2 = select_query('tblknowledgebase', '', array( 'parentid' => $id, 'language' => $_SESSION['Language'] ));
        $data = mysqli_fetch_array($result2);
        if( $data['title'] )
        {
            $title = $data['title'];
        }
        if( $data['article'] )
        {
            $article = $data['article'];
        }
        $kbarticles[] = array( 'id' => $id, 'category' => $category, 'title' => $title, 'urlfriendlytitle' => getModRewriteFriendlyString($title), 'article' => strip_tags($article), 'views' => $views );
    }
    $smarty->assign('kbarticles', $kbarticles);
}
else
{
    if( $action == 'search' )
    {
        check_token();
        $templatefile = 'knowledgebasecat';
        $catid = (int) $catid;
        if( $kbcid )
        {
            $catid = $kbcid;
        }
        else
        {
            if( !$catid )
            {
                $catid = 0;
            }
        }
        $idnumbers = array(  );
        $idnumbers[] = $catid;
        kbgetcatids($catid);
        if( $catid )
        {
            $smartyvalues['catid'] = $catid;
            $catparentid = $catid;
            $i = 0;
            while( $catparentid != '0' )
            {
                $result = select_query('tblknowledgebasecats', '', array( 'id' => $catparentid ));
                $data = mysqli_fetch_array($result);
                $cattempid = $data['id'];
                $catparentid = $data['parentid'];
                $catname = $data['name'];
                $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $cattempid, 'language' => $_SESSION['Language'] ));
                $data = mysqli_fetch_array($result2);
                if( $data['name'] )
                {
                    $catname = $data['name'];
                }
                if( $CONFIG['SEOFriendlyUrls'] )
                {
                    $catbreadcrumbnav = " > <a href=\"knowledgebase/" . $cattempid . '/' . getModRewriteFriendlyString($catname) . "\">" . $catname . "</a>" . $catbreadcrumbnav;
                }
                else
                {
                    $catbreadcrumbnav = " > <a href=\"knowledgebase.php?action=displaycat&amp;catid=" . $cattempid . "\">" . $catname . "</a>" . $catbreadcrumbnav;
                }
                $i++;
                if( 100 < $i )
                {
                    break;
                }
            }
            $breadcrumbnav .= $catbreadcrumbnav;
        }
        $breadcrumbnav .= " > <a href=\"knowledgebase.php?action=search&amp;search=" . $search . "\">Search</a>";
        $smarty->assign('breadcrumbnav', $breadcrumbnav);
        $kbarticles = array(  );
        $smartyvalues['searchterm'] = $search;
        $searchterms = array(  );
        $searchparts = explode(" ", WHMCS_Input_Sanitize::decode($search));
        foreach( $searchparts as $searchpart )
        {
            if( $searchpart )
            {
                $searchterms[] = "(title LIKE '%" . db_escape_string($searchpart) . "%' OR article LIKE '%" . db_escape_string($searchpart) . "%')";
            }
        }
        $searchqry = implode(" AND ", $searchterms);
        if( !$searchqry )
        {
            $searchqry = "id='x'";
        }
        $query = "SELECT DISTINCT id FROM tblknowledgebase WHERE " . $searchqry . " AND (SELECT categoryid FROM tblknowledgebaselinks WHERE ((articleid=tblknowledgebase.id) OR (articleid=tblknowledgebase.parentid)) LIMIT 1) IN (" . db_build_in_array($idnumbers) . ") ORDER BY `order` ASC,`title` ASC";
        $result = full_query($query);
        $articleids = array(  );
        while( $data = mysqli_fetch_array($result) )
        {
            $id = $data['id'];
            $result2 = select_query('tblknowledgebase', '', array( 'id' => $id ));
            $data = mysqli_fetch_array($result2);
            $title = $data['title'];
            $article = $data['article'];
            $views = $data['views'];
            $parentid = $data['parentid'];
            if( $parentid )
            {
                $result2 = select_query('tblknowledgebase', '', array( 'id' => $parentid ));
                $data = mysqli_fetch_array($result2);
                $id = $data['id'];
                $title = $data['title'];
                $article = $data['article'];
                $views = $data['views'];
            }
            $result2 = select_query('tblknowledgebasecats', "tblknowledgebasecats.hidden", array( 'articleid' => $id, 'hidden' => 'on' ), '', '', '', "tblknowledgebaselinks ON tblknowledgebaselinks.categoryid=tblknowledgebasecats.id");
            $data = mysqli_fetch_array($result2);
            if( !$data['hidden'] && !in_array($id, $articleids) )
            {
                $result2 = select_query('tblknowledgebase', '', array( 'parentid' => $id, 'language' => $_SESSION['Language'] ));
                $data = mysqli_fetch_array($result2);
                if( $data['title'] )
                {
                    $title = $data['title'];
                }
                if( $data['article'] )
                {
                    $article = $data['article'];
                }
                $kbarticles[] = array( 'id' => $id, 'title' => $title, 'urlfriendlytitle' => getModRewriteFriendlyString($title), 'article' => strip_tags($article), 'views' => $views );
                $articleids[] = $id;
            }
        }
        $smarty->assign('kbarticles', $kbarticles);
    }
    else
    {
        if( $action == 'displayarticle' )
        {
            $templatefile = 'knowledgebasearticle';
            if( $useful == 'vote' )
            {
                if( $vote == 'yes' )
                {
                    update_query('tblknowledgebase', array( 'useful' => "+1" ), array( 'id' => $id ));
                }
                update_query('tblknowledgebase', array( 'votes' => "+1" ), array( 'id' => $id ));
            }
            update_query('tblknowledgebase', array( 'views' => "+1" ), array( 'id' => $id ));
            $result = select_query('tblknowledgebase', '', array( 'id' => $id ));
            $data = mysqli_fetch_array($result);
            $id = $data['id'];
            $title = $data['title'];
            $article = $data['article'];
            $views = $data['views'];
            $useful = $data['useful'];
            $votes = $data['votes'];
            $private = $data['private'];
            if( !$id )
            {
                redirSystemURL('', "knowledgebase.php");
            }
            $result = select_query('tblknowledgebasecats', 'id,name,parentid,hidden', array( 'articleid' => $id ), '', '', '', "tblknowledgebaselinks ON tblknowledgebasecats.id=tblknowledgebaselinks.categoryid");
            $data = mysqli_fetch_array($result);
            $catid = $data['id'];
            $catname = $data['name'];
            $catparentid = $data['parentid'];
            $hidden = $data['hidden'];
            if( $hidden )
            {
                redirSystemURL('', "knowledgebase.php");
            }
            $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $catid, 'language' => $_SESSION['Language'] ));
            $data = mysqli_fetch_array($result2);
            if( $data['name'] )
            {
                $catname = $data['name'];
            }
            if( $CONFIG['SEOFriendlyUrls'] )
            {
                $catbreadcrumbnav = " > <a href=\"knowledgebase/" . $catid . '/' . getModRewriteFriendlyString($catname) . "\">" . $catname . "</a>";
            }
            else
            {
                $catbreadcrumbnav = " > <a href=\"knowledgebase.php?action=displaycat&amp;catid=" . $catid . "\">" . $catname . "</a>";
            }
            if( $catparentid )
            {
                $i = 0;
                while( $catparentid != '0' )
                {
                    $result = select_query('tblknowledgebasecats', '', array( 'id' => $catparentid ));
                    $data = mysqli_fetch_array($result);
                    $cattempid = $data['id'];
                    $catparentid = $data['parentid'];
                    $catname = $data['name'];
                    $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $cattempid, 'language' => $_SESSION['Language'] ));
                    $data = mysqli_fetch_array($result2);
                    if( $data['name'] )
                    {
                        $catname = $data['name'];
                    }
                    if( $CONFIG['SEOFriendlyUrls'] )
                    {
                        $catbreadcrumbnav = " > <a href=\"knowledgebase/" . $cattempid . '/' . getModRewriteFriendlyString($catname) . "\">" . $catname . "</a>" . $catbreadcrumbnav;
                    }
                    else
                    {
                        $catbreadcrumbnav = " > <a href=\"knowledgebase.php?action=displaycat&amp;catid=" . $cattempid . "\">" . $catname . "</a>" . $catbreadcrumbnav;
                    }
                    $i++;
                    if( 100 < $i )
                    {
                        break;
                    }
                }
            }
            $result2 = select_query('tblknowledgebase', '', array( 'parentid' => $id, 'language' => $_SESSION['Language'] ));
            $data = mysqli_fetch_array($result2);
            if( $data['title'] )
            {
                $title = $data['title'];
            }
            if( $data['article'] )
            {
                $article = $data['article'];
            }
            if( $CONFIG['SEOFriendlyUrls'] )
            {
                $catbreadcrumbnav .= " > <a href=\"knowledgebase/" . $id . '/' . getModRewriteFriendlyString($title) . ".html\">" . $title . "</a>";
            }
            else
            {
                $catbreadcrumbnav .= " > <a href=\"knowledgebase.php?action=displayarticle&amp;id=" . $id . "\">" . $title . "</a>";
            }
            $breadcrumbnav .= $catbreadcrumbnav;
            $smarty->assign('breadcrumbnav', $breadcrumbnav);
            if( !$_SESSION['uid'] && $private == 'on' )
            {
                $goto = 'knowledgebase';
                include("login.php");
            }
            $smartyvalues['kbarticle'] = array( 'id' => $id, 'categoryid' => $catid, 'categoryname' => $catname, 'title' => $title, 'text' => $article, 'views' => $views, 'useful' => $useful, 'votes' => $votes, 'voted' => $vote );
            $catlist = '';
            $result = select_query('tblknowledgebaselinks', '', array( 'articleid' => $id ));
            while( $data = simulate_fetch_assoc($result) )
            {
                $catlist .= $data['categoryid'] . ',';
            }
            $catlist = substr($catlist, 0, 0 - 1);
            $result = select_query('tblknowledgebase', '', "categoryid IN (" . $catlist . ") AND id != " . $id . " ORDER BY RAND()", '', '', '0,5', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
            while( $data = mysqli_fetch_array($result) )
            {
                $id = $data['id'];
                $category = $data['category'];
                $title = $data['title'];
                $article = $data['article'];
                $views = $data['views'];
                $result2 = select_query('tblknowledgebase', '', array( 'parentid' => $id, 'language' => $_SESSION['Language'] ));
                $data = mysqli_fetch_array($result2);
                if( $data['title'] )
                {
                    $title = $data['title'];
                }
                if( $data['article'] )
                {
                    $article = $data['article'];
                }
                $kbarticles[] = array( 'id' => $id, 'category' => $category, 'title' => $title, 'urlfriendlytitle' => getModRewriteFriendlyString($title), 'article' => strip_tags($article), 'views' => $views );
            }
            $smarty->assign('kbarticles', $kbarticles);
        }
        else
        {
            $templatefile = 'knowledgebase';
            $i = 1;
            for( $result = select_query('tblknowledgebasecats', '', array( 'parentid' => '0', 'hidden' => '', 'catid' => 0 ), 'name', 'ASC'); $data = mysqli_fetch_array($result); $i++ )
            {
                $idkb = $data['id'];
                $name = $data['name'];
                $description = $data['description'];
                $result2 = select_query('tblknowledgebasecats', '', array( 'catid' => $idkb, 'language' => $_SESSION['Language'] ));
                $data = mysqli_fetch_array($result2);
                if( $data['name'] )
                {
                    $name = $data['name'];
                }
                if( $data['description'] )
                {
                    $description = $data['description'];
                }
                $kbcats[$i] = array( 'id' => $idkb, 'name' => $name, 'urlfriendlyname' => getModRewriteFriendlyString($name), 'description' => $description );
                $idnumbers = array(  );
                $idnumbers[] = $idkb;
                kbgetcatids($idkb);
                $queryreport = '';
                foreach( $idnumbers as $idnumber )
                {
                    $queryreport .= " OR categoryid='" . $idnumber . "'";
                }
                $queryreport = substr($queryreport, 4);
                $result2 = select_query('tblknowledgebase', "COUNT(*)", "(" . $queryreport . ")", '', '', '', "tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid");
                $data2 = mysqli_fetch_array($result2);
                $categorycount = $data2[0];
                $kbcats[$i]['numarticles'] = $categorycount;
            }
            $smarty->assign('kbcats', $kbcats);
            $result = select_query('tblknowledgebase', "tblknowledgebase.*", "parentid=0", 'views', 'DESC', '0,5');
            while( $data = mysqli_fetch_array($result) )
            {
                $id = $data['id'];
                $category = $data['category'];
                $title = $data['title'];
                $article = $data['article'];
                $views = $data['views'];
                $result2 = select_query('tblknowledgebasecats', "tblknowledgebasecats.hidden", array( 'articleid' => $id, 'hidden' => 'on' ), '', '', '', "tblknowledgebaselinks ON tblknowledgebaselinks.categoryid=tblknowledgebasecats.id");
                $data = mysqli_fetch_array($result2);
                if( !$data['hidden'] )
                {
                    $result2 = select_query('tblknowledgebase', '', array( 'parentid' => $id, 'language' => $_SESSION['Language'] ));
                    $data = mysqli_fetch_array($result2);
                    if( $data['title'] )
                    {
                        $title = $data['title'];
                    }
                    if( $data['article'] )
                    {
                        $article = $data['article'];
                    }
                    $kbmostviews[] = array( 'id' => $id, 'category' => $category, 'title' => $title, 'urlfriendlytitle' => getModRewriteFriendlyString($title), 'article' => strip_tags($article), 'views' => $views );
                }
            }
            $smarty->assign('kbmostviews', $kbmostviews);
        }
    }
}
outputClientArea($templatefile);
function kbGetCatIds($catid)
{
    global $idnumbers;
    $result = select_query('tblknowledgebasecats', 'id', array( 'parentid' => $catid, 'hidden' => '' ));
    while( $data = mysqli_fetch_array($result) )
    {
        $cid = $data[0];
        $idnumbers[] = $cid;
        kbGetCatIds($cid);
    }
}