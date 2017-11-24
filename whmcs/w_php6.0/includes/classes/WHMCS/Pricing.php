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
class WHMCS_Pricing
{
    private $data = array(  );
    private $cycles = array( 'monthly', 'quarterly', 'semiannually', 'annually', 'biennially', 'triennially' );
    public function __construct()
    {
    }
    public function loadPricing($type, $relid, $currencyid = '')
    {
        if( !$currencyid )
        {
            global $currency;
            $currencyid = $currency['id'];
        }
        $result = select_query('tblpricing', '', array( 'type' => $type, 'currency' => (int) $currencyid, 'relid' => (int) $relid ));
        $data = mysql_fetch_array($result);
        $this->data = $data;
    }
    public function getData($key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : '';
    }
    public function getRelID()
    {
        return (int) $this->getData('relid');
    }
    public function getSetup($cycle)
    {
        return $this->getData(substr($cycle, 0, 1) . 'setupfee');
    }
    public function getPrice($cycle)
    {
        return $this->getData($cycle);
    }
    public function getAvailableBillingCycles()
    {
        $active_cycles = array(  );
        foreach( $this->cycles as $cycle )
        {
            if( $this->getData($cycle) != 0 - 1 )
            {
                $active_cycles[] = $cycle;
            }
        }
        return $active_cycles;
    }
    public function hasBillingCyclesAvailable()
    {
        return 0 < count($this->getAvailableBillingCycles()) ? true : false;
    }
    public function getFirstAvailableCycle()
    {
        $cycles = $this->getAvailableBillingCycles();
        return 0 < count($cycles) ? $cycles[0] : '';
    }
    public function getAllCycleOptions()
    {
        $cycles = array(  );
        foreach( $this->cycles as $cycle )
        {
            if( $price = $this->getPrice($cycle) != 0 - 1 )
            {
                $setupfee = $this->getSetup($cycle);
                $price = $this->getPrice($cycle);
                if( !function_exists('getCartConfigOptions') )
                {
                    require(ROOTDIR . "/includes/configoptionsfunctions.php");
                }
                $configoptions = getCartConfigOptions($this->getRelID(), array(  ), $cycle, '', true);
                if( count($configoptions) )
                {
                    foreach( $configoptions as $option )
                    {
                        $setupfee += $option['selectedsetup'];
                        $price += $option['selectedrecurring'];
                    }
                }
                $cycles[] = array( 'cycle' => $cycle, 'setupfee' => $setupfee, 'price' => $price );
            }
        }
        return $cycles;
    }
}