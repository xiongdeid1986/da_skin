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
$result = select_query('tblclients', '', array( 'id' => $clientid ));
$data = mysql_fetch_array($result);
$clientid = $data['id'];
if( !$clientid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Client ID not Found" );
}
else
{
    if( !$description )
    {
        $apiresults = array( 'result' => 'error', 'message' => "You must provide a description" );
    }
    else
    {
        $allowedtypes = array( 'noinvoice', 'nextcron', 'nextinvoice', 'duedate', 'recur' );
        if( $invoiceaction && !in_array($invoiceaction, $allowedtypes) )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid Invoice Action" );
        }
        else
        {
            if( $invoiceaction == 'recur' && (!$recur && !$recurcycle || !$recurfor) )
            {
                $apiresults = array( 'result' => 'error', 'message' => "Recurring must have a unit, cycle and limit" );
            }
            else
            {
                if( $invoiceaction == 'duedate' && !$duedate )
                {
                    $apiresults = array( 'result' => 'error', 'message' => "Due date is required" );
                }
                else
                {
                    if( $invoiceaction == 'noinvoice' )
                    {
                        $invoiceaction = '0';
                    }
                    else
                    {
                        if( $invoiceaction == 'nextcron' )
                        {
                            $invoiceaction = '1';
                            if( !$duedate )
                            {
                                $duedate = date('Y-m-d');
                            }
                        }
                        else
                        {
                            if( $invoiceaction == 'nextinvoice' )
                            {
                                $invoiceaction = '2';
                            }
                            else
                            {
                                if( $invoiceaction == 'duedate' )
                                {
                                    $invoiceaction = '3';
                                }
                                else
                                {
                                    if( $invoiceaction == 'recur' )
                                    {
                                        $invoiceaction = '4';
                                    }
                                }
                            }
                        }
                    }
                    $id = insert_query('tblbillableitems', array( 'userid' => $clientid, 'description' => $description, 'hours' => $hours, 'amount' => $amount, 'recur' => $recur, 'recurcycle' => $recurcycle, 'recurfor' => $recurfor, 'invoiceaction' => $invoiceaction, 'duedate' => $duedate ));
                    $apiresults = array( 'result' => 'success', 'billableid' => $id );
                }
            }
        }
    }
}