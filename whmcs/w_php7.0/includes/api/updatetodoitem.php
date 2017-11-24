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
$id = get_query_val('tbltodolist', 'id', array( 'id' => $itemid ));
if( !$itemid )
{
    $apiresults = array( 'result' => 'error', 'message' => "TODO Item ID Not Found" );
}
else
{
    $adminid = get_query_val('tbladmins', 'id', array( 'id' => $adminid ));
    if( !$adminid )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Admin ID Not Found" );
    }
    else
    {
        $todoarray = array(  );
        if( $date )
        {
            $todoarray['date'] = toMySQLDate($date);
        }
        if( $title )
        {
            $todoarray['title'] = $title;
        }
        if( $description )
        {
            $todoarray['description'] = $description;
        }
        if( $adminid )
        {
            $todoarray['admin'] = $adminid;
        }
        if( $status )
        {
            $todoarray['status'] = $status;
        }
        if( $duedate )
        {
            $todoarray['duedate'] = toMySQLDate($duedate);
        }
        update_query('tbltodolist', $todoarray, array( 'id' => $itemid ));
        $apiresults = array( 'result' => 'success', 'itemid' => $itemid );
    }
}