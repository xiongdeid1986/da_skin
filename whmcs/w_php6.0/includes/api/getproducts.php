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
if( !function_exists('getCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( !function_exists('getCartConfigOptions') )
{
    require(ROOTDIR . "/includes/configoptionsfunctions.php");
}
global $currency;
$currency = getCurrency();
$where = array(  );
if( $pid )
{
    if( is_numeric($pid) )
    {
        $where[] = "id=" . (int) $pid;
    }
    else
    {
        $where[] = "id IN (" . db_escape_string($pid) . ")";
    }
}
if( $gid )
{
    $where[] = "gid=" . (int) $gid;
}
if( $module )
{
    $where[] = "servertype='" . db_escape_string($module) . "'";
}
$result = select_query('tblproducts', '', implode(" AND ", $where));
$apiresults = array( 'result' => 'success', 'totalresults' => mysql_num_rows($result) );
while( $data = mysql_fetch_array($result) )
{
    $pid = $data['id'];
    $productarray = array( 'pid' => $data['id'], 'gid' => $data['gid'], 'type' => $data['type'], 'name' => $data['name'], 'description' => $data['description'], 'module' => $data['servertype'], 'paytype' => $data['paytype'] );
    if( $data['stockcontrol'] )
    {
        $productarray['stockcontrol'] = 'true';
        $productarray['stocklevel'] = $data['qty'];
    }
    $result2 = select_query('tblpricing', "tblcurrencies.code,tblcurrencies.prefix,tblcurrencies.suffix,tblpricing.msetupfee,tblpricing.qsetupfee,tblpricing.ssetupfee,tblpricing.asetupfee,tblpricing.bsetupfee,tblpricing.tsetupfee,tblpricing.monthly,tblpricing.quarterly,tblpricing.semiannually,tblpricing.annually,tblpricing.biennially,tblpricing.triennially", array( 'type' => 'product', 'relid' => $pid ), 'code', 'ASC', '', "tblcurrencies ON tblcurrencies.id=tblpricing.currency");
    while( $data = mysql_fetch_assoc($result2) )
    {
        $code = $data['code'];
        unset($data['code']);
        $productarray['pricing'][$code] = $data;
    }
    $customfieldsdata = array(  );
    $customfields = getCustomFields('product', $pid, '', '', 'on');
    foreach( $customfields as $field )
    {
        $customfieldsdata[] = array( 'id' => $field['id'], 'name' => $field['name'], 'description' => $field['description'], 'required' => $field['required'] );
    }
    $productarray['customfields']['customfield'] = $customfieldsdata;
    $configoptiondata = array(  );
    $configurableoptions = getCartConfigOptions($pid, '', '', '', true);
    foreach( $configurableoptions as $option )
    {
        $options = array(  );
        foreach( $option['options'] as $op )
        {
            $pricing = array(  );
            $result4 = select_query('tblpricing', 'code,msetupfee,qsetupfee,ssetupfee,asetupfee,bsetupfee,tsetupfee,monthly,quarterly,semiannually,annually,biennially,triennially', array( 'type' => 'configoptions', 'relid' => $op['id'] ), '', '', '', "tblcurrencies ON tblcurrencies.id=tblpricing.currency");
            while( $oppricing = mysql_fetch_assoc($result4) )
            {
                $currcode = $oppricing['code'];
                unset($oppricing['code']);
                $pricing[$currcode] = $oppricing;
            }
            $options['option'][] = array( 'id' => $op['id'], 'name' => $op['name'], 'recurring' => $op['recurring'], 'pricing' => $pricing );
        }
        $configoptiondata[] = array( 'id' => $option['id'], 'name' => $option['optionname'], 'type' => $option['optiontype'], 'options' => $options );
    }
    $productarray['configoptions']['configoption'] = $configoptiondata;
    $apiresults['products']['product'][] = $productarray;
}
$responsetype = 'xml';