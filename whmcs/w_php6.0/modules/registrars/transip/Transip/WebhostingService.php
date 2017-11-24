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
require_once(dirname(__FILE__) . "/WebhostingPackage.php");
require_once(dirname(__FILE__) . "/WebHost.php");
require_once(dirname(__FILE__) . "/Cronjob.php");
require_once(dirname(__FILE__) . "/MailBox.php");
require_once(dirname(__FILE__) . "/Db.php");
require_once(dirname(__FILE__) . "/MailForward.php");
require_once(dirname(__FILE__) . "/SubDomain.php");
/**
 * This is the API endpoint for the WebhostingService
 *
 * @package Transip
 * @class WebhostingService
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_WebhostingService
{
    protected static $_soapClient = null;
    const SERVICE = 'WebhostingService';
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
            $classMap = array( 'WebhostingPackage' => 'Transip_WebhostingPackage', 'WebHost' => 'Transip_WebHost', 'Cronjob' => 'Transip_Cronjob', 'MailBox' => 'Transip_MailBox', 'Db' => 'Transip_Db', 'MailForward' => 'Transip_MailForward', 'SubDomain' => 'Transip_SubDomain' );
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
     * Get all domain names that have a webhosting package attached to them.
     *
     * @return string[] List of domain names that have a webhosting package
     */
    public static function getWebhostingDomainNames()
    {
        return self::_getsoapclient(array_merge(array(  ), array( '__method' => 'getWebhostingDomainNames' )))->getWebhostingDomainNames();
    }
    /**
     * Get available webhosting packages
     *
     * @return Transip_WebhostingPackage[] List of available webhosting packages
     */
    public static function getAvailablePackages()
    {
        return self::_getsoapclient(array_merge(array(  ), array( '__method' => 'getAvailablePackages' )))->getAvailablePackages();
    }
    /**
     * Get information about existing webhosting on a domain.
     *
     * Please be aware that the information returned is outdated when
     * a modifying function in Transip_WebhostingService is called (e.g. createCronjob()).
     *
     * Call this function again to refresh the info.
     *
     * @param string $domainName The domain name of the webhosting package to get the info for. Must be owned by this user
     * @return Transip_WebHost WebHost object with all info about the requested webhosting package
     */
    public static function getInfo($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getInfo' )))->getInfo($domainName);
    }
    /**
     * Order webhosting for a domain name
     *
     * @param string $domainName The domain name to order the webhosting for. Must be owned by this user
     * @param Transip_WebhostingPackage $webhostingPackage The webhosting Package to order, one of the packages returned by Transip_WebhostingService::getAvailablePackages()
     * @throws ApiException on error
     */
    public static function order($domainName, $webhostingPackage)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $webhostingPackage ), array( '__method' => 'order' )))->order($domainName, $webhostingPackage);
    }
    /**
     * Get available upgrades packages for a domain name with webhosting. Only those packages will be returned to which
     * the given domain name can be upgraded to.
     *
     * @param string $domainName Domain to get upgrades for. Must be owned by the current user.
     * @return Transip_WebhostingPackage[] Available packages to which the domain name can be upgraded to.
     */
    public static function getAvailableUpgrades($domainName)
    {
        return self::_getsoapclient(array_merge(array( $domainName ), array( '__method' => 'getAvailableUpgrades' )))->getAvailableUpgrades($domainName);
    }
    /**
     * Upgrade the webhosting of a domain name to a new webhosting package to a given new package.
     *
     * @param string $domainName The domain to upgrade webhosting for. Must be owned by the current user.
     * @param string $newWebhostingPackage The new webhosting package, must be one of the packages returned getAvailableUpgrades() for the given domain name
     */
    public static function upgrade($domainName, $newWebhostingPackage)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $newWebhostingPackage ), array( '__method' => 'upgrade' )))->upgrade($domainName, $newWebhostingPackage);
    }
    /**
     * Cancel webhosting for a domain
     *
     * @param string $domainName The domain to cancel the webhosting for
     * @param string $endTime the time to cancel the domain (WebhostingService::CANCELLATIONTIME_END (end of contract) or WebhostingService::CANCELLATIONTIME_IMMEDIATELY (as soon as possible))
     */
    public static function cancel($domainName, $endTime)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $endTime ), array( '__method' => 'cancel' )))->cancel($domainName, $endTime);
    }
    /**
     * Set a new FTP password for a webhosting package
     *
     * @param string $domainName Domain to set webhosting FTP password for
     * @param string $newPassword The new FTP password for the webhosting package
     */
    public static function setFtpPassword($domainName, $newPassword)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $newPassword ), array( '__method' => 'setFtpPassword' )))->setFtpPassword($domainName, $newPassword);
    }
    /**
     * Create a cronjob
     *
     * @param string $domainName the domain name of the webhosting package to create cronjob for
     * @param Transip_Cronjob $cronjob the cronjob to create. All fields must be valid.
     */
    public static function createCronjob($domainName, $cronjob)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $cronjob ), array( '__method' => 'createCronjob' )))->createCronjob($domainName, $cronjob);
    }
    /**
     * Delete a cronjob from a webhosting package.
     * Note, all completely matching cronjobs will be removed
     *
     * @param string $domainName the domain name of the webhosting package to delete a cronjob
     * @param Transip_Cronjob $cronjob Cronjob the cronjob to delete. Be aware that all matching cronjobs will be removed.
     */
    public static function deleteCronjob($domainName, $cronjob)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $cronjob ), array( '__method' => 'deleteCronjob' )))->deleteCronjob($domainName, $cronjob);
    }
    /**
     * Creates a MailBox for a webhosting package.
     * The address field of the MailBox object must be unique.
     *
     * @param string $domainName the domain name of the webhosting package to create the mailbox for
     * @param Transip_MailBox $mailBox MailBox object to create
     */
    public static function createMailBox($domainName, $mailBox)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailBox ), array( '__method' => 'createMailBox' )))->createMailBox($domainName, $mailBox);
    }
    /**
     * Modifies MailBox settings
     *
     * @param string $domainName the domain name of the webhosting package to modify the mailbox for
     * @param Transip_MailBox $mailBox the MailBox to modify
     */
    public static function modifyMailBox($domainName, $mailBox)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailBox ), array( '__method' => 'modifyMailBox' )))->modifyMailBox($domainName, $mailBox);
    }
    /**
     * Sets a new password for a MailBox
     *
     * @param string $domainName the domain name of the webhosting package to set the mailbox password for
     * @param Transip_MailBox $mailBox the MailBox to set the password for
     * @param string $newPassword the new password for the MailBox, cannot be empty.
     */
    public static function setMailBoxPassword($domainName, $mailBox, $newPassword)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailBox, $newPassword ), array( '__method' => 'setMailBoxPassword' )))->setMailBoxPassword($domainName, $mailBox, $newPassword);
    }
    /**
     * Deletes a MailBox from a webhosting package
     *
     * @param string $domainName the domain name of the webhosting package to remove the MailBox from
     * @param Transip_MailBox $mailBox the mailbox object to remove
     */
    public static function deleteMailBox($domainName, $mailBox)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailBox ), array( '__method' => 'deleteMailBox' )))->deleteMailBox($domainName, $mailBox);
    }
    /**
     * Creates a MailForward for a webhosting package
     *
     * @param string $domainName the domain name of the webhosting package to add the MailForward to
     * @param Transip_MailForward $mailForward The MailForward object to create
     */
    public static function createMailForward($domainName, $mailForward)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailForward ), array( '__method' => 'createMailForward' )))->createMailForward($domainName, $mailForward);
    }
    /**
     * Changes an active MailForward object
     *
     * @param string $domainName the domain name of the webhosting package to modify the MailForward from
     * @param Transip_MailForward $mailForward the MailForward to modify
     */
    public static function modifyMailForward($domainName, $mailForward)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailForward ), array( '__method' => 'modifyMailForward' )))->modifyMailForward($domainName, $mailForward);
    }
    /**
     * Deletes an active MailForward object
     *
     * @param string $domainName the domain name of the webhosting package to delete the MailForward from
     * @param Transip_MailForward $mailForward the MailForward to delete
     */
    public static function deleteMailForward($domainName, $mailForward)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $mailForward ), array( '__method' => 'deleteMailForward' )))->deleteMailForward($domainName, $mailForward);
    }
    /**
     * Creates a new database
     *
     * @param string $domainName the domain name of the webhosting package to create the Db for
     * @param Transip_Db $db Db object to create
     */
    public static function createDatabase($domainName, $db)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $db ), array( '__method' => 'createDatabase' )))->createDatabase($domainName, $db);
    }
    /**
     * Changes a Db object
     *
     * @param string $domainName the domain name of the webhosting package to change the Db for
     * @param Transip_Db $db The db object to modify
     */
    public static function modifyDatabase($domainName, $db)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $db ), array( '__method' => 'modifyDatabase' )))->modifyDatabase($domainName, $db);
    }
    /**
     * Sets A database password for a Db
     *
     * @param string $domainName the domain name of the webhosting package of the Db to change the password for
     * @param Transip_Db $db Modified database object to save
     * @param string $newPassword New password for the database
     */
    public static function setDatabasePassword($domainName, $db, $newPassword)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $db, $newPassword ), array( '__method' => 'setDatabasePassword' )))->setDatabasePassword($domainName, $db, $newPassword);
    }
    /**
     * Deletes a Db object
     *
     * @param string $domainName the domain name of the webhosting package to delete the Db for
     * @param Transip_Db $db Db object to remove
     */
    public static function deleteDatabase($domainName, $db)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $db ), array( '__method' => 'deleteDatabase' )))->deleteDatabase($domainName, $db);
    }
    /**
     * Creates a SubDomain
     *
     * @param string $domainName the domain name of the webhosting package to create the SubDomain for
     * @param Transip_SubDomain $subDomain SubDomain object to create
     */
    public static function createSubdomain($domainName, $subDomain)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $subDomain ), array( '__method' => 'createSubdomain' )))->createSubdomain($domainName, $subDomain);
    }
    /**
     * Deletes a SubDomain
     *
     * @param string $domainName the domain name of the webhosting package to delete the SubDomain for
     * @param Transip_SubDomain $subDomain SubDomain object to delete
     */
    public static function deleteSubdomain($domainName, $subDomain)
    {
        return self::_getsoapclient(array_merge(array( $domainName, $subDomain ), array( '__method' => 'deleteSubdomain' )))->deleteSubdomain($domainName, $subDomain);
    }
}