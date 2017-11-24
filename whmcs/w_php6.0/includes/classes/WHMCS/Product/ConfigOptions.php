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
class WHMCS_Product_ConfigOptions
{
    protected $cache = array(  );
    /**
     * Get the currently active system currency ID
     *
     * @return int
     */
    protected function getCurrencyID()
    {
        $whmcs = WHMCS_Application::getinstance();
        return $whmcs->getCurrencyID();
    }
    /**
     * Checks if a given product ID is in the cache
     *
     * @param int $productID
     *
     * @return bool
     */
    protected function isCached($productID)
    {
        return isset($this->cache[$productID]) && is_array($this->cache[$productID]);
    }
    /**
     * Returns data from the cache for a given product ID and label
     *
     * @param int $productID
     * @param string $optionLabel
     *
     * @return string[]
     */
    protected function getFromCache($productID, $optionLabel)
    {
        if( $this->isCached($productID) )
        {
            return $this->cache[$productID][$optionLabel];
        }
        return array(  );
    }
    /**
     * Saves data to the cache for a given product ID and label
     *
     * @param int $productID
     * @param string $optionLabel
     * @param string[] $optionsData
     *
     * @return bool
     */
    protected function storeToCache($productID, $optionLabel, $optionsData)
    {
        $this->cache[$productID][$optionLabel] = $optionsData;
        return true;
    }
    /**
     * Queries config option data from database for a given product ID if not cached
     *
     * @param int $productID
     *
     * @return array
     */
    protected function loadData($productID)
    {
        if( !$this->isCached($productID) )
        {
            $currencyId = $this->getCurrencyID();
            $info = $ops = array(  );
            $query = "SELECT tblproductconfigoptions.id,tblproductconfigoptions.optionname,tblproductconfigoptions.optiontype,tblproductconfigoptions.qtyminimum,tblproductconfigoptions.qtymaximum,(SELECT CONCAT(msetupfee,'|',qsetupfee,'|',ssetupfee,'|',asetupfee,'|',bsetupfee,'|',tsetupfee,'|',monthly,'|',quarterly,'|',semiannually,'|',annually,'|',biennially,'|',triennially) FROM tblpricing WHERE type='configoptions' AND currency=" . (int) $currencyId . " AND relid=(SELECT id FROM tblproductconfigoptionssub WHERE configid=tblproductconfigoptions.id AND hidden=0 ORDER BY sortorder ASC,id ASC LIMIT 1) ) FROM tblproductconfigoptions INNER JOIN tblproductconfiglinks ON tblproductconfigoptions.gid=tblproductconfiglinks.gid WHERE tblproductconfiglinks.pid=" . (int) $productID . " AND tblproductconfigoptions.hidden=0";
            $result = full_query($query);
            while( $data = mysql_fetch_array($result) )
            {
                $info[$data[0]] = array( 'name' => $data['optionname'], 'type' => $data['optiontype'], 'qtyminimum' => $data['qtyminimum'], 'qtymaximum' => $data['qtymaximum'] );
                $ops[$data[0]] = explode("|", $data[5]);
            }
            $this->storeToCache($productID, 'info', $info);
            $this->storeToCache($productID, 'pricing' . $currencyID, $ops);
        }
        return $ops;
    }
    /**
     * Get the starting price for default configurable options for a given product/cycle
     *
     * @param int $productID
     * @param string $billingCycle
     *
     * @return float The amount in the selected currency
     */
    public function getBasePrice($productID, $billingCycle)
    {
        $cycles = new WHMCS_Billing_Cycles();
        if( $cycles->isValidSystemBillingCycle($billingCycle) )
        {
            $this->loadData($productID);
            $optionsInfo = $this->getFromCache($productID, 'info');
            $optionsPricing = $this->getFromCache($productID, 'pricing' . $currencyId);
            $pricingObj = new WHMCS_Billing_Pricing();
            $cycleindex = array_search($billingCycle, $pricingObj->getDBFields());
            $price = 0;
            foreach( $optionsPricing as $configID => $pricing )
            {
                if( $optionsInfo[$configID]['type'] == 1 || $optionsInfo[$configID]['type'] == 2 )
                {
                    $price += $pricing[$cycleindex];
                }
                else
                {
                    if( $optionsInfo[$configID]['type'] == 3 )
                    {
                    }
                    else
                    {
                        if( $optionsInfo[$configID]['type'] == 4 )
                        {
                            $minquantity = $optionsInfo[$configID]['qtyminimum'];
                            if( 0 < $minquantity )
                            {
                                $price += $minquantity * $pricing[$cycleindex];
                            }
                        }
                    }
                }
            }
            return $price;
        }
        return false;
    }
    /**
     * Returns if a given product ID has configurable options
     *
     * @TODO: Currently relies on getBasePrice having been called first, change it so it does not
     *
     * @param int $productID
     *
     * @return boolean
     */
    public function hasConfigOptions($productID)
    {
        $this->loadData($productID);
        $optionsInfo = $this->getFromCache($productID, 'info');
        if( 0 < count($optionsInfo) )
        {
            return true;
        }
        return false;
    }
}