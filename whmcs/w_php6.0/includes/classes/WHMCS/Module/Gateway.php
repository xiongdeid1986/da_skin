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
class WHMCS_Module_Gateway extends WHMCS_Module
{
    protected $type = 'gateways';
    protected $activeList = '';
    public function __construct()
    {
        $whmcs = WHMCS_Application::getinstance();
        $this->addParam('companyname', $whmcs->get_config('CompanyName'));
        $this->addParam('systemurl', $whmcs->isSSLAvailable() ? $whmcs->getSystemSSLURL() : $whmcs->getSystemURL());
        $this->addParam('langpaynow', $whmcs->get_lang('invoicespaynow'));
        $whmcs->load_function('gateway');
        parent::__construct();
    }
    /**
     * Build a gateway module
     *
     * Some very nice long description that also mentions that this should be used from a callback.
     *
     * @throws WHMCS_Exception_Fatal if the gateway isn't found or isn't active.
     *
     * @param string $name
     *
     * @return WHMCS_Module_Gateway
     */
    public static function factory($name)
    {
        $gateway = new WHMCS_Module_Gateway();
        if( !$gateway->load($name) )
        {
            throw new WHMCS_Exception_Fatal("Module Not Found");
        }
        if( !$gateway->isActive() )
        {
            throw new WHMCS_Exception_Fatal("Module Not Activated");
        }
        return $gateway;
    }
    public function getActiveGateways()
    {
        if( is_array($this->activeList) )
        {
            return $this->activeList;
        }
        $this->activeList = array(  );
        $result = select_query('tblpaymentgateways', "DISTINCT gateway", '');
        while( $data = mysql_fetch_array($result) )
        {
            $gateway = $data[0];
            if( WHMCS_Gateways::isnamevalid($gateway) )
            {
                $this->activeList[] = $gateway;
            }
        }
        return $this->activeList;
    }
    public function isActiveGateway($gateway)
    {
        $gateways = $this->getActiveGateways();
        return in_array($gateway, $gateways);
    }
    public function getAvailableGateways($invoiceid = '')
    {
        $validgateways = array(  );
        $result = full_query("SELECT DISTINCT gateway, (SELECT value FROM tblpaymentgateways g2 WHERE g1.gateway=g2.gateway AND setting='name' LIMIT 1) AS `name`, (SELECT `order` FROM tblpaymentgateways g2 WHERE g1.gateway=g2.gateway AND setting='name' LIMIT 1) AS `order` FROM `tblpaymentgateways` g1 WHERE setting='visible' AND value='on' ORDER BY `order` ASC");
        while( $data = mysql_fetch_array($result) )
        {
            $validgateways[$data[0]] = $data[1];
        }
        if( $invoiceid )
        {
            $invoiceid = (int) $invoiceid;
            $invoicegateway = get_query_val('tblinvoices', 'paymentmethod', array( 'id' => $invoiceid ));
            $disabledgateways = array(  );
            $result = select_query('tblinvoiceitems', '', array( 'type' => 'Hosting', 'invoiceid' => $invoiceid ));
            while( $data = mysql_fetch_assoc($result) )
            {
                $relid = $data['relid'];
                if( $relid )
                {
                    $result2 = full_query("SELECT pg.disabledgateways AS disabled FROM tblhosting h LEFT JOIN tblproducts p on h.packageid = p.id LEFT JOIN tblproductgroups pg on p.gid = pg.id where h.id = " . (int) $relid);
                    $data2 = mysql_fetch_assoc($result2);
                    $gateways = explode(',', $data2['disabled']);
                    foreach( $gateways as $gateway )
                    {
                        if( array_key_exists($gateway, $validgateways) && $gateway != $invoicegateway )
                        {
                            unset($validgateways[$gateway]);
                        }
                    }
                }
            }
        }
        return $validgateways;
    }
    public function getFirstAvailableGateway()
    {
        $gateways = $this->getAvailableGateways();
        return key($gateways);
    }
    /**
     * Override the default module load method
     *
     * Loads gateway settings upon success
     *
     * @param string $module The name of the gateway module to load
     *
     * @return bool True for success, false on failure
     */
    public function load($module)
    {
        $loadStatus = parent::load($module);
        if( $loadStatus )
        {
            $this->loadSettings();
        }
        return $loadStatus;
    }
    /**
     * Loads gateway configuration settings for the currently loaded module
     *
     * @return array
     */
    public function loadSettings()
    {
        $gateway = $this->getLoadedModule();
        $settings = array( 'paymentmethod' => $gateway );
        $result = select_query('tblpaymentgateways', '', array( 'gateway' => $gateway ));
        while( $data = mysql_fetch_array($result) )
        {
            $setting = $data['setting'];
            $value = $data['value'];
            $this->addParam($setting, $value);
            $settings[$setting] = $value;
        }
        return $settings;
    }
    /**
     * Determines if loaded gateway module is currently active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getParam('type') ? true : false;
    }
    /**
     * Override the default call method
     *
     * This adds the currently loaded gateway module under the
     * payment method parameter prior to calling gateway functions
     *
     * @return string Module response
     */
    public function call($function, $params = array(  ))
    {
        $this->addParam('paymentmethod', $this->getLoadedModule());
        return parent::call($function, $params);
    }
}