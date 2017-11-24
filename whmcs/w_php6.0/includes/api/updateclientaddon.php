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
$result = select_query('tblhostingaddons', 'id,addonid,hostingid,status', array( 'id' => $id ));
$data = mysql_fetch_array($result);
if( !$data['id'] )
{
    $apiresults = array( 'result' => 'error', 'message' => "Addon ID Not Found" );
}
else
{
    $serviceid = $data['hostingid'];
    $currentstatus = $data['status'];
    $userid = get_query_val('tblhosting', 'userid', array( 'id' => $serviceid ));
    $updateqry = array(  );
    if( $addonid )
    {
        $updateqry['addonid'] = $addonid;
    }
    else
    {
        $addonid = $data['addonid'];
    }
    if( $name )
    {
        $updateqry['name'] = $name;
    }
    if( $setupfee )
    {
        $updateqry['setupfee'] = $setupfee;
    }
    if( $recurring )
    {
        $updateqry['recurring'] = $recurring;
    }
    if( $billingcycle )
    {
        $updateqry['billingcycle'] = $billingcycle;
    }
    if( $nextduedate )
    {
        $updateqry['nextduedate'] = $nextduedate;
    }
    if( $nextinvoicedate )
    {
        $updateqry['nextinvoicedate'] = $nextinvoicedate;
    }
    if( $notes )
    {
        $updateqry['notes'] = $notes;
    }
    if( $status && $status != $currentstatus )
    {
        $updateqry['status'] = $status;
    }
    if( 0 < count($updateqry) )
    {
        update_query('tblhostingaddons', $updateqry, array( 'id' => $id ));
        logActivity("Modified Addon - Addon ID: " . $id . " - Service ID: " . $serviceid);
        if( $currentstatus != 'Active' && $status == 'Active' )
        {
            run_hook('AddonActivated', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
        }
        else
        {
            if( $currentstatus != 'Suspended' && $status == 'Suspended' )
            {
                run_hook('AddonSuspended', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
            }
            else
            {
                if( $currentstatus != 'Terminated' && $status == 'Terminated' )
                {
                    run_hook('AddonTerminated', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
                }
                else
                {
                    if( $currentstatus != 'Cancelled' && $status == 'Cancelled' )
                    {
                        run_hook('AddonCancelled', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
                    }
                    else
                    {
                        if( $currentstatus != 'Fraud' && $status == 'Fraud' )
                        {
                            run_hook('AddonFraud', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
                        }
                        else
                        {
                            run_hook('AddonEdit', array( 'id' => $id, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
                        }
                    }
                }
            }
        }
        $apiresults = array( 'result' => 'success', 'id' => $id );
    }
    else
    {
        $apiresults = array( 'result' => 'error', 'id' => $id, 'message' => "Nothing to Update" );
    }
}