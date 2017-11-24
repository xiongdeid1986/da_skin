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
$activestatuses = $awaitingreplystatuses = array(  );
$result = select_query('tblticketstatuses', 'title,showactive,showawaiting', '');
while( $data = mysql_fetch_array($result) )
{
    if( $data['showactive'] )
    {
        $activestatuses[] = $data[0];
    }
    if( $data['showawaiting'] )
    {
        $awaitingreplystatuses[] = $data[0];
    }
}
$deptfilter = '';
if( !$ignore_dept_assignments )
{
    $result = select_query('tbladmins', 'supportdepts', array( 'id' => $_SESSION['adminid'] ));
    $data = mysql_fetch_array($result);
    $supportdepts = $data[0];
    $supportdepts = explode(',', $supportdepts);
    $deptids = array(  );
    foreach( $supportdepts as $id )
    {
        if( trim($id) )
        {
            $deptids[] = trim($id);
        }
    }
    if( count($deptids) )
    {
        $deptfilter = "WHERE tblticketdepartments.id IN (" . db_build_in_array($deptids) . ") ";
    }
}
$result = full_query("SELECT id,name,(SELECT COUNT(id) FROM tbltickets WHERE did=tblticketdepartments.id AND status IN (" . db_build_in_array($awaitingreplystatuses) . ")) AS awaitingreply,(SELECT COUNT(id) FROM tbltickets WHERE did=tblticketdepartments.id AND status IN (" . db_build_in_array($activestatuses) . ")) AS opentickets FROM tblticketdepartments " . $deptfilter . "ORDER BY name ASC");
$apiresults = array( 'result' => 'success', 'totalresults' => mysql_num_rows($result) );
while( $data = mysql_fetch_array($result) )
{
    $apiresults['departments']['department'][] = array( 'id' => $data['id'], 'name' => $data['name'], 'awaitingreply' => $data['awaitingreply'], 'opentickets' => $data['opentickets'] );
}
$responsetype = 'xml';