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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
$domains = new WHMCS_Domains();
$domainparts = $domains->splitAndCleanDomainInput($domain);
$isValid = $domains->checkDomainisValid($domainparts);
if( $isValid )
{
    $whois = new WHMCS_WHOIS();
    $whois->init();
    if( $whois->canLookupTLD($domainparts['tld']) )
    {
        $result = $whois->lookup($domainparts);
        $whois = $responsetype == 'xml' || $responsetype == 'json' ? $result['whois'] : urlencode($result['whois']);
        $result['whois'] = $whois;
        $apiresults = array( 'result' => 'success', 'status' => $result['result'], 'whois' => $result['whois'] );
    }
    else
    {
        $apiresults = array( 'result' => 'error', 'message' => "The given TLD is not supported for WHOIS lookups" );
        return false;
    }
}
else
{
    $apiresults = array( 'result' => 'error', 'message' => "Domain not valid" );
    return false;
}