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
 * Payment Solution Adapter for classic WHMCS gateway modules
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Payment_Adapter_GatewaysModuleAdapter extends WHMCS_Payment_Adapter_AbstractAdapter
{
    /**
     * Constructor
     *
     * @param string $name Name of solution for use by internal routines
     *
     * @return WHMCS_Payment_Adapter_GatewaysModuleAdapter
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $configuration = $this->getConfigurationFromDefinedFunctions();
        $this->setConfigurationParameters($configuration);
        $this->detectCapabilitiesFromDefinedFunctions();
        $type = $this->detectSolutionTypeFromCapabilities();
        $this->setSolutionType($type);
        return $this;
    }
    protected function detectSolutionTypeFromCapabilities()
    {
        $type = '';
        if( !$this->refundCapable )
        {
            $type = WHMCS_Payment_Solutions::TYPE_ALTERNATE;
        }
        else
        {
            if( $this->captureCapable && $this->linkCapable )
            {
                $type = WHMCS_Payment_Solutions::TYPE_MULTI;
            }
            else
            {
                if( $this->linkCapable && !$this->captureCapable )
                {
                    $type = WHMCS_Payment_Solutions::TYPE_ALTERNATE;
                }
                else
                {
                    if( $this->captureCapable )
                    {
                        $type = WHMCS_Payment_Solutions::TYPE_GATEWAY;
                    }
                    else
                    {
                        throw new WHMCS_Payment_Exception_InvalidModuleException(sprintf("Payment solution module '%s' does not implement either a capture or link function", $name));
                    }
                }
            }
        }
        return $type;
    }
    protected function detectCapabilitiesFromDefinedFunctions()
    {
        $name = $this->getName();
        if( function_exists($name . '_link') )
        {
            $this->linkCapable = true;
        }
        if( function_exists($name . '_capture') )
        {
            $this->captureCapable = true;
        }
        if( function_exists($name . '_storeremote') )
        {
            $this->remotePaymentDetailsStorageCapable = true;
        }
        if( function_exists($name . '_refund') )
        {
            $this->refundCapable = true;
        }
        return $this;
    }
    protected function getConfigurationFromDefinedFunctions()
    {
        $name = $this->getName();
        $config = array(  );
        if( function_exists($name . '_config') )
        {
            $config = call_user_func($name . '_config');
        }
        else
        {
            if( function_exists($name . '_activate') )
            {
                global $GATEWAYMODULE;
                global $GatewayFieldDefines;
                $GatewayFieldDefines = array(  );
                if( !function_exists('defineGatewayField') )
                {
function defineGatewayField($gateway, $type, $name, $defaultvalue, $friendlyname, $size, $description)
{
    global $GatewayFieldDefines;
    if( $type == 'dropdown' )
    {
        $options = $description;
        $description = '';
    }
    else
    {
        $options = '';
    }
    $GatewayFieldDefines[$name] = array( 'FriendlyName' => $friendlyname, 'Type' => $type, 'Size' => $size, 'Description' => $description, 'Value' => $defaultvalue, 'Options' => $options );
}
                }
                $visable_name = isset($GATEWAYMODULE[$name . 'visiblename']) ? $GATEWAYMODULE[$name . 'visiblename'] : $name;
                $GatewayFieldDefines['FriendlyName'] = array( 'Type' => 'System', 'Value' => $visable_name );
                if( isset($GATEWAYMODULE[$name . 'notes']) )
                {
                    $GatewayFieldDefines['UsageNotes'] = array( 'Type' => 'System', 'Value' => $GATEWAYMODULE[$name . 'notes'] );
                }
                call_user_func($name . '_activate');
                $config = $GatewayFieldDefines;
            }
            else
            {
                throw new WHMCS_Payment_Exception_InvalidModuleExceptions(sprintf("Payment solution module '%s' does not implement a configuration function", $name));
            }
        }
        return $config;
    }
    public function captureTransaction($params)
    {
        $name = $this->getName();
        if( !$this->isCaptureCapable() )
        {
            throw new BadMethodCallException(sprintf("Payment solution module '%s' does not implement a capture function", $name));
        }
        return call_user_func($name . '_capture', $params);
    }
    public function refundTransaction($params)
    {
        $name = $this->getName();
        if( !$this->isRefundCapable() )
        {
            throw new BadMethodCallException(sprintf("Payment solution module '%s' does not implement a refund function", $name));
        }
        return call_user_func($name . '_refund', $params);
    }
    public function storePaymentDetailsRemotely($params)
    {
        $name = $this->getName();
        if( !$this->isRemotePaymentDetailsStorageCapable() )
        {
            throw new BadMethodCallException(sprintf("Payment solution module '%s' does not implement a storeremote function", $name));
        }
        return call_user_func($name . '_storeremote', $params);
    }
    public function getHtmlLink($params = null)
    {
        $name = $this->getName();
        if( !$this->isLinkCapable() )
        {
            return parent::gethtmllink($params);
        }
        return call_user_func($name . '_link', $params);
    }
}