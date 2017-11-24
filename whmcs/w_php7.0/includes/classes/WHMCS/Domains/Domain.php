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
 * Model of an individual domain name
 *
 * @todo implement an isValid function and validate domain on instantiation
 * @todo throw exceptions upon an invalid domain being encountered
 * @todo consider adding a getWHOIS method
 */
class WHMCS_Domains_Domain
{
    protected $secondLevel = NULL;
    protected $topLevel = NULL;
    protected $IDNSecondLevel = NULL;
    /**
     * Initialise class with a domain name to work with
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->setDomain($domain);
    }
    /**
     * Set the domain name to work with
     *
     * @todo Add validation of the incoming domain param and throw exception if invalid
     *
     * @param string $domain
     */
    protected function setDomain($domain)
    {
        $whmcs = WHMCS_Application::getinstance();
        $parts = explode(".", $domain, 2);
        $this->secondLevel = $parts[0];
        $this->topLevel = $parts[1];
        $whmcs->load_function('whois');
        $idnConverter = new WHMCS_Domains_Idna();
        $this->IDNSecondLevel = $idnConverter->encode($parts[0]);
    }
    /**
     * Get the Second Level Domain
     *
     * @return string
     */
    public function getSLD()
    {
        return $this->secondLevel;
    }
    /**
     * Get the Top Level Domain
     *
     * @return string
     */
    public function getTLD()
    {
        return $this->topLevel;
    }
    /**
     * Get the domain name to work with
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->getSLD() . "." . $this->getTLD();
    }
    /**
     * Get the Last Segment of the Top Level Domain
     *
     * @return string
     */
    public function getLastTLDSegment()
    {
        $tld = $this->getTLD();
        $tldparts = explode(".", $tld);
        return $tldparts[count($tldparts) - 1];
    }
    /**
     * Get the IDN Encoded value for the SLD.
     *
     * This can be the same as the SLD when the domain is not an IDN.
     *
     * @return string The IDN encoded domain value.
     */
    public function getIDNSecondLevel()
    {
        return $this->IDNSecondLevel;
    }
    /**
     * Check if the domain is an IDN domain
     *
     * @return bool
     */
    public function isIDN()
    {
        if( $this->getSLD() == $this->getIDNSecondLevel() )
        {
            return false;
        }
        return true;
    }
}