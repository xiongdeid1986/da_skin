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
 * Abstract Adapter
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
abstract class WHMCS_Payment_Adapter_AbstractAdapter implements WHMCS_Payment_Adapter_AdapterInterface
{
    protected $type = '';
    protected $name = '';
    protected $config = array(  );
    protected $captureCapable = false;
    protected $refundCapable = false;
    protected $remotePaymentDetailsStorageCapable = false;
    protected $linkCapable = false;
    public function __construct($name = '')
    {
        if( !$name )
        {
            $name = $this->getName();
        }
        $this->setName($name);
        return $this;
    }
    public function setName($name)
    {
        if( !is_string($name) || $name == '' )
        {
            throw new InvalidArgumentException("Name must be a non-empty string");
        }
        $this->name = $name;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getConfigurationParameters()
    {
        return $this->config;
    }
    public function setConfigurationParameters($configuration)
    {
        $this->config = $configuration;
    }
    public function getSolutionType()
    {
        return $this->type;
    }
    public function setSolutionType($type)
    {
        if( !WHMCS_Payment_Solutions::isvalidsolutiontype($type) )
        {
            throw new InvalidArgumentType(sprintf("Unknown Payment Solution type '%s'", $type));
        }
        $this->type = $type;
    }
    public function isLinkCapable()
    {
        return $this->linkCapable;
    }
    public function isCaptureCapable()
    {
        return $this->captureCapable;
    }
    public function isRefundCapable()
    {
        return $this->refundCapable;
    }
    public function isRemotePaymentDetailsStorageCapable()
    {
        return $this->remotePaymentDetailsStorageCapable;
    }
    public function getHtmlLink($params = null)
    {
        foreach( array( 'systemurl', 'invoiceid', 'langpaynow' ) as $element )
        {
            if( !isset($params[$element]) )
            {
                $params[$element] = '';
            }
        }
        $html = "<form method=\"post\" action=\"%s/creditcard.php\" name=\"paymentfrm\">" . "<input type=\"hidden\" name=\"invoiceid\" value=\"%s\">" . "<input type=\"submit\" value=\"%s\">" . "</form>";
        return sprintf($html, $params['systemurl'], $params['invoiceid'], $params['langpaynow']);
    }
    public function captureTransaction($params)
    {
        throw new WHMCS_Payment_Exception_MethodNotImplemented(sprintf("Method %s has been called, but is not defined in class %s", 'captureTransaction', get_class($this)));
    }
    public function refundTransaction($params)
    {
        throw new WHMCS_Payment_Exception_MethodNotImplemented(sprintf("Method %s has been called, but is not defined in class %s", 'refundTransaction', get_class($this)));
    }
    public function storePaymentDetailsRemotely($params)
    {
        throw new WHMCS_Payment_Exception_MethodNotImplemented(sprintf("Method %s has been called, but is not defined in class %s", 'storePaymentDetailsRemotely', get_class($this)));
    }
}