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
require("includes/orderfunctions.php");
require("includes/domainfunctions.php");
require("includes/whoisfunctions.php");
require("includes/configoptionsfunctions.php");
require("includes/customfieldfunctions.php");
require("includes/clientfunctions.php");
require("includes/invoicefunctions.php");
require("includes/processinvoices.php");
require("includes/gatewayfunctions.php");
require("includes/modulefunctions.php");
require("includes/ccfunctions.php");
require("includes/cartfunctions.php");
$nameserverRegexPattern = "/^(?!\\-)(?:[a-zA-Z\\d\\-]{0,62}[a-zA-Z\\d]\\.){2,126}(?!\\d+)[a-zA-Z\\d]{1,63}\$/";
initialiseClientArea($_LANG['carttitle'], '', "<a href=\"cart.php\">" . $_LANG['carttitle'] . "</a>");
checkContactPermission('orders');
$orderfrm = new WHMCS_OrderForm();
$whmcs = WHMCS_Application::getinstance();
$securityqans = $whmcs->get_req_var('securityqans');
$securityqid = $whmcs->get_req_var('securityqid');
$a = $whmcs->get_req_var('a');
$gid = $whmcs->get_req_var('gid');
$pid = $whmcs->get_req_var('pid');
if( substr($pid, 0, 1) == 'b' )
{
    $bid = (int) substr($pid, 1);
    redir("a=add&bid=" . $bid);
}
else
{
    $pid = (int) $pid;
}
$aid = (int) $whmcs->get_req_var('aid');
$ajax = $whmcs->get_req_var('ajax');
$sld = $whmcs->get_req_var('sld');
$tld = $whmcs->get_req_var('tld');
$domains = $whmcs->get_req_var('domains');
$step = $whmcs->get_req_var('step');
$remote_ip = $whmcs->getRemoteIp();
$productInfoKey = (int) $whmcs->get_req_var('i');
if( $productInfoKey < 0 )
{
    $productInfoKey = null;
}
$orderfrmtpl = $whmcs->get_config('OrderFormTemplate');
if( !isValidforPath($orderfrmtpl) )
{
    exit( "Invalid Order Form Template Name" );
}
$orderconf = array(  );
$orderfrmconfig = ROOTDIR . '/templates/orderforms/' . $orderfrmtpl . "/config.php";
if( file_exists($orderfrmconfig) )
{
    include($orderfrmconfig);
}
if( !$ajax && isset($orderconf['denynonajaxaccess']) && is_array($orderconf['denynonajaxaccess']) && in_array($a, $orderconf['denynonajaxaccess']) )
{
    redir();
}
$orderform = true;
$nowrapper = false;
$errormessage = $allowcheckout = '';
$userid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
$currencyid = isset($_SESSION['currency']) ? $_SESSION['currency'] : '';
$currency = getCurrency($userid, $currencyid);
$smartyvalues['currency'] = $currency;
$smartyvalues['ipaddress'] = $remote_ip;
$smartyvalues['ajax'] = $ajax ? true : false;
$numproducts = isset($_SESSION['cart']['products']) ? count($_SESSION['cart']['products']) : 0;
$numaddons = isset($_SESSION['cart']['addons']) ? count($_SESSION['cart']['addons']) : 0;
$numdomains = isset($_SESSION['cart']['domains']) ? count($_SESSION['cart']['domains']) : 0;
$numrenewals = isset($_SESSION['cart']['renewals']) ? count($_SESSION['cart']['renewals']) : 0;
$smartyvalues['numitemsincart'] = $numproducts + $numaddons + $numdomains + $numrenewals;
if( isset($_SESSION['cart']['lastconfigured']) )
{
    bundlesStepCompleteRedirect($_SESSION['cart']['lastconfigured']);
    unset($_SESSION['cart']['lastconfigured']);
}
if( $step == 'fraudcheck' )
{
    $a = 'fraudcheck';
}
if( $promocode = $whmcs->get_req_var('promocode') )
{
    SetPromoCode($promocode);
}
if( $a == 'empty' )
{
    unset($_SESSION['cart']);
    redir("a=view");
}
if( $a == 'startover' )
{
    unset($_SESSION['cart']);
    redir();
}
if( $a == 'remove' && !is_null($productInfoKey) )
{
    if( $r == 'p' && isset($_SESSION['cart']['products'][$productInfoKey]) )
    {
        unset($_SESSION['cart']['products'][$productInfoKey]);
        $_SESSION['cart']['products'] = array_values($_SESSION['cart']['products']);
    }
    else
    {
        if( $r == 'a' && isset($_SESSION['cart']['addons'][$productInfoKey]) )
        {
            unset($_SESSION['cart']['addons'][$productInfoKey]);
            $_SESSION['cart']['addons'] = array_values($_SESSION['cart']['addons']);
        }
        else
        {
            if( $r == 'd' && isset($_SESSION['cart']['domains'][$productInfoKey]) )
            {
                unset($_SESSION['cart']['domains'][$productInfoKey]);
                $_SESSION['cart']['domains'] = array_values($_SESSION['cart']['domains']);
            }
            else
            {
                if( $r == 'r' && isset($_SESSION['cart']['renewals'][$productInfoKey]) )
                {
                    unset($_SESSION['cart']['renewals'][$productInfoKey]);
                }
            }
        }
    }
    redir("a=view");
}
if( $a == 'applypromo' )
{
    $promoerrormessage = SetPromoCode($promocode);
    echo $promoerrormessage;
    exit();
}
if( $a == 'removepromo' )
{
    $_SESSION['cart']['promo'] = '';
    if( $ajax )
    {
        exit();
    }
    redir("a=view");
}
if( (!$a || $a == 'add' && $pid) && ($sld && $tld && !is_array($sld) || is_array($domains)) )
{
    if( is_array($domains) )
    {
        $tempdomain = $domains[0];
        $tempdomain = explode(".", $tempdomain, 2);
        $sld = $tempdomain[0];
        $tld = "." . $tempdomain[1];
    }
    $_SESSION['cartdomain']['sld'] = $sld;
    $_SESSION['cartdomain']['tld'] = $tld;
}
if( !$a )
{
    if( $CONFIG['AllowRegister'] )
    {
        $smartyvalues['registerdomainenabled'] = true;
    }
    if( $CONFIG['AllowTransfer'] )
    {
        $smartyvalues['transferdomainenabled'] = true;
    }
    if( $CONFIG['EnableDomainRenewalOrders'] )
    {
        $smartyvalues['renewalsenabled'] = true;
    }
    if( $gid == 'domains' )
    {
        redir("a=add&domain=register");
    }
    else
    {
        if( $gid == 'addons' )
        {
            if( !$_SESSION['uid'] )
            {
                $orderform = false;
                require("login.php");
            }
            $smarty->assign('gid', 'addons');
            $templatefile = 'addons';
            $productgroups = $orderfrm->getProductGroups();
            $smarty->assign('productgroups', $productgroups);
            $where = array(  );
            $where['userid'] = $_SESSION['uid'];
            $where['domainstatus'] = 'Active';
            if( $pid )
            {
                $where["tblhosting.id"] = $pid;
            }
            $productids = array(  );
            $result = select_query('tblhosting', "tblhosting.id,domain,packageid,name", $where, '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
            while( $data = mysql_fetch_array($result) )
            {
                $productstoids[$data['packageid']][] = array( 'id' => $data['id'], 'product' => $data['name'], 'domain' => $data['domain'] );
                if( !in_array($data['packageid'], $productids) )
                {
                    $productids[] = $data['packageid'];
                }
            }
            $addonids = array(  );
            $result = select_query('tbladdons', 'id,packages', '');
            while( $data = mysql_fetch_array($result) )
            {
                $id = $data['id'];
                $packages = $data['packages'];
                $packages = explode(',', $packages);
                foreach( $productids as $productid )
                {
                    if( in_array($productid, $productids) && !in_array($id, $addonids) )
                    {
                        $addonids[] = $id;
                    }
                }
            }
            $addons = array(  );
            if( count($addonids) )
            {
                $result = select_query('tbladdons', '', "id IN (" . db_build_in_array($addonids) . ")", "weight` ASC,`name", 'ASC');
                while( $data = mysql_fetch_array($result) )
                {
                    $addonid = $data['id'];
                    $packages = $data['packages'];
                    $packages = explode(',', $packages);
                    $name = $data['name'];
                    $description = $data['description'];
                    $billingcycle = $data['billingcycle'];
                    $free = false;
                    if( $billingcycle == "Free Account" )
                    {
                        $free = true;
                    }
                    else
                    {
                        $result2 = select_query('tblpricing', '', array( 'type' => 'addon', 'currency' => $currency['id'], 'relid' => $addonid ));
                        $data = mysql_fetch_array($result2);
                        $setupfee = $data['msetupfee'];
                        $recurring = $data['monthly'];
                        $setupfee = $setupfee == "0.00" ? '' : formatCurrency($setupfee);
                    }
                    $billingcycle = WHMCS_ClientArea::getrawstatus($billingcycle);
                    $billingcycle = $_LANG['orderpaymentterm' . $billingcycle];
                    $packageids = array(  );
                    foreach( $packages as $packageid )
                    {
                        $thisaddonspackages = '';
                        $thisaddonspackages = $productstoids[$packageid];
                        if( $thisaddonspackages )
                        {
                            $packageids = array_merge($packageids, $thisaddonspackages);
                        }
                    }
                    if( count($packageids) )
                    {
                        $addons[] = array( 'id' => $addonid, 'name' => $name, 'description' => $description, 'free' => $free, 'setupfee' => $setupfee, 'recurringamount' => formatCurrency($recurring), 'billingcycle' => $billingcycle, 'productids' => $packageids );
                    }
                }
            }
            $smarty->assign('addons', $addons);
            if( !count($addons) )
            {
                $smarty->assign('noaddons', true);
            }
        }
        else
        {
            if( $gid == 'renewals' )
            {
                if( !$CONFIG['EnableDomainRenewalOrders'] )
                {
                    redir('', "clientarea.php");
                }
                if( !$_SESSION['uid'] )
                {
                    $orderform = false;
                    require("login.php");
                }
                $smarty->assign('gid', 'renewals');
                $templatefile = 'domainrenewals';
                $productgroups = $orderfrm->getProductGroups();
                $smartyvalues['productgroups'] = $productgroups;
                $DomainRenewalGracePeriods = $DomainRenewalMinimums = array(  );
                require(ROOTDIR . "/configuration.php");
                $DomainRenewalGracePeriods = array_merge(array( ".com" => '30', ".net" => '30', ".org" => '30', ".info" => '15', ".biz" => '30', ".mobi" => '30', ".name" => '30', ".asia" => '30', ".tel" => '30', ".in" => '15', ".mn" => '30', ".bz" => '30', ".cc" => '30', ".tv" => '30', ".eu" => '0', ".co.uk" => '97', ".org.uk" => '97', ".me.uk" => '97', ".us" => '30', ".ws" => '0', ".me" => '30', ".cn" => '30', ".nz" => '0', ".ca" => '30' ), $DomainRenewalGracePeriods);
                $DomainRenewalMinimums = array_merge(array( ".co.uk" => '180', ".org.uk" => '180', ".me.uk" => '180', ".com.au" => '90', ".net.au" => '90', ".org.au" => '90' ), $DomainRenewalMinimums);
                $DomainRenewalPriceOptions = array(  );
                $renewals = array(  );
                $result = select_query('tbldomains', '', "userid='" . (int) $_SESSION['uid'] . "' AND (status='Active' OR status='Expired')", 'expirydate', 'ASC');
                while( $data = mysql_fetch_array($result) )
                {
                    $id = $data['id'];
                    $domain = $data['domain'];
                    $expirydate = $data['expirydate'];
                    $status = $data['status'];
                    if( $expirydate == '0000-00-00' )
                    {
                        $expirydate = $data['nextduedate'];
                    }
                    $todaysdatetime = strtotime(date('Ymd'));
                    $expirydatetime = strtotime($expirydate);
                    $daysuntilexpiry = round(($expirydatetime - $todaysdatetime) / 86400);
                    $domainparts = explode(".", $domain, 2);
                    $tld = "." . $domainparts[1];
                    $beforerenewlimit = $ingraceperiod = $pastgraceperiod = false;
                    if( array_key_exists($tld, $DomainRenewalMinimums) && $DomainRenewalMinimums[$tld] < $daysuntilexpiry )
                    {
                        $beforerenewlimit = true;
                    }
                    if( array_key_exists($tld, $DomainRenewalGracePeriods) )
                    {
                        if( $DomainRenewalGracePeriods[$tld] < $daysuntilexpiry * (0 - 1) )
                        {
                            $pastgraceperiod = true;
                        }
                    }
                    else
                    {
                        if( $daysuntilexpiry < 0 )
                        {
                            $pastgraceperiod = true;
                        }
                    }
                    if( !$pastgraceperiod && $daysuntilexpiry < 0 )
                    {
                        $ingraceperiod = true;
                    }
                    if( !array_key_exists($tld, $DomainRenewalPriceOptions) )
                    {
                        $temppricelist = getTLDPriceList($tld, true, true);
                        $renewaloptions = array(  );
                        foreach( $temppricelist as $regperiod => $options )
                        {
                            if( $options['renew'] )
                            {
                                $renewaloptions[] = array( 'period' => $regperiod, 'price' => $options['renew'] );
                            }
                        }
                        $DomainRenewalPriceOptions[$tld] = $renewaloptions;
                    }
                    else
                    {
                        $renewaloptions[] = $DomainRenewalPriceOptions[$tld];
                    }
                    $rawstatus = WHMCS_ClientArea::getrawstatus($status);
                    if( count($renewaloptions) )
                    {
                        $renewals[] = array( 'id' => $id, 'domain' => $domain, 'tld' => $tld, 'status' => $_LANG['clientarea' . $rawstatus], 'expirydate' => fromMySQLDate($expirydate), 'daysuntilexpiry' => $daysuntilexpiry, 'beforerenewlimit' => $beforerenewlimit, 'beforerenewlimitdays' => $DomainRenewalMinimums[$tld], 'ingraceperiod' => $ingraceperiod, 'pastgraceperiod' => $pastgraceperiod, 'graceperioddays' => $DomainRenewalGracePeriods[$tld], 'renewaloptions' => $DomainRenewalPriceOptions[$tld] );
                    }
                }
                $smartyvalues['renewals'] = $renewals;
            }
            else
            {
                $templatefile = 'products';
                $productgroups = $orderfrm->getProductGroups();
                $smartyvalues['productgroups'] = $productgroups;
                if( $pid )
                {
                    $result = select_query('tblproducts', 'id,gid', array( 'id' => $pid ));
                    $data = mysql_fetch_array($result);
                    $pid = $data['id'];
                    $gid = $data['gid'];
                    $smartyvalues['pid'] = $pid;
                }
                else
                {
                    if( !$gid )
                    {
                        $gid = $productgroups[0]['gid'];
                    }
                }
                $groupinfo = $orderfrm->getProductGroupInfo($gid);
                if( count($productgroups) && !$groupinfo )
                {
                    redir();
                }
                $smartyvalues['gid'] = $groupinfo['id'];
                $smartyvalues['groupname'] = $groupinfo['name'];
                $products = $orderfrm->getProducts($gid, true, true);
                $smartyvalues['products'] = $products;
                $smartyvalues['productscount'] = count($products);
            }
        }
    }
}
if( $a == 'add' )
{
    if( $pid )
    {
        $templatefile = 'configureproductdomain';
        $productinfo = $orderfrm->setPid($pid);
        if( !$productinfo )
        {
            redir();
        }
        $_SESSION['cart']['domainoptionspid'] = $productinfo['pid'];
        $smartyvalues['productinfo'] = $productinfo;
        $smartyvalues['pid'] = $productinfo['pid'];
        $pid = $smartyvalues['pid'];
        $type = $productinfo['type'];
        $subdomain = $productinfo['subdomain'];
        $freedomain = $productinfo['freedomain'];
        $freedomaintlds = $productinfo['freedomaintlds'];
        $showdomainoptions = $productinfo['showdomainoptions'];
        $stockcontrol = $productinfo['stockcontrol'];
        $qty = $productinfo['qty'];
        $subdomains = $productinfo['subdomain'];
        if( $stockcontrol && $qty <= 0 )
        {
            $templatefile = 'error';
            $smartyvalues['errortitle'] = $_LANG['outofstock'];
            $smartyvalues['errormsg'] = $_LANG['outofstockdescription'];
            outputClientArea($templatefile, $ajax);
            exit();
        }
        $subdomains = $subdomain ? explode(',', $subdomain) : array(  );
        $passedvariables = array(  );
        if( $skipconfig )
        {
            $passedvariables['skipconfig'] = $skipconfig;
        }
        if( $billingcycle )
        {
            $passedvariables['billingcycle'] = $billingcycle;
        }
        if( $configoption )
        {
            $passedvariables['configoption'] = $configoption;
        }
        if( $customfield )
        {
            $passedvariables['customfield'] = $customfield;
        }
        if( $addons )
        {
            if( !is_array($addons) )
            {
                $passedvariables['addons'] = explode(',', $addons);
            }
            else
            {
                foreach( $addons as $k => $v )
                {
                    $passedvariables['addons'][] = trim($k);
                }
            }
        }
        if( count($passedvariables) )
        {
            $_SESSION['cart']['passedvariables'] = $passedvariables;
        }
        if( $orderconf['directpidstep1'] && !$ajax )
        {
            redir("pid=" . $pid);
        }
        if( $domainselect && !$domains && $ajax && $domainoption != 'incart' && $domainoption != 'owndomain' && $domainoption != 'subdomain' )
        {
            exit( 'nodomains' );
        }
        if( $orderfrm->getProductInfo('showdomainoptions') && !$domains )
        {
            $cartproducts = $_SESSION['cart']['products'];
            $cartdomains = $_SESSION['cart']['domains'];
            if( $cartdomains )
            {
                foreach( $cartdomains as $cartdomain )
                {
                    $domainname = $cartdomain['domain'];
                    if( $cartproducts )
                    {
                        foreach( $cartproducts as $cartproduct )
                        {
                            if( $cartproduct['domain'] == $domainname )
                            {
                                $domainname = '';
                            }
                        }
                    }
                    if( $domainname )
                    {
                        $incartdomains[] = $domainname;
                    }
                }
            }
            if( !in_array($domainoption, array( 'incart', 'register', 'transfer', 'owndomain', 'subdomain' )) )
            {
                $domainoption = '';
            }
            if( $incartdomains && !$domainoption )
            {
                $domainoption = 'incart';
            }
            if( $CONFIG['AllowRegister'] && !$domainoption )
            {
                $domainoption = 'register';
            }
            if( $CONFIG['AllowTransfer'] && !$domainoption )
            {
                $domainoption = 'transfer';
            }
            if( $CONFIG['AllowOwnDomain'] && !$domainoption )
            {
                $domainoption = 'owndomain';
            }
            if( count($subdomains) && !$domainoption )
            {
                $domainoption = 'subdomain';
            }
            $registertlds = getTLDList();
            $transfertlds = getTLDList('transfer');
            $smartyvalues['listtld'] = $registertlds;
            $smartyvalues['registertlds'] = $registertlds;
            $smartyvalues['transfertlds'] = $transfertlds;
            $smartyvalues['showdomainoptions'] = true;
            $smartyvalues['domainoption'] = $domainoption;
            $smartyvalues['registerdomainenabled'] = $CONFIG['AllowRegister'];
            $smartyvalues['transferdomainenabled'] = $CONFIG['AllowTransfer'];
            $smartyvalues['owndomainenabled'] = $CONFIG['AllowOwnDomain'];
            $smartyvalues['subdomain'] = $subdomains[0];
            $smartyvalues['subdomains'] = $subdomains;
            $smartyvalues['incartdomains'] = $incartdomains;
            if( $freedomain && $freedomaintlds )
            {
                $smartyvalues['freedomaintlds'] = $freedomaintlds;
            }
            if( is_array($tld) )
            {
                if( $domainoption == 'register' )
                {
                    $tld = $tld[0];
                    $sld = $sld[0];
                }
                else
                {
                    if( $domainoption == 'transfer' )
                    {
                        $tld = $tld[1];
                        $sld = $sld[1];
                    }
                    else
                    {
                        if( $domainoption == 'owndomain' )
                        {
                            $tld = $tld[2];
                            $sld = $sld[2];
                        }
                        else
                        {
                            if( $domainoption == 'subdomain' )
                            {
                                if( !$subdomains[$tld[3]] )
                                {
                                    $tld[3] = 0;
                                }
                                $tld = $subdomains[$tld[3]];
                                $sld = $sld[3];
                            }
                            else
                            {
                                if( $domainoption == 'incart' )
                                {
                                    $incartdomain = explode(".", $incartdomain, 2);
                                    $tld = $incartdomain[1];
                                    $sld = $incartdomain[0];
                                }
                            }
                        }
                    }
                }
            }
            $nocontinue = false;
            if( !$sld && !$tld && isset($_SESSION['cartdomain']['sld']) && isset($_SESSION['cartdomain']['tld']) && in_array($_SESSION['cartdomain']['tld'], $registertlds) )
            {
                $sld = $_SESSION['cartdomain']['sld'];
                $tld = $_SESSION['cartdomain']['tld'];
                $nocontinue = true;
                unset($_SESSION['cartdomain']);
            }
            $sld = cleanDomainInput($sld);
            $tld = cleanDomainInput($tld);
            if( substr($sld, 0 - 1) == "." )
            {
                $sld = substr($sld, 0, 0 - 1);
            }
            if( $sld && $tld && ($domainoption == 'register' && !in_array($tld, $registertlds) || $domainoption == 'transfer' && !in_array($tld, $transfertlds)) )
            {
                $sld = '';
                $tld = '';
            }
            $smartyvalues['sld'] = $sld;
            $smartyvalues['tld'] = $tld;
            if( $tld && substr($tld, 0, 1) != "." )
            {
                $tld = "." . $tld;
            }
            if( isset($_REQUEST['sld']) || isset($_REQUEST['tld']) || $sld )
            {
                $validate = new WHMCS_Validate();
                if( $domainoption == 'subdomain' )
                {
                    if( !is_array($BannedSubdomainPrefixes) )
                    {
                        $BannedSubdomainPrefixes = array(  );
                    }
                    if( $whmcs->get_config('BannedSubdomainPrefixes') )
                    {
                        $bannedprefixes = $whmcs->get_config('BannedSubdomainPrefixes');
                        $bannedprefixes = explode(',', $bannedprefixes);
                        $BannedSubdomainPrefixes = array_merge($BannedSubdomainPrefixes, $bannedprefixes);
                    }
                    if( !checkDomainisValid($sld, ".com") )
                    {
                        $errormessage .= "<li>" . $_LANG['ordererrordomaininvalid'];
                    }
                    else
                    {
                        if( in_array($sld, $BannedSubdomainPrefixes) )
                        {
                            $errormessage .= "<li>" . $_LANG['ordererrorsbudomainbanned'];
                        }
                        else
                        {
                            $result = select_query('tblhosting', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (domainstatus!='Terminated' AND domainstatus!='Cancelled' AND domainstatus!='Fraud')");
                            $data = mysql_fetch_array($result);
                            $subchecks = $data[0];
                            if( $subchecks )
                            {
                                $errormessage = "<li>" . $_LANG['ordererrorsubdomaintaken'];
                            }
                        }
                    }
                    run_validate_hook($validate, 'CartSubdomainValidation', array( 'subdomain' => $sld, 'domain' => $tld ));
                }
                else
                {
                    if( !checkDomainisValid($sld, $tld) )
                    {
                        $errormessage .= $_LANG['ordererrordomaininvalid'];
                    }
                    if( ($domainoption == 'register' || $domainoption == 'transfer') && $CONFIG['AllowDomainsTwice'] )
                    {
                        $result = select_query('tbldomains', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (status!='Expired' AND status!='Cancelled' AND status!='Fraud')");
                        $data = mysql_fetch_array($result);
                        $domaincheck = $data[0];
                        if( $domaincheck )
                        {
                            $errormessage = "<li>" . $_LANG['ordererrordomainalreadyexists'];
                        }
                    }
                    else
                    {
                        if( $domainoption == 'owndomain' && $CONFIG['AllowDomainsTwice'] )
                        {
                            $result = select_query('tblhosting', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (domainstatus!='Terminated' AND domainstatus!='Cancelled' AND domainstatus!='Fraud')");
                            $data = mysql_fetch_array($result);
                            $domaincheck = $data[0];
                            if( $domaincheck )
                            {
                                $errormessage = "<li>" . $_LANG['ordererrordomainalreadyexists'];
                            }
                        }
                    }
                    run_validate_hook($validate, 'ShoppingCartValidateDomain', array( 'domainoption' => $domainoption, 'sld' => $sld, 'tld' => $tld ));
                }
                if( $validate->hasErrors() )
                {
                    $errormessage .= $validate->getHTMLErrorOutput();
                }
                $smartyvalues['errormessage'] = $errormessage;
            }
            if( !$errormessage && !$nocontinue )
            {
                if( ($domainoption == 'register' || $domainoption == 'transfer') && $sld && $tld )
                {
                    if( $domainoption == 'register' )
                    {
                        $searchvar = 'available';
                    }
                    else
                    {
                        $searchvar = 'unavailable';
                    }
                    $smartyvalues['searchvar'] = $searchvar;
                    $result = lookupDomain($sld, $tld);
                    $regoptions = array(  );
                    if( $result['result'] == $searchvar )
                    {
                        $regoptions = getTLDPriceList($tld, true);
                    }
                    $availabilityresults[] = array( 'domain' => $sld . $tld, 'status' => $result['result'], 'regoptions' => $regoptions );
                    $tldslist = $CONFIG['BulkCheckTLDs'];
                    if( $tldslist && $domainoption == 'register' )
                    {
                        $tldslist = explode(',', $tldslist);
                        foreach( $tldslist as $lookuptld )
                        {
                            if( $lookuptld != $tld )
                            {
                                $result = lookupDomain($sld, $lookuptld);
                                $regoptions = array(  );
                                if( $result['result'] == $searchvar )
                                {
                                    $regoptions = getTLDPriceList($lookuptld, true);
                                }
                                $availabilityresults[] = array( 'domain' => $sld . $lookuptld, 'status' => $result['result'], 'regoptions' => $regoptions );
                            }
                        }
                    }
                    $smartyvalues['availabilityresults'] = $availabilityresults;
                    $smartyvalues['domains'] = $domains;
                }
                if( ($domainoption == 'owndomain' || $domainoption == 'subdomain' || $domainoption == 'incart') && $sld && $tld )
                {
                    $smartyvalues['showdomainoptions'] = false;
                    $domains = array( $sld . $tld );
                    $productconfig = true;
                }
            }
        }
        else
        {
            $productconfig = true;
        }
        if( $productconfig )
        {
            $passedvariables = $_SESSION['cart']['passedvariables'];
            unset($_SESSION['cart']['passedvariables']);
            cartPreventDuplicateProduct($domains[0]);
            $prodarray = array( 'pid' => $pid, 'domain' => $domains[0], 'billingcycle' => $passedvariables['billingcycle'], 'configoptions' => $passedvariables['configoption'], 'customfields' => $passedvariables['customfield'], 'addons' => $passedvariables['addons'], 'server' => '', 'noconfig' => true );
            if( isset($passedvariables['bnum']) )
            {
                $prodarray['bnum'] = $passedvariables['bnum'];
            }
            if( isset($passedvariables['bitem']) )
            {
                $prodarray['bitem'] = $passedvariables['bitem'];
            }
            $_SESSION['cart']['products'][] = $prodarray;
            $newprodnum = count($_SESSION['cart']['products']) - 1;
            if( $domainoption == 'register' || $domainoption == 'transfer' )
            {
                foreach( $domains as $domainname )
                {
                    cartPreventDuplicateDomain($domainname);
                    $regperiod = $domainsregperiod[$domainname];
                    $domainparts = explode(".", $domainname, 2);
                    $temppricelist = getTLDPriceList("." . $domainparts[1]);
                    if( !isset($temppricelist[$regperiod][$domainoption]) )
                    {
                        if( is_array($regperiods) )
                        {
                            foreach( $regperiods as $period )
                            {
                                if( substr($period, 0, strlen($domainname)) == $domainname )
                                {
                                    $regperiod = substr($period, strlen($domainname));
                                }
                            }
                        }
                        if( !$regperiod )
                        {
                            $tldyears = array_keys($temppricelist);
                            $regperiod = $tldyears[0];
                        }
                    }
                    $domarray = array( 'type' => $domainoption, 'domain' => $domainname, 'regperiod' => $regperiod );
                    if( isset($passedvariables['bnum']) )
                    {
                        $domarray['bnum'] = $passedvariables['bnum'];
                    }
                    if( isset($passedvariables['bitem']) )
                    {
                        $domarray['bitem'] = $passedvariables['bitem'];
                    }
                    $_SESSION['cart']['domains'][] = $domarray;
                }
            }
            $_SESSION['cart']['newproduct'] = true;
            if( $ajax )
            {
                $ajax = "&ajax=1";
            }
            else
            {
                if( $passedvariables['skipconfig'] )
                {
                    unset($_SESSION['cart']['products'][$newprodnum]['noconfig']);
                    $_SESSION['cart']['lastconfigured'] = array( 'type' => 'product', 'i' => $newprodnum );
                    redir("a=view");
                }
            }
            redir("a=confproduct&i=" . $newprodnum . $ajax);
        }
    }
    else
    {
        if( $aid )
        {
            $requestAddonID = (int) $whmcs->get_req_var('aid');
            $requestServiceID = (int) $whmcs->get_req_var('serviceid');
            $requestProductID = (int) $whmcs->get_req_var('productid');
            if( !$requestServiceID && $requestProductID )
            {
                $requestServiceID = $requestProductID;
            }
            if( !$requestAddonID || !$requestServiceID )
            {
                redir("gid=addons");
            }
            $data = get_query_vals('tblhosting', 'id,packageid', array( 'id' => $requestServiceID, 'userid' => WHMCS_Session::get('uid'), 'domainstatus' => 'Active' ));
            $serviceid = $data['id'];
            $pid = $data['packageid'];
            if( !$serviceid )
            {
                redir("gid=addons");
            }
            $data = get_query_vals('tbladdons', 'id,packages', array( 'id' => $requestAddonID ));
            $aid = $data['id'];
            $packages = $data['packages'];
            if( !$aid )
            {
                redir("gid=addons");
            }
            $packages = explode(',', $packages);
            if( !in_array($pid, $packages) )
            {
                redir("gid=addons");
            }
            $_SESSION['cart']['addons'][] = array( 'id' => $aid, 'productid' => $serviceid );
            if( $ajax )
            {
                exit();
            }
            redir("a=view");
        }
        else
        {
            if( $domain )
            {
                if( $CONFIG['AllowRegister'] )
                {
                    $smartyvalues['registerdomainenabled'] = true;
                }
                if( $CONFIG['AllowTransfer'] )
                {
                    $smartyvalues['transferdomainenabled'] = true;
                }
                if( $CONFIG['EnableDomainRenewalOrders'] )
                {
                    $smartyvalues['renewalsenabled'] = true;
                }
                if( !in_array($domain, array( 'register', 'transfer' )) )
                {
                    $domain = 'register';
                }
                if( $domain == 'register' && !$CONFIG['AllowRegister'] )
                {
                    redir();
                }
                if( $domain == 'transfer' && !$CONFIG['AllowTransfer'] )
                {
                    redir();
                }
                if( $domains )
                {
                    $passedvariables = $_SESSION['cart']['passedvariables'];
                    unset($_SESSION['cart']['passedvariables']);
                    foreach( $domains as $domainname )
                    {
                        cartPreventDuplicateDomain($domainname);
                        $regperiod = $domainsregperiod[$domainname];
                        $domainparts = explode(".", $domainname, 2);
                        $temppricelist = getTLDPriceList("." . $domainparts[1]);
                        if( !isset($temppricelist[$regperiod][$domain]) )
                        {
                            if( is_array($regperiods) )
                            {
                                foreach( $regperiods as $period )
                                {
                                    if( substr($period, 0, strlen($domainname)) == $domainname )
                                    {
                                        $regperiod = substr($period, strlen($domainname));
                                    }
                                }
                            }
                            if( !$regperiod )
                            {
                                $tldyears = array_keys($temppricelist);
                                $regperiod = $tldyears[0];
                            }
                        }
                        $domarray = array( 'type' => $domain, 'domain' => $domainname, 'regperiod' => $regperiod, 'eppcode' => $eppcode );
                        if( isset($passedvariables['addons']) )
                        {
                            foreach( $passedvariables['addons'] as $domaddon )
                            {
                                $domarray[$domaddon] = true;
                            }
                        }
                        if( isset($passedvariables['bnum']) )
                        {
                            $domarray['bnum'] = $passedvariables['bnum'];
                        }
                        if( isset($passedvariables['bitem']) )
                        {
                            $domarray['bitem'] = $passedvariables['bitem'];
                        }
                        $_SESSION['cart']['domains'][] = $domarray;
                    }
                    if( $ajax )
                    {
                        $ajax = "&ajax=1";
                    }
                    $newdomnum = count($_SESSION['cart']['domains']) - 1;
                    $_SESSION['cart']['lastconfigured'] = array( 'type' => 'domain', 'i' => $newdomnum );
                    if( !$ajax && is_array($orderconf['denynonajaxaccess']) && in_array('confdomains', $orderconf['denynonajaxaccess']) )
                    {
                        $smartyvalues['selecteddomains'] = $_SESSION['cart']['domains'];
                        $smartyvalues['skipselect'] = true;
                    }
                    else
                    {
                        redir("a=confdomains" . $ajax);
                    }
                }
                $templatefile = 'adddomain';
                $productgroups = $orderfrm->getProductGroups();
                $smarty->assign('productgroups', $productgroups);
                $smartyvalues['domain'] = $domain;
                $tldslist = $domain == 'register' ? getTLDList() : getTLDList('transfer');
                if( !$sld && !$tld && isset($_SESSION['cartdomain']) && in_array($_SESSION['cartdomain']['tld'], $tldslist) )
                {
                    $sld = $_SESSION['cartdomain']['sld'];
                    $tld = $_SESSION['cartdomain']['tld'];
                }
                $sld = cleanDomainInput($sld);
                $tld = cleanDomainInput($tld);
                if( !in_array($tld, $tldslist) )
                {
                    $sld = $tld = '';
                }
                if( !$domains )
                {
                    $domains[] = $sld . $tld;
                }
                $smartyvalues['domains'] = $domains;
                $smartyvalues['tlds'] = $tldslist;
                $smartyvalues['sld'] = $sld;
                $smartyvalues['tld'] = $tld;
                if( $sld && $tld )
                {
                    if( !checkDomainisValid($sld, $tld) )
                    {
                        $errormessage .= $_LANG['ordererrordomaininvalid'];
                    }
                    if( $CONFIG['AllowDomainsTwice'] )
                    {
                        $result = select_query('tbldomains', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (status!='Expired' AND status!='Cancelled')");
                        $data = mysql_fetch_array($result);
                        $domaincheck = $data[0];
                        if( $domaincheck )
                        {
                            $errormessage .= "<li>" . $_LANG['ordererrordomainalreadyexists'];
                        }
                    }
                    $smartyvalues['errormessage'] = $errormessage;
                }
                if( $sld && $tld && !$errormessage )
                {
                    if( $domain == 'register' )
                    {
                        $searchvar = 'available';
                    }
                    else
                    {
                        $searchvar = 'unavailable';
                    }
                    $smarty->assign('searchvar', $searchvar);
                    $result = lookupDomain($sld, $tld);
                    $regoptions = array(  );
                    if( $result['result'] == $searchvar )
                    {
                        $regoptions = getTLDPriceList($tld, true);
                    }
                    $availabilityresults[] = array( 'domain' => $sld . $tld, 'status' => $result['result'], 'regoptions' => $regoptions );
                    $tldslist = $CONFIG['BulkCheckTLDs'];
                    if( $tldslist && $domain == 'register' )
                    {
                        $tldslist = explode(',', $tldslist);
                        foreach( $tldslist as $lookuptld )
                        {
                            if( $lookuptld != $tld && checkDomainisValid($sld, $lookuptld) )
                            {
                                $result = lookupDomain($sld, $lookuptld);
                                $regoptions = array(  );
                                if( $result['result'] == $searchvar )
                                {
                                    $regoptions = getTLDPriceList($lookuptld, true);
                                }
                                $availabilityresults[] = array( 'domain' => $sld . $lookuptld, 'status' => $result['result'], 'regoptions' => $regoptions );
                            }
                        }
                    }
                    $smarty->assign('availabilityresults', $availabilityresults);
                }
            }
            else
            {
                if( $renewals )
                {
                    if( $renewalid )
                    {
                        $_SESSION['cart']['renewals'][$renewalid] = $renewalperiod;
                    }
                    else
                    {
                        if( !count($renewalids) )
                        {
                            redir("gid=renewals");
                        }
                        else
                        {
                            foreach( $renewalids as $domainid )
                            {
                                $_SESSION['cart']['renewals'][$domainid] = $renewalperiod[$domainid];
                            }
                        }
                    }
                    if( $ajax )
                    {
                        exit();
                    }
                    redir("a=view");
                }
                else
                {
                    if( $bid )
                    {
                        $data = get_query_vals('tblbundles', '', array( 'id' => $bid ));
                        $bid = $data['id'];
                        $validfrom = $data['validfrom'];
                        $validuntil = $data['validuntil'];
                        $uses = $data['uses'];
                        $maxuses = $data['maxuses'];
                        $itemdata = $data['itemdata'];
                        $itemdata = unserialize($itemdata);
                        $vals = $itemdata[0];
                        if( $validfrom != '0000-00-00' && date('Ymd') < str_replace('-', '', $validfrom) || $validuntil != '0000-00-00' && str_replace('-', '', $validuntil) < date('Ymd') )
                        {
                            $templatefile = 'error';
                            $smartyvalues['errortitle'] = $_LANG['bundlevaliddateserror'];
                            $smartyvalues['errormsg'] = $_LANG['bundlevaliddateserrordesc'];
                            outputClientArea($templatefile);
                            exit();
                        }
                        if( $maxuses && $maxuses <= $uses )
                        {
                            $templatefile = 'error';
                            $smartyvalues['errortitle'] = $_LANG['bundlemaxusesreached'];
                            $smartyvalues['errormsg'] = $_LANG['bundlemaxusesreacheddesc'];
                            outputClientArea($templatefile);
                            exit();
                        }
                        $_SESSION['cart']['bundle'][] = array( 'bid' => $bid, 'step' => '0', 'complete' => '0' );
                        $totalnum = count($_SESSION['cart']['bundle']);
                        $vals['bnum'] = $totalnum - 1;
                        $vals['bitem'] = '0';
                        $vals['billingcycle'] = str_replace(array( '-', " " ), '', strtolower($vals['billingcycle']));
                        $_SESSION['cart']['passedvariables'] = $vals;
                        redir("a=add&pid=" . $vals['pid']);
                    }
                    else
                    {
                        redir();
                    }
                }
            }
        }
    }
}
if( $a == 'domainoptions' )
{
    $productinfo = $orderfrm->setPid($_SESSION['cart']['domainoptionspid']);
    if( $checktype == 'register' || $checktype == 'transfer' )
    {
        if( $domain )
        {
            $domainparts = explode(".", $domain, 2);
            $sld = $domainparts[0];
            $tld = $domainparts[1];
        }
        $sld = cleanDomainInput($sld);
        $tld = cleanDomainInput($tld);
        $domain = $sld . $tld;
        if( $sld != 'www' && $sld && $tld && checkDomainisValid($sld, $tld) )
        {
            if( substr($tld, 0, 1) != "." )
            {
                $tld = "." . $tld;
            }
            if( $CONFIG['AllowDomainsTwice'] )
            {
                $result = select_query('tbldomains', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (status!='Expired' AND status!='Cancelled')");
                $data = mysql_fetch_array($result);
                $domaincheck = $data[0];
            }
            if( $domaincheck )
            {
                $smartyvalues['alreadyindb'] = true;
            }
            else
            {
                $regenabled = $CONFIG['AllowRegister'];
                $transferenabled = $CONFIG['AllowTransfer'];
                $owndomainenabled = $CONFIG['AllowOwnDomain'];
                $whoislookup = lookupDomain($sld, $tld);
                $domainstatus = $whoislookup['result'];
                if( !$checktype )
                {
                    $checktype = $domainstatus == 'available' ? 'register' : 'transfer';
                }
                $smartyvalues['status'] = $domainstatus;
                if( $regenabled )
                {
                    $regoptions = getTLDPriceList($tld, true);
                    $smartyvalues['regoptionscount'] = count($regoptions);
                    $smartyvalues['regoptions'] = $regoptions;
                }
                if( $transferenabled )
                {
                    $transferoptions = getTLDPriceList($tld, true, 'transfer');
                    $smartyvalues['transferoptionscount'] = count($transferoptions);
                    $smartyvalues['transferoptions'] = $transferoptions;
                    $transferprice = current($transferoptions);
                    $smartyvalues['transferterm'] = key($transferoptions);
                    $smartyvalues['transferprice'] = $transferprice['transfer'];
                }
                $smartyvalues['domain'] = $domain;
                $smartyvalues['checktype'] = $checktype;
                $smartyvalues['regenabled'] = $regenabled;
                $smartyvalues['transferenabled'] = $transferenabled;
                $smartyvalues['owndomainenabled'] = $owndomainenabled;
                if( $checktype == 'register' && $regenabled )
                {
                    $tldslist = $CONFIG['BulkCheckTLDs'];
                    $othersuggestions = array(  );
                    if( $tldslist )
                    {
                        $tldslist = explode(',', $tldslist);
                        foreach( $tldslist as $lookuptld )
                        {
                            if( $lookuptld != $tld && checkDomainisValid($sld, $lookuptld) )
                            {
                                $result = lookupDomain($sld, $lookuptld);
                                if( $result['result'] == 'available' )
                                {
                                    $othersuggestions[] = array( 'domain' => $sld . $lookuptld, 'status' => $result['result'], 'regoptions' => getTLDPriceList($lookuptld, true) );
                                }
                            }
                        }
                    }
                }
                $smartyvalues['othersuggestions'] = $othersuggestions;
            }
        }
        else
        {
            $smartyvalues['invalid'] = true;
        }
    }
    else
    {
        if( $checktype == 'owndomain' )
        {
            if( $sld && $tld && checkDomainisValid($sld, $tld) )
            {
                if( substr($tld, 0, 1) != "." )
                {
                    $tld = "." . $tld;
                }
                if( $CONFIG['AllowDomainsTwice'] )
                {
                    $result = select_query('tblhosting', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (domainstatus!='Terminated' AND domainstatus!='Cancelled' AND domainstatus!='Fraud')");
                    $data = mysql_fetch_array($result);
                    $domaincheck = $data[0];
                    if( $domaincheck )
                    {
                        $smartyvalues['alreadyindb'] = true;
                    }
                }
                $smartyvalues['checktype'] = $checktype;
                $smartyvalues['sld'] = $sld;
                $smartyvalues['tld'] = $tld;
            }
            else
            {
                $smartyvalues['invalid'] = true;
            }
        }
        else
        {
            if( $checktype == 'subdomain' )
            {
                if( !is_array($BannedSubdomainPrefixes) )
                {
                    $BannedSubdomainPrefixes = array(  );
                }
                if( $whmcs->get_config('BannedSubdomainPrefixes') )
                {
                    $bannedprefixes = $whmcs->get_config('BannedSubdomainPrefixes');
                    $bannedprefixes = explode(',', $bannedprefixes);
                    $BannedSubdomainPrefixes = array_merge($BannedSubdomainPrefixes, $bannedprefixes);
                }
                if( !checkDomainisValid($sld, ".com") )
                {
                    $smartyvalues['invalid'] = true;
                }
                else
                {
                    if( in_array($sld, $BannedSubdomainPrefixes) )
                    {
                        $smartyvalues['invalid'] = true;
                        $smartyvalues['reason'] = $_LANG['ordererrorsbudomainbanned'];
                    }
                    else
                    {
                        $result = select_query('tblhosting', "COUNT(*)", "domain='" . db_escape_string($sld . $tld) . "' AND (domainstatus!='Terminated' AND domainstatus!='Cancelled' AND domainstatus!='Fraud')");
                        $data = mysql_fetch_array($result);
                        $subchecks = $data[0];
                        if( $subchecks )
                        {
                            $smartyvalues['invalid'] = true;
                            $smartyvalues['reason'] = $_LANG['ordererrorsubdomaintaken'];
                        }
                        else
                        {
                            $smartyvalues['checktype'] = $checktype;
                            $smartyvalues['sld'] = $sld;
                            $smartyvalues['tld'] = $tld;
                        }
                    }
                }
            }
            else
            {
                if( $checktype == 'incart' )
                {
                    $smartyvalues['checktype'] = 'owndomain';
                    $domainparts = explode(".", $sld, 2);
                    $sld = $domainparts[0];
                    $tld = $domainparts[1];
                    $smartyvalues['sld'] = $sld;
                    $smartyvalues['tld'] = $tld;
                }
            }
        }
    }
    $validate = new WHMCS_Validate();
    if( $checktype == 'subdomain' )
    {
        run_validate_hook($validate, 'CartSubdomainValidation', array( 'subdomain' => $sld, 'domain' => $tld ));
    }
    else
    {
        run_validate_hook($validate, 'ShoppingCartValidateDomain', array( 'domainoption' => $checktype, 'sld' => $sld, 'tld' => $tld ));
    }
    if( $validate->hasErrors() )
    {
        $domainError = $validate->getHTMLErrorOutput();
        $smartyvalues['invalid'] = true;
        $smartyvalues['reason'] = $domainError;
    }
    $templatefile = 'domainoptions';
}
if( $a == 'cyclechange' )
{
    if( !is_int($productInfoKey) || !$billingcycle )
    {
        if( $ajax )
        {
            throw new WHMCS_Exception_Exit($_LANG['invoiceserror']);
        }
        redir();
    }
    if( $orderfrm->validateBillingCycle($billingcycle) )
    {
        $_SESSION['cart']['products'][$productInfoKey]['billingcycle'] = $billingcycle;
    }
    $a = 'confproduct';
}
if( $a == 'confproduct' )
{
    $templatefile = 'configureproduct';
    if( is_null($productInfoKey) || !isset($_SESSION['cart']['products'][$productInfoKey]) || !is_array($_SESSION['cart']['products'][$productInfoKey]) )
    {
        if( $ajax )
        {
            exit( $_LANG['invoiceserror'] );
        }
        redir();
    }
    $newproduct = $_SESSION['cart']['newproduct'];
    unset($_SESSION['cart']['newproduct']);
    $pid = $_SESSION['cart']['products'][$productInfoKey]['pid'];
    $productinfo = $orderfrm->setPid($pid);
    if( !$productinfo )
    {
        redir();
    }
    $_SESSION['cart']['cartsummarypid'] = $productinfo['pid'];
    $pid = $productinfo['pid'];
    if( $configure )
    {
        global $errormessage;
        $errormessage = '';
        $result = select_query('tblproducts', 'type', array( 'id' => $pid ));
        $data = mysql_fetch_array($result);
        $producttype = $data['type'];
        if( $producttype == 'server' )
        {
            $hostname = $whmcs->get_req_var('hostname');
            $ns1prefix = $whmcs->get_req_var('ns1prefix');
            $ns2prefix = $whmcs->get_req_var('ns2prefix');
            $rootpw = $whmcs->get_req_var('rootpw');
            if( !$hostname )
            {
                $errormessage .= "<li>" . $_LANG['ordererrorservernohostname'];
            }
            else
            {
                $result = select_query('tblhosting', "COUNT(id)", array( 'domain' => $hostname . "." . $_SESSION['cart']['products'][$productInfoKey]['domain'], 'domainstatus' => array( 'sqltype' => 'NEQ', 'value' => 'Cancelled' ), 'domainstatus' => array( 'sqltype' => 'NEQ', 'value' => 'Terminated' ), 'domainstatus' => array( 'sqltype' => 'NEQ', 'value' => 'Fraud' ) ));
                $data = mysql_fetch_array($result);
                $existingcount = $data[0];
                if( $existingcount )
                {
                    $errormessage .= "<li>" . $_LANG['ordererrorserverhostnameinuse'];
                }
            }
            if( !$ns1prefix || !$ns2prefix )
            {
                $errormessage .= "<li>" . $_LANG['ordererrorservernonameservers'];
            }
            if( !$rootpw )
            {
                $errormessage .= "<li>" . $_LANG['ordererrorservernorootpw'];
            }
            $serverarray = array( 'hostname' => $hostname, 'ns1prefix' => $ns1prefix, 'ns2prefix' => $ns2prefix, 'rootpw' => $rootpw );
        }
        $configoption = $whmcs->get_req_var('configoption');
        if( $configoption )
        {
            $configOpsReturn = validateAndSanitizeQuantityConfigOptions($configoption);
            $configoptionsarray = $configOpsReturn['validOptions'];
            $errormessage .= $configOpsReturn['errorMessage'];
        }
        $addonsarray = is_array($addons) ? array_keys($addons) : '';
        $errormessage .= bundlesValidateProductConfig($productInfoKey, $billingcycle, $configoptionsarray, $addonsarray);
        $_SESSION['cart']['products'][$productInfoKey]['billingcycle'] = $billingcycle;
        $_SESSION['cart']['products'][$productInfoKey]['server'] = $serverarray;
        $_SESSION['cart']['products'][$productInfoKey]['configoptions'] = $configoptionsarray;
        $_SESSION['cart']['products'][$productInfoKey]['customfields'] = $customfield;
        $_SESSION['cart']['products'][$productInfoKey]['addons'] = $addonsarray;
        if( $calctotal )
        {
            $productinfo = $orderfrm->setPid($_SESSION['cart']['products'][$productInfoKey]['pid']);
            $ordersummarytemp = '/templates/orderforms/' . $orderfrm->getTemplate() . "/ordersummary.tpl";
            if( file_exists(ROOTDIR . $ordersummarytemp) )
            {
                $carttotals = calcCartTotals(false, true);
                $templatevars = array( 'producttotals' => $carttotals['products'][$productInfoKey], 'carttotals' => $carttotals );
                echo processSingleTemplate($ordersummarytemp, $templatevars);
            }
            exit();
        }
        if( !$ajax && !$nocyclerefresh && $previousbillingcycle != $billingcycle )
        {
            redir("a=confproduct&i=" . $productInfoKey);
        }
        $validate = new WHMCS_Validate();
        $validate->validateCustomFields('product', $pid, true);
        run_validate_hook($validate, 'ShoppingCartValidateProductUpdate', $_REQUEST);
        if( $validate->hasErrors() )
        {
            $errormessage .= $validate->getHTMLErrorOutput();
        }
        if( $errormessage )
        {
            if( $ajax )
            {
                exit( $errormessage );
            }
            $smartyvalues['errormessage'] = $errormessage;
        }
        else
        {
            unset($_SESSION['cart']['products'][$productInfoKey]['noconfig']);
            $_SESSION['cart']['lastconfigured'] = array( 'type' => 'product', 'i' => $productInfoKey );
            if( $ajax )
            {
                exit();
            }
            redir("a=confdomains");
        }
    }
    $billingcycle = $_SESSION['cart']['products'][$productInfoKey]['billingcycle'];
    $server = $_SESSION['cart']['products'][$productInfoKey]['server'];
    $customfields = $_SESSION['cart']['products'][$productInfoKey]['customfields'];
    $configoptions = $_SESSION['cart']['products'][$productInfoKey]['configoptions'];
    $addons = $_SESSION['cart']['products'][$productInfoKey]['addons'];
    $domain = $_SESSION['cart']['products'][$productInfoKey]['domain'];
    $noconfig = $_SESSION['cart']['products'][$productInfoKey]['noconfig'];
    $billingcycle = $orderfrm->validateBillingCycle($billingcycle);
    $pricing = getPricingInfo($pid);
    $configurableoptions = getCartConfigOptions($pid, $configoptions, $billingcycle, '', true);
    $customfields = getCustomFields('product', $pid, '', '', 'on', $customfields);
    $addonsarray = getAddons($pid, $addons);
    $recurringcycles = 0;
    if( $pricing['type'] == 'recurring' )
    {
        if( 0 <= $pricing['rawpricing']['monthly'] )
        {
            $recurringcycles++;
        }
        if( 0 <= $pricing['rawpricing']['quarterly'] )
        {
            $recurringcycles++;
        }
        if( 0 <= $pricing['rawpricing']['semiannually'] )
        {
            $recurringcycles++;
        }
        if( 0 <= $pricing['rawpricing']['annually'] )
        {
            $recurringcycles++;
        }
        if( 0 <= $pricing['rawpricing']['biennially'] )
        {
            $recurringcycles++;
        }
    }
    if( $newproduct && $productinfo['type'] != 'server' && ($pricing['type'] != 'recurring' || $recurringcycles <= 1) && !count($configurableoptions) && !count($customfields) && !count($addonsarray) )
    {
        unset($_SESSION['cart']['products'][$productInfoKey]['noconfig']);
        $_SESSION['cart']['lastconfigured'] = array( 'type' => 'product', 'i' => $productInfoKey );
        if( $ajax )
        {
            exit();
        }
        redir("a=confdomains");
    }
    $serverarray = array( 'hostname' => isset($server['hostname']) ? $server['hostname'] : '', 'ns1prefix' => isset($server['ns1prefix']) ? $server['ns1prefix'] : '', 'ns2prefix' => isset($server['ns2prefix']) ? $server['ns2prefix'] : '', 'rootpw' => isset($server['rootpw']) ? $server['rootpw'] : '' );
    $smartyvalues['editconfig'] = true;
    $smartyvalues['firstconfig'] = $noconfig ? true : false;
    $smartyvalues['i'] = $productInfoKey;
    $smartyvalues['productinfo'] = $productinfo;
    $smartyvalues['pricing'] = $pricing;
    $smartyvalues['billingcycle'] = $billingcycle;
    $smartyvalues['server'] = $serverarray;
    $smartyvalues['configurableoptions'] = $configurableoptions;
    $smartyvalues['addons'] = $addonsarray;
    $smartyvalues['customfields'] = $customfields;
    $smartyvalues['domain'] = $domain;
}
if( $a == 'confdomains' )
{
    $templatefile = 'configuredomains';
    $skipstep = true;
    $_SESSION['cartdomain'] = '';
    if( $update || $validate )
    {
        $domains = $_SESSION['cart']['domains'];
        foreach( $domains as $key => $domainname )
        {
            if( $validate )
            {
                $domainfield[$key] = $_SESSION['cart']['domains'][$key]['fields'];
            }
            else
            {
                $_SESSION['cart']['domains'][$key]['dnsmanagement'] = $_POST['dnsmanagement'][$key];
                $_SESSION['cart']['domains'][$key]['emailforwarding'] = $_POST['emailforwarding'][$key];
                $_SESSION['cart']['domains'][$key]['idprotection'] = $_POST['idprotection'][$key];
                $_SESSION['cart']['domains'][$key]['eppcode'] = $_POST['epp'][$key];
            }
            $domainparts = explode(".", $domainname['domain'], 2);
            if( $domainname['type'] == 'register' )
            {
                $additflds = new WHMCS_Domains_AdditionalFields();
                $additflds->setTLD($domainparts[1]);
                $additflds->setFieldValues($domainfield[$key]);
                $missingfields = $additflds->getMissingRequiredFields();
                foreach( $missingfields as $missingfield )
                {
                    $errormessage .= "<li>" . $missingfield . " " . $_LANG['clientareaerrorisrequired'] . " (" . $domainname['domain'] . ")";
                }
                $_SESSION['cart']['domains'][$key]['fields'] = $domainfield[$key];
            }
            else
            {
                $result = select_query('tbldomainpricing', '', array( 'extension' => "." . $domainparts[1] ));
                $data = mysql_fetch_array($result);
                if( $data['eppcode'] && !$_POST['epp'][$key] )
                {
                    $errormessage .= "<li>" . $_LANG['domaineppcoderequired'] . " " . $domainname['domain'];
                }
            }
        }
        for( $i = 1; $i <= 5; $i++ )
        {
            $ns = $whmcs->get_req_var('domainns' . $i);
            if( preg_match($nameserverRegexPattern, $ns) )
            {
                $_SESSION['cart']['ns' . $i] = $ns;
            }
            if( $ns == '' && isset($_SESSION['cart']['ns' . $i]) )
            {
                unset($_SESSION['cart']['ns' . $i]);
            }
        }
        $validate = new WHMCS_Validate();
        run_validate_hook($validate, 'ShoppingCartValidateDomainsConfig', $_REQUEST);
        if( $validate->hasErrors() )
        {
            $errormessage .= $validate->getHTMLErrorOutput();
        }
        if( $ajax )
        {
            exit( $errormessage );
        }
        if( $errormessage )
        {
            $smartyvalues['errormessage'] = $errormessage;
        }
        else
        {
            redir("a=view");
        }
    }
    $domains = $_SESSION['cart']['domains'];
    if( $domains )
    {
        foreach( $domains as $key => $domainname )
        {
            $regperiod = $domainname['regperiod'];
            $domainparts = explode(".", $domainname['domain'], 2);
            $sld = $domainparts[0];
            $tld = $domainparts[1];
            $result = select_query('tbldomainpricing', '', array( 'extension' => "." . $tld ));
            $data = mysql_fetch_array($result);
            $domainconfigsshowing = $eppenabled = false;
            if( $data['dnsmanagement'] )
            {
                $domainconfigsshowing = true;
            }
            if( $data['emailforwarding'] )
            {
                $domainconfigsshowing = true;
            }
            if( $data['idprotection'] )
            {
                $domainconfigsshowing = true;
            }
            $result = select_query('tblpricing', '', array( 'type' => 'domainaddons', 'currency' => $currency['id'], 'relid' => 0 ));
            $data2 = mysql_fetch_array($result);
            $domaindnsmanagementprice = $data2['msetupfee'] * $regperiod;
            $domainemailforwardingprice = $data2['qsetupfee'] * $regperiod;
            $domainidprotectionprice = $data2['ssetupfee'] * $regperiod;
            $domaindnsmanagementprice = $domaindnsmanagementprice == "0.00" ? $_LANG['orderfree'] : formatCurrency($domaindnsmanagementprice);
            $domainemailforwardingprice = $domainemailforwardingprice == "0.00" ? $_LANG['orderfree'] : formatCurrency($domainemailforwardingprice);
            $domainidprotectionprice = $domainidprotectionprice == "0.00" ? $_LANG['orderfree'] : formatCurrency($domainidprotectionprice);
            if( $data['eppcode'] && $domainname['type'] == 'transfer' )
            {
                $eppenabled = true;
                $domainconfigsshowing = true;
            }
            $domainfields = array(  );
            if( $domainname['type'] == 'register' )
            {
                $additflds = new WHMCS_Domains_AdditionalFields();
                $additflds->setTLD($tld);
                $additflds->setFieldValues($domainname['fields']);
                $domainfields = $additflds->getFieldsForOutput($key);
                if( count($domainfields) )
                {
                    $domainconfigsshowing = true;
                }
            }
            $products = $_SESSION['cart']['products'];
            $hashosting = false;
            if( $products )
            {
                foreach( $products as $product )
                {
                    if( $product['domain'] == $domainname['domain'] )
                    {
                        $hashosting = true;
                    }
                }
            }
            if( !$hashosting )
            {
                $atleastonenohosting = true;
            }
            if( $atleastonenohosting )
            {
                $skipstep = false;
            }
            $domainsarray[$key] = array( 'domain' => $domainname['domain'], 'regperiod' => $domainname['regperiod'], 'dnsmanagement' => $data['dnsmanagement'], 'emailforwarding' => $data['emailforwarding'], 'idprotection' => $data['idprotection'], 'dnsmanagementprice' => $domaindnsmanagementprice, 'emailforwardingprice' => $domainemailforwardingprice, 'idprotectionprice' => $domainidprotectionprice, 'dnsmanagementselected' => $domainname['dnsmanagement'], 'emailforwardingselected' => $domainname['emailforwarding'], 'idprotectionselected' => $domainname['idprotection'], 'eppenabled' => $eppenabled, 'eppvalue' => $domainname['eppcode'], 'fields' => $domainfields, 'configtoshow' => $domainconfigsshowing, 'hosting' => $hashosting );
            if( $domainconfigsshowing || $eppenabled || $domainfields || $data['dnsmanagement'] || $data['emailforwarding'] || $data['idprotection'] )
            {
                $skipstep = false;
            }
        }
    }
    $smartyvalues['domains'] = $domainsarray;
    $smartyvalues['atleastonenohosting'] = $atleastonenohosting;
    if( !$skipstep && !$_SESSION['cart']['ns1'] && !$_SESSION['cart']['ns2'] )
    {
        for( $i = 1; $i <= 5; $i++ )
        {
            $_SESSION['cart']['ns' . $i] = isset($CONFIG['DefaultNameserver' . $i]) ? $CONFIG['DefaultNameserver' . $i] : null;
        }
    }
    for( $i = 1; $i <= 5; $i++ )
    {
        $ns = isset($_SESSION['cart']['ns' . $i]) ? $_SESSION['cart']['ns' . $i] : '';
        $smartyvalues['domainns' . $i] = $ns;
    }
    if( $skipstep )
    {
        if( $ajax )
        {
            exit();
        }
        redir("a=view");
    }
}
if( $a == 'checkout' )
{
    $domainconfigerror = false;
    $domains = $_SESSION['cart']['domains'];
    if( $domains )
    {
        foreach( $domains as $key => $domaindata )
        {
            $domainparts = explode(".", $domaindata['domain'], 2);
            if( $domaindata['type'] == 'register' )
            {
                $additflds = new WHMCS_Domains_AdditionalFields();
                $additflds->setTLD($domainparts[1]);
                $additflds->setFieldValues($domaindata['fields']);
                if( $additflds->isMissingRequiredFields() )
                {
                    $domainconfigerror = true;
                }
            }
            else
            {
                $result = select_query('tbldomainpricing', 'eppcode', array( 'extension' => "." . $domainparts[1] ));
                $data = mysql_fetch_array($result);
                if( $data['eppcode'] && !$domaindata['eppcode'] )
                {
                    $domainconfigerror = true;
                }
            }
        }
    }
    if( $domainconfigerror )
    {
        if( $ajax )
        {
            $errormessage .= "<li>" . $_LANG['carterrordomainconfigskipped'];
        }
        else
        {
            redir("a=confdomains&validate=1");
        }
    }
    $allowcheckout = true;
    $a = view;
}
if( $a == 'addcontact' )
{
    $allowcheckout = true;
    $addcontact = true;
    $a = view;
}
if( $a == 'view' )
{
    $templatefile = 'viewcart';
    $errormessage = '';
    $gateways = new WHMCS_Gateways();
    $availablegateways = getAvailableOrderPaymentGateways();
    $securityquestions = getSecurityQuestions();
    if( ($submit || $checkout) && !$validatepromo )
    {
        if( $orderfrm->getNumItemsInCart() <= 0 )
        {
            redir("a=view");
        }
        $_SESSION['cart']['paymentmethod'] = $paymentmethod;
        $_SESSION['cart']['notes'] = $notes;
        if( !$_SESSION['uid'] )
        {
            if( $custtype == 'existing' )
            {
                if( !validateClientLogin($loginemail, $loginpw) )
                {
                    $errormessage .= "<li>" . $_LANG['loginincorrect'];
                }
                if( isset($_SESSION['2faverifyc']) )
                {
                    $_SESSION['2fafromcart'] = true;
                    redir('', "clientarea.php");
                }
            }
            else
            {
                $_SESSION['cart']['user'] = array( 'firstname' => $firstname, 'lastname' => $lastname, 'companyname' => $companyname, 'email' => $email, 'address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'postcode' => $postcode, 'country' => $country, 'phonenumber' => $phonenumber );
                $errormessage = checkDetailsareValid('', true, true, false);
            }
        }
        if( $contact == 'new' )
        {
            redir("a=addcontact");
        }
        if( $contact == 'addingnew' )
        {
            $errormessage .= checkContactDetails('', false, 'domaincontact');
        }
        if( $availablegateways[$paymentmethod]['type'] == 'CC' && $ccinfo )
        {
            if( $ccinfo == 'new' )
            {
                $errormessage .= updateCCDetails('', $cctype, $ccnumber, $cccvv, $ccexpirymonth . $ccexpiryyear, $ccstartmonth . $ccstartyear, $ccissuenum);
            }
            if( !$cccvv )
            {
                $errormessage .= "<li>" . $_LANG['creditcardccvinvalid'];
            }
            $_SESSION['cartccdetail'] = encrypt(base64_encode(serialize(array( $cctype, $ccnumber, $ccexpirymonth, $ccexpiryyear, $ccstartmonth, $ccstartyear, $ccissuenum, $cccvv, $nostore ))));
        }
        $validate = new WHMCS_Validate();
        $cartCheckoutHookData = $_REQUEST;
        $cartCheckoutHookData['promocode'] = $orderfrm->getCartDataByKey('promo');
        $cartCheckoutHookData['userid'] = WHMCS_Session::get('uid');
        run_validate_hook($validate, 'ShoppingCartValidateCheckout', $cartCheckoutHookData);
        if( isset($_SESSION['uid']) && $whmcs->get_config('EnableTOSAccept') )
        {
            $validate->validate('required', 'accepttos', 'ordererroraccepttos');
        }
        if( $validate->hasErrors() )
        {
            $errormessage .= $validate->getHTMLErrorOutput();
        }
        $currency = getCurrency($_SESSION['uid'], $_SESSION['currency']);
        if( $_POST['updateonly'] )
        {
            $errormessage = '';
        }
        if( $ajax && $errormessage )
        {
            exit( $errormessage );
        }
        if( !$errormessage && !$_POST['updateonly'] )
        {
            if( !$_SESSION['uid'] )
            {
                $userid = addClient($firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $password, $securityqid, $securityqans);
            }
            if( $contact == 'addingnew' )
            {
                $contact = addContact($_SESSION['uid'], $domaincontactfirstname, $domaincontactlastname, $domaincontactcompanyname, $domaincontactemail, $domaincontactaddress1, $domaincontactaddress2, $domaincontactcity, $domaincontactstate, $domaincontactpostcode, $domaincontactcountry, $domaincontactphonenumber);
            }
            $_SESSION['cart']['contact'] = $contact;
            define('INORDERFORM', true);
            $carttotals = calcCartTotals(true);
            if( $ccinfo == 'new' && !$nostore )
            {
                updateCCDetails($_SESSION['uid'], $cctype, $ccnumber, $cccvv, $ccexpirymonth . $ccexpiryyear, $ccstartmonth . $ccstartyear, $ccissuenum);
            }
            $orderid = $_SESSION['orderdetails']['OrderID'];
            $fraudmodule = getActiveFraudModule();
            if( $CONFIG['SkipFraudForExisting'] )
            {
                $result = select_query('tblorders', "COUNT(*)", array( 'status' => 'Active', 'userid' => $_SESSION['uid'] ));
                $data = mysql_fetch_array($result);
                if( $data[0] )
                {
                    $fraudmodule = '';
                }
            }
            $result = full_query("SELECT COUNT(*) FROM tblinvoices INNER JOIN tblorders ON tblorders.invoiceid=tblinvoices.id WHERE tblorders.id='" . db_escape_string($orderid) . "' AND tblinvoices.status='Paid' AND subtotal>0");
            $data = mysql_fetch_array($result);
            if( $data[0] )
            {
                $fraudmodule = '';
            }
            if( !$fraudmodule )
            {
                if( $ajax )
                {
                    exit();
                }
                redir("a=complete");
            }
            logActivity("Order ID " . $orderid . " Fraud Check Initiated");
            update_query('tblorders', array( 'status' => 'Fraud' ), array( 'id' => $orderid ));
            if( $_SESSION['orderdetails']['Products'] )
            {
                foreach( $_SESSION['orderdetails']['Products'] as $productid )
                {
                    update_query('tblhosting', array( 'domainstatus' => 'Fraud' ), array( 'id' => $productid, 'domainstatus' => 'Pending' ));
                }
            }
            if( $_SESSION['orderdetails']['Addons'] )
            {
                foreach( $_SESSION['orderdetails']['Addons'] as $addonid )
                {
                    update_query('tblhostingaddons', array( 'status' => 'Fraud' ), array( 'id' => $addonid, 'status' => 'Pending' ));
                }
            }
            if( $_SESSION['orderdetails']['Domains'] )
            {
                foreach( $_SESSION['orderdetails']['Domains'] as $domainid )
                {
                    update_query('tbldomains', array( 'status' => 'Fraud' ), array( 'id' => $domainid, 'status' => 'Pending' ));
                }
            }
            update_query('tblinvoices', array( 'status' => 'Cancelled' ), array( 'id' => $_SESSION['orderdetails']['InvoiceID'], 'status' => 'Unpaid' ));
            $fraud = new WHMCS_Module_Fraud();
            if( $fraud->load($fraudmodule) )
            {
                $results = $fraud->doFraudCheck($orderid);
                $_SESSION['orderdetails']['fraudcheckresults'] = $results;
            }
            if( $ajax )
            {
                exit();
            }
            redir("a=fraudcheck");
        }
        if( !$paymentmethod )
        {
            $errormessage .= "<li>No payment gateways available so order cannot proceed";
        }
    }
    $smartyvalues['errormessage'] = $errormessage;
    if( isset($_POST['qty']) && is_array($_POST['qty']) )
    {
        check_token();
        $didQtyChangeRemoveProducts = false;
        foreach( $_POST['qty'] as $i => $qty )
        {
            $i = (int) $i;
            $qty = (int) $qty;
            if( is_array($_SESSION['cart']['products'][$i]) )
            {
                if( 0 < $qty )
                {
                    $_SESSION['cart']['products'][$i]['qty'] = $qty;
                }
                else
                {
                    if( $qty == 0 )
                    {
                        unset($_SESSION['cart']['products'][$i]);
                        $didQtyChangeRemoveProducts = true;
                    }
                }
            }
        }
        if( $didQtyChangeRemoveProducts )
        {
            $_SESSION['cart']['products'] = array_values($_SESSION['cart']['products']);
        }
        redir("a=view");
    }
    if( $promocode )
    {
        $promoerrormessage = SetPromoCode($promocode);
        if( $promoerrormessage )
        {
            $smartyvalues['errormessage'] = "<li>" . $promoerrormessage;
        }
        if( $paymentmethod )
        {
            $_SESSION['cart']['paymentmethod'] = $paymentmethod;
        }
        if( $notes )
        {
            $_SESSION['cart']['notes'] = $notes;
        }
        if( $firstname )
        {
            $_SESSION['cart']['user'] = array( 'firstname' => $firstname, 'lastname' => $lastname, 'companyname' => $companyname, 'email' => $email, 'address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'postcode' => $postcode, 'country' => $country, 'phonenumber' => $phonenumber );
        }
    }
    $smartyvalues['promotioncode'] = $_SESSION['cart']['promo'];
    $ignorenoconfig = $cartsummary ? true : false;
    $carttotals = calcCartTotals('', $ignorenoconfig);
    $promotype = $carttotals['promotype'];
    $promovalue = $carttotals['promovalue'];
    $promorecurring = $carttotals['promorecurring'];
    $promodescription = $promotype == 'Percentage' ? $promovalue . "%" : $promovalue;
    if( $promotype == "Price Override" )
    {
        $promodescription .= " " . $_LANG['orderpromopriceoverride'];
    }
    else
    {
        if( $promotype == "Free Setup" )
        {
            $promodescription = $_LANG['orderpromofreesetup'];
        }
    }
    $promodescription .= " " . $promorecurring . " " . $_LANG['orderdiscount'];
    $smartyvalues['promotiondescription'] = $promodescription;
    foreach( $carttotals as $k => $v )
    {
        $smartyvalues[$k] = $v;
    }
    $smartyvalues['taxenabled'] = $CONFIG['TaxEnabled'];
    $paymentmethod = $_SESSION['cart']['paymentmethod'];
    if( !$paymentmethod )
    {
        foreach( $availablegateways as $k => $v )
        {
            $paymentmethod = $k;
            break;
        }
    }
    $smartyvalues['selectedgateway'] = $paymentmethod;
    $smartyvalues['selectedgatewaytype'] = $availablegateways[$paymentmethod]['type'];
    $smartyvalues['gateways'] = $availablegateways;
    $smartyvalues['ccinfo'] = $ccinfo;
    $smartyvalues['cctype'] = $cctype;
    $smartyvalues['ccnumber'] = $ccnumber;
    $smartyvalues['ccexpirymonth'] = $ccexpirymonth;
    $smartyvalues['ccexpiryyear'] = $ccexpiryyear;
    $smartyvalues['ccstartmonth'] = $ccstartmonth;
    $smartyvalues['ccstartyear'] = $ccstartyear;
    $smartyvalues['ccissuenum'] = $ccissuenum;
    $smartyvalues['cccvv'] = $cccvv;
    $smartyvalues['acceptedcctypes'] = explode(',', $CONFIG['AcceptedCardTypes']);
    $smartyvalues['showccissuestart'] = $CONFIG['ShowCCIssueStart'];
    $smartyvalues['shownostore'] = $CONFIG['CCAllowCustomerDelete'];
    $smartyvalues['months'] = $gateways->getCCDateMonths();
    $smartyvalues['startyears'] = $gateways->getCCStartDateYears();
    $smartyvalues['years'] = $gateways->getCCExpiryDateYears();
    $smartyvalues['expiryyears'] = $smartyvalues['years'];
    $cartitems = count($carttotals['products']) + count($carttotals['addons']) + count($carttotals['domains']) + count($carttotals['renewals']);
    if( !$cartitems )
    {
        $allowcheckout = false;
    }
    $smartyvalues['cartitems'] = $cartitems;
    $smartyvalues['checkout'] = $allowcheckout;
    if( $_SESSION['uid'] )
    {
        $clientsdetails = getClientsDetails();
        $clientsdetails['country'] = $clientsdetails['countryname'];
        $custtype = 'existing';
        $smartyvalues['loggedin'] = true;
    }
    else
    {
        $clientsdetails = $_SESSION['cart']['user'];
        $customfields = getCustomFields('client', '', '', '', 'on', $customfield);
        $_SESSION['loginurlredirect'] = "cart.php?a=login";
        if( !$custtype )
        {
            $custtype = 'new';
        }
    }
    $smartyvalues['custtype'] = $custtype;
    $smartyvalues['clientsdetails'] = $clientsdetails;
    include("includes/countries.php");
    if( !isset($country) )
    {
        $country = $_SESSION['cart']['user']['country'];
    }
    $smartyvalues['clientcountrydropdown'] = getCountriesDropDown($country);
    $smartyvalues['password'] = $password;
    $smartyvalues['password2'] = $password2;
    $smartyvalues['securityqans'] = $securityqans;
    $smartyvalues['securityqid'] = $securityqid;
    $smartyvalues['customfields'] = $customfields;
    $smartyvalues['securityquestions'] = $securityquestions;
    $smartyvalues['shownotesfield'] = $CONFIG['ShowNotesFieldonCheckout'];
    if( !$notes )
    {
        $notes = $_LANG['ordernotesdescription'];
    }
    $smartyvalues['notes'] = $notes;
    $smartyvalues['accepttos'] = $CONFIG['EnableTOSAccept'];
    $smartyvalues['tosurl'] = $CONFIG['TermsOfService'];
    if( count($_SESSION['cart']['domains']) )
    {
        $smartyvalues['domainsinorder'] = true;
    }
    $domaincontacts = array(  );
    $result = select_query('tblcontacts', '', array( 'userid' => $_SESSION['uid'], 'address1' => array( 'sqltype' => 'NEQ', 'value' => '' ) ), "firstname` ASC,`lastname", 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $domaincontacts[] = array( 'id' => $data['id'], 'name' => $data['firstname'] . " " . $data['lastname'] );
    }
    $smartyvalues['domaincontacts'] = $domaincontacts;
    $smartyvalues['contact'] = $contact;
    if( $contact == 'addingnew' )
    {
        $addcontact = true;
    }
    $smartyvalues['addcontact'] = $addcontact;
    $smartyvalues['domaincontact'] = array( 'firstname' => $domaincontactfirstname, 'lastname' => $domaincontactlastname, 'companyname' => $domaincontactcompanyname, 'email' => $domaincontactemail, 'address1' => $domaincontactaddress1, 'address2' => $domaincontactaddress2, 'city' => $domaincontactcity, 'state' => $domaincontactstate, 'postcode' => $domaincontactpostcode, 'country' => $domaincontactcountry, 'phonenumber' => $domaincontactphonenumber );
    $smartyvalues['domaincontactcountrydropdown'] = getCountriesDropDown($domaincontactcountry, 'domaincontactcountry');
    $gatewaysoutput = array(  );
    foreach( $availablegateways as $module => $vals )
    {
        $gatewayModule = new WHMCS_Module_Gateway();
        $gatewayModule->load($module);
        $params = array( 'amount' => $carttotals['rawtotal'], 'currency' => $currency['code'] );
        if( $gatewayModule->functionExists('orderformoutput') )
        {
            $output = $gatewayModule->call('orderformoutput', $params);
            if( $output )
            {
                $gatewaysoutput[] = $output;
            }
        }
    }
    $smartyvalues['gatewaysoutput'] = $gatewaysoutput;
    if( $cartsummary )
    {
        $ajax = '1';
        $templatefile = 'cartsummary';
        $productinfo = $orderfrm->setPid($_SESSION['cart']['cartsummarypid']);
    }
}
if( $a == 'login' )
{
    if( $_SESSION['uid'] )
    {
        redir("a=checkout");
    }
    $templatefile = 'login';
    $_SESSION['loginurlredirect'] = "cart.php?a=login";
    if( $incorrect )
    {
        $smartyvalues['incorrect'] = true;
    }
}
if( $a == 'fraudcheck' )
{
    $orderid = $_SESSION['orderdetails']['OrderID'];
    $results = isset($_SESSION['orderdetails']['fraudcheckresults']) ? $_SESSION['orderdetails']['fraudcheckresults'] : '';
    unset($_SESSION['orderdetails']['fraudcheckresults']);
    if( !$results )
    {
        $fraudmodule = getActiveFraudModule();
        if( $CONFIG['SkipFraudForExisting'] )
        {
            $result = select_query('tblorders', "COUNT(*)", array( 'status' => 'Active', 'userid' => $_SESSION['uid'] ));
            $data = mysql_fetch_array($result);
            if( $data[0] )
            {
                $fraudmodule = '';
            }
        }
        $result = full_query("SELECT COUNT(*) FROM tblinvoices INNER JOIN tblorders ON tblorders.invoiceid=tblinvoices.id WHERE tblorders.id='" . db_escape_string($orderid) . "' AND tblinvoices.status='Paid' AND subtotal>0");
        $data = mysql_fetch_array($result);
        if( $data[0] )
        {
            $fraudmodule = '';
        }
        if( !$fraudmodule )
        {
            redir("a=complete");
        }
        $fraud = new WHMCS_Module_Fraud();
        if( $fraud->load($fraudmodule) )
        {
            $results = $fraud->doFraudCheck($orderid);
        }
    }
    $hookresults = array( 'orderid' => $orderid, 'ordernumber' => $_SESSION['orderdetails']['OrderNumber'], 'fraudresults' => $_SESSION['orderdetails']['fraudcheckresults'], 'invoiceid' => $_SESSION['orderdetails']['InvoiceID'], 'amount' => $_SESSION['orderdetails']['TotalDue'], 'fraudresults' => $results, 'isfraud' => $results['error'], 'clientdetails' => getClientsDetails($_SESSION['uid']) );
    run_hook('AfterFraudCheck', array( $hookresults ));
    $error = $results['error'];
    if( $results['userinput'] )
    {
        logActivity("Order ID " . $orderid . " Fraud Check Awaiting User Input");
        $templatefile = 'fraudcheck';
        $smarty->assign('errortitle', $results['title']);
        $smarty->assign('error', $results['description']);
        outputClientArea($templatefile);
        exit();
    }
    if( $error )
    {
        logActivity("Order ID " . $orderid . " Failed Fraud Check");
        $templatefile = 'fraudcheck';
        $smarty->assign('errortitle', $error['title']);
        $smarty->assign('error', $error['description']);
        outputClientArea($templatefile);
        exit();
    }
    update_query('tblorders', array( 'status' => 'Pending' ), array( 'id' => $orderid ));
    if( $_SESSION['orderdetails']['Products'] )
    {
        foreach( $_SESSION['orderdetails']['Products'] as $productid )
        {
            update_query('tblhosting', array( 'domainstatus' => 'Pending' ), array( 'id' => $productid, 'domainstatus' => 'Fraud' ));
        }
    }
    if( $_SESSION['orderdetails']['Addons'] )
    {
        foreach( $_SESSION['orderdetails']['Addons'] as $addonid )
        {
            update_query('tblhostingaddons', array( 'status' => 'Pending' ), array( 'id' => $addonid, 'status' => 'Fraud' ));
        }
    }
    if( $_SESSION['orderdetails']['Domains'] )
    {
        foreach( $_SESSION['orderdetails']['Domains'] as $domainid )
        {
            update_query('tbldomains', array( 'status' => 'Pending' ), array( 'id' => $domainid, 'status' => 'Fraud' ));
        }
    }
    update_query('tblinvoices', array( 'status' => 'Unpaid' ), array( 'id' => $_SESSION['orderdetails']['InvoiceID'], 'status' => 'Cancelled' ));
    logActivity("Order ID " . $orderid . " Passed Fraud Check");
    redir("a=complete");
}
if( $a == 'complete' )
{
    if( !is_array($_SESSION['orderdetails']) )
    {
        redir();
    }
    $orderid = $_SESSION['orderdetails']['OrderID'];
    $invoiceid = $_SESSION['orderdetails']['InvoiceID'];
    $paymentmethod = $_SESSION['orderdetails']['PaymentMethod'];
    if( WHMCS_Session::get('InOrderButNeedProcessPaidInvoiceAction') && 0 < (int) $invoiceid )
    {
        processPaidInvoice($invoiceid);
    }
    $total = 0;
    if( $invoiceid )
    {
        $result = select_query('tblinvoices', 'id,total,paymentmethod,status', array( 'userid' => $_SESSION['uid'], 'id' => $invoiceid ));
        $data = mysql_fetch_array($result);
        $invoiceid = $data['id'];
        $total = $data['total'];
        $paymentmethod = $data['paymentmethod'];
        $status = $data['status'];
        if( !$invoiceid )
        {
            exit( "Invalid Invoice ID" );
        }
        $clientsdetails = getClientsDetails($_SESSION['uid']);
    }
    $paymentmethod = WHMCS_Gateways::makesafename($paymentmethod);
    if( !$paymentmethod )
    {
        exit( "Unexpected payment method value. Exiting." );
    }
    $result = select_query('tblhosting', "tblhosting.id,tblproducts.servertype", array( "tblhosting.orderid" => $orderid, "tblhosting.domainstatus" => 'Pending', "tblproducts.autosetup" => 'order' ), '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $servertype = $data['servertype'];
        if( getNewClientAutoProvisionStatus($_SESSION['uid']) )
        {
            logActivity("Running Module Create on Order");
            if( !isValidforPath($servertype) )
            {
                exit( "Invalid Server Module Name" );
            }
            include_once(ROOTDIR . '/modules/servers/' . $servertype . '/' . $servertype . ".php");
            $moduleresult = ServerCreateAccount($id);
            if( $moduleresult == 'success' )
            {
                sendMessage('defaultnewacc', $id);
            }
        }
        else
        {
            logActivity("Module Create on Order Suppressed for New Client");
        }
    }
    $gateway = new WHMCS_Module_Gateway();
    $gateway->load($paymentmethod);
    if( $invoiceid && $status == 'Unpaid' && $gateway->functionExists('orderformcheckout') )
    {
        $invoice = new WHMCS_Invoice($invoiceid);
        try
        {
            $params = $invoice->initialiseGatewayAndParams();
        }
        catch( Exception $e )
        {
            logActivity("Failed to initialise payment gateway module: " . $e->getMessage());
            throw new WHMCS_Exception_Fatal("Could not initialise payment gateway. Please contact support.");
        }
        $params = array_merge($params, $invoice->getGatewayInvoiceParams());
        $captureresult = $gateway->call('orderformcheckout', $params);
        if( is_array($captureresult) )
        {
            $gatewayname = $params['name'];
            logTransaction($gatewayname, $captureresult['rawdata'], ucfirst($captureresult['status']));
            if( $captureresult['status'] == 'success' )
            {
                addInvoicePayment($invoiceid, $captureresult['transid'], '', $captureresult['fee'], $paymentmethod);
                $_SESSION['orderdetails']['paymentcomplete'] = true;
                $status = 'Paid';
            }
        }
    }
    if( $invoiceid && $status == 'Unpaid' )
    {
        $gatewaytype = get_query_val('tblpaymentgateways', 'value', array( 'gateway' => $paymentmethod, 'setting' => 'type' ));
        if( !isValidforPath($paymentmethod) )
        {
            exit( "Invalid Payment Gateway Name" );
        }
        $gatewaypath = ROOTDIR . '/modules/gateways/' . $paymentmethod . ".php";
        if( file_exists($gatewaypath) && !function_exists($paymentmethod . '_config') && !function_exists($paymentmethod . '_link') && !function_exists($paymentmethod . '_capture') )
        {
            require_once($gatewaypath);
        }
        if( ($gatewaytype == 'CC' || $gatewaytype == 'OfflineCC') && ($CONFIG['AutoRedirectoInvoice'] == 'on' || $CONFIG['AutoRedirectoInvoice'] == 'gateway') )
        {
            if( function_exists($paymentmethod . '_nolocalcc') )
            {
            }
            else
            {
                redir("invoiceid=" . $invoiceid, "creditcard.php");
            }
        }
        if( $CONFIG['AutoRedirectoInvoice'] == 'on' )
        {
            redir("id=" . $invoiceid, "viewinvoice.php");
        }
        if( $CONFIG['AutoRedirectoInvoice'] == 'gateway' )
        {
            if( in_array($paymentmethod, array( 'mailin', 'banktransfer' )) )
            {
                redir("id=" . $invoiceid, "viewinvoice.php");
            }
            $invoice = new WHMCS_Invoice($invoiceid);
            $paymentbutton = $invoice->getPaymentLink();
            unset($orderform);
            $templatefile = 'forwardpage';
            $smarty->assign('message', $_LANG['forwardingtogateway']);
            $smarty->assign('code', $paymentbutton);
            $smarty->assign('invoiceid', $invoiceid);
            outputClientArea($templatefile);
            exit();
        }
    }
    $amount = get_query_val('tblorders', 'amount', array( 'userid' => $_SESSION['uid'], 'id' => $orderid ));
    $ispaid = false;
    if( $invoiceid )
    {
        $invoiceStatus = get_query_val('tblinvoices', 'status', array( 'id' => $invoiceid ));
        $ispaid = $invoiceStatus == 'Paid' ? true : false;
        if( $ispaid )
        {
            $_SESSION['orderdetails']['paymentcomplete'] = true;
        }
    }
    $templatefile = 'complete';
    $smartyvalues = array_merge($smartyvalues, array( 'orderid' => $orderid, 'ordernumber' => $_SESSION['orderdetails']['OrderNumber'], 'invoiceid' => $invoiceid, 'ispaid' => $ispaid, 'amount' => $amount, 'paymentmethod' => $paymentmethod, 'clientdetails' => getClientsDetails($_SESSION['uid']) ));
    $addons_html = run_hook('ShoppingCartCheckoutCompletePage', $smartyvalues);
    $smartyvalues['addons_html'] = $addons_html;
}
if( !$templatefile )
{
    redir();
}
$nowrapper = isset($_REQUEST['ajax']) ? true : false;
$smartyvalues['carttpl'] = $orderfrm->getTemplate();
outputClientArea($templatefile, $nowrapper);