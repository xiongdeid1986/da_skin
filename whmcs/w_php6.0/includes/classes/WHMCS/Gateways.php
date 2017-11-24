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
/**
 * WHMCS Payment Gateways Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Gateways
{
    private $modulename = '';
    private static $gateways = null;
    private $displaynames = null;
    public function __construct()
    {
    }
    public function getDisplayNames()
    {
        $result = select_query('tblpaymentgateways', 'gateway,value', array( 'setting' => 'name' ), 'order', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $this->displaynames[$data['gateway']] = $data['value'];
        }
        return $this->displaynames;
    }
    public function getDisplayName($gateway)
    {
        if( !is_array($this->displaynames) )
        {
            $this->getDisplayNames();
        }
        return array_key_exists($gateway, $this->displaynames) ? $this->displaynames[$gateway] : $gateway;
    }
    public static function isNameValid($gateway)
    {
        if( !is_string($gateway) || empty($gateway) )
        {
            return false;
        }
        if( !ctype_alnum(str_replace(array( '_', '-' ), '', $gateway)) )
        {
            return false;
        }
        return true;
    }
    public static function getActiveGateways()
    {
        if( is_array(self::$gateways) )
        {
            return self::$gateways;
        }
        self::$gateways = array(  );
        $result = select_query('tblpaymentgateways', "DISTINCT gateway", '');
        while( $data = mysql_fetch_array($result) )
        {
            $gateway = $data[0];
            if( WHMCS_Gateways::isnamevalid($gateway) )
            {
                self::$gateways[] = $gateway;
            }
        }
        return self::$gateways;
    }
    public function isActiveGateway($gateway)
    {
        $gateways = $this->getActiveGateways();
        return in_array($gateway, $gateways);
    }
    public static function makeSafeName($gateway)
    {
        $validgateways = WHMCS_Gateways::getactivegateways();
        return in_array($gateway, $validgateways) ? $gateway : '';
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
            if( array_key_exists($invoicegateway, $validgateways) === false )
            {
                $validgateways[$invoicegateway] = get_query_val('tblpaymentgateways', 'value', array( 'setting' => 'name', 'gateway' => $invoicegateway ));
            }
        }
        return $validgateways;
    }
    public function getFirstAvailableGateway()
    {
        $gateways = $this->getAvailableGateways();
        return key($gateways);
    }
    public function getCCDateMonths()
    {
        $months = array(  );
        for( $i = 1; $i <= 12; $i++ )
        {
            $months[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return $months;
    }
    public function getCCStartDateYears()
    {
        $startyears = array(  );
        for( $i = date('Y') - 12; $i <= date('Y'); $i++ )
        {
            $startyears[] = $i;
        }
        return $startyears;
    }
    public function getCCExpiryDateYears()
    {
        $expiryyears = array(  );
        for( $i = date('Y'); $i <= date('Y') + 12; $i++ )
        {
            $expiryyears[] = $i;
        }
        return $expiryyears;
    }
}