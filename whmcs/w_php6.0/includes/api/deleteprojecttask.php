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
$projectid = (int) $_REQUEST['projectid'];
$taskid = (int) $_REQUEST['taskid'];
if( !$projectid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Project ID is Required" );
}
else
{
    if( !$taskid )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Task ID is Required" );
    }
    else
    {
        $result = select_query('mod_project', '', array( 'id' => (int) $projectid ));
        $data = mysql_fetch_assoc($result);
        $projectid = $data['id'];
        if( !$projectid )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Project ID Not Found" );
        }
        else
        {
            $result_taskid = select_query('mod_projecttasks', 'id', array( 'id' => $_REQUEST['taskid'] ));
            $data_taskid = mysql_fetch_array($result_taskid);
            if( !$data_taskid['id'] )
            {
                $apiresults = array( 'result' => 'error', 'message' => "Task ID Not Found" );
            }
            else
            {
                delete_query('mod_projecttasks', array( 'id' => $taskid, 'projectid' => $projectid ));
                $apiresults = array( 'result' => 'success', 'message' => "Task has been deleted" );
            }
        }
    }
}