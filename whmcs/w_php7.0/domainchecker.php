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
require("includes/domainfunctions.php");
require("includes/whoisfunctions.php");
$capatacha = clientAreaInitCaptcha();
$pagetitle = $_LANG['domaintitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"domainchecker.php\">" . $_LANG['domaintitle'] . "</a>";
$templatefile = 'domainchecker';
$pageicon = "images/domains_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
$search = $whmcs->get_req_var('search');
$domain = trim($whmcs->get_req_var('domain'));
$bulkdomains = $whmcs->get_req_var('bulkdomains');
$tld = trim($whmcs->get_req_var('tld'));
$tlds = $whmcs->get_req_var('tlds');
$ext = trim($whmcs->get_req_var('ext'));
$direct = $whmcs->get_req_var('direct');
$sld = '';
$invalidtld = '';
$domains = new WHMCS_Domains();
$availabilityresults = array(  );
$search_tlds = array(  );
$tldslist = array(  );
$userid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
$currencyid = isset($_SESSION['currency']) ? $_SESSION['currency'] : '';
$currency = getCurrency($userid, $currencyid);
$smartyvalues['currency'] = $currency;
if( $whmcs->get_config('BulkDomainSearchEnabled') )
{
    $smartyvalues['bulkdomainsearchenabled'] = true;
}
else
{
    $search = '';
}
$_SESSION['domaincheckerwhois'] = array(  );
$tldslist2 = getTLDList();
foreach( $tldslist2 as $k => $v )
{
    $tldslist[$k + 1] = $v;
}
if( $search == 'bulk' || $search == 'bulkregister' || $search == 'bulktransfer' )
{
    if( $search == 'bulktransfer' )
    {
        $templatefile = 'bulkdomaintransfer';
        $getpricesfor = 'transfer';
    }
    else
    {
        $templatefile = 'bulkdomainchecker';
        $getpricesfor = 'register';
    }
    $smartyvalues['bulk'] = true;
    $bulkdomains = strtolower($bulkdomains);
    $smartyvalues['bulkdomains'] = $bulkdomains;
    if( $bulkdomains )
    {
        check_token('domainchecker');
        $validate = new WHMCS_Validate();
        if( $capatacha )
        {
            $validate->validate('captcha', 'code', 'captchaverifyincorrect');
        }
        if( $validate->hasErrors() )
        {
            $smartyvalues['inccode'] = true;
            $bulkdomains = false;
        }
        if( $bulkdomains )
        {
            $bulkdomains = explode("\r\n", $bulkdomains);
            $domaincount = 0;
            foreach( $bulkdomains as $domain )
            {
                $domainparts = $domains->splitAndCleanDomainInput($domain);
                $sld = $domainparts['sld'];
                $tld = $domainparts['tld'];
                if( $domaincount < 20 )
                {
                    if( in_array($tld, $tldslist) && checkDomainisValid($sld, $tld) )
                    {
                        $_SESSION['domaincheckerwhois'][] = $sld . $tld;
                        $result = lookupDomain($sld, $tld);
                        if( $result['result'] != 'error' )
                        {
                            $tlddata = getTLDPriceList($tld, $getpricesfor);
                            $availabilityresults[] = array( 'domain' => $sld . $tld, 'status' => $result['result'], 'regoptions' => $tlddata );
                        }
                    }
                    else
                    {
                        $smartyvalues['invalid'] = true;
                    }
                }
                $domaincount++;
            }
        }
    }
}
else
{
    if( !$tld && $ext )
    {
        $tld = $ext;
    }
    if( $tld )
    {
        if( substr($tld, 0, 1) != "." )
        {
            $tld = "." . $tld;
        }
        $domain .= $tld;
    }
    if( trim($domain) == "eg. yourdomain.com" )
    {
        $domain = "yourdomain.com";
    }
    $domainparts = $domains->splitAndCleanDomainInput($domain);
    $sld = $domainparts['sld'];
    $tld = $domainparts['tld'];
    if( empty($tlds) && !empty($domain) && !$tld )
    {
        $bulkCheckTLDs = $whmcs->get_config('BulkCheckTLDs');
        if( $bulkCheckTLDs )
        {
            $tlds = explode(',', $bulkCheckTLDs);
        }
        else
        {
            $tld = current($tldslist);
        }
    }
    $search_tlds = array(  );
    $search_tlds[] = $tld;
    if( is_array($tlds) )
    {
        foreach( $tlds as $tldx )
        {
            if( !in_array($tldx, $search_tlds) )
            {
                $search_tlds[] = $tldx;
            }
        }
    }
    foreach( $search_tlds as $k => $temptld )
    {
        if( !in_array($temptld, $tldslist) )
        {
            $invalidtld = $temptld;
            unset($search_tlds[$k]);
        }
    }
    if( !count($search_tlds) && $invalidtld )
    {
        $search_tlds[] = current($tldslist);
    }
    $search_tlds = array_values($search_tlds);
    $checkdomain = false;
    if( $sld && count($search_tlds) )
    {
        $checkdomain = true;
    }
    $validate = new WHMCS_Validate();
    if( $capatacha )
    {
        $validate->validate('captcha', 'code', 'captchaverifyincorrect');
    }
    if( !$direct && $sld && $validate->hasErrors() )
    {
        $smartyvalues['inccode'] = true;
        $checkdomain = false;
    }
    if( $whmcs->get_req_var('transfer') )
    {
        if( $domain != $_LANG['domaincheckerdomainexample'] )
        {
            redir("a=add&domain=transfer&sld=" . $sld . "&tld=" . $search_tlds[0], "cart.php");
        }
        else
        {
            redir("a=add&domain=transfer", "cart.php");
        }
    }
    if( $whmcs->get_req_var('hosting') )
    {
        if( $domain != $_LANG['domaincheckerdomainexample'] )
        {
            redir("sld=" . $sld . "&tld=" . $search_tlds[0], "cart.php");
        }
        else
        {
            redir('', "cart.php");
        }
    }
    $smartyvalues['domain'] = $sld . $tld;
    $smartyvalues['sld'] = $sld;
    $smartyvalues['tld'] = 0 < count($search_tlds) ? $search_tlds[0] : '';
    $smartyvalues['ext'] = $smartyvalues['tld'];
    $smartyvalues['tlds'] = $search_tlds;
    $smartyvalues['tldslist'] = $tldslist;
    $smartyvalues['invalidtld'] = $invalidtld;
    if( $checkdomain )
    {
        check_token("WHMCS.domainchecker");
        $smartyvalues['lookup'] = true;
        if( !checkDomainisValid($sld, $search_tlds[0]) )
        {
            $smartyvalues['invalid'] = true;
        }
        else
        {
            $count = 0;
            if( count($search_tlds) )
            {
                foreach( $search_tlds as $tld )
                {
                    $result = lookupDomain($sld, $tld);
                    $_SESSION['domaincheckerwhois'][] = $sld . $tld;
                    if( !$count )
                    {
                        if( $result['result'] == 'available' )
                        {
                            $smartyvalues['available'] = true;
                        }
                        else
                        {
                            if( $result['result'] == 'error' )
                            {
                                $smartyvalues['error'] = true;
                            }
                        }
                    }
                    $tlddata = getTLDPriceList($tld, true);
                    $availabilityresults[] = array( 'domain' => $sld . $tld, 'status' => $result['result'], 'regoptions' => $tlddata, 'errordetail' => $result['errordetail'] );
                    $count++;
                }
            }
        }
    }
}
$smartyvalues['availabilityresults'] = $availabilityresults;
$tldpricelist = array(  );
if( $tldslist )
{
    foreach( $tldslist as $sel_tld )
    {
        $tldpricing = getTLDPriceList($sel_tld, true);
        $firstoption = current($tldpricing);
        $year = key($tldpricing);
        $tldpricelist[] = array( 'tld' => $sel_tld, 'period' => $year, 'register' => $firstoption['register'], 'transfer' => $firstoption['transfer'], 'renew' => $firstoption['renew'] );
    }
}
$smartyvalues['tldpricelist'] = $tldpricelist;
$smartyvalues['capatacha'] = $capatacha;
$smartyvalues['recapatchahtml'] = clientAreaReCaptchaHTML();
outputClientArea($templatefile);