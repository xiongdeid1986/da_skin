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
if( !function_exists('createInvoices') )
{
    require(ROOTDIR . "/includes/processinvoices.php");
}
if( !function_exists('getClientsDetails') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('updateInvoiceTotal') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
if( !function_exists('getGatewaysArray') )
{
    require(ROOTDIR . "/includes/gatewayfunctions.php");
}
if( !function_exists('getRegistrarConfigOptions') )
{
    require(ROOTDIR . "/includes/registrarfunctions.php");
}
if( !function_exists('ModuleBuildParams') )
{
    require(ROOTDIR . "/includes/modulefunctions.php");
}
if( $clientid )
{
    $clientid = get_query_val('tblclients', 'id', array( 'id' => $clientid ));
    if( !$clientid )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
        return NULL;
    }
}
$invoicecount = 0;
if( is_array($serviceids) || is_array($addonids) || is_array($domainids) )
{
    $specificitems = array( 'products' => $serviceids, 'addons' => $addonids, 'domains' => $domainids );
    $invoiceid = createInvoices($clientid, $noemails, '', $specificitems);
}
else
{
    $invoiceid = createInvoices($clientid, $noemails);
}
$apiresults = array( 'result' => 'success', 'numcreated' => $invoicecount );
if( $clientid )
{
    $apiresults['latestinvoiceid'] = $invoiceid;
}