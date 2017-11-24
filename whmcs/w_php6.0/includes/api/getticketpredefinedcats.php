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
$result = select_query('tblticketpredefinedcats', "COUNT(id)");
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$apiresults = array( 'result' => 'success', 'totalresults' => $totalresults );
$result = full_query("SELECT c.*, COUNT(r.id) AS replycount FROM tblticketpredefinedcats c LEFT JOIN tblticketpredefinedreplies r ON c.id=r.catid GROUP BY c.id ORDER BY c.name ASC");
while( $data = mysql_fetch_assoc($result) )
{
    $apiresults['predefinedreplies']['predefinedreply'][] = array( 'id' => $data['id'], 'parentid' => $data['parentid'], 'name' => $data['name'], 'replycount' => $data['replycount'] );
    $apiresults['categories']['category'][] = $data;
}
$responsetype = 'xml';