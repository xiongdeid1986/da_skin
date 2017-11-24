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
 * Varilogix_Call handles placing the call, as well as fetching the response
 * for you.
 *
 * @package vlx-sdk
 * @author Eric Coleman <eric@varilogix.com>
 **/
class Varilogix_Call
{
    public $_api = "https://v3.varilogix.com/api/%s/call/";
    public $_apiName = null;
    public $email = NULL;
    public $password = NULL;
    public $profile = NULL;
    public $service = NULL;
    public $domain = NULL;
    public $amount = NULL;
    public $name = NULL;
    public $telephone = NULL;
    public $country = NULL;
    public $pin = NULL;
    public $city = NULL;
    public $state = NULL;
    public $postalcode = NULL;
    public $ip = NULL;
    public $email_address = NULL;
    public $bin = NULL;
    public $test = 'false';
    public $_code = NULL;
    public $_message = NULL;
    public $_rawResponse = NULL;
    /**
     * Constructor
     *
     * @param   string  $apiName    your api name
     * @param   string  $email      users email address
     * @param   string  $password   md5 hash of the users password
     * @param   int     $profile    active profile to use
     * @return  Varilogix_Call
     * @author  Eric Coleman <eric@varilogix.com>
     **/
    public function Varilogix_Call($apiName, $email, $password, $profile)
    {
        if( strlen($password) != 32 )
        {
            trigger_error(E_USER_ERROR, "password must be an MD5 hash");
        }
        $this->_apiName = $apiName;
        $this->_api = sprintf($this->_api, $apiName);
        $this->email = $email;
        $this->password = $password;
        $this->profile = $profile;
    }
    /**
     * Set the product information that is being ordered.
     *
     * @param string $service   The service / item being sold
     * @param float $amount     The amount of the order
     */
    public function setProductInfo($service, $amount)
    {
        $this->service = $service;
        $this->amount = $amount;
    }
    /**
     * Set domain information avavailble for this order.  This will
     * allow us to automatically run whois information on each domain that
     * is being ordered or used in your billing software.
     *
     * Multiple domain example
     * <code>
     * $domains = array('varilogix.com', 'cnn.com', 'google.com');
     * $call->setDomainInfo($domains);
     * </code>
     *
     * Single domain example
     * <code>
     * $call->setDomainInfo('varilogix.com');
     * </code>
     *
     * @param string|array $domains single domain, or array of domains
     */
    public function setDomainInfo($domains)
    {
        if( is_array($domains) )
        {
            $this->domain = implode(',', $domains);
        }
        else
        {
            $this->domain = $domains;
        }
    }
    /**
     * Set the customer information
     *
     * @param string $name  Customer name
     * @param string $email Customer email address
     * @param string $telephone Customer telephone number
     * @param string $country  ISO 3166-1 country code
     */
    public function setCustomerInfo($name, $email, $telephone, $country)
    {
        $this->name = $name;
        $this->email_address = $email;
        $this->telephone = $telephone;
        $this->country = $country;
    }
    /**
     * Set the pin number to be used for this call.  The pin MUST be
     * 4 digits
     *
     * @see   generatePin
     * @param int $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    }
    /**
    * Set AFIS information for this call
    *
    * @param string $city
    * @param string $state
    * @param mixed $postal
    * @param int $bin
    */
    public function setAfisInformation($city, $state, $postal, $bin = null)
    {
        $this->city = $city;
        $this->state = $state;
        $this->postalcode = $postal;
        if( !is_null($bin) )
        {
            $this->bin = $bin;
        }
    }
    public function isTest($test)
    {
        if( $test )
        {
            $this->test = 'true';
        }
        else
        {
            $this->test = 'false';
        }
    }
    /**
     * Generate a 4 digit call pin
     *
     * @see setPin
     * @static
     * @return int
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function generatePin()
    {
        $pin = null;
        for( $i = 0; $i < 4; $i++ )
        {
            $pin .= mt_rand(1, 9);
        }
        return (int) $pin;
    }
    /**
     * Returns the result of the call request.  You can see the codes in
     * /docs/ERROR_CODES.txt
     *
     * @see getCode
     * @see getMessage
     * @return bool
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function call()
    {
        if( empty($this->ip) )
        {
            $this->ip = $this->_getUserIp();
        }
        $call = new Varilogix_Request($this->_api);
        $call->setParams(get_object_vars($this));
        $this->_rawResponse = $call->execute();
        $result = explode(',', $this->_rawResponse);
        $this->_code = trim($result[1]);
        $this->_message = trim($result[2]);
        return trim($result[0]);
    }
    /**
     * Returns the error code
     *
     * @return int
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function getCode()
    {
        return $this->_code;
    }
    /**
     * Returns the error message
     *
     * @return string
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function getMessage()
    {
        return $this->_message;
    }
    /**
     * Gets a users ip.
     *
     * Credit: php.net (ip-to-country)
     *
     * @return string
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function _getUserIp()
    {
        $ip = false;
        if( !empty($_SERVER['HTTP_CLIENT_IP']) )
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
        {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if( $ip )
            {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for( $i = 0; $i < count($ips); $i++ )
            {
                if( !eregi("^(10|172\\.16|192\\.168)\\.", $ips[$i]) )
                {
                    if( version_compare(phpversion(), "5.0.0", ">=") )
                    {
                        if( ip2long($ips[$i]) != false )
                        {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                    else
                    {
                        if( ip2long($ips[$i]) != 0 - 1 )
                        {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }
        return $ip ? $ip : $_SERVER['REMOTE_ADDR'];
    }
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }
}