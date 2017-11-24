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
$aInt = new WHMCS_Admin("Configure Product Addons");
$aInt->title = $aInt->lang('addons', 'productaddons');
$aInt->sidebar = 'config';
$aInt->icon = 'productaddons';
$aInt->helplink = "Product Addons";
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $apppackages = is_array($packages) ? implode(',', $packages) : '';
    if( $id )
    {
        update_query('tbladdons', array( 'name' => $name, 'description' => WHMCS_Input_Sanitize::decode($description), 'billingcycle' => $billingcycle, 'packages' => $apppackages, 'tax' => $tax, 'showorder' => $showorder, 'autoactivate' => $autoactivate, 'suspendproduct' => $suspendproduct, 'downloads' => implode(',', $downloads), 'welcomeemail' => $welcomeemail, 'weight' => $weight ), array( 'id' => $id ));
    }
    else
    {
        $id = insert_query('tbladdons', array( 'name' => $name, 'description' => WHMCS_Input_Sanitize::decode($description), 'billingcycle' => $billingcycle, 'packages' => $apppackages, 'tax' => $tax, 'showorder' => $showorder, 'autoactivate' => $autoactivate, 'suspendproduct' => $suspendproduct, 'downloads' => implode(',', $downloads), 'welcomeemail' => $welcomeemail, 'weight' => $weight ));
        $creatednew = true;
    }
    foreach( $_POST['currency'] as $currency_id => $pricing )
    {
        if( $creatednew )
        {
            insert_query('tblpricing', array( 'type' => 'addon', 'currency' => $currency_id, 'relid' => $id, 'msetupfee' => $pricing['msetupfee'], 'monthly' => $pricing['monthly'] ));
        }
        else
        {
            update_query('tblpricing', array( 'msetupfee' => $pricing['msetupfee'], 'monthly' => $pricing['monthly'] ), array( 'type' => 'addon', 'currency' => $currency_id, 'relid' => $id ));
        }
    }
    run_hook('AddonConfigSave', array( 'id' => $id ));
    if( $creatednew )
    {
        redir("created=true");
    }
    else
    {
        redir("saved=true");
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $addonname = get_query_val('tbladdons', 'name', array( 'id' => $id ));
    update_query('tblhostingaddons', array( 'addonid' => '0', 'name' => $addonname ), array( 'addonid' => $id ));
    delete_query('tbladdons', array( 'id' => $id ));
    delete_query('tblpricing', array( 'type' => 'addon', 'relid' => $id ));
    infoBox($aInt->lang('addons', 'addondeletesuccess'), $aInt->lang('addon', 'addondelsuccessinfo'));
    redir("deleted=true");
}
ob_start();
if( !$action )
{
    if( $saved )
    {
        infoBox($aInt->lang('addons', 'changesuccess'), $aInt->lang('addons', 'changesuccessinfo'));
    }
    if( $deleted )
    {
        infoBox($aInt->lang('addons', 'addondeletesuccess'), $aInt->lang('addons', 'addondelsuccessinfo'));
    }
    if( $created )
    {
        infoBox($aInt->lang('addons', 'addonaddsuccess'), $aInt->lang('addons', 'addonaddsuccessinfo'));
    }
    echo $infobox;
    $aInt->deleteJSConfirm('doDelete', 'addons', 'areyousuredelete', $_SERVER['PHP_SELF'] . "?action=delete&id=");
    echo "\n<p>";
    echo $aInt->lang('addons', 'description');
    echo "</p>\n\n<p><strong>";
    echo $aInt->lang('addons', 'options');
    echo ":</strong> <a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=manage\">";
    echo $aInt->lang('addons', 'addnew');
    echo "</a></p>\n\n";
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tbladdons', '', '', "weight` ASC,`name", 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $addonid = $data['id'];
        $packages = $data['packages'];
        $name = $data['name'];
        $description = $data['description'];
        $recurring = $data['recurring'];
        $setupfee = $data['setupfee'];
        $billingcycle = $data['billingcycle'];
        $showorder = $data['showorder'];
        $weight = $data['weight'];
        $showorder = $showorder ? "<img src=\"images/icons/tick.png\" alt=\"Yes\" border=\"0\" />" : "&nbsp;";
        $tabledata[] = array( $name, $description, $billingcycle, $showorder, $weight, "<a href=\"?action=manage&id=" . $addonid . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $addonid . "')\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('addons', 'name'), $aInt->lang('fields', 'description'), $aInt->lang('fields', 'billingcycle'), $aInt->lang('addons', 'showonorder'), $aInt->lang('addons', 'weighting'), '', '' ), $tabledata);
}
else
{
    if( $action == 'manage' )
    {
        if( $id )
        {
            $managetitle = $aInt->lang('addons', 'editaddon');
            $result = select_query('tbladdons', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $packages = $data['packages'];
            $name = $data['name'];
            $description = $data['description'];
            $recurring = $data['recurring'];
            $setupfee = $data['setupfee'];
            $billingcycle = $data['billingcycle'];
            $tax = $data['tax'];
            $showorder = $data['showorder'];
            $autoactivate = $data['autoactivate'];
            $suspendproduct = $data['suspendproduct'];
            $downloads = $data['downloads'];
            $welcomeemail = $data['welcomeemail'];
            $weight = $data['weight'];
            $packages = explode(',', $packages);
            $downloads = explode(',', $downloads);
        }
        else
        {
            $managetitle = $aInt->lang('addons', 'createnew');
            $packages = array(  );
            $weight = 0;
        }
        echo "<p><b>" . $managetitle . "</b></p>";
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=save&id=";
        echo $id;
        echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\" width=\"20%\">";
        echo $aInt->lang('addons', 'name');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"40\" value=\"";
        echo $name;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><textarea name=\"description\" cols=60 rows=3>";
        echo WHMCS_Input_Sanitize::encode($description);
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'billingcycle');
        echo "</td><td class=\"fieldarea\">";
        echo $aInt->cyclesDropDown($billingcycle);
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('global', 'pricing');
        echo "</td><td class=\"fieldarea\"><br />\n<table cellspacing=\"1\" bgcolor=\"#cccccc\" align=\"center\">\n";
        $headerrow = $setupfeerow = $recurringrow = '';
        $result = select_query('tblcurrencies', 'id,code', '', 'code', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $currency_id = $data['id'];
            $currency_code = $data['code'];
            if( $id )
            {
                $result2 = select_query('tblpricing', '', array( 'type' => 'addon', 'currency' => $currency_id, 'relid' => $id ));
                $data = mysql_fetch_array($result2);
                $pricing_id = $data['id'];
                if( !$pricing_id )
                {
                    insert_query('tblpricing', array( 'type' => 'addon', 'currency' => $currency_id, 'relid' => $id ));
                    $result2 = select_query('tblpricing', '', array( 'type' => 'addon', 'currency' => $currency_id, 'relid' => $id ));
                    $data = mysql_fetch_array($result2);
                }
            }
            $headerrow .= "<td width=\"100\">" . $currency_code . "</td>";
            $setupfeerow .= "<td><input type=\"text\" name=\"currency[" . $currency_id . "]" . "[msetupfee]\" size=\"10\" value=\"" . $data['msetupfee'] . "\"></td>";
            $recurringrow .= "<td><input type=\"text\" name=\"currency[" . $currency_id . "]" . "[monthly]\" size=\"10\" value=\"" . $data['monthly'] . "\"></td>";
        }
        echo "<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold\"><td width=\"100\"></td>" . $headerrow . "</tr><tr bgcolor=\"#ffffff\" style=\"text-align:center\"><td bgcolor=\"#efefef\"><b>Setup Fee</b></td>" . $setupfeerow . "</tr><tr bgcolor=\"#ffffff\" style=\"text-align:center\"><td bgcolor=\"#efefef\"><b>Recurring</b></td>" . $recurringrow . "</tr>";
        echo "</table><br>\n</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('addons', 'taxaddon');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"tax\"";
        if( $tax == 'on' )
        {
            echo " checked";
        }
        echo " id=\"tax\"> <label for=tax>";
        echo $aInt->lang('addons', 'taxaddoninfo');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('addons', 'showonorder');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"showorder\"";
        if( $showorder == 'on' )
        {
            echo " checked";
        }
        echo " id=\"showorder\"> <label for=showorder>";
        echo $aInt->lang('addons', 'showonorderinfo');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('addons', 'autoactpayment');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"autoactivate\"";
        if( $autoactivate )
        {
            echo " checked";
        }
        echo " id=\"autoactivate\"> <label for=autoactivate>";
        echo $aInt->lang('addons', 'autoactpaymentinfo');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('addons', 'suspendparentproduct');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"suspendproduct\"";
        if( $suspendproduct )
        {
            echo " checked";
        }
        echo " id=\"suspendproduct\"> <label for=suspendproduct>";
        echo $aInt->lang('addons', 'suspendparentproductinfo');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'associateddl');
        echo "</td><td class=\"fieldarea\"><table cellpadding=\"0\" cellspacing=\"0\"><tr><td><select name=\"downloads[]\" size=\"5\" multiple>";
        $query = "SELECT tbldownloads.*,tbldownloadcats.name FROM tbldownloads INNER JOIN tbldownloadcats ON tbldownloads.category=tbldownloadcats.id WHERE tbldownloads.productdownload='on' ORDER BY tbldownloadcats.name ASC,tbldownloads.title ASC";
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $downloadid = $data['id'];
            $downloadcat = $data['name'];
            $downloadname = $data['title'];
            echo "<option value=\"" . $downloadid . "\"";
            if( @in_array($downloadid, $downloads) )
            {
                echo " selected";
            }
            echo ">" . $downloadcat . " - " . $downloadname . "</option>";
        }
        echo "</select></td><td>&nbsp;";
        echo $aInt->lang('downloads', 'ctrlmultiple');
        echo "</td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('products', 'welcomeemail');
        echo "</td><td class=\"fieldarea\"><select name=\"welcomeemail\"><option value=\"0\">";
        echo $aInt->lang('global', 'none');
        echo "</option>\n";
        $query = "SELECT * FROM tblemailtemplates WHERE type='product' AND language='' ORDER BY name ASC";
        $result = full_query($query);
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
        echo $aInt->lang('addons', 'weighting');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"weight\" size=\"10\" value=\"";
        echo $weight;
        echo "\" /> ";
        echo $aInt->lang('addons', 'weightinginfo');
        echo "</td></tr>\n";
        $hookret = run_hook('AddonConfig', array( 'id' => $id ));
        foreach( $hookret as $hookdat )
        {
            foreach( $hookdat as $k => $v )
            {
                echo "<td class=\"fieldlabel\">" . $k . "</td><td class=\"fieldarea\">" . $v . "</td></tr>";
            }
        }
        echo "</table>\n\n<p><b>";
        echo $aInt->lang('addons', 'applicableproducts');
        echo "</b></p>\n\n<table width=\"100%\"><tr>\n";
        $prodcount = 0;
        $products = new WHMCS_Product_Products();
        $productsList = $products->getProducts();
        foreach( $productsList as $data )
        {
            $id = $data['id'];
            $groupname = $data['groupname'];
            $name = $data['name'];
            echo "<td width=\"33%\"><label><input type=\"checkbox\" name=\"packages[]\" value=\"" . $id . "\"";
            if( in_array($id, $packages) )
            {
                echo " checked";
            }
            echo "> " . $groupname . " - " . $name . "</label></td>";
            $prodcount++;
            if( $prodcount == '3' )
            {
                echo "</tr><tr>";
                $prodcount = 0;
            }
        }
        echo "</tr></table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->display();