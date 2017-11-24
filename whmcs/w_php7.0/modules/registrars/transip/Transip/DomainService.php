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
require_once(dirname(__FILE__) . "/DomainCheckResult.php");
require_once(dirname(__FILE__) . "/Domain.php");
require_once(dirname(__FILE__) . "/Nameserver.php");
require_once(dirname(__FILE__) . "/WhoisContact.php");
require_once(dirname(__FILE__) . "/DnsEntry.php");
require_once(dirname(__FILE__) . "/DomainBranding.php");
require_once(dirname(__FILE__) . "/Tld.php");
require_once(dirname(__FILE__) . "/DomainAction.php");
/**
 * This is the API endpoint for the DomainService
 *
 * @package Transip
 * @class DomainService
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_DomainService
{
    protected static $_soapClient = null;
    const SERVICE = 'DomainService';
    const AVAILABILITY_INYOURACCOUNT = 'inyouraccount';
    const AVAILABILITY_UNAVAILABLE = 'unavailable';
    const AVAILABILITY_NOTFREE = 'notfree';
    const AVAILABILITY_FREE = 'free';
    const CANCELLATIONTIME_END = 'end';
    const CANCELLATIONTIME_IMMEDIATELY = 'immediately';
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
            $classMap = array( 'DomainCheckResult' => 'Transip_DomainCheckResult', 'Domain' => 'Transip_Domain', 'Nameserver' => 'Transip_Nameserver', 'WhoisContact' => 'Transip_WhoisContact', 'DnsEntry' => 'Transip_DnsEntry', 'DomainBranding' => 'Transip_DomainBranding', 'Tld' => 'Transip_Tld', 'DomainAction' => 'Transip_DomainAction' );
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
     * Checks the availability of multiple domains.
     *
     * @param string[] $domainNames the domain names to check for availability. A maximum of 20 domainNames at once can be checked.
     * @return Transip_DomainCheckResult[] A list of DomainCheckResult objects, holding the domainName and the status per result.
     * @example examples/DomainService-batchCheckAvailability.php
     */
    public static function batchCheckAvailability($domainNames)
    {
        return self::_getsoapclient(array_merge(array( $domainNames ), array( '__method' => 'batchCheckAvailability' )))->batchCheckAvailability($domainNames);
    }
    /**
     * Checks the availability of a domain.
     *
     * @param string $domainName the domain name to check for availability
     * @return string the availability status of the domain name:
     * @example examples/DomainService-checkAvailability.php
     */
    public static function checkAvailability($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'checkAvailability' )))->checkAvailability($domainName);
    }
    /**
     * Gets the whois of a domain name
     *
     * @param string $domainName the domain name to get the whois for
     * @return string the whois data for the domain
     * @example examples/DomainService-getWhois.php
     */
    public static function getWhois($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getWhois' )))->getWhois($domainName);
    }
    /**
     * Gets the names of all domains in your account.
     *
     * @return string[] A list of all domains in your account
     * @example examples/DomainService-getDomainNames.php
     */
    public static function getDomainNames()
    {
        return self::_getsoapclient(array_merge(array(  ), array( '__method' => 'getDomainNames' )))->getDomainNames();
    }
    /**
     * Get information about a domainName.
     *
     * @param string $domainName The domainName to get the information for.
     * @return Transip_Domain A Domain object holding the data for the requested domainName.
     * @example examples/DomainService-DomainService-getInfo.php
     */
    public static function getInfo($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getInfo' )))->getInfo($domainName);
    }
    /**
     * Get information about a list of Domain names.
     *
     * @param string[] $domainNames A list of Domain names you want information for.
     * @return Transip_Domain[] Domain objects.
     */
    public static function batchGetInfo($domainNames)
    {
        return self::_getsoapclient(array_merge(array( $domainNames ), array( '__method' => 'batchGetInfo' )))->batchGetInfo($domainNames);
    }
    /**
     * Gets the Auth code for a domainName
     *
     * @param string $domainName the domainName to get the authcode for
     * @deprecated
     * @return string the authentication code for a domain name
     * @example examples/DomainService-DomainService-getAuthCode.php
     */
    public static function getAuthCode($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getAuthCode' )))->getAuthCode($domainName);
    }
    /**
     * Gets the lock status for a domainName
     *
     * @param string $domainName the domainName to get the lock status for
     * @return boolean true iff the domain is locked at the registry
     * @deprecated use getInfo()
     */
    public static function getIsLocked($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getIsLocked' )))->getIsLocked($domainName);
    }
    /**
     * Registers a domain name, will automatically create and sign a proposition for it
     *
     * @param Transip_Domain $domain the Domain object holding information about the domain that needs to be registered.
     * @requires readwrite mode
     * @example examples/DomainService-DomainService-register-whois.php
     */
    public static function register($domain)
    {
        return self::_getsoapclient(array_merge(array( $domain ), array( '__method' => 'register' )))->register($domain);
    }
    /**
     * Cancels a domain name, will automatically create and sign a cancellation document
     * Please note that domains with webhosting cannot be cancelled through the API
     *
     * @param string $domainName the domainname that needs to be cancelled
     * @param string $endTime the time to cancel the domain (DomainService::CANCELLATIONTIME_END (end of contract) or DomainService::CANCELLATIONTIME_IMMEDIATELY (as soon as possible))
     * @requires readwrite mode
     * @example examples/DomainService-DomainService-cancel.php
     */
    public static function cancel($domainName, $endTime)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $endTime ), array( '__method' => 'cancel' )))->cancel($domainName, $endTime);
    }
    /**
     * Transfers a domain with changing the owner, not all TLDs support this (e.g. nl)
     *
     * @param Transip_Domain $domain the Domain object holding information about the domain that needs to be transfered
     * @param string $authCode the authorization code for domains needing this for transfers (e.g. .com or .org transfers). Leave empty when n/a.
     * @requires readwrite mode
     * @example examples/DomainService-DomainService-transfer.php
     */
    public static function transferWithOwnerChange($domain, $authCode)
    {
        return self::_getsoapclient(array_merge(array( $domain, $authCode ), array( '__method' => 'transferWithOwnerChange' )))->transferWithOwnerChange($domain, $authCode);
    }
    /**
     * Transfers a domain without changing the owner
     *
     * @param Transip_Domain $domain the Domain object holding information about the domain that needs to be transfered
     * @param string $authCode the authorization code for domains needing this for transfers (e.g. .com or .org transfers). Leave empty when n/a.
     * @requires readwrite mode
     * @example examples/DomainService-DomainService-transfer.php
     */
    public static function transferWithoutOwnerChange($domain, $authCode)
    {
        return self::_getsoapclient(array_merge(array( $domain, $authCode ), array( '__method' => 'transferWithoutOwnerChange' )))->transferWithoutOwnerChange($domain, $authCode);
    }
    /**
     * Starts a nameserver change for this domain, will replace all existing nameservers with the new nameservers
     *
     * @param string $domainName the domainName to change the nameservers for
     * @param Transip_Nameserver[] $nameservers the list of new nameservers for this domain
     * @example examples/DomainService-DomainService-setNameservers.php
     */
    public static function setNameservers($domainName, $nameservers)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $nameservers ), array( '__method' => 'setNameservers' )))->setNameservers($domainName, $nameservers);
    }
    /**
     * Lock this domain in real time
     *
     * @param string $domainName the domainName to set the lock for
     * @example examples/DomainService-DomainService-setLock.php
     */
    public static function setLock($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'setLock' )))->setLock($domainName);
    }
    /**
     * unlocks this domain in real time
     *
     * @param string $domainName the domainName to unlock
     * @example examples/DomainService-DomainService-setLock.php
     */
    public static function unsetLock($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'unsetLock' )))->unsetLock($domainName);
    }
    /**
     * Sets the DnEntries for this Domain, will replace all existing dns entries with the new entries
     *
     * @param string $domainName the domainName to change the dns entries for
     * @param Transip_DnsEntry[] $dnsEntries the list of new DnsEntries for this domain
     * @example examples/DomainService-DomainService-setDnsEntries.php
     */
    public static function setDnsEntries($domainName, $dnsEntries)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $dnsEntries ), array( '__method' => 'setDnsEntries' )))->setDnsEntries($domainName, $dnsEntries);
    }
    /**
     * Starts an owner change of a Domain, brings additional costs with the following TLDs:
     * .nl
     * .be
     * .eu
     *
     * @param string $domainName the domainName to change the owner for
     * @param Transip_WhoisContact $registrantWhoisContact the new contact data for this
     * @example examples/DomainService-DomainService-setOwner.php
     */
    public static function setOwner($domainName, $registrantWhoisContact)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $registrantWhoisContact ), array( '__method' => 'setOwner' )))->setOwner($domainName, $registrantWhoisContact);
    }
    /**
     * Starts a contact change of a domain, this will replace all existing contacts
     *
     * @param string $domainName the domainName to change the contacts for
     * @param Transip_WhoisContact[] $contacts the list of new contacts for this domain
     * @example examples/DomainService-DomainService-setContacts.php
     */
    public static function setContacts($domainName, $contacts)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $contacts ), array( '__method' => 'setContacts' )))->setContacts($domainName, $contacts);
    }
    /**
     * Get TransIP supported TLDs
     *
     * @return Transip_Tld[] Array of Tld objects
     * @example examples/DomainService-DomainService-getAllTldInfos.php
     */
    public static function getAllTldInfos()
    {
        return self::_getsoapclient(array_merge(array(  ), array( '__method' => 'getAllTldInfos' )))->getAllTldInfos();
    }
    /**
     * Get info about a specific TLD
     *
     * @param string $tldName The tld to get information abot
     * @return Transip_Tld Tld object with info about this Tld
     * @example examples/DomainService-DomainService-getAllTldInfos.php
     */
    public static function getTldInfo($tldName)
    {
        return self::_getsoapclient(array_merge(array( $tldName ), array( '__method' => 'getTldInfo' )))->getTldInfo($tldName);
    }
    /**
     * Gets info about the action this domain is currently running
     *
     * @param string $domainName Name of the domain
     * @return Transip_DomainAction if this domain is currently running an action, a corresponding DomainAction with info about the action will be returned.
     * @example examples/DomainService-DomainService-domainActions.php
     */
    public static function getCurrentDomainAction($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getCurrentDomainAction' )))->getCurrentDomainAction($domainName);
    }
    /**
     * Retries a failed domain action with new domain data. The Domain#name field must contain
     * the name of the Domain, the nameserver, contacts, dnsEntries fields contain the new data for this domain.
     * Set a field to null to not change the data.
     *
     * @param Transip_Domain $domain the domain with data to retry
     * @example examples/DomainService-DomainService-domainActions.php
     */
    public static function retryCurrentDomainActionWithNewData($domain)
    {
        return self::_getsoapclient(array_merge(array( $domain ), array( '__method' => 'retryCurrentDomainActionWithNewData' )))->retryCurrentDomainActionWithNewData($domain);
    }
    /**
     * Retry a transfer action with a new authcode
     *
     * @param Transip_Domain $domain the domain to try the transfer with a different authcode for
     * @param string $newAuthCode New authorization code to try
     * @example examples/DomainService-DomainService-domainActions.php
     */
    public static function retryTransferWithDifferentAuthCode($domain, $newAuthCode)
    {
        return self::_getsoapclient(array_merge(array( $domain, $newAuthCode ), array( '__method' => 'retryTransferWithDifferentAuthCode' )))->retryTransferWithDifferentAuthCode($domain, $newAuthCode);
    }
    /**
     * Cancels a failed domain action
     *
     * @param Transip_Domain $domain the domain to cancel the action for
     * @example examples/DomainService-DomainService-domainActions.php
     */
    public static function cancelDomainAction($domain)
    {
        return self::_getsoapclient(array_merge(array( $domain ), array( '__method' => 'cancelDomainAction' )))->cancelDomainAction($domain);
    }
}