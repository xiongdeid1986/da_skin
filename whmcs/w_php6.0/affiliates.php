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
include("includes/affiliatefunctions.php");
include("includes/ticketfunctions.php");
$pagetitle = $_LANG['affiliatestitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"affiliates.php\">" . $_LANG['affiliatestitle'] . "</a>";
$pageicon = "images/affiliate_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
if( isset($_SESSION['uid']) )
{
    checkContactPermission('affiliates');
    $result = select_query('tblaffiliates', '', array( 'clientid' => $_SESSION['uid'] ));
    $data = mysql_fetch_array($result);
    $id = $affiliateid = $data['id'];
    if( !$affiliateid )
    {
        if( isset($_REQUEST['activate']) )
        {
            check_token();
            affiliateActivate($_SESSION['uid']);
            redir();
        }
        $result = select_query('tblclients', 'currency', array( 'id' => $_SESSION['uid'] ));
        $data = mysql_fetch_array($result);
        $clientcurrency = $data['currency'];
        $bonusdeposit = convertCurrency($CONFIG['AffiliateBonusDeposit'], 1, $clientcurrency);
        $templatefile = 'affiliatessignup';
        $smarty->assign('affiliatesystemenabled', $CONFIG['AffiliateEnabled']);
        $smarty->assign('bonusdeposit', formatCurrency($bonusdeposit));
        $smarty->assign('payoutpercentage', $CONFIG['AffiliateEarningPercent'] . "%");
    }
    else
    {
        $templatefile = 'affiliates';
        $affiliateClientID = (int) WHMCS_Session::get('uid');
        $currency = getCurrency($affiliateClientID);
        $date = $data['date'];
        $date = fromMySQLDate($date);
        $visitors = $data['visitors'];
        $balance = $data['balance'];
        $withdrawn = $data['withdrawn'];
        $result = select_query('tblaffiliatesaccounts', "COUNT(id)", array( 'affiliateid' => $id ));
        $data = mysql_fetch_array($result);
        $signups = $data[0];
        $result = select_query('tblaffiliatespending', "SUM(tblaffiliatespending.amount)", array( 'affiliateid' => $id ), 'clearingdate', 'DESC', '', "tblaffiliatesaccounts ON tblaffiliatesaccounts.id=tblaffiliatespending.affaccid INNER JOIN tblhosting ON tblhosting.id=tblaffiliatesaccounts.relid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblclients ON tblclients.id=tblhosting.userid");
        $data = mysql_fetch_array($result);
        $pendingcommissions = $data[0];
        $conversionrate = round($signups / $visitors * 100, 2);
        $smarty->assign('affiliateid', $id);
        $smarty->assign('referrallink', $CONFIG['SystemURL'] . "/aff.php?aff=" . $id);
        $smarty->assign('date', $date);
        $smarty->assign('visitors', $visitors);
        $smarty->assign('signups', $signups);
        $smarty->assign('conversionrate', $conversionrate);
        $smarty->assign('pendingcommissions', formatCurrency($pendingcommissions));
        $smarty->assign('balance', formatCurrency($balance));
        $smarty->assign('withdrawn', formatCurrency($withdrawn));
        $affpayoutmin = $CONFIG['AffiliatePayout'];
        $affpayoutmin = convertCurrency($affpayoutmin, 1, $currency['id']);
        if( $affpayoutmin <= $balance )
        {
            $smarty->assign('withdrawlevel', 'true');
            if( $action == 'withdrawrequest' )
            {
                $deptid = '';
                if( $CONFIG['AffiliateDepartment'] )
                {
                    $deptid = get_query_val('tblticketdepartments', 'id', array( 'id' => $CONFIG['AffiliateDepartment'] ));
                }
                if( !$deptid )
                {
                    $deptid = get_query_val('tblticketdepartments', 'id', array( 'hidden' => '' ), 'order', 'ASC');
                }
                $message = "Affiliate Account Withdrawal Request.  Details below:\n\nClient ID: " . $_SESSION['uid'] . "\nAffiliate ID: " . $id . "\nBalance: " . $balance;
                $ticketdetails = openNewTicket($_SESSION['uid'], $_SESSION['cid'], $deptid, "Affiliate Withdrawal Request", $message, 'Medium');
                redir("withdraw=1");
            }
        }
        if( $whmcs->get_req_var('withdraw') )
        {
            $smarty->assign('withdrawrequestsent', 'true');
        }
        $content .= "\n<p><b>" . $_LANG['affiliatesreferals'] . "</b></p>\n<table align=\"center\" id=\"affiliates\" cellspacing=\"1\">\n<tr><td id=\"affiliatesheading\">" . $_LANG['affiliatessignupdate'] . "</td><td id=\"affiliatesheading\">" . $_LANG['affiliateshostingpackage'] . "</td><td id=\"affiliatesheading\">" . $_LANG['affiliatesamount'] . "</td><td id=\"affiliatesheading\">" . $_LANG['affiliatescommision'] . "</td><td id=\"affiliatesheading\">" . $_LANG['affiliatesstatus'] . "</td></tr>\n";
        $numitems = get_query_val('tblaffiliatesaccounts', "COUNT(*)", array( 'affiliateid' => $affiliateid ), '', '', '', "tblhosting ON tblhosting.id=tblaffiliatesaccounts.relid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblclients ON tblclients.id=tblhosting.userid");
        list($orderby, $sort, $limit) = clientAreaTableInit('affiliates', 'regdate', 'DESC', $numitems);
        $smartyvalues['orderby'] = $orderby;
        $smartyvalues['sort'] = strtolower($sort);
        if( $orderby == 'product' )
        {
            $orderby = "tblproducts`.`name";
        }
        else
        {
            if( $orderby == 'amount' )
            {
                $orderby = "tblhosting`.`amount";
            }
            else
            {
                if( $orderby == 'billingcycle' )
                {
                    $orderby = "tblhosting`.`billingcycle";
                }
                else
                {
                    if( $orderby == 'status' )
                    {
                        $orderby = "tblhosting`.`domainstatus";
                    }
                    else
                    {
                        $orderby = "tblhosting`.`regdate";
                    }
                }
            }
        }
        $referrals = array(  );
        $result = select_query('tblaffiliatesaccounts', "tblaffiliatesaccounts.*,tblproducts.name,tblhosting.userid,tblhosting.domainstatus,tblhosting.amount,tblhosting.firstpaymentamount,tblhosting.regdate,tblhosting.billingcycle", array( 'affiliateid' => $affiliateid ), $orderby, $sort, $limit, "tblhosting ON tblhosting.id=tblaffiliatesaccounts.relid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblclients ON tblclients.id=tblhosting.userid");
        while( $data = mysql_fetch_array($result) )
        {
            $affaccid = $data['id'];
            $lastpaid = $data['lastpaid'];
            $relid = $data['relid'];
            $referralClientID = $data['userid'];
            $firstpaymentamount = $data['firstpaymentamount'];
            $amount = $data['amount'];
            $date = $data['regdate'];
            $service = $data['name'];
            $billingcycle = $data['billingcycle'];
            $status = $data['domainstatus'];
            $date = fromMySQLDate($date);
            $commission = calculateAffiliateCommission($affiliateid, $relid, $lastpaid);
            if( !$domain )
            {
                $domain = '';
            }
            $lastpaid = $lastpaid == '0000-00-00' ? 'Never' : fromMySQLDate($lastpaid);
            $status = $_LANG['clientarea' . strtolower($status)];
            $billingcyclelang = strtolower($billingcycle);
            $billingcyclelang = str_replace(" ", '', $billingcyclelang);
            $billingcyclelang = str_replace('-', '', $billingcyclelang);
            $billingcyclelang = $_LANG['orderpaymentterm' . $billingcyclelang];
            $currency = getCurrency($referralClientID);
            if( $billingcycle == 'Free' || $billingcycle == "Free Account" )
            {
                $amountdesc = $billingcyclelang;
            }
            else
            {
                if( $billingcycle == "One Time" )
                {
                    $amountdesc = formatCurrency($firstpaymentamount) . " " . $billingcyclelang;
                }
                else
                {
                    $amountdesc = $firstpaymentamount != $amount ? formatCurrency($firstpaymentamount) . " " . $_LANG['affiliatesinitialthen'] . " " : '';
                    $amountdesc .= formatCurrency($amount) . " " . $billingcyclelang;
                }
            }
            $currency = getCurrency($affiliateClientID);
            $referrals[] = array( 'id' => $affaccid, 'date' => $date, 'service' => $service, 'package' => $service, 'userid' => $referralClientID, 'amount' => $amount, 'billingcycle' => $billingcyclelang, 'amountdesc' => $amountdesc, 'commission' => formatCurrency($commission), 'lastpaid' => $lastpaid, 'status' => $status );
        }
        $smarty->assign('referrals', $referrals);
        $smartyvalues = array_merge($smartyvalues, clientAreaTablePageNav($numitems));
        $currency = getCurrency($affiliateClientID);
        $commissionhistory = array(  );
        $result = select_query('tblaffiliateshistory', '', array( 'affiliateid' => $affiliateid ), 'id', 'DESC', '0,10');
        while( $data = mysql_fetch_array($result) )
        {
            $historyid = $data['id'];
            $date = $data['date'];
            $affaccid = $data['affaccid'];
            $amount = $data['amount'];
            $date = fromMySQLDate($date);
            $commissionhistory[] = array( 'date' => $date, 'referralid' => $affaccid, 'amount' => formatCurrency($amount) );
        }
        $smarty->assign('commissionhistory', $commissionhistory);
        $withdrawalshistory = array(  );
        $result = select_query('tblaffiliateswithdrawals', '', array( 'affiliateid' => $id ), 'id', 'DESC');
        while( $data = mysql_fetch_array($result) )
        {
            $historyid = $data['id'];
            $date = $data['date'];
            $amount = $data['amount'];
            $date = fromMySQLDate($date);
            $withdrawalshistory[] = array( 'date' => $date, 'amount' => formatCurrency($amount) );
        }
        $smarty->assign('withdrawalshistory', $withdrawalshistory);
        $affiliatelinkscode = WHMCS_Input_Sanitize::decode($CONFIG['AffiliateLinks']);
        $affiliatelinkscode = str_replace("[AffiliateLinkCode]", $CONFIG['SystemURL'] . "/aff.php?aff=" . $id, $affiliatelinkscode);
        $affiliatelinkscode = str_replace("<(", "&lt;", $affiliatelinkscode);
        $affiliatelinkscode = str_replace(")>", "&gt;", $affiliatelinkscode);
        $smarty->assign('affiliatelinkscode', $affiliatelinkscode);
    }
}
else
{
    $goto = 'affiliates';
    include("login.php");
}
if( $CONFIG['AffiliateEnabled'] != 'on' )
{
    $smarty->assign('inactive', 'true');
}
outputClientArea($templatefile);