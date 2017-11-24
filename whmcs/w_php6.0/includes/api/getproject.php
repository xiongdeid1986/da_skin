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
if( !isset($_REQUEST['projectid']) )
{
    $apiresults = array( 'result' => 'error', 'message' => "Project ID Not Set" );
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
        $apiresults['projectinfo'] = $data;
        $result_task = select_query('mod_projecttasks', '', array( 'projectid' => (int) $projectid ));
        while( $data_tasks = mysql_fetch_assoc($result_task) )
        {
            $data_tasks['timelogs'] = array(  );
            $result_time = select_query('mod_projecttimes', '', array( 'taskid' => (int) $data_tasks['id'] ));
            while( $DATA = mysql_fetch_assoc($result_time) )
            {
                $DATA['starttime'] = date("Y-m-d H:i:s", $DATA['start']);
                $DATA['endtime'] = date("Y-m-d H:i:s", $DATA['end']);
                $data_tasks['timelogs']['timelog'][] = $DATA;
            }
            $apiresults['tasks']['task'][] = $data_tasks;
        }
        $apiresults['messages'] = array(  );
        $result_message = select_query('mod_projectmessages', '', array( 'projectid' => (int) $projectid ));
        while( $DATA_message = mysql_fetch_assoc($result_message) )
        {
            $apiresults['messages']['message'][] = $DATA_message;
        }
        $responsetype = 'xml';
    }
}