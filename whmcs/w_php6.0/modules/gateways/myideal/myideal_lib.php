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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}

class Security
{
    /**
    *  reads in a certificate file and creates a fingerprint
    *  @param Filename of the certificate
    *  @return fingerprint
    */
    public function createCertFingerprint($filename)
    {
        $fp = fopen(dirname(__FILE__) . '/security/' . $filename, 'r');
        if( !$fp )
        {
            return false;
        }
        $cert = fread($fp, 8192);
        fclose($fp);
        $data = openssl_x509_read($cert);
        if( !openssl_x509_export($data, $data) )
        {
            return false;
        }
        $data = str_replace("-----BEGIN CERTIFICATE-----", '', $data);
        $data = str_replace("-----END CERTIFICATE-----", '', $data);
        $data = base64_decode($data);
        $fingerprint = sha1($data);
        $fingerprint = strtoupper($fingerprint);
        return $fingerprint;
    }
    /**
    * function to sign a message
    * @param filename of the private key
    * @param message to sign
    * @return signature
    */
    public function signMessage($priv_keyfile, $key_pass, $data)
    {
        $data = preg_replace("/\\s/", '', $data);
        $fp = fopen(dirname(__FILE__) . '/security/' . $priv_keyfile, 'r');
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key, $key_pass);
        openssl_sign($data, $signature, $pkeyid);
        openssl_free_key($pkeyid);
        return $signature;
    }
    /**
    * function to verify a message
    * @param filename of the public key to decrypt the signature
    * @param message to verify
    * @param sent signature
    * @return signature
    */
    public function verifyMessage($certfile, $data, $signature)
    {
        $ok = 0;
        $fp = fopen(dirname(__FILE__) . '/security/' . $certfile, 'r');
        if( !$fp )
        {
            return false;
        }
        $cert = fread($fp, 8192);
        fclose($fp);
        $pubkeyid = openssl_get_publickey($cert);
        $ok = openssl_verify($data, $signature, $pubkeyid);
        openssl_free_key($pubkeyid);
        return $ok;
    }
    /**
    * @param fingerprint that´s been sent
    * @param the configuration file loaded in as an array
    * @return the filename of the certificate with this fingerprint
    */
    public function getCertificateName($fingerprint, $config)
    {
        $count = 0;
        $certFilename = $config['CERTIFICATE' . $count];
        while( isset($certFilename) )
        {
            $buff = $this->createCertFingerprint($certFilename);
            if( $fingerprint == $buff )
            {
                return $certFilename;
            }
            $count += 1;
            $certFilename = $config['CERTIFICATE' . $count];
        }
        return false;
    }
}

/**
* This bean class represents an issuer as received by a directory response.
*
*/
class IssuerBean
{
    public $issuerID = '';
    public $issuerName = '';
    public $issuerList = '';
    /**
    * @returns a readable representation of the IssuerBean
    */
    public function toString()
    {
        return "IssuerBean: issuerID=" . $this->issuerID . " issuerName=" . $this->issuerName . " issuerList=" . $this->issuerList;
    }
    /**
    * @return Returns the issuerID.
    */
    public function getIssuerID()
    {
        return $this->issuerID;
    }
    /**
    * @param issuerID The issuerID to set.
    */
    public function setIssuerID($issuerID)
    {
        $this->issuerID = $issuerID;
    }
    /**
    * @return Returns the issuerList. ('Short', 'Long')
    */
    public function getIssuerList()
    {
        return $this->issuerList;
    }
    /**
    * @param issuerList The issuerList to set.
    */
    public function setIssuerList($issuerList)
    {
        $this->issuerList = $issuerList;
    }
    /**
    * @return Returns the issuerName.
    */
    public function getIssuerName()
    {
        return $this->issuerName;
    }
    /**
    * @param issuerName The issuerName to set.
    */
    public function setIssuerName($issuerName)
    {
        $this->issuerName = $issuerName;
    }
}

/**
* This is a base class for all Ideal Requests and should not be instantiated directly.
* It contains some fields that are used by all Requests in iDEAL payment.
*/
class IdealRequest
{
    public $merchantID = '';
    public $subID = '';
    public $authentication = '';
    /**
    * clears all parameters
    */
    public function clear()
    {
        $this->merchantID = '';
        $this->subID = '';
        $this->authentication = '';
    }
    /**
    * @returns a readable representation of the Class
    */
    public function toString()
    {
        return "IdealRequest: merchantID = " . $this->merchantID . " subID = " . $this->subID . " authentication = " . $this->authentication;
    }
    /**
    * this method checks, whether all mandatory properties are set.
    * @return true if all fields are valid, otherwise returns false
    */
    public function checkMandatory()
    {
        if( 0 < strlen($this->merchantID) && 0 < strlen($this->subID) && 0 < strlen($this->authentication) )
        {
            return true;
        }
        return false;
    }
    /**
    * @return Returns the authentication.
    */
    public function getAuthentication()
    {
        return $this->authentication;
    }
    /**
    * @param authentication The type of authentication to set.
    * Currently only 'RSA_SHA1' is implemented. (mandatory)
    */
    public function setAuthentication($authentication)
    {
        $this->authentication = trim($authentication);
    }
    /**
    * @return Returns the merchantID.
    */
    public function getMerchantID()
    {
        return $this->merchantID;
    }
    /**
    * @param merchantID The merchantID to set. (mandatory)
    */
    public function setMerchantID($merchantID)
    {
        $this->merchantID = trim($merchantID);
    }
    /**
    * @return Returns the subID.
    */
    public function getSubID()
    {
        return $this->subID;
    }
    /**
    * @param subID The subID to set. (mandatory)
    */
    public function setSubID($subID)
    {
        $this->subID = trim($subID);
    }
}

class IdealResponse
{
    public $ok = false;
    public $errorMessage = '';
    public $errorCode = '';
    public $errorDetail = '';
    public $suggestedAction = '';
    public $suggestedExpirationPeriod = '';
    public $consumerMessage = '';
    /**
    * @return If an error has ocurred during the previous Request, this method returns a detailed
    * message about what went wrong. isOk() returnes false in that case.
    */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    /**
    * sets the error string
    * @param errorMessage The errorMessage to set.
    */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    public function setErrorDetail($errorDetail)
    {
        $this->errorDetail = $errorDetail;
    }
    public function getErrorDetail()
    {
        return $this->errorDetail;
    }
    public function setSuggestedAction($suggestedAction)
    {
        $this->suggestedAction = $suggestedAction;
    }
    public function getSuggestedAction()
    {
        return $this->suggestedAction;
    }
    public function setSuggestedExpirationPeriod($suggestedExpirationPeriod)
    {
        $this->suggestedExpirationPeriod = $suggestedExpirationPeriod;
    }
    public function getSuggestedExpirationPeriod()
    {
        return $this->suggestedExpirationPeriod;
    }
    public function setConsumerMessage($consumerMessage)
    {
        $this->consumerMessage = $consumerMessage;
    }
    public function getConsumerMessage()
    {
        return $this->consumerMessage;
    }
    /**
    * @return true, if the request was processed successfully, otherwise false. If
    * false, additional information can be received calling getErrorMessage()
    */
    public function isOk()
    {
        return $this->ok;
    }
    /**
    * @param ok sets the OK flag
    */
    public function setOk($ok)
    {
        $this->ok = $ok;
    }
}

/**
* This class encapsulates all data needed for a DirectoryRequest for the iDEAL Payment. To send a Request, an
* Instance has to be created with "new...". After that, all mandatory properties must be set.
* When done, processRequest() of class ThinMPI can be called with this request class.
*
*/
class DirectoryRequest extends IdealRequest
{
    /**
    * clears all parameters
    */
    public function clear()
    {
        IdealRequest::clear();
    }
    /**
    * this method checks, whether all mandatory properties are set.
    * @return true if all fields are valid, otherwise returns false
    */
    public function checkMandatory()
    {
        if( IdealRequest::checkmandatory() )
        {
            return true;
        }
        return false;
    }
}

/**
* This class contains all necessary data that can be returned from a iDEAL DirectoryRequest.
*/
class DirectoryResponse extends IdealResponse
{
    public $acquirerID = '';
    public $issuerList = array(  );
    /**
    * @return Returns a list if IssuerBean objects.
    * The List contains all Issuers that were send by the acquirer System during the Directory Request.
    * The Issuers are stored as IssuerBean objects.
    */
    public function getIssuerList()
    {
        return $this->issuerList;
    }
    /**
    * @return Returns the acquirerID from the answer XML message.
    */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }
    /**
    * @param sets the acquirerID
    */
    public function setAcqirerID($acquirerID)
    {
        $this->acquirerID = $acquirerID;
    }
    /**
    * adds an Issuer to the IssuerList
    */
    public function addIssuer($bean)
    {
        if( is_a($bean, 'IssuerBean') )
        {
            array_push($this->issuerList, $bean);
        }
    }
}

/**
* This class encapsulates all data needed for a AcquirerStatusRequest for the iDEAL Payment. To send a Request, an
* Instance has to be created with "new...". After that, all mandatory properties must be set.
* When done, processRequest() of class ThinMPI can be called with this request class.
*/
class AcquirerStatusRequest extends IdealRequest
{
    public $transactionID = '';
    /**
    * rests all input data to empty strings
    */
    public function clear()
    {
        IdealRequest::clear();
        $this->transactionID = '';
    }
    /**
    * this method checks, wheather all mandatory properties were set.
    * If done so, true is returned, otherwise false.
    * @return If done so, true is returned, otherwise false.
    */
    public function checkMandatory()
    {
        if( IdealRequest::checkmandatory() && 0 < strlen($this->transactionID) )
        {
            return true;
        }
        return false;
    }
    /**
    * @returns a readable representation of the Class
    */
    public function toString()
    {
        return IdealRequest::tostring() . " AcquirerStatusRequest: transactionID = " . $this->transactionID;
    }
    /**
    * @return Returns the transactionID.
    */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    /**
    * @param transactionID The transactionID of the corresponding transaction. (mandatory)
    */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;
    }
}

/**
* This class contains all necessary data that can be returned from a iDEAL AcquirerTrxRequest.
*/
class AcquirerStatusResponse extends IdealResponse
{
    public $authenticated = false;
    public $consumerName = '';
    public $consumerAccountNumber = '';
    public $consumerCity = '';
    public $transactionID = '';
    public $status = '';
    /**
    * @return Returns true, if the transaction was authenticated, otherwise false.
    */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }
    /**
    * @param authenticated The authenticated flag to be set.
    */
    public function setAuthenticated($authenticated)
    {
        $this->authenticated = $authenticated;
    }
    /**
    * @return Returns the consumerAccountNumber.
    */
    public function getConsumerAccountNumber()
    {
        return $this->consumerAccountNumber;
    }
    /**
    * @param consumerAccountNumber The consumerAccountNumber to set.
    */
    public function setConsumerAccountNumber($consumerAccountNumber)
    {
        $this->consumerAccountNumber = $consumerAccountNumber;
    }
    /**
    * @return Returns the consumerCity.
    */
    public function getConsumerCity()
    {
        return $this->consumerCity;
    }
    /**
    * @param consumerCity The consumerCity to set.
    */
    public function setConsumerCity($consumerCity)
    {
        $this->consumerCity = $consumerCity;
    }
    /**
    * @return Returns the consumerName.
    */
    public function getConsumerName()
    {
        return $this->consumerName;
    }
    /**
    * @param consumerName The consumerName to set.
    */
    public function setConsumerName($consumerName)
    {
        $this->consumerName = $consumerName;
    }
    /**
    * @return Returns the transactionID.
    */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    /**
    * @param transactionID The transactionID to set.
    */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;
    }
    /**
    * @return Returns the status.
    */
    public function getStatus()
    {
        return $this->status;
    }
    /**
    * @param status The status to set.
    */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}

/**
* This class encapsulates all data needed for a AcquirerTrxRequest for the iDEAL Payment. To send a Request, an
* Instance has to be created with "new...". After that, all mandatory properties must be set.
* When done, processRequest() of class ThinMPI can be called with this request class.
*/
class AcquirerTrxRequest extends IdealRequest
{
    public $issuerID = '';
    public $merchantReturnURL = '';
    public $purchaseID = '';
    public $amount = '';
    public $currency = '';
    public $expirationPeriod = '';
    public $language = '';
    public $description = '';
    public $entranceCode = '';
    /**
    * @return Returns the amount.
    */
    public function getAmount()
    {
        return $this->amount;
    }
    /**
    * @param amount The amount to set. (mandatory)
    */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
    /**
    * @return Returns the currency.
    */
    public function getCurrency()
    {
        return $this->currency;
    }
    /**
    * @param currency The currency to set, e.g. 'EUR'. (mandatory)
    */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
    /**
    * @return Returns the payment description.
    */
    public function getDescription()
    {
        return $this->description;
    }
    /**
    * @param description The payment description to set. (optional)
    */
    public function setDescription($description)
    {
        if( $description != null )
        {
            $this->description = $description;
        }
    }
    /**
    * @return Returns the entranceCode.
    */
    public function getEntranceCode()
    {
        return $this->entranceCode;
    }
    /**
    * @param entranceCode The entranceCode to set. (mandatory)
    */
    public function setEntranceCode($entranceCode)
    {
        $this->entranceCode = $entranceCode;
    }
    /**
    * @return Returns the expirationPeriod.
    */
    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }
    /**
    * @param expirationPeriod The expirationPeriod to set. (mandatory)
    */
    public function setExpirationPeriod($expirationPeriod)
    {
        $this->expirationPeriod = $expirationPeriod;
    }
    /**
    * @return Returns the issuerID.
    */
    public function getIssuerID()
    {
        return $this->issuerID;
    }
    /**
    * @param issuerID The issuerID to set. (mandatory)
    */
    public function setIssuerID($issuerID)
    {
        $this->issuerID = $issuerID;
    }
    /**
    * @return Returns the language.
    */
    public function getLanguage()
    {
        return $this->language;
    }
    /**
    * @param language The language to set, e.g 'nl'. (mandatory)
    */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
    /**
    * @return Returns the merchantReturnURL.
    */
    public function getMerchantReturnURL()
    {
        return $this->merchantReturnURL;
    }
    /**
    * @param merchantReturnURL The merchantReturnURL to set. (mandatory)
    */
    public function setMerchantReturnURL($merchantReturnURL)
    {
        $this->merchantReturnURL = $merchantReturnURL;
    }
    /**
    * @return Returns the purchaseID.
    */
    public function getPurchaseID()
    {
        return $this->purchaseID;
    }
    /**
    * @param purchaseID The purchaseID to set. (mandatory)
    */
    public function setPurchaseID($purchaseID)
    {
        $this->purchaseID = $purchaseID;
    }
    public function clear()
    {
        IdealRequest::clear();
        $this->issuerID = '';
        $this->merchantReturnURL = '';
        $this->purchaseID = '';
        $this->amount = '';
        $this->currency = '';
        $this->expirationPeriod = '';
        $this->language = '';
        $this->description = '';
        $this->entranceCode = '';
    }
    /**
    * this method checks, whether all mandatory properties were set.
    * If done so, true is returned, otherwise false.
    * @return If done so, true is returned, otherwise false.
    */
    public function checkMandatory()
    {
        if( IdealRequest::checkmandatory() == true && 0 < strlen($this->issuerID) && 0 < strlen($this->merchantReturnURL) && 0 < strlen($this->purchaseID) && 0 < strlen($this->amount) && 0 < strlen($this->currency) && 0 < strlen($this->expirationPeriod) && 0 < strlen($this->language) && 0 < strlen($this->entranceCode) && 0 < strlen($this->description) )
        {
            return true;
        }
        return false;
    }
    /**
    * @returns a readable representation of the Class
    */
    public function toString()
    {
        return IdealRequest::tostring() . " AcquirerTrxRequest: issuerID = " . $this->issuerID . " merchantReturnURL = " . $this->merchantReturnURL . " purchaseID = " . $this->purchaseID . " amount = " . $this->amount . " currency = " . $this->currency . " expirationPeriod = " . $this->expirationPeriod . " language = " . $this->language . " entranceCode = " . $this->entranceCode . " description = " . $this->description;
    }
}

class AcquirerTrxResponse extends IdealResponse
{
    public $acquirerID = NULL;
    public $issuerAuthenticationURL = NULL;
    public $transactionID = NULL;
    /**
    * @return Returns the acquirerID.
    */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }
    /**
    * @param acquirerID The acquirerID to set. (mandatory)
    */
    public function setAcquirerID($acquirerID)
    {
        $this->acquirerID = $acquirerID;
    }
    /**
    * @return Returns the issuerAuthenticationURL.
    */
    public function getIssuerAuthenticationURL()
    {
        return $this->issuerAuthenticationURL;
    }
    /**
    * @param issuerAuthenticationURL The issuerAuthenticationURL to set.
    */
    public function setIssuerAuthenticationURL($issuerAuthenticationURL)
    {
        $this->issuerAuthenticationURL = $issuerAuthenticationURL;
    }
    /**
    * @return Returns the transactionID.
    */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    /**
    * @param transactionID The transactionID to set.
    */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;
    }
}
function LoadConfiguration()
{
    $myideal_conf = array(  );
    require(dirname(__FILE__) . "/../../../configuration.php");
    $whmcsmysql = @mysql_connect($db_host, $db_username, $db_password);
    @mysql_select_db($db_name) or exit( "Could not connect to the database" );
    $testmode = false;
    $acquirerurl = "ssl://ideal.secure-ing.com:443/ideal/iDeal";
    $acquirertesturl = "ssl://idealtest.secure-ing.com:443/ideal/iDeal";
    $authenticationtype = 'SHA1_RSA';
    $res = full_query("SELECT setting, value FROM tblpaymentgateways WHERE gateway='myideal'");
    while( $row = mysql_fetch_array($res) )
    {
        $setting = $row['setting'];
        switch( $setting )
        {
            case 'merchantid':
                $myideal_conf['MERCHANTID'] = $row['value'];
                break;
            case 'subid':
                $myideal_conf['SUBID'] = $row['value'];
                break;
            case 'privatekey':
                $myideal_conf['PRIVATEKEY'] = $row['value'];
                break;
            case 'privatekeypass':
                $myideal_conf['PRIVATEKEYPASS'] = $row['value'];
                break;
            case 'privatecert':
                $myideal_conf['PRIVATECERT'] = $row['value'];
                break;
            case 'certificate0':
                $myideal_conf['CERTIFICATE0'] = $row['value'];
                break;
            case 'acquirertimeout':
                $myideal_conf['ACQUIRERTIMEOUT'] = $row['value'];
                break;
            case 'currency':
                $myideal_conf['CURRENCY'] = $row['value'];
                break;
            case 'expirationperiod':
                $myideal_conf['EXPIRATIONPERIOD'] = $row['value'];
                break;
            case 'language':
                $myideal_conf['LANGUAGE'] = $row['value'];
                break;
            case 'description':
                $myideal_conf['DESCRIPTION'] = $row['value'];
                break;
            case 'entrancecode':
                $myideal_conf['ENTRANCECODE'] = $row['value'];
                break;
            case 'logfile':
                $myideal_conf['LOGFILE'] = $row['value'];
                break;
            case 'testmode':
                if( $row['value'] == 'on' )
                {
                    $testmode = true;
                }
        }
        break;
    }
    if( $testmode == true )
    {
        $myideal_conf['ACQUIRERURL'] = $acquirertesturl;
    }
    else
    {
        $myideal_conf['ACQUIRERURL'] = $acquirerurl;
    }
    $myideal_conf['AUTHENTICATIONTYPE'] = $authenticationtype;
    $res = full_query("SELECT value FROM tblconfiguration WHERE setting='SystemURL'");
    $row = mysql_fetch_array($res);
    $systemurl = $row[0];
    $myideal_conf['MERCHANTRETURNURL'] = $systemurl . "/modules/gateways/myideal/StatReq.php";
    return $myideal_conf;
}