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
function affiliateActivate($userid)
{
    global $CONFIG;
    $result = select_query('tblclients', 'currency', array( 'id' => $userid ));
    $data = mysql_fetch_array($result);
    $clientcurrency = $data['currency'];
    $bonusdeposit = convertCurrency($CONFIG['AffiliateBonusDeposit'], 1, $clientcurrency);
    $result = select_query('tblaffiliates', 'id', array( 'clientid' => $userid ));
    $data = mysql_fetch_array($result);
    $affiliateid = $data['id'];
    if( !$affiliateid )
    {
        $affiliateid = insert_query('tblaffiliates', array( 'date' => "now()", 'clientid' => $userid, 'balance' => $bonusdeposit ));
    }
    logActivity("Activated Affiliate Account - Affiliate ID: " . $affiliateid . " - User ID: " . $userid, $userid);
    run_hook('AffiliateActivation', array( 'affid' => $affiliateid, 'userid' => $userid ));
}