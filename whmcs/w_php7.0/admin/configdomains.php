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
$aInt = new WHMCS_Admin("Configure Domain Pricing");
$aInt->title = $aInt->lang('domains', 'pricingtitle');
$aInt->sidebar = 'config';
$aInt->icon = 'domains';
$aInt->helplink = "Domain Pricing";
$aInt->requiredFiles(array( 'registrarfunctions' ));
ob_start();
$whmcs = WHMCS_Application::getinstance();
$action = $whmcs->get_req_var('action');
$success = $whmcs->get_req_var('success');
$error = $whmcs->get_req_var('error');
if( $action == 'saveorder' )
{
    check_token("WHMCS.admin.default");
    $pricingarr = explode("&amp;", $pricingarr);
    $dpnum = 0;
    foreach( $pricingarr as $v )
    {
        $v = explode('-', $v);
        $v = $v[1];
        if( $v )
        {
            update_query('tbldomainpricing', array( 'order' => $dpnum ), array( 'id' => $v ));
            $dpnum++;
        }
    }
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'showduplicatetld' )
{
    $tldsresult = select_query('tbldomainpricing', 'extension', '');
    $tldoptions = "<option value=\"\">" . $aInt->lang('domains', 'selecttldtoduplicate') . "</option>";
    while( $tldsdata = simulate_fetch_assoc($tldsresult) )
    {
        $tldoptions .= "<option value=\"" . $tldsdata['extension'] . "\">" . $tldsdata['extension'] . "</option>";
    }
    echo "<form method=\"post\" id=\"duplicatetldform\" action=\"" . $_SERVER['PHP_SELF'] . "\">" . generate_token('form') . "<table><tr><td>Existing TLD:</td><td><input type=\"hidden\" name=\"action\" value=\"duplicatetld\" /><select name=\"tld\">" . $tldoptions . "</select></td></tr><tr><td>New TLD:</td><td><input type=\"text\" name=\"newtld\" size=\"6\" /></td></tr></table></form>";
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'duplicatetld' )
{
    check_token("WHMCS.admin.default");
    $newtld = trim($newtld);
    if( !$tld || !$newtld )
    {
        redir("error=emptytld");
    }
    if( substr($newtld, 0, 1) != "." )
    {
        $newtld = "." . $newtld;
    }
    if( get_query_val('tbldomainpricing', 'id', array( 'extension' => $newtld )) )
    {
        redir("error=" . str_replace("%s", $newtld, $aInt->lang('domains', 'extensionalreadyexist')));
    }
    $tlddata = get_query_vals('tbldomainpricing', "id,dnsmanagement, emailforwarding, idprotection, eppcode, autoreg", array( 'extension' => $tld ));
    $relid = $tlddata['id'];
    $newtlddata = array(  );
    $newtlddata['extension'] = $newtld;
    $newtlddata['dnsmanagement'] = $tlddata['dnsmanagement'];
    $newtlddata['emailforwarding'] = $tlddata['emailforwarding'];
    $newtlddata['idprotection'] = $tlddata['idprotection'];
    $newtlddata['eppcode'] = $tlddata['eppcode'];
    $newtlddata['autoreg'] = $tlddata['autoreg'];
    $newtlddata['order'] = get_query_val('tbldomainpricing', "MAX(`order`)", '') + 1;
    $newrelid = insert_query('tbldomainpricing', $newtlddata);
    $regpricingresult = select_query('tblpricing', "*", array( 'relid' => $relid, 'type' => 'domainregister' ));
    while( $regpricingdata = simulate_fetch_assoc($regpricingresult) )
    {
        unset($regpricingdata['id']);
        $regpricingdata['relid'] = $newrelid;
        insert_query('tblpricing', $regpricingdata);
    }
    $transferpricingresult = select_query('tblpricing', "*", array( 'relid' => $relid, 'type' => 'domaintransfer' ));
    while( $transferpricingdata = simulate_fetch_assoc($transferpricingresult) )
    {
        unset($transferpricingdata['id']);
        $transferpricingdata['relid'] = $newrelid;
        insert_query('tblpricing', $transferpricingdata);
    }
    $renewpricingresult = select_query('tblpricing', "*", array( 'relid' => $relid, 'type' => 'domainrenew' ));
    while( $renewpricingdata = simulate_fetch_assoc($renewpricingresult) )
    {
        unset($renewpricingdata['id']);
        $renewpricingdata['relid'] = $newrelid;
        insert_query('tblpricing', $renewpricingdata);
    }
    redir("success=true");
}
if( $action == 'resetpricing' )
{
    check_token("WHMCS.admin.default");
    $id = $_GET['id'];
    $cugroupid = $_GET['cugroupid'];
    if( !$cugroupid )
    {
        redir("action=editpricing&id=" . $id);
    }
    $result0 = select_query('tblclientgroups', 'id,groupname', '', 'groupname', 'ASC');
    $result = select_query('tblcurrencies', '', '', 'code', 'ASC');
    while( $data = simulate_fetch_assoc($result) )
    {
        $curr_id = $data['id'];
        $curr_code = $data['code'];
        $currenciesarray[$curr_id] = $curr_code;
    }
    foreach( $currenciesarray as $curr_id => $curr_code )
    {
        $regresult2_baseslab = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
        $regvalues = simulate_fetch_assoc($regresult2_baseslab);
        update_query('tblpricing', array( 'msetupfee' => $regvalues['msetupfee'], 'qsetupfee' => $regvalues['qsetupfee'], 'ssetupfee' => $regvalues['ssetupfee'], 'asetupfee' => $regvalues['asetupfee'], 'bsetupfee' => $regvalues['bsetupfee'], 'monthly' => $regvalues['monthly'], 'quarterly' => $regvalues['quarterly'], 'semiannually' => $regvalues['semiannually'], 'annually' => $regvalues['annually'], 'biennially' => $regvalues['biennially'] ), array( 'type' => 'domainregister', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $transresult2_baseslab = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
        $transvalues = simulate_fetch_assoc($transresult2_baseslab);
        update_query('tblpricing', array( 'msetupfee' => $transvalues['msetupfee'], 'qsetupfee' => $transvalues['qsetupfee'], 'ssetupfee' => $transvalues['ssetupfee'], 'asetupfee' => $transvalues['asetupfee'], 'bsetupfee' => $transvalues['bsetupfee'], 'monthly' => $transvalues['monthly'], 'quarterly' => $transvalues['quarterly'], 'semiannually' => $transvalues['semiannually'], 'annually' => $transvalues['annually'], 'biennially' => $transvalues['biennially'] ), array( 'type' => 'domaintransfer', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $renewresult2_baseslab = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
        $renewvalues = simulate_fetch_assoc($renewresult2_baseslab);
        update_query('tblpricing', array( 'msetupfee' => $renewvalues['msetupfee'], 'qsetupfee' => $renewvalues['qsetupfee'], 'ssetupfee' => $renewvalues['ssetupfee'], 'asetupfee' => $renewvalues['asetupfee'], 'bsetupfee' => $renewvalues['bsetupfee'], 'monthly' => $renewvalues['monthly'], 'quarterly' => $renewvalues['quarterly'], 'semiannually' => $renewvalues['semiannually'], 'annually' => $renewvalues['annually'], 'biennially' => $renewvalues['biennially'] ), array( 'type' => 'domainrenew', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
    }
    redir("action=editpricing&id=" . $id . "&selectedcugroupid=" . $cugroupid . "&resetcomplete=true");
}
if( $action == 'deactivateslab' )
{
    check_token("WHMCS.admin.default");
    $id = $_GET['id'];
    $cugroupid = $_GET['cugroupid'];
    delete_query('tblpricing', array( 'type' => 'domainregister', 'tsetupfee' => $cugroupid, 'relid' => $id ));
    delete_query('tblpricing', array( 'type' => 'domaintransfer', 'tsetupfee' => $cugroupid, 'relid' => $id ));
    delete_query('tblpricing', array( 'type' => 'domainrenew', 'tsetupfee' => $cugroupid, 'relid' => $id ));
    redir("action=editpricing&id=" . $id . "&selectedcugroupid=" . $cugroupid . "&deactivated=true");
}
if( $action == 'activateslab' )
{
    check_token("WHMCS.admin.default");
    $id = $_GET['id'];
    $cugroupid = $_GET['cugroupid'];
    $result = select_query('tblcurrencies', '', '', 'code', 'ASC');
    while( $data = simulate_fetch_assoc($result) )
    {
        $curr_id = $data['id'];
        $curr_code = $data['code'];
        $currenciesarray[$curr_id] = $curr_code;
    }
    foreach( $currenciesarray as $curr_id => $curr_code )
    {
        $result2 = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id = $data['id'];
        if( !$pricing_id )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id = $data['id'];
            if( !$pricing_id )
            {
                insert_query('tblpricing', array( 'type' => 'domainregister', 'currency' => $curr_id, 'relid' => $id ));
            }
            else
            {
                insert_query('tblpricing', array( 'type' => 'domainregister', 'currency' => $curr_id, 'relid' => $id, 'tsetupfee' => $cugroupid, 'msetupfee' => $data['msetupfee'], 'qsetupfee' => $data['qsetupfee'], 'ssetupfee' => $data['ssetupfee'], 'asetupfee' => $data['asetupfee'], 'bsetupfee' => $data['bsetupfee'], 'monthly' => $data['monthly'], 'quarterly' => $data['quarterly'], 'semiannually' => $data['semiannually'], 'annually' => $data['annually'], 'biennially' => $data['biennially'] ));
            }
        }
        $result2 = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id = $data['id'];
        if( !$pricing_id )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id = $data['id'];
            if( !$pricing_id )
            {
                insert_query('tblpricing', array( 'type' => 'domaintransfer', 'currency' => $curr_id, 'relid' => $id ));
            }
            else
            {
                insert_query('tblpricing', array( 'type' => 'domaintransfer', 'currency' => $curr_id, 'relid' => $id, 'tsetupfee' => $cugroupid, 'msetupfee' => $data['msetupfee'], 'qsetupfee' => $data['qsetupfee'], 'ssetupfee' => $data['ssetupfee'], 'asetupfee' => $data['asetupfee'], 'bsetupfee' => $data['bsetupfee'], 'monthly' => $data['monthly'], 'quarterly' => $data['quarterly'], 'semiannually' => $data['semiannually'], 'annually' => $data['annually'], 'biennially' => $data['biennially'] ));
            }
        }
        $result2 = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => $cugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id = $data['id'];
        if( !$pricing_id )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id = $data['id'];
            if( !$pricing_id )
            {
                insert_query('tblpricing', array( 'type' => 'domainrenew', 'currency' => $curr_id, 'relid' => $id ));
                insert_query('tblpricing', array( 'type' => 'domainrenew', 'currency' => $curr_id, 'relid' => $id, 'tsetupfee' => $cugroupid, 'msetupfee' => $data['msetupfee'], 'qsetupfee' => $data['qsetupfee'], 'ssetupfee' => $data['ssetupfee'], 'asetupfee' => $data['asetupfee'], 'bsetupfee' => $data['bsetupfee'], 'monthly' => $data['monthly'], 'quarterly' => $data['quarterly'], 'semiannually' => $data['semiannually'], 'annually' => $data['annually'], 'biennially' => $data['biennially'] ));
            }
            else
            {
                insert_query('tblpricing', array( 'type' => 'domainrenew', 'currency' => $curr_id, 'relid' => $id, 'tsetupfee' => $cugroupid, 'msetupfee' => $data['msetupfee'], 'qsetupfee' => $data['qsetupfee'], 'ssetupfee' => $data['ssetupfee'], 'asetupfee' => $data['asetupfee'], 'bsetupfee' => $data['bsetupfee'], 'monthly' => $data['monthly'], 'quarterly' => $data['quarterly'], 'semiannually' => $data['semiannually'], 'annually' => $data['annually'], 'biennially' => $data['biennially'] ));
            }
        }
    }
    redir("action=editpricing&id=" . $id . "&selectedcugroupid=" . $cugroupid . "&activated=true");
}
if( $action == 'editpricing' )
{
    $cugrouparray = array(  );
    if( isset($_GET['selectedcugroupid']) )
    {
        $selectedcugroupid = $_GET['selectedcugroupid'];
    }
    else
    {
        $selectedcugroupid = 0;
    }
    if( $register )
    {
        check_token("WHMCS.admin.default");
        foreach( $register as $cugroupid => $register_values )
        {
            foreach( $register_values as $curr_id => $values )
            {
                update_query('tblpricing', array( 'msetupfee' => $values[1], 'qsetupfee' => $values[2], 'ssetupfee' => $values[3], 'asetupfee' => $values[4], 'bsetupfee' => $values[5], 'monthly' => $values[6], 'quarterly' => $values[7], 'semiannually' => $values[8], 'annually' => $values[9], 'biennially' => $values[10] ), array( 'type' => 'domainregister', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
            }
        }
        foreach( $transfer as $cugroupid => $transfer_values )
        {
            foreach( $transfer_values as $curr_id => $values )
            {
                update_query('tblpricing', array( 'msetupfee' => $values[1], 'qsetupfee' => $values[2], 'ssetupfee' => $values[3], 'asetupfee' => $values[4], 'bsetupfee' => $values[5], 'monthly' => $values[6], 'quarterly' => $values[7], 'semiannually' => $values[8], 'annually' => $values[9], 'biennially' => $values[10] ), array( 'type' => 'domaintransfer', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
            }
        }
        foreach( $renew as $cugroupid => $renew_values )
        {
            foreach( $renew_values as $curr_id => $values )
            {
                update_query('tblpricing', array( 'msetupfee' => $values[1], 'qsetupfee' => $values[2], 'ssetupfee' => $values[3], 'asetupfee' => $values[4], 'bsetupfee' => $values[5], 'monthly' => $values[6], 'quarterly' => $values[7], 'semiannually' => $values[8], 'annually' => $values[9], 'biennially' => $values[10] ), array( 'type' => 'domainrenew', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
            }
        }
    }
    $result = select_query('tbldomainpricing', '', array( 'id' => $id ));
    $data = mysqli_fetch_array($result);
    $extension = $data['extension'];
    $aInt->title = $aInt->lang('domains', 'pricetitle') . " " . $extension;
    ob_start();
    if( isset($_GET['activated']) )
    {
        infoBox($_ADMINLANG['domains']['activatepricingslab'], $_ADMINLANG['global']['changesuccessdesc']);
    }
    if( isset($_GET['deactivated']) )
    {
        infoBox($_ADMINLANG['domains']['deactivatepricingslab'], $_ADMINLANG['global']['changesuccessdesc']);
    }
    if( isset($_GET['resetcomplete']) )
    {
        infoBox($_ADMINLANG['domains']['resetpricingslab'], $_ADMINLANG['global']['changesuccessdesc']);
    }
    echo $infobox;
    echo "\n<p>";
    echo $aInt->lang('domains', 'slabsintro');
    echo "</p>\n<p>";
    echo $aInt->lang('domains', 'leaveatzero');
    echo "</p>\n\n";
    $result = select_query('tblclientgroups', 'id,groupname', '', 'groupname', 'ASC');
    while( $data = simulate_fetch_assoc($result) )
    {
        $cugroupid = $data['id'];
        $cugroupname = $data['groupname'];
        $cugrouparray[$cugroupid] = $cugroupname;
    }
    $result = select_query('tblcurrencies', '', '', 'code', 'ASC');
    while( $data = simulate_fetch_assoc($result) )
    {
        $curr_id = $data['id'];
        $curr_code = $data['code'];
        $currenciesarray[$curr_id] = $curr_code;
    }
    foreach( $currenciesarray as $curr_id => $curr_code )
    {
        $result2 = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id1a = $data['id'];
        if( !$pricing_id1a )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id1b = $data['id'];
            if( !$pricing_id1b )
            {
                $pricing_id1a = insert_query('tblpricing', array( 'type' => 'domainregister', 'currency' => $curr_id, 'relid' => $id ));
            }
        }
        $result2 = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id2a = $data['id'];
        if( !$pricing_id2a )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id2b = $data['id'];
            if( !$pricing_id2b )
            {
                $pricing_id2a = insert_query('tblpricing', array( 'type' => 'domaintransfer', 'currency' => $curr_id, 'relid' => $id ));
            }
        }
        $result2 = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
        $data = mysqli_fetch_array($result2);
        $pricing_id3a = $data['id'];
        if( !$pricing_id3a )
        {
            $result2 = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => '0', 'currency' => $curr_id, 'relid' => $id ));
            $data = mysqli_fetch_array($result2);
            $pricing_id3b = $data['id'];
            if( !$pricing_id3b )
            {
                $pricing_id3a = insert_query('tblpricing', array( 'type' => 'domainrenew', 'currency' => $curr_id, 'relid' => $id ));
            }
        }
    }
    echo "\n<form id=\"domains_edit\" method=\"post\" action=\"";
    echo $_SERVER['PHP_SELF'];
    echo "?action=editpricing&id=";
    echo $id;
    echo "&selectedcugroupid=";
    echo $selectedcugroupid;
    echo "\">\n";
    $onChangeurl = $_SERVER['PHP_SELF'] . "?action=editpricing&id=" . $id . "&selectedcugroupid=";
    echo "<p align=\"center\">";
    echo $aInt->lang('domains', 'pricingslabfor');
    echo " <select name=\"selectedcugroupid\" onchange=\"location.href='";
    echo $onChangeurl;
    echo "'+this.value;\">\n<option value=\"0\">";
    echo $aInt->lang('domains', 'defaultpricingslab');
    echo "</option>\n";
    if( is_array($cugrouparray) )
    {
        foreach( $cugrouparray as $cugrouparrayid => $cugrouparrayname )
        {
            echo "<option";
            if( $selectedcugroupid == $cugrouparrayid )
            {
                echo " selected=\"selected\"";
            }
            echo " value=\"" . $cugrouparrayid . "\">" . $cugrouparrayname . " " . $aInt->lang('fields', 'clientgroup') . "</option>";
        }
    }
    echo "</select></p>\n\n";
    $noslabpricing = !$pricing_id1a || !$pricing_id2a || !$pricing_id3a ? true : false;
    if( $selectedcugroupid != 0 )
    {
        echo "<p align=\"center\">";
        if( $noslabpricing )
        {
            echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=activateslab&id=" . $id . "&cugroupid=" . $selectedcugroupid . generate_token('link') . "\" onclick=\"return confirm('" . $aInt->lang('domains', 'activatepricingslabconfirm', 1) . "')\">";
        }
        echo $aInt->lang('domains', 'activatepricingslab') . "</a> | ";
        if( !$noslabpricing )
        {
            echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=deactivateslab&id=" . $id . "&cugroupid=" . $selectedcugroupid . generate_token('link') . "\" onclick=\"return confirm('" . $aInt->lang('domains', 'deactivatepricingslabconfirm', 1) . "')\">";
        }
        echo $aInt->lang('domains', 'deactivatepricingslab') . "</a> | ";
        if( !$noslabpricing )
        {
            echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=resetpricing&id=" . $id . "&cugroupid=" . $selectedcugroupid . generate_token('link') . "\" onclick=\"return confirm('" . $aInt->lang('domains', 'resetpricingslab', 1) . "')\">";
        }
        echo $aInt->lang('domains', 'resetpricingslab') . "</a></p>";
    }
    if( !$noslabpricing )
    {
        $totalcurrencies = count($currenciesarray);
        echo "\n<table width=100% align=center cellpadding=2 cellspacing=1 bgcolor=#cccccc>\n<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\"><td></td><td>";
        echo $aInt->lang('currencies', 'currency');
        echo "</td><td>";
        echo $aInt->lang('domains', 'actionreg');
        echo "</td><td>";
        echo $aInt->lang('domains', 'transfer');
        echo "</td><td>";
        echo $aInt->lang('domains', 'renewal');
        echo "</td></tr>\n";
        $years = 1;
        while( $years <= 10 )
        {
            echo "<tr bgcolor=\"#ffffff\" style=\"text-align:center;\"><td rowspan=\"" . $totalcurrencies . "\" bgcolor=\"#efefef\"><b>" . $years . " " . $aInt->lang('domains', 'years') . "</b></td>";
            $i = 0;
            foreach( $currenciesarray as $curr_id => $curr_code )
            {
                $result2_baseslab = select_query('tblpricing', '', array( 'type' => 'domainregister', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
                $regdata_baseslab = mysqli_fetch_array($result2_baseslab);
                $register[$selectedcugroupid][$curr_id] = array( '1' => $regdata_baseslab['msetupfee'], '2' => $regdata_baseslab['qsetupfee'], '3' => $regdata_baseslab['ssetupfee'], '4' => $regdata_baseslab['asetupfee'], '5' => $regdata_baseslab['bsetupfee'], '6' => $regdata_baseslab['monthly'], '7' => $regdata_baseslab['quarterly'], '8' => $regdata_baseslab['semiannually'], '9' => $regdata_baseslab['annually'], '10' => $regdata_baseslab['biennially'] );
                $transresult2_baseslab = select_query('tblpricing', '', array( 'type' => 'domaintransfer', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
                $transdata_baseslab = mysqli_fetch_array($transresult2_baseslab);
                $transfer[$selectedcugroupid][$curr_id] = array( '1' => $transdata_baseslab['msetupfee'], '2' => $transdata_baseslab['qsetupfee'], '3' => $transdata_baseslab['ssetupfee'], '4' => $transdata_baseslab['asetupfee'], '5' => $transdata_baseslab['bsetupfee'], '6' => $transdata_baseslab['monthly'], '7' => $transdata_baseslab['quarterly'], '8' => $transdata_baseslab['semiannually'], '9' => $transdata_baseslab['annually'], '10' => $transdata_baseslab['biennially'] );
                $result2_baseslab = select_query('tblpricing', '', array( 'type' => 'domainrenew', 'tsetupfee' => $selectedcugroupid, 'currency' => $curr_id, 'relid' => $id ));
                $rendata_baseslab = mysqli_fetch_array($result2_baseslab);
                $renew[$selectedcugroupid][$curr_id] = array( '1' => $rendata_baseslab['msetupfee'], '2' => $rendata_baseslab['qsetupfee'], '3' => $rendata_baseslab['ssetupfee'], '4' => $rendata_baseslab['asetupfee'], '5' => $rendata_baseslab['bsetupfee'], '6' => $rendata_baseslab['monthly'], '7' => $rendata_baseslab['quarterly'], '8' => $rendata_baseslab['semiannually'], '9' => $rendata_baseslab['annually'], '10' => $rendata_baseslab['biennially'] );
                if( 0 < $i )
                {
                    echo "</tr><tr bgcolor=\"#ffffff\" style=\"text-align:center;\">";
                }
                echo "<td>" . $curr_code . "</td><td><input type=\"text\" name=\"register[" . $selectedcugroupid . "]" . "[" . $curr_id . "]" . "[" . $years . "]" . "\" value=\"" . $register[$selectedcugroupid][$curr_id][$years] . "\" size=\"10\"></td><td><input type=\"text\" name=\"transfer[" . $selectedcugroupid . "]" . "[" . $curr_id . "]" . "[" . $years . "]" . "\" value=\"" . $transfer[$selectedcugroupid][$curr_id][$years] . "\" size=\"10\"></td><td><input type=\"text\" name=\"renew[" . $selectedcugroupid . "]" . "[" . $curr_id . "]" . "[" . $years . "]" . "\" value=\"" . $renew[$selectedcugroupid][$curr_id][$years] . "\" size=\"10\"></td>";
                $i++;
            }
            echo "</tr>";
            $years += 1;
        }
        echo "</tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\" /> <input type=\"button\" value=\"下一个\" id=\"next_id\" class=\"button\">\n\n";
    }
    else
    {
        echo "\n<p align=\"center\"> <input type=\"button\" value=\"";
        echo $aInt->lang('addons', 'closewindow');
        echo "\" onclick=\"window.close();\" class=\"button\" /></p>\n \n";
    }
    echo "</form>\n\n";
	echo "<script type=\"text/javascript\" src=\"/ddweb_public/js/domains_edit.js\"></script>\n";
    $content = ob_get_contents();
    ob_end_clean();
    $aInt->content = $content;
    $aInt->displayPopUp();
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tbldomainpricing', array( 'id' => $id ));
    foreach( array( 'domainregister', 'domaintransfer', 'domainrenew' ) as $type )
    {
        delete_query('tblpricing', array( 'type' => $type, 'relid' => $id ));
    }
    redir("deleted=true");
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    foreach( $tld as $id => $extension )
    {
        update_query('tbldomainpricing', array( 'extension' => trim(strtolower($extension)), 'dnsmanagement' => $dns[$id], 'emailforwarding' => $email[$id], 'idprotection' => $idprot[$id], 'eppcode' => $eppcode[$id], 'autoreg' => $autoreg[$id] ), array( 'id' => $id ));
    }
    $newtld = trim($newtld);
    if( $newtld )
    {
        if( substr($newtld, 0, 1) != "." )
        {
            $newtld = "." . $newtld;
        }
        $result = select_query('tbldomainpricing', '', array( 'extension' => $newtld ));
        $num_rows = mysqli_num_rows($result);
        if( 0 < $num_rows )
        {
            $error = str_replace("%s", $newtld, $aInt->lang('domains', 'extensionalreadyexist'));
        }
        else
        {
            $result = select_query('tbldomainpricing', '', '', 'order', 'DESC');
            $data = mysqli_fetch_array($result);
            $lastorder = $data['order'] + 1;
            insert_query('tbldomainpricing', array( 'extension' => trim(strtolower($newtld)), 'dnsmanagement' => $newdns, 'emailforwarding' => $newemail, 'idprotection' => $newidprot, 'eppcode' => $neweppcode, 'autoreg' => $newautoreg, 'order' => $lastorder ));
        }
    }
    if( $error )
    {
        redir("error=" . $error);
    }
    redir("success=true");
}
if( $action == 'saveaddons' )
{
    check_token("WHMCS.admin.default");
    foreach( $_POST['currency'] as $currency_id => $pricing )
    {
        update_query('tblpricing', $pricing, array( 'type' => 'domainaddons', 'currency' => $currency_id, 'relid' => 0 ));
    }
    redir("success=true");
}
$aInt->deleteJSConfirm('doDelete', 'domains', 'delsureextension', "?action=delete&id=");
$jquerycode = "\n\$('#domainpricing').tableDnD({\n        onDrop: function(table, row) {\n        \$.post(\"configdomains.php\", { action: \"saveorder\", pricingarr: \$('#domainpricing').tableDnDSerialize(), token: \"" . generate_token('plain') . "\" });\n    },\n    dragHandle: \"sortcol\"\n    });\n";
if( $success )
{
    infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('global', 'changesuccessdesc'));
}
if( $error )
{
    if( $error == 'emptytld' )
    {
        $error = $aInt->lang('domains', 'sourcenewtldempty');
    }
    infoBox($aInt->lang('global', 'erroroccurred'), $error, 'error');
}
echo $infobox;
echo "<p>" . $aInt->lang('domains', 'pricinginfo') . "</p>";
echo "\n<form method=\"post\" action=\"";
echo $_SERVER['PHP_SELF'];
echo "\">\n<input type=\"hidden\" name=\"action\" value=\"save\" />\n\n<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" id=\"domainpricing\">\n<tr><th>";
echo $aInt->lang('fields', 'tld');
echo "</th><th>";
echo $aInt->lang('global', 'pricing');
echo "</th><th>";
echo $aInt->lang('domains', 'dnsmanagement');
echo "</th><th>";
echo $aInt->lang('domains', 'emailforwarding');
echo "</th><th>";
echo $aInt->lang('domains', 'idprotection');
echo "</th><th>";
echo $aInt->lang('domains', 'eppcode');
echo "</th><th>";
echo $aInt->lang('domains', 'autoreg');
echo "</th><th width=\"20\"></th><th width=\"20\"></th></tr>\n";
$result = select_query('tbldomainpricing', '', '', 'order', 'ASC');
while( $data = mysqli_fetch_array($result) )
{
    $id = $data['id'];
    $extension = $data['extension'];
    $autoreg = $data['autoreg'];
    $dnsmanagement = $data['dnsmanagement'];
    $emailforwarding = $data['emailforwarding'];
    $idprotection = $data['idprotection'];
    $eppcode = $data['eppcode'];
    $order = $data['order'];
    echo "<tr id=\"dp-";
    echo $id;
    echo "\">\n<td><input type=\"text\" name=\"tld[";
    echo $id;
    echo "]\" value=\"";
    echo $extension;
    echo "\" size=\"6\"></td>\n<td><a href=\"#\" onclick=\"window.open('configdomains.php?action=editpricing&id=";
    echo $id;
    echo "','domainpricing','width=500,height=650,scrollbars=yes,resizable=yes');return false\">";
    echo $aInt->lang('domains', 'openpricing');
    echo "</a></td>\n<td><input type=\"checkbox\" name=\"dns[";
    echo $id;
    echo "]\"";
    if( $dnsmanagement )
    {
        echo " checked";
    }
    echo "></td>\n<td><input type=\"checkbox\" name=\"email[";
    echo $id;
    echo "]\"";
    if( $emailforwarding )
    {
        echo " checked";
    }
    echo "></td>\n<td><input type=\"checkbox\" name=\"idprot[";
    echo $id;
    echo "]\"";
    if( $idprotection )
    {
        echo " checked";
    }
    echo "></td>\n<td><input type=\"checkbox\" name=\"eppcode[";
    echo $id;
    echo "]\"";
    if( $eppcode )
    {
        echo " checked";
    }
    echo "></td>\n<td>";
    echo getRegistrarsDropdownMenu($autoreg, "autoreg[" . $id . "]");
    echo "</td>\n<td class=\"sortcol\">&nbsp;</td>\n<td><a href=\"#\" onClick=\"doDelete('";
    echo $id;
    echo "');return false\"><img src=\"images/icons/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"";
    echo $aInt->lang('global', 'delete');
    echo "\"></a></td>\n</tr>\n";
}
echo "<tr class=\"addtld\">\n<td><input type=\"text\" name=\"newtld\" size=\"6\"></td>\n<td></td>\n<td><input type=\"checkbox\" name=\"newdns\" checked></td>\n<td><input type=\"checkbox\" name=\"newemail\" checked></td>\n<td><input type=\"checkbox\" name=\"newidprot\" checked></td>\n<td><input type=\"checkbox\" name=\"neweppcode\" checked></td>\n<td>";
echo getRegistrarsDropdownMenu($autoreg, 'newautoreg');
echo "</td>\n<td></td>\n<td></td>\n</tr>\n</table>\n</div>\n\n<script src=\"../includes/jscript/jqueryro.js\"></script>\n<style>\ntd.sortcol {\n    background-image: url(\"images/updown.gif\");\n    background-repeat: no-repeat;\n    background-position: center center;\n    cursor: move;\n}\ntable.datatable .tDnD_whileDrag td,table.datatable .addtld td {\n    background-color: #eeeeee;\n}\n</style>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"btn btn-primary\" /> <input type=\"button\" id=\"showduplicatetld\" value=\"";
echo $aInt->lang('domains', 'duplicatetld');
echo "\" class=\"btn\" /></p>\n\n</form>\n\n";
echo $aInt->jqueryDialog('duplicatetld', $aInt->lang('domains', 'duplicatetld'), $aInt->lang('global', 'loading'), array( $aInt->lang('global', 'ok') => "\$(\"#duplicatetldform\").submit()" ));
$jquerycode .= "\$(\"#showduplicatetld\").click(\n    function() {\n        \$(\"#duplicatetld\").dialog(\"open\");\n        \$(\"#duplicatetld\").load(\"configdomains.php?action=showduplicatetld\");\n        return false;\n    }\n);";
echo "\n<h2>";
echo $aInt->lang('domains', 'domainaddons');
echo "</h2>\n\n<form method=\"post\" action=\"";
echo $_SERVER['PHP_SELF'];
echo "\">\n<input type=\"hidden\" name=\"action\" value=\"saveaddons\" />\n\n<div class=\"tablebg\" align=\"center\">\n<table class=\"datatable\" width=\"60%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" id=\"domainpricing\">\n<tr><th>";
echo $aInt->lang('currencies', 'currency');
echo "</th><th>";
echo $aInt->lang('domains', 'dnsmanagement');
echo "</th><th>";
echo $aInt->lang('domains', 'emailforwarding');
echo "</th><th>";
echo $aInt->lang('domains', 'idprotection');
echo "</th></tr>\n";
$result = select_query('tblcurrencies', 'id,code', '', 'code', 'ASC');
while( $data = mysqli_fetch_array($result) )
{
    $currency_id = $data['id'];
    $currency_code = $data['code'];
    $result2 = select_query('tblpricing', '', array( 'type' => 'domainaddons', 'currency' => $currency_id, 'relid' => 0 ));
    $data = mysqli_fetch_array($result2);
    $pricing_id = $data['id'];
    if( !$pricing_id )
    {
        insert_query('tblpricing', array( 'type' => 'domainaddons', 'currency' => $currency_id, 'relid' => 0 ));
        $result2 = select_query('tblpricing', '', array( 'type' => 'domainaddons', 'currency' => $currency_id, 'relid' => 0 ));
        $data = mysqli_fetch_array($result2);
    }
    $msetupfee = $data['msetupfee'];
    $qsetupfee = $data['qsetupfee'];
    $ssetupfee = $data['ssetupfee'];
    echo "<tr><td><b>" . $currency_code . "</b></td><td><input type=\"text\" name=\"currency[" . $currency_id . "]" . "[msetupfee]\" size=\"10\" value=\"" . $msetupfee . "\"></td><td><input type=\"text\" name=\"currency[" . $currency_id . "]" . "[qsetupfee]\" size=\"10\" value=\"" . $qsetupfee . "\"></td><td><input type=\"text\" name=\"currency[" . $currency_id . "]" . "[ssetupfee]\" size=\"10\" value=\"" . $ssetupfee . "\"></td></tr>";
}
echo "</table>\n</div>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"btn\" /></p>\n\n</form>\n\n";
//echo "<script>var inputs__=document.getElementById('domainpricing').getElementsByTagName('input');for(var i=0;i<inputs__.length;i++){if(inputs__[i].type=='checkbox'){inputs__[i].checked=true;}}</script>";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();