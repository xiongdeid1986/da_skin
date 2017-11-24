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
function getCartConfigOptions($pid, $values, $cycle, $accountid = '', $orderform = '')
{
    global $CONFIG;
    global $_LANG;
    global $currency;
    $whmcs = WHMCS_Application::getinstance();
    if( count($currency) < 1 )
    {
        $currency = getCurrency('', $whmcs->getCurrencyID());
    }
    if( $cycle == 'onetime' )
    {
        $cycle = 'monthly';
    }
    $configoptions = array(  );
    $cycle = strtolower(str_replace('-', '', $cycle));
    if( $cycle == "one time" )
    {
        $cycle = 'monthly';
    }
    $showhidden = $_SESSION['adminid'] && defined('ADMINAREA') ? true : false;
    if( !function_exists('getBillingCycleMonths') )
    {
        require(ROOTDIR . "/includes/invoicefunctions.php");
    }
    $cyclemonths = getBillingCycleMonths($cycle);
    if( $accountid )
    {
        $values = $options = array(  );
        $accountid = (int) $accountid;
        $query = "SELECT tblproductconfigoptionssub.id, tblproductconfigoptionssub.configid\nFROM tblproductconfigoptionssub\nINNER JOIN tblproductconfigoptions ON tblproductconfigoptionssub.configid = tblproductconfigoptions.id\nINNER JOIN tblproductconfiglinks ON tblproductconfigoptions.gid = tblproductconfiglinks.gid\nINNER JOIN tblhosting on tblproductconfiglinks.pid = tblhosting.packageid\nWHERE tblhosting.id = " . $accountid . "\nAND tblproductconfigoptions.optiontype IN (3, 4)\nGROUP BY tblproductconfigoptionssub.configid\nORDER BY tblproductconfigoptionssub.sortorder ASC, id ASC;";
        $configOptionsResult = full_query($query);
        while( $configOptionsData = mysql_fetch_assoc($configOptionsResult) )
        {
            $options[$configOptionsData['id']] = $configOptionsData['configid'];
        }
        if( count($options) )
        {
            foreach( $options as $subID => $configOptionID )
            {
                $isOptionSaved = (string) get_query_val('tblhostingconfigoptions', 'configid', array( 'configid' => $configOptionID, 'relid' => $accountid ));
                if( !$isOptionSaved )
                {
                    insert_query('tblhostingconfigoptions', array( 'relid' => $accountid, 'configid' => $configOptionID, 'optionid' => $subID, 'qty' => 0 ));
                }
            }
        }
        $result = select_query('tblhostingconfigoptions', '', array( 'relid' => $accountid ));
        while( $data = mysql_fetch_array($result) )
        {
            $configid = $data['configid'];
            $result2 = select_query('tblproductconfigoptions', '', array( 'id' => $configid ));
            $data2 = mysql_fetch_array($result2);
            $optiontype = $data2['optiontype'];
            if( $optiontype == 3 || $optiontype == 4 )
            {
                $configoptionvalue = $data['qty'];
            }
            else
            {
                $configoptionvalue = $data['optionid'];
            }
            $values[$configid] = $configoptionvalue;
        }
    }
    $where = array( 'pid' => $pid );
    if( !$showhidden )
    {
        $where['hidden'] = 0;
    }
    $result2 = select_query('tblproductconfigoptions', '', $where, "order` ASC,`id", 'ASC', '', "tblproductconfiglinks ON tblproductconfiglinks.gid=tblproductconfigoptions.gid");
    while( $data2 = mysql_fetch_array($result2) )
    {
        $optionid = $data2['id'];
        $optionname = $data2['optionname'];
        $optiontype = $data2['optiontype'];
        $optionhidden = $data2['hidden'];
        $qtyminimum = $data2['qtyminimum'];
        $qtymaximum = $data2['qtymaximum'];
        if( strpos($optionname, "|") )
        {
            $optionname = explode("|", $optionname);
            $optionname = trim($optionname[1]);
        }
        $options = array(  );
        $selname = $selectedoption = $selsetup = $selrecurring = '';
        $selectedqty = 0;
        $foundPreselectedValue = false;
        $selvalue = $values[$optionid];
        if( $optiontype == '3' )
        {
            $result3 = select_query('tblproductconfigoptionssub', '', array( 'configid' => $optionid ));
            $data3 = mysql_fetch_array($result3);
            $opid = $data3['id'];
            $ophidden = $data3['hidden'];
            $opname = $data3['optionname'];
            if( strpos($opname, "|") )
            {
                $opname = explode("|", $opname);
                $opname = trim($opname[1]);
            }
            $opnameonly = $opname;
            $result4 = select_query('tblpricing', '', array( 'type' => 'configoptions', 'currency' => $currency['id'], 'relid' => $opid ));
            $data = mysql_fetch_array($result4);
            $setup = $data[substr($cycle, 0, 1) . 'setupfee'];
            $price = $fullprice = $data[$cycle];
            if( $orderform && $CONFIG['ProductMonthlyPricingBreakdown'] )
            {
                $price = $price / $cyclemonths;
            }
            if( 0 < $price )
            {
                $opname .= " " . formatCurrency($price);
            }
            $setupvalue = 0 < $setup ? " + " . formatCurrency($setup) . " " . $_LANG['ordersetupfee'] : '';
            $options[] = array( 'id' => $opid, 'hidden' => $ophidden, 'name' => $opname . $setupvalue, 'nameonly' => $opnameonly, 'recurring' => $price );
            if( !$selvalue )
            {
                $selvalue = 0;
            }
            $selectedqty = $selvalue;
            $selvalue = $opid;
            $selname = $_LANG['no'];
            if( $selectedqty )
            {
                $selname = $_LANG['yes'];
                $selectedoption = $opname;
                $selsetup = $setup;
                $selrecurring = $fullprice;
            }
        }
        else
        {
            if( $optiontype == '4' )
            {
                $result3 = select_query('tblproductconfigoptionssub', '', array( 'configid' => $optionid ));
                $data3 = mysql_fetch_array($result3);
                $opid = $data3['id'];
                $ophidden = $data3['hidden'];
                $opname = $data3['optionname'];
                if( strpos($opname, "|") )
                {
                    $opname = explode("|", $opname);
                    $opname = trim($opname[1]);
                }
                $opnameonly = $opname;
                $result4 = select_query('tblpricing', '', array( 'type' => 'configoptions', 'currency' => $currency['id'], 'relid' => $opid ));
                $data = mysql_fetch_array($result4);
                $setup = $data[substr($cycle, 0, 1) . 'setupfee'];
                $price = $fullprice = $data[$cycle];
                if( $orderform && $CONFIG['ProductMonthlyPricingBreakdown'] )
                {
                    $price = $price / $cyclemonths;
                }
                if( 0 < $price )
                {
                    $opname .= " " . formatCurrency($price);
                }
                $setupvalue = 0 < $setup ? " + " . formatCurrency($setup) . " " . $_LANG['ordersetupfee'] : '';
                $options[] = array( 'id' => $opid, 'hidden' => $ophidden, 'name' => $opname . $setupvalue, 'nameonly' => $opnameonly, 'recurring' => $price );
                if( !is_numeric($selvalue) || $selvalue < 0 )
                {
                    $selvalue = $qtyminimum;
                }
                if( 0 < $qtyminimum && $selvalue < $qtyminimum )
                {
                    $selvalue = $qtyminimum;
                }
                if( 0 < $qtymaximum && $qtymaximum < $selvalue )
                {
                    $selvalue = $qtymaximum;
                }
                $selectedqty = $selvalue;
                $selvalue = $opid;
                $selname = $selectedqty;
                $selectedoption = $opname;
                $selsetup = $setup * $selectedqty;
                $selrecurring = $fullprice * $selectedqty;
            }
            else
            {
                $result3 = select_query('tblproductconfigoptionssub', "tblpricing.*,tblproductconfigoptionssub.*", array( "tblproductconfigoptionssub.configid" => $optionid, "tblpricing.type" => 'configoptions', "tblpricing.currency" => $currency['id'] ), "tblproductconfigoptionssub`.`sortorder` ASC,`tblproductconfigoptionssub`.`id", 'ASC', '', "tblpricing ON tblpricing.relid=tblproductconfigoptionssub.id");
                while( $data3 = mysql_fetch_array($result3) )
                {
                    $opid = $data3['id'];
                    $ophidden = $data3['hidden'];
                    $setup = $data3[substr($cycle, 0, 1) . 'setupfee'];
                    $price = $fullprice = $data3[$cycle];
                    if( $orderform && $CONFIG['ProductMonthlyPricingBreakdown'] )
                    {
                        $price = $price / $cyclemonths;
                    }
                    $setupvalue = 0 < $setup ? " + " . formatCurrency($setup) . " " . $_LANG['ordersetupfee'] : '';
                    $opname = $data3['optionname'];
                    if( strpos($opname, "|") )
                    {
                        $opnameArr = explode("|", $opname);
                        if( defined('APICALL') )
                        {
                            $opname = trim($opnameArr[0]);
                            $setupvalue = '';
                        }
                        else
                        {
                            $opname = trim($opnameArr[1]);
                        }
                    }
                    $opnameonly = $opname;
                    if( 0 < $price && !defined('APICALL') )
                    {
                        $opname .= " " . formatCurrency($price);
                    }
                    if( $showhidden || !$ophidden )
                    {
                        $options[] = array( 'id' => $opid, 'name' => $opname . $setupvalue, 'nameonly' => $opnameonly, 'nameandprice' => $opname, 'setup' => $setup, 'fullprice' => $fullprice, 'recurring' => $price, 'hidden' => $ophidden );
                    }
                    if( $opid == $selvalue || !$selvalue && !$ophidden )
                    {
                        $selname = $opnameonly;
                        $selectedoption = $opname;
                        $selsetup = $setup;
                        $selrecurring = $fullprice;
                        $selvalue = $opid;
                        $foundPreselectedValue = true;
                    }
                }
                if( !$foundPreselectedValue )
                {
                    $selname = $options[0]['nameonly'];
                    $selectedoption = $options[0]['nameandprice'];
                    $selsetup = $options[0]['setup'];
                    $selrecurring = $options[0]['fullprice'];
                    $selvalue = $options[0]['id'];
                }
            }
        }
        $configoptions[] = array( 'id' => $optionid, 'hidden' => $optionhidden, 'optionname' => $optionname, 'optiontype' => $optiontype, 'selectedvalue' => $selvalue, 'selectedqty' => $selectedqty, 'selectedname' => $selname, 'selectedoption' => $selectedoption, 'selectedsetup' => $selsetup, 'selectedrecurring' => $selrecurring, 'qtyminimum' => $qtyminimum, 'qtymaximum' => $qtymaximum, 'options' => $options );
    }
    return $configoptions;
}
function validateAndSanitizeQuantityConfigOptions($configoption)
{
    $whmcs = WHMCS_Application::getinstance();
    $validConfigOptions = $errorConfigIDs = array(  );
    $errorMessage = '';
    foreach( $configoption as $configid => $optionvalue )
    {
        $data = get_query_vals('tblproductconfigoptions', '', array( 'id' => $configid ));
        $optionname = $data['optionname'];
        $optiontype = $data['optiontype'];
        $qtyminimum = $data['qtyminimum'];
        $qtymaximum = $data['qtymaximum'];
        if( strpos($optionname, "|") )
        {
            $optionname = explode("|", $optionname);
            $optionname = trim($optionname[1]);
        }
        if( $optiontype == '3' )
        {
            $optionvalue = $optionvalue ? '1' : '0';
        }
        else
        {
            if( $optiontype == '4' )
            {
                $optionvalue = (int) $optionvalue;
                if( $qtyminimum < 0 )
                {
                    $qtyminimum = 0;
                }
                if( $optionvalue < 0 || $optionvalue < $qtyminimum && 0 < $qtyminimum || 0 < $qtymaximum && $qtymaximum < $optionvalue )
                {
                    if( $qtymaximum <= 0 )
                    {
                        $qtymaximum = $whmcs->get_lang('clientareaunlimited');
                    }
                    $errorMessage .= "<li>" . sprintf($whmcs->get_lang('configoptionqtyminmax'), $optionname, $qtyminimum, $qtymaximum);
                    $errorConfigIDs[] = $configid;
                    $optionvalue = 0 < $qtyminimum ? $qtyminimum : 0;
                }
            }
            else
            {
                $optionvalue = get_query_val('tblproductconfigoptionssub', 'id', array( 'configid' => $configid, 'id' => $optionvalue ));
                if( !$optionvalue )
                {
                    $errorMessage .= "<li>The option selected for " . $optionname . " is not valid";
                    $errorConfigIDs[] = $configid;
                }
            }
        }
        $validConfigOptions[$configid] = $optionvalue;
    }
    return array( 'validOptions' => $validConfigOptions, 'errorConfigIDs' => $errorConfigIDs, 'errorMessage' => $errorMessage );
}