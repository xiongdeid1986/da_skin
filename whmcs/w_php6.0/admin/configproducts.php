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
$aInt = new WHMCS_Admin("View Products/Services");
$aInt->title = $aInt->lang('products', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'configproducts';
$aInt->helplink = "Configuring Products/Services";
$aInt->requiredFiles(array( 'modulefunctions', 'gatewayfunctions' ));
if( $action == 'getdownloads' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Edit Products/Services", true) )
    {
        exit( "Access Denied" );
    }
    $dir = $_POST['dir'];
    $dir = preg_replace("/[^0-9]/", '', $dir);
    echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
    $result = select_query('tbldownloadcats', '', array( 'parentid' => $dir ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $catid = $data['id'];
        $catname = $data['name'];
        echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"dir" . $catid . "/\">" . $catname . "</a></li>";
    }
    $result = select_query('tbldownloads', '', array( 'category' => $dir ), 'title', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $downid = $data['id'];
        $downtitle = $data['title'];
        $downfilename = $data['location'];
        $ext = end(explode(".", $downfilename));
        echo "<li class=\"file ext_" . $ext . "\"><a href=\"#\" rel=\"" . $downid . "\">" . $downtitle . "</a></li>";
    }
    echo "</ul>";
    exit();
}
if( $action == 'managedownloads' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Edit Products/Services", true) )
    {
        exit( "Access Denied" );
    }
    $result = select_query('tblproducts', 'downloads', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $downloads = $data['downloads'];
    $downloads = unserialize($downloads);
    if( !is_array($downloads) )
    {
        $downloads = array(  );
    }
    if( $adddl && !in_array($adddl, $downloads) )
    {
        $downloads[] = $adddl;
    }
    if( $remdl )
    {
        foreach( $downloads as $key => $downloadid )
        {
            if( $downloadid == $remdl )
            {
                unset($downloads[$key]);
            }
        }
    }
    update_query('tblproducts', array( 'downloads' => serialize($downloads) ), array( 'id' => $id ));
    printproductdownlads($downloads);
    exit();
}
if( $action == 'quickupload' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Edit Products/Services", true) )
    {
        exit( "Access Denied" );
    }
    $categorieslist = '';
    buildcategorieslist(0, 0);
    echo "<form method=\"post\" action=\"configproducts.php?action=uploadfile&id=" . $id . "\" id=\"quickuploadfrm\" enctype=\"multipart/form-data\">\n" . generate_token('form') . "\n<table width=\"100%\">\n<tr><td width=\"80\">Category:</td><td><select name=\"catid\" style=\"width:95%;\">" . $categorieslist . "</select></td></tr>\n<tr><td>Title:</td><td><input type=\"text\" name=\"title\" style=\"width:95%;\" /></td></tr>\n<tr><td>Description:</td><td><input type=\"text\" name=\"description\" style=\"width:95%;\" /></td></tr>\n<tr><td>Choose File:</td><td><input type=\"file\" name=\"uploadfile\" style=\"width:95%;\" /></td></tr>\n</table>\n</form>";
    exit();
}
if( $action == 'uploadfile' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Edit Products/Services", true) )
    {
        exit( "Access Denied" );
    }
    try
    {
        $file = new WHMCS_File_Upload('uploadfile');
        $filename = $file->move($whmcs->getDownloadsDir());
    }
    catch( Exception $e )
    {
        $aInt->gracefulExit($e->getMessage());
    }
    if( !$title )
    {
        $title = $filename;
    }
    $adddl = insert_query('tbldownloads', array( 'category' => $catid, 'type' => 'zip', 'title' => $title, 'description' => $description, 'location' => $filename, 'clientsonly' => 'on', 'productdownload' => 'on' ));
    logActivity("Added New Product Download - " . $title);
    $result = select_query('tblproducts', 'downloads', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $downloads = $data['downloads'];
    $downloads = unserialize($downloads);
    if( !is_array($downloads) )
    {
        $downloads = array(  );
    }
    $downloads[] = $adddl;
    update_query('tblproducts', array( 'downloads' => serialize($downloads) ), array( 'id' => $id ));
    redir("action=edit&id=" . $id . "&tab=7");
}
if( $action == 'adddownloadcat' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Edit Products/Services", true) )
    {
        exit( "Access Denied" );
    }
    $categorieslist = '';
    buildcategorieslist(0, 0);
    echo "<form method=\"post\" action=\"configproducts.php?action=createdownloadcat&id=" . $id . "\" id=\"adddownloadcatfrm\" enctype=\"multipart/form-data\">\n" . generate_token('form') . "\n<table width=\"100%\">\n<tr><td width=\"80\">Category:</td><td><select name=\"catid\" style=\"width:95%;\">" . $categorieslist . "</select></td></tr>\n<tr><td>Name:</td><td><input type=\"text\" name=\"title\" style=\"width:95%;\" /></td></tr>\n<tr><td>Description:</td><td><input type=\"text\" name=\"description\" style=\"width:95%;\" /></td></tr>\n</table>\n</form>";
    exit();
}
if( $action == 'createdownloadcat' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Products/Services");
    insert_query('tbldownloadcats', array( 'parentid' => $catid, 'name' => $title, 'description' => $description, 'hidden' => '' ));
    logActivity("Added New Download Category - " . $title);
    redir("action=edit&id=" . $id . "&tab=7");
}
if( $action == 'add' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create New Products/Services");
    $pid = insert_query('tblproducts', array( 'type' => $type, 'gid' => $gid, 'name' => $productname, 'paytype' => 'free', 'showdomainoptions' => 'on' ));
    redir("action=edit&id=" . $pid);
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Products/Services");
    $savefreedomainpaymentterms = $freedomainpaymentterms ? implode(',', $freedomainpaymentterms) : '';
    $savefreedomaintlds = $freedomaintlds ? implode(',', $freedomaintlds) : '';
    if( $tax == 'on' )
    {
        $tax = '1';
    }
    $overagesenabled = $overagesenabled ? '1,' . $overageunitsdisk . ',' . $overageunitsbw : '';
    $table = 'tblproducts';
    $array = array( 'type' => $type, 'gid' => $gid, 'name' => $name, 'description' => WHMCS_Input_Sanitize::decode($description), 'hidden' => $hidden, 'showdomainoptions' => $showdomainops, 'welcomeemail' => $welcomeemail, 'stockcontrol' => $stockcontrol, 'qty' => $qty, 'proratabilling' => $proratabilling, 'proratadate' => $proratadate, 'proratachargenextmonth' => $proratachargenextmonth, 'paytype' => $paytype, 'allowqty' => $allowqty, 'subdomain' => $subdomain, 'autosetup' => $autosetup, 'servertype' => $servertype, 'servergroup' => $servergroup, 'freedomain' => $freedomain, 'freedomainpaymentterms' => $savefreedomainpaymentterms, 'freedomaintlds' => $savefreedomaintlds, 'recurringcycles' => $recurringcycles, 'autoterminatedays' => $autoterminatedays, 'autoterminateemail' => $autoterminateemail, 'upgradepackages' => serialize($upgradepackages), 'configoptionsupgrade' => $configoptionsupgrade, 'billingcycleupgrade' => $billingcycleupgrade, 'upgradeemail' => $upgradeemail, 'overagesenabled' => $overagesenabled, 'overagesdisklimit' => $overagesdisklimit, 'overagesbwlimit' => $overagesbwlimit, 'overagesdiskprice' => $overagesdiskprice, 'overagesbwprice' => $overagesbwprice, 'tax' => $tax, 'affiliatepaytype' => $affiliatepaytype, 'affiliatepayamount' => $affiliatepayamount, 'affiliateonetime' => $affiliateonetime, 'order' => $order, 'retired' => $retired );
    $result = select_query('tblproducts', 'servertype', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $storedServerType = $data['servertype'];
    $hasServerTypeChanged = $servertype != $storedServerType;
    $counter = 1;
    while( $counter <= 24 )
    {
        $array['configoption' . $counter] = $hasServerTypeChanged ? '' : trim($packageconfigoption[$counter]);
        $counter += 1;
    }
    $where = array( 'id' => $id );
    update_query($table, $array, $where);
    foreach( $_POST['currency'] as $currency_id => $pricing )
    {
        $setupFeeReset = false;
        $setupFeeVars = array( 'msetupfee', 'qsetupfee', 'ssetupfee', 'asetupfee', 'bsetupfee', 'tsetupfee' );
        foreach( $setupFeeVars as $setupFeeVar )
        {
            if( $pricing[$setupFeeVar] && $pricing[$setupFeeVar] < 0 )
            {
                $pricing[$setupFeeVar] = 0;
                $setupFeeReset = true;
            }
        }
        update_query('tblpricing', $pricing, array( 'type' => 'product', 'currency' => $currency_id, 'relid' => $id ));
    }
    if( $customfieldname )
    {
        foreach( $customfieldname as $fid => $value )
        {
            update_query('tblcustomfields', array( 'fieldname' => $value, 'fieldtype' => $customfieldtype[$fid], 'description' => $customfielddesc[$fid], 'fieldoptions' => $customfieldoptions[$fid], 'regexpr' => WHMCS_Input_Sanitize::decode($customfieldregexpr[$fid]), 'adminonly' => $customadminonly[$fid], 'required' => $customrequired[$fid], 'showorder' => $customshoworder[$fid], 'showinvoice' => $customshowinvoice[$fid], 'sortorder' => $customsortorder[$fid] ), array( 'id' => $fid ));
        }
    }
    if( $addfieldname )
    {
        insert_query('tblcustomfields', array( 'type' => 'product', 'relid' => $id, 'fieldname' => $addfieldname, 'fieldtype' => $addfieldtype, 'description' => $addcustomfielddesc, 'fieldoptions' => $addfieldoptions, 'regexpr' => WHMCS_Input_Sanitize::decode($addregexpr), 'adminonly' => $addadminonly, 'required' => $addrequired, 'showorder' => $addshoworder, 'showinvoice' => $addshowinvoice, 'sortorder' => $addsortorder ));
    }
    delete_query('tblproductconfiglinks', array( 'pid' => $id ));
    if( $configoptionlinks )
    {
        foreach( $configoptionlinks as $gid )
        {
            insert_query('tblproductconfiglinks', array( 'gid' => $gid, 'pid' => $id ));
        }
    }
    RebuildModuleHookCache();
    run_hook('ProductEdit', array_merge(array( 'pid' => $id ), $array));
    run_hook('AdminProductConfigFieldsSave', array( 'pid' => $id ));
    $redirectURL = "action=edit&id=" . $id . ($tab ? "&tab=" . $tab : '') . "&success=true";
    if( $setupFeeReset )
    {
        $redirectURL .= "&setupReset=true";
    }
    redir($redirectURL);
}
if( $sub == 'deletecustomfield' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Products/Services");
    delete_query('tblcustomfields', array( 'id' => $fid ));
    delete_query('tblcustomfieldsvalues', array( 'fieldid' => $fid ));
    redir("action=edit&id=" . $id . "&tab=" . $tab);
}
if( $action == 'duplicatenow' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Create New Products/Services");
    $result = select_query('tblproducts', '', array( 'id' => $existingproduct ));
    $data = mysql_fetch_array($result);
    $addstr = '';
    foreach( $data as $key => $value )
    {
        if( is_numeric($key) )
        {
            if( $key == '0' )
            {
                $value = '';
            }
            if( $key == '3' )
            {
                $value = $newproductname;
            }
            $addstr .= "'" . db_escape_string($value) . "',";
        }
    }
    $addstr = substr($addstr, 0, 0 - 1);
    full_query("INSERT INTO tblproducts VALUES (" . $addstr . ")");
    $newproductid = mysql_insert_id();
    $result = select_query('tblpricing', '', array( 'type' => 'product', 'relid' => $existingproduct ));
    while( $data = mysql_fetch_array($result) )
    {
        $addstr = '';
        foreach( $data as $key => $value )
        {
            if( is_numeric($key) )
            {
                if( $key == '0' )
                {
                    $value = '';
                }
                if( $key == '3' )
                {
                    $value = $newproductid;
                }
                $addstr .= "'" . db_escape_string($value) . "',";
            }
        }
        $addstr = substr($addstr, 0, 0 - 1);
        full_query("INSERT INTO tblpricing VALUES (" . $addstr . ")");
    }
    $result2 = select_query('tblcustomfields', '', array( 'type' => 'product', 'relid' => $existingproduct ), 'id', 'ASC');
    while( $data = mysql_fetch_array($result2) )
    {
        $addstr = '';
        foreach( $data as $key => $value )
        {
            if( is_numeric($key) )
            {
                if( $key == '0' )
                {
                    $value = '';
                }
                if( $key == '2' )
                {
                    $value = $newproductid;
                }
                $addstr .= "'" . db_escape_string($value) . "',";
            }
        }
        $addstr = substr($addstr, 0, 0 - 1);
        full_query("INSERT INTO tblcustomfields VALUES (" . $addstr . ")");
    }
    redir("action=edit&id=" . $newproductid);
}
if( $sub == 'savegroup' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Product Groups");
    $file = new WHMCS_File_Directory('templates/orderforms');
    $orderTemplates = $file->getSubdirectories();
    if( $orderformtpl && !in_array($orderformtpl, $orderTemplates) )
    {
        $orderformtpl = '';
    }
    $disabledgateways = array(  );
    $gateways2 = getGatewaysArray();
    foreach( $gateways2 as $gateway => $gwname )
    {
        if( !$gateways[$gateway] )
        {
            $disabledgateways[] = $gateway;
        }
    }
    if( $ids )
    {
        update_query('tblproductgroups', array( 'name' => $name, 'orderfrmtpl' => $orderfrmtpl, 'disabledgateways' => implode(',', $disabledgateways), 'hidden' => $hidden ), array( 'id' => $ids ));
    }
    else
    {
        insert_query('tblproductgroups', array( 'name' => $name, 'orderfrmtpl' => $orderfrmtpl, 'disabledgateways' => implode(',', $disabledgateways), 'hidden' => $hidden, 'order' => get_query_val('tblproductgroups', "`order`", '', 'order', 'DESC') + 1 ));
    }
    redir();
}
if( $sub == 'deletegroup' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Product Groups");
    delete_query('tblproductgroups', array( 'id' => $id ));
    redir();
}
if( $sub == 'delete' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Products/Services");
    run_hook('ProductDelete', array( 'pid' => $id ));
    delete_query('tblproducts', array( 'id' => $id ));
    delete_query('tblproductconfiglinks', array( 'pid' => $id ));
    delete_query('tblcustomfields', array( 'type' => 'product', 'relid' => $id ));
    full_query("DELETE FROM tblcustomfieldsvalues WHERE fieldid NOT IN (SELECT id FROM tblcustomfields)");
    redir();
}
if( $sub == 'moveup' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Product Groups");
    $result = select_query('tblproductgroups', '', array( "`order`" => $order ));
    $data = mysql_fetch_array($result);
    $premid = $data['id'];
    $order1 = $order - 1;
    update_query('tblproductgroups', array( 'order' => $order ), array( "`order`" => $order1 ));
    update_query('tblproductgroups', array( 'order' => $order1 ), array( 'id' => $premid ));
    redir();
}
if( $sub == 'movedown' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Product Groups");
    $result = select_query('tblproductgroups', '', array( "`order`" => $order ));
    $data = mysql_fetch_array($result);
    $premid = $data['id'];
    $order1 = $order + 1;
    update_query('tblproductgroups', array( 'order' => $order ), array( "`order`" => $order1 ));
    update_query('tblproductgroups', array( 'order' => $order1 ), array( 'id' => $premid ));
    redir();
}
if( $action == 'updatesort' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Products/Services");
    foreach( $so as $pid => $sort )
    {
        update_query('tblproducts', array( 'order' => $sort ), array( 'id' => $pid ));
    }
    redir();
}
ob_start();
if( $action == '' )
{
    $result = select_query('tblproductgroups', "COUNT(*)", '');
    $data = mysql_fetch_array($result);
    $num_rows = $data[0];
    $result = select_query('tblproducts', "COUNT(*)", '');
    $data = mysql_fetch_array($result);
    $num_rows2 = $data[0];
    $aInt->deleteJSConfirm('doDelete', 'products', 'deleteproductconfirm', "?sub=delete&id=");
    $aInt->deleteJSConfirm('doGroupDelete', 'products', 'deletegroupconfirm', "?sub=deletegroup&id=");
    echo "\n<p>";
    echo $aInt->lang('products', 'description');
    echo "</p>\n<p><b>";
    echo $aInt->lang('addons', 'options');
    echo ":</b> <a id=\"Create-Group-link\" href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=creategroup\">";
    echo $aInt->lang('products', 'createnewgroup');
    echo "</a> | ";
    if( $num_rows == '0' )
    {
        echo "<font color=#cccccc>" . $aInt->lang('products', 'createnewproduct') . "</font>";
    }
    else
    {
        echo "<a id=\"Create-Product-link\"  href=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=create\">";
        echo $aInt->lang('products', 'createnewproduct');
        echo "</a>";
    }
    echo " | ";
    if( $num_rows2 == '0' )
    {
        echo "<font color=#cccccc>" . $aInt->lang('products', 'duplicateproduct') . "</font>";
    }
    else
    {
        echo "<a id=\"Duplicate-Product-link\"  href=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=duplicate\">";
        echo $aInt->lang('products', 'duplicateproduct');
        echo "</a>";
    }
    echo "</p>\n\n<form method=\"post\" action=\"configproducts.php?action=updatesort\">\n<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n<tr><th>";
    echo $aInt->lang('products', 'productname');
    echo "</th><th>";
    echo $aInt->lang('fields', 'type');
    echo "</th><th>";
    echo $aInt->lang('products', 'sortorder');
    echo "</th><th>";
    echo $aInt->lang('products', 'paytype');
    echo "</th><th>";
    echo $aInt->lang('products', 'stock');
    echo "</th><th>";
    echo $aInt->lang('products', 'autosetup');
    echo "</th><th width=\"20\"></th><th width=\"20\"></th></tr>\n";
    $result = select_query('tblproductgroups', '', '', 'order', 'DESC');
    $data = mysql_fetch_array($result);
    $lastorder = $data['order'];
    $result2 = select_query('tblproductgroups', '', '', 'order', 'ASC');
    $k = 0;
    while( $data = mysql_fetch_array($result2) )
    {
        $k++;
        $groupid = $data['id'];
        update_query('tblproductgroups', array( 'order' => $k ), array( 'id' => $groupid ));
        $name = $data['name'];
        $hidden = $data['hidden'];
        $order = $data['order'];
        $result = select_query('tblproducts', "COUNT(*)", array( 'gid' => $groupid ));
        $data = mysql_fetch_array($result);
        $num_rows = $data[0];
        if( 0 < $num_rows )
        {
            $deletelink = "alert('" . $aInt->lang('products', 'deletegrouperror', 1) . "')";
        }
        else
        {
            $deletelink = "doGroupDelete('" . $groupid . "')";
        }
        echo "<tr><td colspan=\"6\" style=\"background-color:#ffffdd;\"><div class=\"prodGroup\" align=\"left\"><b>" . $aInt->lang('fields', 'groupname') . ":</b> " . $name . " ";
        if( $hidden == 'on' )
        {
            echo "(Hidden) ";
        }
        if( $order != '1' )
        {
            echo "<a href=\"?sub=moveup&order=" . $order . generate_token('link') . "\"><img src=\"images/moveup.gif\" border=\"0\" align=\"absmiddle\" alt=\"" . $aInt->lang('products', 'navmoveup') . "\"></a>";
        }
        if( $order != $lastorder )
        {
            echo "<a href=\"?sub=movedown&order=" . $order . generate_token('link') . "\"><img src=\"images/movedown.gif\" border=\"0\" align=\"absmiddle\" alt=\"" . $aInt->lang('products', 'navmovedown') . "\"></a>";
        }
        echo "</div></td><td style=\"background-color:#ffffdd;\" align=center><a href=\"?action=editgroup&ids=" . $groupid . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></td><td  style=\"background-color:#ffffdd;\" align=center><a href=\"#\" onClick=\"" . $deletelink . ";return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a></td></tr>";
        $result = select_query('tblproducts', "id,type,name,paytype,autosetup,proratabilling,stockcontrol,qty,servertype,hidden,`order`,(SELECT COUNT(*) FROM tblhosting WHERE tblhosting.packageid=tblproducts.id) AS usagecount", array( 'gid' => $groupid ), "order` ASC,`name", 'ASC');
        for( $i = 0; $data = mysql_fetch_array($result); $i++ )
        {
            $id = $data['id'];
            $type = $data['type'];
            $name = $data['name'];
            $paytype = $data['paytype'];
            $autosetup = $data['autosetup'];
            $proratabilling = $data['proratabilling'];
            $stockcontrol = $data['stockcontrol'];
            $qty = $data['qty'];
            $servertype = ucfirst($data['servertype']);
            $hidden = $data['hidden'];
            $sortorder = $data['order'];
            $num_rows = $data['usagecount'];
            if( 0 < $num_rows )
            {
                $deletelink = "alert('" . $aInt->lang('products', 'deleteproducterror', 1) . "')";
            }
            else
            {
                $deletelink = "doDelete('" . $id . "')";
            }
            if( $autosetup == 'on' )
            {
                $autosetup = $aInt->lang('products', 'asetupafteracceptpendingorder');
            }
            else
            {
                if( $autosetup == 'order' )
                {
                    $autosetup = $aInt->lang('products', 'asetupinstantlyafterorder');
                }
                else
                {
                    if( $autosetup == 'payment' )
                    {
                        $autosetup = $aInt->lang('products', 'asetupafterpay');
                    }
                    else
                    {
                        if( $autosetup == '' )
                        {
                            $autosetup = $aInt->lang('products', 'off');
                        }
                    }
                }
            }
            if( $paytype == 'free' )
            {
                $paymenttype = $aInt->lang('billingcycles', 'free');
            }
            else
            {
                if( $paytype == 'onetime' )
                {
                    $paymenttype = $aInt->lang('billingcycles', 'onetime');
                }
                else
                {
                    $paymenttype = $aInt->lang('status', 'recurring');
                }
            }
            if( $proratabilling )
            {
                $paymenttype .= " (" . $aInt->lang('products', 'proratabilling') . ")";
            }
            if( $type == 'hostingaccount' )
            {
                $producttype = $aInt->lang('products', 'hostingaccount');
            }
            else
            {
                if( $type == 'reselleraccount' )
                {
                    $producttype = $aInt->lang('products', 'reselleraccount');
                }
                else
                {
                    if( $type == 'server' )
                    {
                        $producttype = $aInt->lang('products', 'dedicatedvpsserver');
                    }
                    else
                    {
                        $producttype = $aInt->lang('products', 'otherproductservice');
                    }
                }
            }
            if( $servertype )
            {
                $producttype .= " (" . $servertype . ")";
            }
            if( $stockcontrol )
            {
                $qtystock = $qty;
            }
            else
            {
                $qtystock = '-';
            }
            if( $hidden )
            {
                $name .= " (Hidden)";
                $hidden = " style=\"background-color:#efefef;\"";
            }
            echo "<tr class=\"product\" style=\"text-align:center;\"><td" . $hidden . ">" . $name . "</td><td" . $hidden . ">" . $producttype . "</td><td><input type=\"text\" name=\"so[" . $id . "]" . "\" value=\"" . $sortorder . "\" size=\"5\" style=\"font-size:10px;\" /></td><td" . $hidden . ">" . $paymenttype . "</td><td" . $hidden . ">" . $qtystock . "</td><td" . $hidden . ">" . $autosetup . "</td><td" . $hidden . "><a href=\"?action=edit&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a></td><td" . $hidden . "><a href=\"#\" onClick=\"" . $deletelink . ";return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a></td></tr>";
        }
        if( $i == '0' )
        {
            echo "<tr><td colspan=10 align=center>" . $aInt->lang('products', 'noproductsingroupsetup') . "</td></tr>";
        }
        else
        {
            echo "<tr><td></td><td></td><td><div align=\"center\"><input type=\"submit\" value=\"" . $aInt->lang('products', 'updatesort') . "\" style=\"font-size:10px;\" /></div></td><td></td><td></td><td></td><td></td><td></td></tr>";
        }
        $i = 0;
    }
    if( $k == '0' )
    {
        echo "<tr><td colspan=10 align=center>" . $aInt->lang('products', 'nogroupssetup') . "</td></tr>";
    }
    echo "</table>\n</div>\n</form>\n\n";
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblproducts', '', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $id = $data['id'];
        $type = $data['type'];
        $groupid = $gid = $data['gid'];
        $name = $data['name'];
        $description = $data['description'];
        $showdomainops = $data['showdomainoptions'];
        $hidden = $data['hidden'];
        $welcomeemail = $data['welcomeemail'];
        $paytype = $data['paytype'];
        $allowqty = $data['allowqty'];
        $subdomain = $data['subdomain'];
        $autosetup = $data['autosetup'];
        $servergroup = $data['servergroup'];
        $stockcontrol = $data['stockcontrol'];
        $qty = $data['qty'];
        $proratabilling = $data['proratabilling'];
        $proratadate = $data['proratadate'];
        $proratachargenextmonth = $data['proratachargenextmonth'];
        $servertype = $data['servertype'];
        $freedomain = $data['freedomain'];
        $counter = 1;
        while( $counter <= 24 )
        {
            $packageconfigoption[$counter] = isset($data['configoption' . $counter]) ? $data['configoption' . $counter] : null;
            $counter += 1;
        }
        $freedomainpaymentterms = $data['freedomainpaymentterms'];
        $freedomaintlds = $data['freedomaintlds'];
        $recurringcycles = $data['recurringcycles'];
        $autoterminatedays = $data['autoterminatedays'];
        $autoterminateemail = $data['autoterminateemail'];
        $tax = $data['tax'];
        $upgradepackages = $data['upgradepackages'];
        $configoptionsupgrade = $data['configoptionsupgrade'];
        $billingcycleupgrade = $data['billingcycleupgrade'];
        $upgradeemail = $data['upgradeemail'];
        $overagesenabled = $data['overagesenabled'];
        $overagesdisklimit = $data['overagesdisklimit'];
        $overagesbwlimit = $data['overagesbwlimit'];
        $overagesdiskprice = $data['overagesdiskprice'];
        $overagesbwprice = $data['overagesbwprice'];
        $affiliatepayamount = $data['affiliatepayamount'];
        $affiliatepaytype = $data['affiliatepaytype'];
        $affiliateonetime = $data['affiliateonetime'];
        $downloads = $data['downloads'];
        $retired = $data['retired'];
        $freedomainpaymentterms = explode(',', $freedomainpaymentterms);
        $freedomaintlds = explode(',', $freedomaintlds);
        $overagesenabled = explode(',', $overagesenabled);
        $upgradepackages = unserialize($upgradepackages);
        $downloads = unserialize($downloads);
        $order = $data['order'];
        echo "<script type=\"text/javascript\" src=\"../includes/jscript/jquerylq.js\"></script>\n<script type=\"text/javascript\" src=\"../includes/jscript/jqueryFileTree.js\"></script>\n<link href=\"../includes/jscript/css/jqueryFileTree.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n\n<h2>Edit Product</h2>\n<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "?action=save&id=" . $id;
        echo "\" name=\"packagefrm\">";
        $jscode = "function deletecustomfield(id) {\nif (confirm(\"Are you sure you want to delete this field and ALL DATA associated with it?\")) {\nwindow.location='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "&tab=3&sub=deletecustomfield&fid='+id+'" . generate_token('link') . "';\n}}\nfunction deleteoption(id) {\nif (confirm(\"Are you sure you want to delete this product configuration?\")) {\nwindow.location='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "&tab=4&sub=deleteoption&confid='+id+'" . generate_token('link') . "';\n}}";
        $jquerycode = "\$('#productdownloadsbrowser').fileTree({ root: '0', script: 'configproducts.php?action=getdownloads" . generate_token('link') . "', folderEvent: 'click', expandSpeed: 1, collapseSpeed: 1 }, function(file) {\n    \$.post(\"configproducts.php?action=managedownloads&id=" . $id . generate_token('link') . "&adddl=\"+file, function(data) {\n        \$(\"#productdownloadslist\").html(data);\n    });\n});\n\$(\".removedownload\").livequery(\"click\", function(event) {\n    var dlid = \$(this).attr(\"rel\");\n    \$.post(\"configproducts.php?action=managedownloads&id=" . $id . generate_token('link') . "&remdl=\"+dlid, function(data) {\n        \$(\"#productdownloadslist\").html(data);\n    });\n});\n\$(\"#showquickupload\").click(\n    function() {\n        \$(\"#quickupload\").dialog(\"open\");\n        \$(\"#quickupload\").load(\"configproducts.php?action=quickupload&id=" . $id . generate_token('link') . "\");\n        return false;\n    }\n);\n\$(\"#showadddownloadcat\").click(\n    function() {\n        \$(\"#adddownloadcat\").dialog(\"open\");\n        \$(\"#adddownloadcat\").load(\"configproducts.php?action=adddownloadcat&id=" . $id . generate_token('link') . "\");\n        return false;\n    }\n);\n";
        if( $success )
        {
            infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('global', 'changesuccessdesc'));
        }
        echo $infobox;
        if( $setupReset == 'true' )
        {
            infoBox($aInt->lang('global', 'information'), $aInt->lang('products', 'setupreset'));
            echo $infobox;
        }
        echo $aInt->Tabs(array( $aInt->lang('products', 'tabsdetails'), $aInt->lang('global', 'pricing'), $aInt->lang('products', 'tabsmodulesettings'), $aInt->lang('setup', 'customfields'), $aInt->lang('setup', 'configoptions'), $aInt->lang('products', 'tabsupgrades'), $aInt->lang('products', 'tabsfreedomain'), $aInt->lang('setup', 'other'), $aInt->lang('products', 'tabslinks') ));
        echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'producttype');
        echo "</td><td class=\"fieldarea\"><select name=\"type\" onChange=\"doFieldUpdate()\"><option value=\"hostingaccount\"";
        if( $type == 'hostingaccount' )
        {
            echo " SELECTED";
        }
        echo ">";
        echo $aInt->lang('products', 'hostingaccount');
        echo "<option value=\"reselleraccount\"";
        if( $type == 'reselleraccount' )
        {
            echo " SELECTED";
        }
        echo ">";
        echo $aInt->lang('products', 'reselleraccount');
        echo "<option value=\"server\"";
        if( $type == 'server' )
        {
            echo " SELECTED";
        }
        echo ">";
        echo $aInt->lang('products', 'dedicatedvpsserver');
        echo "<option value=\"other\"";
        if( $type == 'other' )
        {
            echo " SELECTED";
        }
        echo ">";
        echo $aInt->lang('setup', 'other');
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'productgroup');
        echo "</td><td class=\"fieldarea\"><select name=\"gid\">";
        $result = select_query('tblproductgroups', '', '', 'order', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $select_gid = $data['id'];
            $select_name = $data['name'];
            echo "<option value=\"" . $select_gid . "\"";
            if( $select_gid == $groupid )
            {
                echo " selected";
            }
            echo ">" . $select_name . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'productname');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"40\" name=\"name\" value=\"";
        echo $name;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'productdesc');
        echo "</td><td class=\"fieldarea\"><table cellsapcing=0 cellpadding=0><tr><td><textarea name=\"description\" cols=60 rows=5>";
        echo WHMCS_Input_Sanitize::encode($description);
        echo "</textarea></td><td>";
        echo $aInt->lang('products', 'htmlallowed');
        echo "<br>&lt;br /&gt; ";
        echo $aInt->lang('products', 'htmlnewline');
        echo "<br>&lt;strong&gt;";
        echo $aInt->lang('products', 'htmlbold');
        echo "&lt;/strong&gt; <b>";
        echo $aInt->lang('products', 'htmlbold');
        echo "</b><br>&lt;em&gt;";
        echo $aInt->lang('products', 'htmlitalics');
        echo "&lt;/em&gt; <i>";
        echo $aInt->lang('products', 'htmlitalics');
        echo "</i></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'welcomeemail');
        echo "</td><td class=\"fieldarea\"><select name=\"welcomeemail\"><option value=\"0\">";
        echo $aInt->lang('global', 'none');
        echo "</option>";
        $emails = array( "Hosting Account Welcome Email", "Reseller Account Welcome Email", "Dedicated/VPS Server Welcome Email", "SHOUTcast Welcome Email", "Other Product/Service Welcome Email" );
        foreach( $emails as $email )
        {
            $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'product', 'name' => $email, 'language' => '' ));
            while( $data = mysql_fetch_array($result) )
            {
                $mid = $data['id'];
                $name = $data['name'];
                echo "<option value=\"" . $mid . "\"";
                if( $welcomeemail == $mid )
                {
                    echo " selected";
                }
                echo ">" . $name . "</option>";
            }
        }
        $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'product', 'custom' => '1', 'language' => '' ), 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $mid = $data['id'];
            $name = $data['name'];
            echo "<option value=\"" . $mid . "\"";
            if( $welcomeemail == $mid )
            {
                echo " selected";
            }
            echo ">" . $name . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'requiredomain');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"showdomainops\"";
        if( $showdomainops == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'domainregoptionstick');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'stockcontrol');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"stockcontrol\"";
        if( $stockcontrol == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'stockcontroldesc');
        echo ":</label> <input type=\"text\" name=\"qty\" size=\"4\" value=\"";
        echo $qty;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'sortorder');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"order\" value=\"";
        echo $order;
        echo "\" size=\"5\"> ";
        echo $aInt->lang('products', 'sortorderdesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'applytax');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"tax\"";
        if( $tax == '1' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'applytaxdesc');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'hidden');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"hidden\"";
        if( $hidden == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'hiddendesc');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'retired');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"retired\" value=\"1\"";
        if( $retired )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'retireddesc');
        echo "</label></td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'paymenttype');
        echo "</td><td class=\"fieldarea\"><label><input type=\"radio\" name=\"paytype\" id=\"PayType-Free\" value=\"free\" onclick=\"\$('#pricingtbl').fadeOut()\"";
        if( $paytype == 'free' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('billingcycles', 'free');
        echo "</label> <label><input type=\"radio\" name=\"paytype\" value=\"onetime\" id=\"PayType-OneTime\" onclick=\"\$('#pricingtbl').fadeIn()\"";
        if( $paytype == 'onetime' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('billingcycles', 'onetime');
        echo "</label> <label><input type=\"radio\" name=\"paytype\" value=\"recurring\" id=\"PayType-Recurring\" onclick=\"\$('#pricingtbl').fadeIn()\"";
        if( $paytype == 'recurring' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('global', 'recurring');
        echo "</label></td></tr>\n<tr id=\"pricingtbl\"";
        if( $paytype == 'free' )
        {
            echo " style=\"display:none;\"";
        }
        echo "><td colspan=\"2\" align=\"center\"><br>\n<table cellspacing=\"1\" bgcolor=\"#cccccc\">\n<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold\"><td width=80>";
        echo $aInt->lang('currencies', 'currency');
        echo "</td><td width=80></td><td width=120>";
        echo $aInt->lang('billingcycles', 'onetime');
        echo '/';
        echo $aInt->lang('billingcycles', 'monthly');
        echo "</td><td width=90>";
        echo $aInt->lang('billingcycles', 'quarterly');
        echo "</td><td width=100>";
        echo $aInt->lang('billingcycles', 'semiannually');
        echo "</td><td width=90>";
        echo $aInt->lang('billingcycles', 'annually');
        echo "</td><td width=90>";
        echo $aInt->lang('billingcycles', 'biennially');
        echo "</td><td width=90>";
        echo $aInt->lang('billingcycles', 'triennially');
        echo "</td></tr>\n";
        $result = select_query('tblcurrencies', 'id,code', '', 'code', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $currency_id = $data['id'];
            $currency_code = $data['code'];
            $result2 = select_query('tblpricing', '', array( 'type' => 'product', 'currency' => $currency_id, 'relid' => $id ));
            $data = mysql_fetch_array($result2);
            $pricing_id = $data['id'];
            $cycles = array( 'monthly', 'quarterly', 'semiannually', 'annually', 'biennially', 'triennially' );
            if( !$pricing_id )
            {
                $insertarr = array( 'type' => 'product', 'currency' => $currency_id, 'relid' => $id );
                foreach( $cycles as $cycle )
                {
                    $insertarr[$cycle] = '-1';
                }
                insert_query('tblpricing', $insertarr);
                $result2 = select_query('tblpricing', '', array( 'type' => 'product', 'currency' => $currency_id, 'relid' => $id ));
                $data = mysql_fetch_array($result2);
            }
            $setupfields = $pricingfields = $disablefields = '';
            foreach( $cycles as $cycle )
            {
                $price = $data[$cycle];
                $setupfields .= "<td><input type=\"text\" name=\"currency[" . $currency_id . "][" . substr($cycle, 0, 1) . "setupfee]\" id=\"setup_" . $currency_code . '_' . $cycle . "\" size=\"10\" value=\"" . $data[substr($cycle, 0, 1) . 'setupfee'] . "\"" . ($price == '-1' ? " style=\"display:none\"" : '') . " /></td>";
                $pricingfields .= "<td><input type=\"text\" name=\"currency[" . $currency_id . "][" . $cycle . "]\" id=\"pricing_" . $currency_code . '_' . $cycle . "\" size=\"10\" value=\"" . $price . "\"" . ($price == '-1' ? " style=\"display:none;\"\"" : '') . " /></td>";
                $disablefields .= "<td><input type=\"checkbox\" class=\"pricingtgl\" currency=\"" . $currency_code . "\" cycle=\"" . $cycle . "\"" . ($price == '-1' ? '' : " checked=\"checked\"") . " /></td>";
            }
            echo "<tr bgcolor=\"#ffffff\" style=\"text-align:center\"><td rowspan=\"3\" bgcolor=\"#efefef\"><b>" . $currency_code . "</b></td><td>" . $aInt->lang('fields', 'setupfee') . "</td>" . $setupfields . "</td></tr><tr bgcolor=\"#ffffff\" style=\"text-align:center\"><td>" . $aInt->lang('fields', 'price') . "</td>" . $pricingfields . "</tr><tr bgcolor=\"#ffffff\" style=\"text-align:center\"><td>" . $aInt->lang('global', 'enable') . "</td>" . $disablefields . "</tr>";
        }
        $jquerycode .= "\$(\".pricingtgl\").click(function() {\n    var cycle = \$(this).attr(\"cycle\");\n    var currency = \$(this).attr(\"currency\");\n\n    if (\$(this).is(\":checked\")) {\n\n        \$(\"#pricing_\" + currency + \"_\" + cycle).val(\"0.00\").show();\n        \$(\"#setup_\" + currency + \"_\" + cycle).show();\n    } else {\n        \$(\"#pricing_\" + currency + \"_\" + cycle).val(\"-1.00\").hide();\n        \$(\"#setup_\" + currency + \"_\" + cycle).hide();\n    }\n});";
        echo "</table><br>\n</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'allowqty');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"allowqty\" value=\"1\"";
        if( $allowqty )
        {
            echo " checked";
        }
        echo " /> ";
        echo $aInt->lang('products', 'allowqtydesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'recurringcycleslimit');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"recurringcycles\" value=\"";
        echo $recurringcycles;
        echo "\" size=\"7\" /> ";
        echo $aInt->lang('products', 'recurringcycleslimitdesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'autoterminatefixedterm');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"autoterminatedays\" value=\"";
        echo $autoterminatedays;
        echo "\" size=\"7\" /> ";
        echo $aInt->lang('products', 'autoterminatefixedtermdesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'terminationemail');
        echo "</td><td class=\"fieldarea\"><select name=\"autoterminateemail\"><option value=\"0\">";
        echo $aInt->lang('global', 'none');
        echo "</option>";
        $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'product', 'language' => '' ));
        while( $data = mysql_fetch_array($result) )
        {
            $mid = $data['id'];
            $name = $data['name'];
            echo "<option value=\"" . $mid . "\"";
            if( $autoterminateemail == $mid )
            {
                echo " selected";
            }
            echo ">" . $name . "</option>";
        }
        echo "</select> ";
        echo $aInt->lang('products', 'chooseemailtplfixedtermend');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'proratabilling');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"proratabilling\"";
        if( $proratabilling == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'tickboxtoenable');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'proratadate');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"proratadate\" size=\"3\" value=\"";
        echo $proratadate;
        echo "\"> ";
        echo $aInt->lang('products', 'proratadatedesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'chargenextmonth');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"proratachargenextmonth\" size=\"3\" value=\"";
        echo $proratachargenextmonth;
        echo "\"> ";
        echo $aInt->lang('products', 'chargenextmonthdesc');
        echo "</td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab2box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\" width=150>";
        echo $aInt->lang('products', 'modulename');
        echo "</td><td class=\"fieldarea\"><select name=\"servertype\" onChange=\"submit()\"><option value=\"\">";
        echo $aInt->lang('global', 'none');
        $server = new WHMCS_Module_Server();
        $modulesarray = $server->getList();
        foreach( $modulesarray as $module )
        {
            echo "<option value=\"" . $module . "\"";
            if( $module == $servertype )
            {
                echo " selected";
            }
            echo ">" . ucfirst($module) . "</option>";
        }
        echo "</select></td></tr>\n";
        if( $servertype )
        {
            echo "<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('products', 'servergroup');
            echo "</td><td class=\"fieldarea\"><select name=\"servergroup\"><option value=\"0\">";
            echo $aInt->lang('global', 'none');
            echo "</option>";
            $result2 = select_query('tblservergroups', '', '', 'name', 'ASC');
            while( $data2 = mysql_fetch_array($result2) )
            {
                $groupid = $data2['id'];
                $groupname = $data2['name'];
                echo "<option value=\"" . $groupid . "\"";
                if( $groupid == $servergroup )
                {
                    echo " selected";
                }
                echo ">" . $groupname . "</option>";
            }
            echo "</select></td></tr>\n";
        }
        echo "</table>\n\n<br>\n\n";
        if( $servertype && in_array($servertype, $modulesarray) )
        {
            echo "\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\"><tr>\n";
            if( !isValidforPath($servertype) )
            {
                exit( "Invalid Server Module Name" );
            }
            include("../modules/servers/" . $servertype . '/' . $servertype . ".php");
            if( function_exists($servertype . '_ConfigOptions') )
            {
                $configarray = call_user_func($servertype . '_ConfigOptions', array(  ));
                $i = 0;
                $previouslyConfigured = false;
                if( !empty($packageconfigoption) )
                {
                    foreach( $packageconfigoption as $value )
                    {
                        if( $value !== '' )
                        {
                            $previouslyConfigured = true;
                        }
                    }
                }
                foreach( $configarray as $key => $values )
                {
                    $i++;
                    if( !$values['FriendlyName'] )
                    {
                        $values['FriendlyName'] = $key;
                    }
                    $values['Name'] = "packageconfigoption[" . $i . "]";
                    $values['Value'] = isset($packageconfigoption[$i]) && $previouslyConfigured ? $packageconfigoption[$i] : null;
                    echo "<td class=\"fieldlabel\">" . $values['FriendlyName'] . "</td><td class=\"fieldarea\">" . moduleConfigFieldOutput($values) . "</td>";
                    if( $i % 2 )
                    {
                    }
                    else
                    {
                        echo "</tr><tr>";
                    }
                }
            }
            echo "</tr></table>\n\n<br>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=20><input type=\"radio\" name=\"autosetup\" value=\"order\" id=\"autosetup_order\"";
            if( $autosetup == 'order' )
            {
                echo " CHECKED";
            }
            echo "></td><td class=\"fieldarea\">";
            echo $aInt->lang('products', 'asetupinstantlyafterorderdesc');
            echo "</td></tr>\n<tr><td><input type=\"radio\" name=\"autosetup\" value=\"payment\" id=\"autosetup_payment\"";
            if( $autosetup == 'payment' )
            {
                echo " CHECKED";
            }
            echo "></td><td class=\"fieldarea\">";
            echo $aInt->lang('products', 'asetupafterpaydesc');
            echo "</td></tr>\n<tr><td><input type=\"radio\" name=\"autosetup\" value=\"on\" id=\"autosetup_on\"";
            if( $autosetup == 'on' )
            {
                echo " CHECKED";
            }
            echo "></td><td class=\"fieldarea\">";
            echo $aInt->lang('products', 'asetupmadesc');
            echo "</td></tr>\n<tr><td><input type=\"radio\" name=\"autosetup\" value=\"\" id=\"autosetup_no\"";
            if( $autosetup == '' )
            {
                echo " CHECKED";
            }
            echo "></td><td class=\"fieldarea\">";
            echo $aInt->lang('products', 'noautosetupdesc');
            echo "</td></tr>\n</table>\n\n";
        }
        echo "\n  </div>\n</div>\n<div id=\"tab3box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
        $result = select_query('tblcustomfields', '', array( 'type' => 'product', 'relid' => $id ), "sortorder` ASC,`id", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $fid = $data['id'];
            $fieldname = $data['fieldname'];
            $fieldtype = $data['fieldtype'];
            $description = $data['description'];
            $fieldoptions = $data['fieldoptions'];
            $regexpr = $data['regexpr'];
            $adminonly = $data['adminonly'];
            $required = $data['required'];
            $showorder = $data['showorder'];
            $showinvoice = $data['showinvoice'];
            $sortorder = $data['sortorder'];
            echo "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=100 class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'fieldname');
            echo "</td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><input type=\"text\" name=\"customfieldname[";
            echo $fid;
            echo "]\" value=\"";
            echo $fieldname;
            echo "\" size=\"30\"></td><td align=\"right\">";
            echo $aInt->lang('customfields', 'order');
            echo " <input type=\"text\" name=\"customsortorder[";
            echo $fid;
            echo "]\" value=\"";
            echo $sortorder;
            echo "\" size=\"5\"></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'fieldtype');
            echo "</td><td class=\"fieldarea\"><select name=\"customfieldtype[";
            echo $fid;
            echo "]\">\n<option value=\"text\"";
            if( $fieldtype == 'text' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetextbox');
            echo "</option>\n<option value=\"link\"";
            if( $fieldtype == 'link' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typelink');
            echo "</option>\n<option value=\"password\"";
            if( $fieldtype == 'password' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typepassword');
            echo "</option>\n<option value=\"dropdown\"";
            if( $fieldtype == 'dropdown' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typedropdown');
            echo "</option>\n<option value=\"tickbox\"";
            if( $fieldtype == 'tickbox' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetickbox');
            echo "</option>\n<option value=\"textarea\"";
            if( $fieldtype == 'textarea' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetextarea');
            echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'description');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfielddesc[";
            echo $fid;
            echo "]\" value=\"";
            echo $description;
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'descriptioninfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'validation');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfieldregexpr[";
            echo $fid;
            echo "]\" value=\"";
            echo WHMCS_Input_Sanitize::encode($regexpr);
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'validationinfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'selectoptions');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfieldoptions[";
            echo $fid;
            echo "]\" value=\"";
            echo $fieldoptions;
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'selectoptionsinfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><input type=\"checkbox\" name=\"customadminonly[";
            echo $fid;
            echo "]\"";
            if( $adminonly == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'adminonly');
            echo " <input type=\"checkbox\" name=\"customrequired[";
            echo $fid;
            echo "]\"";
            if( $required == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'requiredfield');
            echo " <input type=\"checkbox\" name=\"customshoworder[";
            echo $fid;
            echo "]\"";
            if( $showorder == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'orderform');
            echo " <input type=\"checkbox\" name=\"customshowinvoice[";
            echo $fid;
            echo "]\"";
            if( $showinvoice )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'showinvoice');
            echo "</td><td align=\"right\"><a href=\"#\" onClick=\"deletecustomfield('";
            echo $fid;
            echo "');return false\">";
            echo $aInt->lang('customfields', 'deletefield');
            echo "</a></td></tr></table></td></tr>\n</table><br>\n";
        }
        echo "<b>";
        echo $aInt->lang('customfields', 'addfield');
        echo "</b><br><br>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=100 class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'fieldname');
        echo "</td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><input type=\"text\" name=\"addfieldname\" size=\"30\"></td><td align=\"right\">";
        echo $aInt->lang('customfields', 'order');
        echo " <input type=\"text\" name=\"addsortorder\" size=\"5\" value=\"0\"></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'fieldtype');
        echo "</td><td class=\"fieldarea\"><select name=\"addfieldtype\">\n<option value=\"text\">";
        echo $aInt->lang('customfields', 'typetextbox');
        echo "</option>\n<option value=\"link\">";
        echo $aInt->lang('customfields', 'typelink');
        echo "</option>\n<option value=\"password\">";
        echo $aInt->lang('customfields', 'typepassword');
        echo "</option>\n<option value=\"dropdown\">";
        echo $aInt->lang('customfields', 'typedropdown');
        echo "</option>\n<option value=\"tickbox\">";
        echo $aInt->lang('customfields', 'typetickbox');
        echo "</option>\n<option value=\"textarea\">";
        echo $aInt->lang('customfields', 'typetextarea');
        echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addcustomfielddesc\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'descriptioninfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'validation');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addregexpr\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'validationinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'selectoptions');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addfieldoptions\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'selectoptionsinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"addadminonly\"> ";
        echo $aInt->lang('customfields', 'adminonly');
        echo " <input type=\"checkbox\" name=\"addrequired\"> ";
        echo $aInt->lang('customfields', 'requiredfield');
        echo " <input type=\"checkbox\" name=\"addshoworder\"> ";
        echo $aInt->lang('customfields', 'orderform');
        echo " <input type=\"checkbox\" name=\"addshowinvoice\"> ";
        echo $aInt->lang('customfields', 'showinvoice');
        echo "</td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab4box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"150\" class=\"fieldlabel\">";
        echo $aInt->lang('products', 'assignedoptiongroups');
        echo "</td><td class=\"fieldarea\"><select name=\"configoptionlinks[]\" size=\"8\" style=\"width:90%\" multiple>\n";
        $configoptionlinks = array(  );
        $result = select_query('tblproductconfiglinks', '', array( 'pid' => $id ));
        while( $data = mysql_fetch_array($result) )
        {
            $configoptionlinks[] = $data['gid'];
        }
        $result = select_query('tblproductconfiggroups', '', '', 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $confgroupid = $data['id'];
            $name = $data['name'];
            $description = $data['description'];
            echo "<option value=\"" . $confgroupid . "\"";
            if( in_array($confgroupid, $configoptionlinks) )
            {
                echo " selected";
            }
            echo ">" . $name . " - " . $description . "</option>";
        }
        echo "</select></td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab5box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'packagesupgrades');
        echo "</td><td class=\"fieldarea\"><select name=\"upgradepackages[]\" size=\"10\" multiple>";
        $query = "SELECT tblproducts.id,tblproductgroups.name AS groupname,tblproducts.name AS productname FROM tblproducts INNER JOIN tblproductgroups ON tblproductgroups.id=tblproducts.gid ORDER BY tblproductgroups.`order`,tblproducts.`order`,tblproducts.name ASC";
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $productid = $data['id'];
            $groupname = $data['groupname'];
            $productname = $data['productname'];
            if( $id != $productid )
            {
                echo "<option value=\"" . $productid . "\"";
                if( @in_array($productid, $upgradepackages) )
                {
                    echo " selected";
                }
                echo ">" . $groupname . " - " . $productname . "</option>";
            }
        }
        echo "</select><br>";
        echo $aInt->lang('products', 'usectrlclickpkgs');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('setup', 'configoptions');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"configoptionsupgrade\"";
        if( $configoptionsupgrade )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'tickboxallowconfigoptupdowngrades');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'upgradeemail');
        echo "</td><td class=\"fieldarea\"><select name=\"upgradeemail\"><option value=\"0\">";
        echo $aInt->lang('global', 'none');
        echo "</option>";
        $emails = array( $aInt->lang('products', 'emailshostingac'), $aInt->lang('products', 'emailsresellerac'), $aInt->lang('products', 'emailsvpsdediserver'), $aInt->lang('products', 'emailsother') );
        foreach( $emails as $email )
        {
            $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'product', 'name' => $email, 'language' => '' ));
            while( $data = mysql_fetch_array($result) )
            {
                $mid = $data['id'];
                $name = $data['name'];
                echo "<option value=\"" . $mid . "\"";
                if( $upgradeemail == $mid )
                {
                    echo " selected";
                }
                echo ">" . $name . "</option>";
            }
        }
        $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'product', 'custom' => '1', 'language' => '' ));
        while( $data = mysql_fetch_array($result) )
        {
            $mid = $data['id'];
            $name = $data['name'];
            echo "<option value=\"" . $mid . "\"";
            if( $upgradeemail == $mid )
            {
                echo " selected";
            }
            echo ">" . $name . "</option>";
        }
        echo "</select></td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab6box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'tabsfreedomain');
        echo "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"freedomain\" value=\"\"";
        if( !$freedomain )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('global', 'none');
        echo "<br /><input type=\"radio\" name=\"freedomain\" value=\"once\"";
        if( $freedomain == 'once' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'freedomainrenewnormal');
        echo "<br /><input type=\"radio\" name=\"freedomain\" value=\"on\"";
        if( $freedomain == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('products', 'freedomainfreerenew');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'freedomainpayterms');
        echo "</td><td class=\"fieldarea\"><select name=\"freedomainpaymentterms[]\" size=\"6\" multiple>\n<option value=\"onetime\"";
        if( in_array('onetime', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'onetime');
        echo "</option>\n<option value=\"monthly\"";
        if( in_array('monthly', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'monthly');
        echo "</option>\n<option value=\"quarterly\"";
        if( in_array('quarterly', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'quarterly');
        echo "</option>\n<option value=\"semiannually\"";
        if( in_array('semiannually', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'semiannually');
        echo "</option>\n<option value=\"annually\"";
        if( in_array('annually', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'annually');
        echo "</option>\n<option value=\"biennially\"";
        if( in_array('biennially', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'biennially');
        echo "</option>\n<option value=\"triennially\"";
        if( in_array('triennially', $freedomainpaymentterms) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('billingcycles', 'triennially');
        echo "</option>\n</select><br>";
        echo $aInt->lang('products', 'selectfreedomainpayterms');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'freedomaintlds');
        echo "</td>\n    <td class=\"fieldarea\"><select name=\"freedomaintlds[]\" size=\"5\" multiple>";
        $query = "SELECT DISTINCT extension FROM tbldomainpricing ORDER BY `order` ASC";
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $extension = $data['extension'];
            echo "<option";
            if( in_array($extension, $freedomaintlds) )
            {
                echo " selected";
            }
            echo ">" . $extension;
        }
        echo "</select><br>";
        echo $aInt->lang('products', 'usectrlclickpayterms');
        echo "</td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab7box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n";
        $producteditfieldsarray = run_hook('AdminProductConfigFields', array( 'pid' => $id ));
        if( is_array($producteditfieldsarray) )
        {
            foreach( $producteditfieldsarray as $pv )
            {
                foreach( $pv as $k => $v )
                {
                    echo "<tr><td class=\"fieldlabel\">" . $k . "</td><td class=\"fieldarea\">" . $v . "</td></tr>";
                }
            }
        }
        echo "<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'customaffiliatepayout');
        echo "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"affiliatepaytype\" value=\"\"";
        if( $affiliatepaytype == '' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('affiliates', 'usedefault');
        echo " <input type=\"radio\" name=\"affiliatepaytype\" value=\"percentage\"";
        if( $affiliatepaytype == 'percentage' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('affiliates', 'percentage');
        echo " <input type=\"radio\" name=\"affiliatepaytype\" value=\"fixed\"";
        if( $affiliatepaytype == 'fixed' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('affiliates', 'fixedamount');
        echo " <input type=\"radio\" name=\"affiliatepaytype\" value=\"none\"";
        if( $affiliatepaytype == 'none' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('affiliates', 'nocommission');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('affiliates', 'affiliatepayamount');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"affiliatepayamount\" value=\"";
        echo $affiliatepayamount;
        echo "\" size=\"10\"> <input type=\"checkbox\" name=\"affiliateonetime\"";
        if( $affiliateonetime == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('affiliates', 'onetimepayout');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'subdomainoptions');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"subdomain\" value=\"";
        echo $subdomain;
        echo "\" size=\"40\"> ";
        echo $aInt->lang('products', 'subdomainoptionsdesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'associateddownloads');
        echo "</td><td class=\"fieldarea\">";
        echo $aInt->lang('products', 'associateddownloadsdesc');
        echo "<br />\n<table align=\"center\"><tr><td valign=\"top\">\n<div align=\"center\"><strong>";
        echo $aInt->lang('products', 'availablefiles');
        echo "</strong></div>\n<div id=\"productdownloadsbrowser\" style=\"width: 250px;height: 200px;border-top: solid 1px #BBB;border-left: solid 1px #BBB;border-bottom: solid 1px #FFF;border-right: solid 1px #FFF;background: #FFF;overflow: scroll;padding: 5px;\"></div>\n</td><td><></td><td valign=\"top\">\n<div align=\"center\"><strong>";
        echo $aInt->lang('products', 'selectedfiles');
        echo "</strong></div>\n<div id=\"productdownloadslist\" style=\"width: 250px;height: 200px;border-top: solid 1px #BBB;border-left: solid 1px #BBB;border-bottom: solid 1px #FFF;border-right: solid 1px #FFF;background: #FFF;overflow: scroll;padding: 5px;\">";
        printproductdownlads($downloads);
        echo "</div>\n</td></tr></table>\n<div align=\"center\"><input type=\"button\" value=\"";
        echo $aInt->lang('products', 'addcategory');
        echo "\" class=\"button\" id=\"showadddownloadcat\" /> <input type=\"button\" value=\"";
        echo $aInt->lang('products', 'quickupload');
        echo "\" class=\"button\" id=\"showquickupload\" /></div>\n</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'overagesbilling');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"overagesenabled\" value=\"1\"";
        if( $overagesenabled[0] )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('global', 'ticktoenable');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'overagesoftlimits');
        echo "</td><td class=\"fieldarea\">";
        echo $aInt->lang('products', 'overagediskusage');
        echo " <input type=\"text\" name=\"overagesdisklimit\" value=\"";
        echo $overagesdisklimit;
        echo "\" size=\"10\"> <select name=\"overageunitsdisk\"><option>MB</option><option";
        if( $overagesenabled[1] == 'GB' )
        {
            echo " selected";
        }
        echo ">GB</option><option";
        if( $overagesenabled[1] == 'TB' )
        {
            echo " selected";
        }
        echo ">TB</option></select> ";
        echo $aInt->lang('products', 'overagebandwidth');
        echo " <input type=\"text\" name=\"overagesbwlimit\" value=\"";
        echo $overagesbwlimit;
        echo "\" size=\"10\"> <select name=\"overageunitsbw\"><option>MB</option><option";
        if( $overagesenabled[2] == 'GB' )
        {
            echo " selected";
        }
        echo ">GB</option><option";
        if( $overagesenabled[2] == 'TB' )
        {
            echo " selected";
        }
        echo ">TB</option></select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'overagecosts');
        echo "</td><td class=\"fieldarea\">";
        echo $aInt->lang('products', 'overagediskusage');
        echo " <input type=\"text\" name=\"overagesdiskprice\" value=\"";
        echo $overagesdiskprice;
        echo "\" size=\"10\"> ";
        echo $aInt->lang('products', 'overagebandwidth');
        echo " <input type=\"text\" name=\"overagesbwprice\" value=\"";
        echo $overagesbwprice;
        echo "\" size=\"10\"> (";
        echo $aInt->lang('products', 'priceperunit');
        echo ")</td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab8box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'directscartlink');
        echo "</td><td class=\"fieldarea\"><input id=\"Direct-Link\" type=\"text\" size=\"100\" value=\"";
        echo $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
        echo "/cart.php?a=add&pid=";
        echo $id;
        echo "\" readonly></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'directscarttpllink');
        echo "</td><td class=\"fieldarea\"><input id=\"Direct-Link-With-Template\" type=\"text\" size=\"100\" value=\"";
        echo $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
        echo "/cart.php?a=add&pid=";
        echo $id;
        echo "&carttpl=cart\" readonly></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'directscartdomlink');
        echo "</td><td class=\"fieldarea\"><input id=\"Direct-Link-Including-Domain\" type=\"text\" size=\"100\" value=\"";
        echo $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
        echo "/cart.php?a=add&pid=";
        echo $id;
        echo "&sld=whmcs&tld=.com\" readonly></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'productgcartlink');
        echo "</td><td class=\"fieldarea\"><input id=\"Product-Group-Cart-Link\" type=\"text\" size=\"100\" value=\"";
        echo $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
        echo "/cart.php?gid=";
        echo $gid;
        echo "\" readonly></td></tr>\n</table>\n\n  </div>\n</div>\n\n<p align=\"center\"><input type=\"submit\" value=\"Save Changes\" class=\"button\"> <input type=\"button\" value=\"";
        echo $aInt->lang('products', 'backtoproductlist');
        echo "\" onClick=\"window.location='configproducts.php'\" class=\"button\"></p>\n\n<input type=\"hidden\" name=\"tab\" id=\"tab\" value=\"";
        echo $_REQUEST['tab'];
        echo "\" />\n\n</form>\n\n";
        echo $aInt->jqueryDialog('quickupload', "Quick File Upload", "Loading...", array( 'Save' => "\$('#quickuploadfrm').submit();\n", 'Cancel' => '' ));
        echo $aInt->jqueryDialog('adddownloadcat', "Add Category", "Loading...", array( 'Save' => "\$('#adddownloadcatfrm').submit();\n", 'Cancel' => '' ));
    }
    else
    {
        if( $action == 'create' )
        {
            checkPermission("Create New Products/Services");
            echo "\n<h2>Add New Product</h2>\n\n<form id=\"frmAddProduct\" method=\"post\" action=\"";
            echo $whmcs->getPhpSelf();
            echo "?action=add\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=150 class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'producttype');
            echo "</td><td class=\"fieldarea\"><select name=\"type\" id=\"Product-Type\"><option value=\"hostingaccount\"";
            if( $type == 'hostingaccount' )
            {
                echo " SELECTED";
            }
            echo ">";
            echo $aInt->lang('products', 'hostingaccount');
            echo "<option value=\"reselleraccount\"";
            if( $type == 'reselleraccount' )
            {
                echo " SELECTED";
            }
            echo ">";
            echo $aInt->lang('products', 'reselleraccount');
            echo "<option value=\"server\"";
            if( $type == 'server' )
            {
                echo " SELECTED";
            }
            echo ">";
            echo $aInt->lang('products', 'dedicatedvpsserver');
            echo "<option value=\"other\"";
            if( $type == 'other' )
            {
                echo " SELECTED";
            }
            echo ">";
            echo $aInt->lang('products', 'otherproductservice');
            echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('products', 'productgroup');
            echo "</td><td class=\"fieldarea\"><select name=\"gid\">";
            $query2 = "SELECT * FROM tblproductgroups ORDER BY `order` ASC";
            $result2 = full_query($query2);
            while( $data = mysql_fetch_array($result2) )
            {
                $gid = $data['id'];
                $gname = $data['name'];
                echo "<option value=\"" . $gid . "\"";
                if( $gid == $groupid )
                {
                    echo " SELECTED";
                }
                echo ">" . $gname;
            }
            echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('products', 'productname');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"productname\" size=\"50\"></td></tr>\n</table>\n<P ALIGN=\"center\"><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'continue');
            echo " >>\" class=\"button\"></P>\n</form>\n\n";
        }
        else
        {
            if( $action == 'duplicate' )
            {
                checkPermission("Create New Products/Services");
                echo "\n<h2>";
                echo $aInt->lang('products', 'duplicateproduct');
                echo "</h2>\n\n<form method=\"post\" action=\"";
                echo $whmcs->getPhpSelf();
                echo "?action=duplicatenow\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=150 class=\"fieldlabel\">";
                echo $aInt->lang('products', 'existingproduct');
                echo "</td><td class=\"fieldarea\"><select name=\"existingproduct\">";
                $products = new WHMCS_Product_Products();
                $productsList = $products->getProducts();
                foreach( $productsList as $data )
                {
                    $pid = $data['id'];
                    $groupname = $data['groupname'];
                    $prodname = $data['name'];
                    echo "<option value=\"" . $pid . "\">" . $groupname . " - " . $prodname . "</option>";
                }
                echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
                echo $aInt->lang('products', 'newproductname');
                echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"newproductname\" size=\"50\"></td></tr>\n</table>\n<P ALIGN=\"center\"><input type=\"submit\" value=\"";
                echo $aInt->lang('global', 'continue');
                echo " >>\" class=\"button\"></P>\n</form>\n\n";
            }
            else
            {
                if( $action == 'creategroup' || $action == 'editgroup' )
                {
                    checkPermission("Manage Product Groups");
                    $result = select_query('tblproductgroups', '', array( 'id' => $ids ));
                    $data = mysql_fetch_array($result);
                    $ids = $data['id'];
                    $name = $data['name'];
                    $orderfrmtpl = $data['orderfrmtpl'];
                    $disabledgateways = $data['disabledgateways'];
                    $hidden = $data['hidden'];
                    $disabledgateways = explode(',', $disabledgateways);
                    echo "\n<h2>";
                    echo $aInt->lang('products', $action == 'creategroup' ? 'creategroup' : 'editgroup');
                    echo "</h2>\n\n<form id=\"frmAddProductGroup\" method=\"post\" action=\"";
                    echo $whmcs->getPhpSelf();
                    echo "?sub=savegroup&ids=";
                    echo $ids;
                    echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"25%\" class=\"fieldlabel\">";
                    echo $aInt->lang('products', 'productgroupname');
                    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"40\" value=\"";
                    echo $name;
                    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\"><br></td><td class=\"fieldarea\"></td></tr>\n<tr><td class=\"fieldlabel\">";
                    echo $aInt->lang('products', 'orderfrmtpl');
                    echo "</td><td class=\"fieldarea\">\n";
                    $template = $whmcs->get_config('OrderFormTemplate');
                    if( $template == 'ajaxcart' )
                    {
                        echo $aInt->lang('products', 'orderfrmtplajaxcartenabled');
                    }
                    else
                    {
                        echo "<div><label><input type=\"radio\" name=\"orderfrmtpl\" value=\"\"";
                        if( !$orderfrmtpl )
                        {
                            echo " checked";
                        }
                        echo " /> Use Default</label></div>\n<div class=\"clear\"></div>\n";
                        $file = new WHMCS_File_Directory('templates/orderforms');
                        $orderTemplates = $file->getSubdirectories();
                        foreach( $orderTemplates as $template )
                        {
                            $thumbnail = "../templates/orderforms/" . $template . "/thumbnail.gif";
                            if( !file_exists($thumbnail) )
                            {
                                $thumbnail = "images/ordertplpreview.gif";
                            }
                            echo "<div style=\"float:left;padding:10px;text-align:center;\"><label";
                            if( $template == 'ajaxcart' )
                            {
                                echo " style=\"color:#888;\"";
                            }
                            echo "><img src=\"" . $thumbnail . "\" width=\"165\" height=\"90\" style=\"border:5px solid #fff;\" /><br /><input type=\"radio\" name=\"orderfrmtpl\" value=\"" . $template . "\"";
                            if( $template == $orderfrmtpl )
                            {
                                echo " checked";
                            }
                            if( $template == 'ajaxcart' )
                            {
                                echo " disabled=\"disabled\"";
                            }
                            echo "> " . ucfirst($template);
                            if( $template == 'ajaxcart' )
                            {
                                echo " (" . $aInt->lang('products', 'orderfrmtplunavailable') . "*)";
                            }
                            echo "</label></div>";
                        }
                        echo "<div class=\"clear\"></div><p>* " . $aInt->lang('products', 'orderfrmtplunavailableexpl') . "</p>";
                    }
                    echo "</td></tr>\n<tr><td class=\"fieldlabel\"><br></td><td class=\"fieldarea\"></td></tr>\n<tr><td class=\"fieldlabel\">";
                    echo $aInt->lang('products', 'availablepgways');
                    echo "</td><td class=\"fieldarea\">";
                    $gateways = getGatewaysArray();
                    foreach( $gateways as $gateway => $name )
                    {
                        echo "<label><input type=\"checkbox\" name=\"gateways[" . $gateway . "] pgateway_checkbox\"" . (!in_array($gateway, $disabledgateways) ? " checked" : '') . " /> " . $name . "</label><br />";
                    }
                    echo "</td></tr>\n<tr><td class=\"fieldlabel\"><br></td><td class=\"fieldarea\"></td></tr>\n<tr><td class=\"fieldlabel\">";
                    echo $aInt->lang('fields', 'hidden');
                    echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"hidden\"";
                    if( $hidden == 'on' )
                    {
                        echo " checked";
                    }
                    echo "> ";
                    echo $aInt->lang('products', 'hiddengroupdesc');
                    echo "</label></td></tr>\n";
                    if( $ids )
                    {
                        echo "<tr><td class=\"fieldlabel\"><br></td><td class=\"fieldarea\"></td></tr>\n<tr><td class=\"fieldlabel\">";
                        echo $aInt->lang('products', 'directcartlink');
                        echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"100\" value=\"";
                        echo $CONFIG['SystemURL'];
                        echo "/cart.php?gid=";
                        echo ltrim($ids, 0);
                        echo "\" readonly></td></tr>\n";
                    }
                    echo "</table>\n<p align=\"center\"><input type=\"submit\" value=\"";
                    echo $aInt->lang('global', 'savechanges');
                    echo "\" class=\"btn btn-primary\" /> <input type=\"button\" value=\"";
                    echo $aInt->lang('global', 'cancelchanges');
                    echo "\" onclick=\"window.location='configproducts.php'\" class=\"btn\" /></p>\n</form>\n\n";
                }
            }
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();
function printProductDownlads($downloads)
{
    if( !is_array($downloads) )
    {
        $downloads = array(  );
    }
    echo "<ul class=\"jqueryFileTree\">";
    foreach( $downloads as $downloadid )
    {
        $result = select_query('tbldownloads', '', array( 'id' => $downloadid ));
        $data = mysql_fetch_array($result);
        $downid = $data['id'];
        $downtitle = $data['title'];
        $downfilename = $data['location'];
        $ext = end(explode(".", $downfilename));
        echo "<li class=\"file ext_" . $ext . "\"><a href=\"#\" class=\"removedownload\" rel=\"" . $downid . "\">" . $downtitle . "</a></li>";
    }
    echo "</ul>";
}
function buildCategoriesList($level, $parentlevel)
{
    global $categorieslist;
    global $categories;
    $result = select_query('tbldownloadcats', '', array( 'parentid' => $level ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $parentid = $data['parentid'];
        $category = $data['name'];
        $categorieslist .= "<option value=\"" . $id . "\">";
        for( $i = 1; $i <= $parentlevel; $i++ )
        {
            $categorieslist .= "- ";
        }
        $categorieslist .= $category . "</option>";
        buildCategoriesList($id, $parentlevel + 1);
    }
}