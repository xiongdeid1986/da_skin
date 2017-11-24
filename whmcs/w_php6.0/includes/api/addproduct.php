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
if( !$name )
{
    $apiresults = array( 'result' => 'error', 'message' => "You must supply a name for the product" );
    return false;
}
if( !$type )
{
    $type = 'other';
}
if( $stockcontrol || $qty )
{
    $stockcontrol = 'on';
}
else
{
    $stockcontrol = '';
}
if( !$paytype )
{
    $paytype = 'free';
}
if( $hidden )
{
    $hidden = 'on';
}
if( $showdomainoptions )
{
    $showdomainoptions = 'on';
}
$tax = $tax ? '1' : '0';
$pid = insert_query('tblproducts', array( 'type' => $type, 'gid' => $gid, 'name' => $name, 'description' => $description, 'hidden' => $hidden, 'showdomainoptions' => $showdomainoptions, 'welcomeemail' => $welcomeemail, 'stockcontrol' => $stockcontrol, 'qty' => $qty, 'proratabilling' => $proratabilling, 'proratadate' => $proratadate, 'proratachargenextmonth' => $proratachargenextmonth, 'paytype' => $paytype, 'subdomain' => $subdomain, 'autosetup' => $autosetup, 'servertype' => $module, 'servergroup' => $servergroupid, 'configoption1' => $configoption1, 'configoption2' => $configoption2, 'configoption3' => $configoption3, 'configoption4' => $configoption4, 'configoption5' => $configoption5, 'configoption6' => $configoption6, 'tax' => $tax, 'order' => $order ));
foreach( $pricing as $currency => $values )
{
    insert_query('tblpricing', array( 'type' => 'product', 'currency' => $currency, 'relid' => $pid, 'msetupfee' => $values['msetupfee'], 'qsetupfee' => $values['qsetupfee'], 'ssetupfee' => $values['ssetupfee'], 'asetupfee' => $values['asetupfee'], 'bsetupfee' => $values['bsetupfee'], 'tsetupfee' => $values['tsetupfee'], 'monthly' => $values['monthly'], 'quarterly' => $values['quarterly'], 'semiannually' => $values['semiannually'], 'annually' => $values['annually'], 'biennially' => $values['biennially'], 'triennially' => $values['triennially'] ));
}
$apiresults = array( 'result' => 'success', 'pid' => $pid );