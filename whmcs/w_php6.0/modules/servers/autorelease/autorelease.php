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
function autorelease_ConfigOptions()
{
    $depts = array(  );
    $depts[] = "0|None";
    $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $depts[] = $id . "|" . $name;
    }
    $configarray = array( "Create Action" => array( 'Type' => 'dropdown', 'Options' => "None,Add To-Do List Item,Create Support Ticket" ), "Suspend Action" => array( 'Type' => 'dropdown', 'Options' => "None,Add To-Do List Item,Create Support Ticket" ), "Unsuspend Action" => array( 'Type' => 'dropdown', 'Options' => "None,Add To-Do List Item,Create Support Ticket" ), "Terminate Action" => array( 'Type' => 'dropdown', 'Options' => "None,Add To-Do List Item,Create Support Ticket" ), "Renew Action" => array( 'Type' => 'dropdown', 'Options' => "None,Add To-Do List Item,Create Support Ticket" ), "Support Dept ID" => array( 'Type' => 'dropdown', 'Options' => implode(',', $depts) ) );
    return $configarray;
}
function autorelease_CreateAccount($params)
{
    if( $params['configoption1'] == "Add To-Do List Item" )
    {
        $todoarray['title'] = "Service Provisioned";
        $todoarray['description'] = "Service ID # " . $params['serviceid'] . " was just auto provisioned";
        $todoarray['status'] = 'Pending';
        $todoarray['duedate'] = date('Y-m-d');
        $todoarray['date'] = $todoarray['duedate'];
        insert_query('tbltodolist', $todoarray);
    }
    else
    {
        if( $params['configoption1'] == "Create Support Ticket" )
        {
            $postfields['action'] = 'openticket';
            $postfields['clientid'] = $params['clientsdetails']['userid'];
            $postfields['deptid'] = $params['configoption6'] ? $params['configoption6'] : '1';
            $postfields['subject'] = "Service Provisioned";
            $postfields['message'] = "Service ID # " . $params['serviceid'] . " was just auto provisioned";
            $postfields['priority'] = 'Low';
            localAPI($postfields['action'], $postfields, 1);
        }
    }
    updateService(array( 'username' => '', 'password' => '' ));
    return 'success';
}
function autorelease_SuspendAccount($params)
{
    if( $params['configoption2'] == "Add To-Do List Item" )
    {
        $todoarray['title'] = "Service Suspension";
        $todoarray['description'] = "Service ID # " . $params['serviceid'] . " requires suspension";
        $todoarray['status'] = 'Pending';
        $todoarray['duedate'] = date('Y-m-d');
        $todoarray['date'] = $todoarray['duedate'];
        insert_query('tbltodolist', $todoarray);
    }
    else
    {
        if( $params['configoption2'] == "Create Support Ticket" )
        {
            $postfields['action'] = 'openticket';
            $postfields['clientid'] = $params['clientsdetails']['userid'];
            $postfields['deptid'] = $params['configoption6'] ? $params['configoption6'] : '1';
            $postfields['subject'] = "Service Suspension";
            $postfields['message'] = "Service ID # " . $params['serviceid'] . " requires suspension";
            $postfields['priority'] = 'Low';
            localAPI($postfields['action'], $postfields, 1);
        }
    }
    return 'success';
}
function autorelease_UnsuspendAccount($params)
{
    if( $params['configoption3'] == "Add To-Do List Item" )
    {
        $todoarray['title'] = "Service Reactivation";
        $todoarray['description'] = "Service ID # " . $params['serviceid'] . " requires unsuspending";
        $todoarray['status'] = 'Pending';
        $todoarray['duedate'] = date('Y-m-d');
        $todoarray['date'] = $todoarray['duedate'];
        insert_query('tbltodolist', $todoarray);
    }
    else
    {
        if( $params['configoption3'] == "Create Support Ticket" )
        {
            $postfields['action'] = 'openticket';
            $postfields['clientid'] = $params['clientsdetails']['userid'];
            $postfields['deptid'] = $params['configoption6'] ? $params['configoption6'] : '1';
            $postfields['subject'] = "Service Reactivation";
            $postfields['message'] = "Service ID # " . $params['serviceid'] . " requires unsuspending";
            $postfields['priority'] = 'Low';
            localAPI($postfields['action'], $postfields, 1);
        }
    }
    return 'success';
}
function autorelease_TerminateAccount($params)
{
    if( $params['configoption4'] == "Add To-Do List Item" )
    {
        $todoarray['title'] = "Service Termination";
        $todoarray['description'] = "Service ID # " . $params['serviceid'] . " requires termination";
        $todoarray['status'] = 'Pending';
        $todoarray['duedate'] = date('Y-m-d');
        $todoarray['date'] = $todoarray['duedate'];
        insert_query('tbltodolist', $todoarray);
    }
    else
    {
        if( $params['configoption4'] == "Create Support Ticket" )
        {
            $postfields['action'] = 'openticket';
            $postfields['clientid'] = $params['clientsdetails']['userid'];
            $postfields['deptid'] = $params['configoption6'] ? $params['configoption6'] : '1';
            $postfields['subject'] = "Service Termination";
            $postfields['message'] = "Service ID # " . $params['serviceid'] . " requires termination";
            $postfields['priority'] = 'Low';
            localAPI($postfields['action'], $postfields, 1);
        }
    }
    return 'success';
}
function autorelease_Renew($params)
{
    if( $params['configoption5'] == "Add To-Do List Item" )
    {
        $todoarray['title'] = "Service Renewal";
        $todoarray['description'] = "Service ID # " . $params['serviceid'] . " was just renewed";
        $todoarray['status'] = 'Pending';
        $todoarray['duedate'] = date('Y-m-d');
        $todoarray['date'] = $todoarray['duedate'];
        insert_query('tbltodolist', $todoarray);
    }
    else
    {
        if( $params['configoption5'] == "Create Support Ticket" )
        {
            $postfields['action'] = 'openticket';
            $postfields['clientid'] = $params['clientsdetails']['userid'];
            $postfields['deptid'] = $params['configoption6'] ? $params['configoption6'] : '1';
            $postfields['subject'] = "Service Renewal";
            $postfields['message'] = "Service ID # " . $params['serviceid'] . " was just renewed";
            $postfields['priority'] = 'Low';
            localAPI($postfields['action'], $postfields, 1);
        }
    }
    return 'success';
}