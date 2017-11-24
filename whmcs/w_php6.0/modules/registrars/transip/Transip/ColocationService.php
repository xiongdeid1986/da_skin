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
require_once(dirname(__FILE__) . "/ApiSettings.php");
require_once(dirname(__FILE__) . "/DataCenterVisitor.php");
/**
 * This is the API endpoint for the ColocationService
 *
 * @package Transip
 * @class ColocationService
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_ColocationService
{
    protected static $_soapClient = null;
    const SERVICE = 'ColocationService';
    /**
     * Gets the singleton SoapClient which is used to connect to the TransIP Api.
     *
     * @param  mixed       $parameters  Parameters.
     * @return SoapClient               The SoapClient object to which we can connect to the TransIP API
     */
    public static function _getSoapClient($parameters = array(  ))
    {
        $endpoint = Transip_ApiSettings::$endpoint;
        if( self::$_soapClient === null )
        {
            $extensions = get_loaded_extensions();
            $errors = array(  );
            if( !class_exists('SoapClient') || !in_array('soap', $extensions) )
            {
                $errors[] = "The PHP SOAP extension doesn't seem to be installed. You need to install the PHP SOAP extension. (See: http://www.php.net/manual/en/book.soap.php)";
            }
            if( !in_array('openssl', $extensions) )
            {
                $errors[] = "The PHP OpenSSL extension doesn't seem to be installed. You need to install PHP with the OpenSSL extension. (See: http://www.php.net/manual/en/book.openssl.php)";
            }
            if( !empty($errors) )
            {
                exit( "<p>" . implode("</p>\n<p>", $errors) . "</p>" );
            }
            $classMap = array( 'DataCenterVisitor' => 'Transip_DataCenterVisitor' );
            $options = array( 'classmap' => $classMap, 'encoding' => 'utf-8', 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'trace' => false );
            $wsdlUri = "https://" . $endpoint . "/wsdl/?service=" . self::SERVICE;
            try
            {
                self::$_soapClient = new SoapClient($wsdlUri, $options);
            }
            catch( SoapFault $sf )
            {
                throw new Exception("Unable to connect to endpoint '" . $endpoint . "'");
            }
            self::$_soapClient->__setCookie('login', Transip_ApiSettings::$login);
            self::$_soapClient->__setCookie('mode', Transip_ApiSettings::$mode);
        }
        $timestamp = time();
        $nonce = uniqid('', true);
        self::$_soapClient->__setCookie('timestamp', $timestamp);
        self::$_soapClient->__setCookie('nonce', $nonce);
        self::$_soapClient->__setCookie('signature', self::_urlencode(self::_sign(array_merge($parameters, array( '__service' => self::SERVICE, '__hostname' => $endpoint, '__timestamp' => $timestamp, '__nonce' => $nonce )))));
        return self::$_soapClient;
    }
    /**
     * Calculates the hash to sign our request with based on the given parameters.
     *
     * @param  mixed   $parameters  The parameters to sign.
     * @return string               Base64 encoded signing hash.
     */
    protected static function _sign($parameters)
    {
        $matches = array(  );
        if( !preg_match("/-----BEGIN(?: RSA|) PRIVATE KEY-----(.*)-----END(?: RSA|) PRIVATE KEY-----/si", Transip_ApiSettings::$privateKey, $matches) )
        {
            exit( "<p>Could not find your private key, please supply your private key in the ApiSettings file. You can request a new private key in your TransIP Controlpanel.</p>" );
        }
        $key = $matches[1];
        $key = preg_replace("/\\s*/s", '', $key);
        $key = chunk_split($key, 64, "\n");
        $key = "-----BEGIN PRIVATE KEY-----\n" . $key . "-----END PRIVATE KEY-----";
        $digest = self::_sha512asn1(self::_encodeparameters($parameters));
        if( !@openssl_private_encrypt($digest, $signature, $key) )
        {
            exit( "<p>Could not sign your request, please supply your private key in the ApiSettings file. You can request a new private key in your TransIP Controlpanel.</p>" );
        }
        return base64_encode($signature);
    }
    /**
     * Creates a digest of the given data, with an asn1 header.
     *
     * @param  string  $data  The data to create a digest of.
     * @return string         The digest of the data, with asn1 header.
     */
    protected static function _sha512Asn1($data)
    {
        $digest = hash('sha512', $data, true);
        $asn1 = chr(48) . chr(81);
        $asn1 .= chr(48) . chr(13);
        $asn1 .= chr(6) . chr(9);
        $asn1 .= chr(96) . chr(134) . chr(72) . chr(1) . chr(101);
        $asn1 .= chr(3) . chr(4);
        $asn1 .= chr(2) . chr(3);
        $asn1 .= chr(5) . chr(0);
        $asn1 .= chr(4) . chr(64);
        $asn1 .= $digest;
        return $asn1;
    }
    /**
     * Encodes the given paramaters into a url encoded string based upon RFC 3986.
     *
     * @param  mixed   $parameters  The parameters to encode.
     * @param  string  $keyPrefix   Key prefix.
     * @return string               The given parameters encoded according to RFC 3986.
     */
    protected static function _encodeParameters($parameters, $keyPrefix = null)
    {
        if( !is_array($parameters) && !is_object($parameters) )
        {
            return self::_urlencode($parameters);
        }
        $encodedData = array(  );
        foreach( $parameters as $key => $value )
        {
            $encodedKey = is_null($keyPrefix) ? self::_urlencode($key) : $keyPrefix . "[" . self::_urlencode($key) . "]";
            if( is_array($value) || is_object($value) )
            {
                $encodedData[] = self::_encodeparameters($value, $encodedKey);
            }
            else
            {
                $encodedData[] = $encodedKey . "=" . self::_urlencode($value);
            }
        }
        return implode("&", $encodedData);
    }
    /**
     * Our own function to encode a string according to RFC 3986 since.
     * PHP < 5.3.0 encodes the ~ character which is not allowed.
     *
     * @param string $string The string to encode.
     * @return string The encoded string according to RFC 3986.
     */
    protected static function _urlencode($string)
    {
        $string = rawurlencode($string);
        return str_replace("%7E", "~", $string);
    }
    /**
     * Requests access to the data-center
     *
     * @param string $when the datetime of the wanted datacenter access, in YYYY-MM-DD hh:mm:ss format
     * @param int $duration the expected duration of the visit, in minutes
     * @param string[] $visitors the names of the visitors for this data-center visit, must be at least 1 and at most 20
     * @param string $phoneNumber if an SMS with access codes needs to be sent, set the phonenumber of the receiving phone here;
     * @return Transip_DataCenterVisitor[] An array of Visitor objects holding information (such as reservation and access number) about
     */
    public static function requestAccess($when, $duration, $visitors, $phoneNumber)
    {
        return self::_getsoapclient(array_merge(array( $when, $duration, $visitors, $phoneNumber ), array( '__method' => 'requestAccess' )))->requestAccess($when, $duration, $visitors, $phoneNumber);
    }
    /**
     * Request remote hands to the data-center
     *
     * @param string $coloName The name of the colocation
     * @param string $contactName The contact name
     * @param string $phoneNumber Phone number to contact
     * @param int $expectedDuration Expected duration of the job in minutes
     * @param string $instructions What to do
     */
    public static function requestRemoteHands($coloName, $contactName, $phoneNumber, $expectedDuration, $instructions)
    {
        return self::_getsoapclient(array_merge(array( $coloName, $contactName, $phoneNumber, $expectedDuration, $instructions ), array( '__method' => 'requestRemoteHands' )))->requestRemoteHands($coloName, $contactName, $phoneNumber, $expectedDuration, $instructions);
    }
    /**
     * Get coloNames for customer
     *
     * @return string[] Array with colo names
     */
    public static function getColoNames()
    {
        return self::_getsoapclient(array_merge(array(  ), array( '__method' => 'getColoNames' )))->getColoNames();
    }
    /**
     * Get IpAddresses that are active and assigned to a Colo.
     * Both ipv4 and ipv6 addresses are returned: ipv4 adresses in dot notation,
     * ipv6 addresses in ipv6 presentation.
     *
     * @param string $coloName The name of the colo to get the ipaddresses for for
     * @return string[] Array with assigned IPv4 and IPv6 addresses for the colo
     */
    public static function getIpAddresses($coloName)
    {
        return self::_getsoapclient(array_merge(array( $coloName ), array( '__method' => 'getIpAddresses' )))->getIpAddresses($coloName);
    }
    /**
     * Get ipranges that are assigned to a Colo. Both ipv4 and ipv6 ranges are
     * returned, in CIDR notation.
     *
     * @param string $coloName The name of the colo to get the ranges for
     * @see http://en.wikipedia.org/wiki/CIDR_notation
     * @return string[] Array of ipranges in CIDR format assigned to this colo.
     */
    public static function getIpRanges($coloName)
    {
        return self::_getsoapclient(array_merge(array( $coloName ), array( '__method' => 'getIpRanges' )))->getIpRanges($coloName);
    }
    /**
     * Adds a new IpAddress, either an ipv6 or an ipv4 address.
     * The service will validate the address, ensure the user is entitled
     * to the address and will add the address to the correct Colo and range.
     *
     * @param string $ipAddress The IpAddress to create, can be either ipv4 or ipv6.
     * @param string $reverseDns The RDNS name for this IpAddress
     */
    public static function createIpAddress($ipAddress, $reverseDns)
    {
        return self::_getsoapclient(array_merge(array( $ipAddress, $reverseDns ), array( '__method' => 'createIpAddress' )))->createIpAddress($ipAddress, $reverseDns);
    }
    /**
     * Deletes an IpAddress currently in use this account.
     * IpAddress can be either ipv4 or ipv6. The service will validate
     * if the user has rights to remove the address and will remove it completely,
     * together with any RDNS or monitoring assigned to the address.
     *
     * @param string $ipAddress the IpAddress to delete, can be either ipv4 or ipv6.
     */
    public static function deleteIpAddress($ipAddress)
    {
        return self::_getsoapclient(array_merge(array( $ipAddress ), array( '__method' => 'deleteIpAddress' )))->deleteIpAddress($ipAddress);
    }
    /**
     * Get the Reverse DNS for an IpAddress assigned to the user
     * Throws an Exception when the Address does not exist or is not
     * owned by the user.
     *
     * @param string $ipAddress the IpAddress, either ipv4 or ipv6
     * @return string rdns
     */
    public static function getReverseDns($ipAddress)
    {
        return self::_getsoapclient(array_merge(array( $ipAddress ), array( '__method' => 'getReverseDns' )))->getReverseDns($ipAddress);
    }
    /**
     * Set the RDNS name for an ipAddress.
     * Throws an Exception when the Address does not exist or is not
     * owned by the user.
     *
     * @param string $ipAddress The IpAddress to set the reverse dns for, can be either ipv4 or ipv6.
     * @param string $reverseDns The new reverse DNS, must be a valid RDNS value.
     */
    public static function setReverseDns($ipAddress, $reverseDns)
    {
        return self::_getsoapclient(array_merge(array( $ipAddress, $reverseDns ), array( '__method' => 'setReverseDns' )))->setReverseDns($ipAddress, $reverseDns);
    }
}