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
function getTLDList($type = 'register')
{
    global $currency;
    $currency_id = $currency['id'];
    $clientgroupid = isset($_SESSION['uid']) ? get_query_val('tblclients', 'groupid', array( 'id' => $_SESSION['uid'] )) : '0';
    if( !$clientgroupid )
    {
        $clientgroupid = 0;
    }
    $checkfields = array( 'msetupfee', 'qsetupfee', 'ssetupfee', 'asetupfee', 'bsetupfee', 'tsetupfee', 'monthly', 'quarterly', 'semiannually', 'annually', 'biennially', 'triennially' );
    $extensions = array(  );
    $result = select_query('tbldomainpricing', 'id,extension', '', 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $extension = $data['extension'];
        $wherequery = '';
        $pricinggroup = $clientgroupid;
        $data = get_query_vals('tblpricing', '', array( 'type' => 'domainregister', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => $clientgroupid ));
        if( !$data )
        {
            $pricinggroup = '0';
            $data = get_query_vals('tblpricing', '', array( 'type' => 'domainregister', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => '0' ));
        }
        $i = 0;
        if( is_array($data) )
        {
            foreach( $data as $k => $v )
            {
                if( is_integer($k) && 3 < $k )
                {
                    if( 0 < $v && $checkfields[$i] )
                    {
                        $wherequery .= $checkfields[$i] . ">=0 OR ";
                    }
                    $i++;
                }
            }
        }
        if( $wherequery )
        {
            $result2 = select_query('tblpricing', "COUNT(*)", "type='domain" . $type . "' AND currency='" . $currency_id . "' AND relid='" . $id . "' AND tsetupfee=" . $pricinggroup . " AND (" . substr($wherequery, 0, 0 - 4) . ")");
            $data = mysql_fetch_array($result2);
            if( $data[0] )
            {
                $extensions[] = $extension;
            }
        }
    }
    return $extensions;
}
function getTLDPriceList($tld, $display = '', $renewpricing = '', $userid = '')
{
    global $currency;
    if( $renewpricing == 'renew' )
    {
        $renewpricing = true;
    }
    $currency_id = $currency['id'];
    $result = select_query('tbldomainpricing', 'id', array( 'extension' => $tld ));
    $data = mysql_fetch_array($result);
    $id = $data['id'];
    if( !$userid && isset($_SESSION['uid']) )
    {
        $userid = $_SESSION['uid'];
    }
    $clientgroupid = $userid ? get_query_val('tblclients', 'groupid', array( 'id' => $userid )) : '0';
    $checkfields = array( 'msetupfee', 'qsetupfee', 'ssetupfee', 'asetupfee', 'bsetupfee', 'monthly', 'quarterly', 'semiannually', 'annually', 'biennially' );
    if( !$renewpricing || $renewpricing === 'transfer' )
    {
        $data = get_query_vals('tblpricing', '', array( 'type' => 'domainregister', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => $clientgroupid ));
        if( !$data )
        {
            $data = get_query_vals('tblpricing', '', array( 'type' => 'domainregister', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => '0' ));
        }
        foreach( $checkfields as $k => $v )
        {
            $register[$k + 1] = $data[$v];
        }
        $data = get_query_vals('tblpricing', '', array( 'type' => 'domaintransfer', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => $clientgroupid ));
        if( !$data )
        {
            $data = get_query_vals('tblpricing', '', array( 'type' => 'domaintransfer', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => '0' ));
        }
        foreach( $checkfields as $k => $v )
        {
            $transfer[$k + 1] = $data[$v];
        }
    }
    if( !$renewpricing || $renewpricing !== 'transfer' )
    {
        $data = get_query_vals('tblpricing', '', array( 'type' => 'domainrenew', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => $clientgroupid ));
        if( !$data )
        {
            $data = get_query_vals('tblpricing', '', array( 'type' => 'domainrenew', 'currency' => $currency_id, 'relid' => $id, 'tsetupfee' => '0' ));
        }
        foreach( $checkfields as $k => $v )
        {
            $renew[$k + 1] = $data[$v];
        }
    }
    $tldpricing = array(  );
    $years = 1;
    while( $years <= 10 )
    {
        if( $renewpricing === 'transfer' )
        {
            if( 0 < $register[$years] && 0 <= $transfer[$years] )
            {
                if( $display )
                {
                    $transfer[$years] = formatCurrency($transfer[$years]);
                }
                $tldpricing[$years]['transfer'] = $transfer[$years];
            }
        }
        else
        {
            if( $renewpricing )
            {
                if( 0 < $renew[$years] )
                {
                    if( $display )
                    {
                        $renew[$years] = formatCurrency($renew[$years]);
                    }
                    $tldpricing[$years]['renew'] = $renew[$years];
                }
            }
            else
            {
                if( 0 < $register[$years] )
                {
                    if( $display )
                    {
                        $register[$years] = formatCurrency($register[$years]);
                    }
                    $tldpricing[$years]['register'] = $register[$years];
                    if( 0 <= $transfer[$years] )
                    {
                        if( $display )
                        {
                            $transfer[$years] = formatCurrency($transfer[$years]);
                        }
                        $tldpricing[$years]['transfer'] = $transfer[$years];
                    }
                    if( 0 < $renew[$years] )
                    {
                        if( $display )
                        {
                            $renew[$years] = formatCurrency($renew[$years]);
                        }
                        $tldpricing[$years]['renew'] = $renew[$years];
                    }
                }
            }
        }
        $years += 1;
    }
    return $tldpricing;
}
function cleanDomainInput($val)
{
    global $CONFIG;
    $val = trim($val);
    if( !$CONFIG['AllowIDNDomains'] )
    {
        $val = strtolower($val);
    }
    return $val;
}
function checkDomainisValid($sld, $tld)
{
    global $CONFIG;
    if( $sld[0] == '-' || $sld[strlen($sld) - 1] == '-' )
    {
        return 0;
    }
    $isIdn = $isIdnTld = $skipAllowIDNDomains = false;
    if( $CONFIG['AllowIDNDomains'] )
    {
        WHMCS_Application::getinstance()->load_function('whois');
        $idnConvert = new WHMCS_Domains_Idna();
        $idnConvert->encode($sld);
        if( $idnConvert->get_last_error() && $idnConvert->get_last_error() != "The given string does not contain encodable chars" )
        {
            return 0;
        }
        if( $idnConvert->get_last_error() && $idnConvert->get_last_error() == "The given string does not contain encodable chars" )
        {
            $skipAllowIDNDomains = true;
        }
        else
        {
            $isIdn = true;
        }
    }
    if( $isIdn === false )
    {
        if( preg_replace("/[^.%\$^'#~@&*(),_£?!+=:{}[]()|\\/ \\\\ ]/", '', $sld) )
        {
            return 0;
        }
        if( (!$CONFIG['AllowIDNDomains'] || $skipAllowIDNDomains === true) && preg_replace("/[^a-z0-9-.]/i", '', $sld . $tld) != $sld . $tld )
        {
            return 0;
        }
        if( preg_replace("/[^a-z0-9-.]/", '', $tld) != $tld )
        {
            return 0;
        }
        $validMask = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
        if( strspn($sld, $validMask) != strlen($sld) )
        {
            return 0;
        }
    }
    run_hook('DomainValidation', array( 'sld' => $sld, 'tld' => $tld ));
    if( $sld === false && $sld !== 0 || !$tld )
    {
        return 0;
    }
    $coreTLDs = array( ".com", ".net", ".org", ".info", 'biz', ".mobi", ".name", ".asia", ".tel", ".in", ".mn", ".bz", ".cc", ".tv", ".us", ".me", ".co.uk", ".me.uk", ".org.uk", ".net.uk", ".ch", ".li", ".de", ".jp" );
    $DomainMinLengthRestrictions = $DomainMaxLengthRestrictions = array(  );
    require(ROOTDIR . "/configuration.php");
    foreach( $coreTLDs as $cTLD )
    {
        if( !array_key_exists($cTLD, $DomainMinLengthRestrictions) )
        {
            $DomainMinLengthRestrictions[$cTLD] = 3;
        }
        if( !array_key_exists($cTLD, $DomainMaxLengthRestrictions) )
        {
            $DomainMaxLengthRestrictions[$cTLD] = 63;
        }
    }
    if( array_key_exists($tld, $DomainMinLengthRestrictions) && strlen($sld) < $DomainMinLengthRestrictions[$tld] )
    {
        return 0;
    }
    if( array_key_exists($tld, $DomainMaxLengthRestrictions) && $DomainMaxLengthRestrictions[$tld] < strlen($sld) )
    {
        return 0;
    }
    return 1;
}
function disableAutoRenew($domainid)
{
    $data = get_query_vals('tbldomains', 'id,domain,nextduedate', array( 'id' => $domainid ));
    $domainid = $data['id'];
    $domainname = $data['domain'];
    $nextduedate = $data['nextduedate'];
    if( !$domainid )
    {
        return false;
    }
    update_query('tbldomains', array( 'nextinvoicedate' => $nextduedate, 'donotrenew' => 'on' ), array( 'id' => $domainid ));
    if( $_SESSION['adminid'] )
    {
        logActivity("Admin Disabled Domain Auto Renew - Domain ID: " . $domainid . " - Domain: " . $domainname);
    }
    else
    {
        logActivity("Client Disabled Domain Auto Renew - Domain ID: " . $domainid . " - Domain: " . $domainname);
    }
    $result = select_query('tblinvoiceitems', "tblinvoiceitems.id,tblinvoiceitems.invoiceid", array( 'type' => 'Domain', 'relid' => $domainid, 'status' => 'Unpaid', "tblinvoices.userid" => $_SESSION['uid'] ), '', '', '', "tblinvoices ON tblinvoices.id=tblinvoiceitems.invoiceid");
    while( $data = mysql_fetch_array($result) )
    {
        $itemid = $data['id'];
        $invoiceid = $data['invoiceid'];
        $result2 = select_query('tblinvoiceitems', "COUNT(*)", array( 'invoiceid' => $invoiceid ));
        $data = mysql_fetch_array($result2);
        $itemcount = $data[0];
        $otheritemcount = 0;
        if( 1 < $itemcount )
        {
            $otheritemcount = get_query_val('tblinvoiceitems', "COUNT(*)", "invoiceid=" . (int) $invoiceid . " AND id!=" . (int) $itemid . " AND type NOT IN ('PromoHosting','PromoDomain','GroupDiscount')");
        }
        if( $itemcount == 1 || $otheritemcount == 0 )
        {
            update_query('tblinvoiceitems', array( 'type' => '', 'relid' => '0' ), array( 'id' => $itemid ));
            update_query('tblinvoices', array( 'status' => 'Cancelled' ), array( 'id' => $invoiceid ));
            logActivity("Cancelled Previous Domain Renewal Invoice - Invoice ID: " . $invoiceid . " - Domain: " . $domainname);
            run_hook('InvoiceCancelled', array( 'invoiceid' => $invoiceid ));
        }
        else
        {
            delete_query('tblinvoiceitems', array( 'id' => $itemid ));
            updateInvoiceTotal($invoiceid);
            logActivity("Removed Previous Domain Renewal Line Item - Invoice ID: " . $invoiceid . " - Domain: " . $domainname);
        }
    }
}