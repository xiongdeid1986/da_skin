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
 * This class models a Domain
 *
 * @package Transip
 * @class Domain
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_Domain
{
    public $name = '';
    public $nameservers = array(  );
    public $contacts = array(  );
    public $dnsEntries = array(  );
    public $branding = NULL;
    public $authCode = '';
    public $isLocked = false;
    public $registrationDate = '';
    public $renewalDate = '';
    /**
     * Constructs a new Domain
     *
     * @param string $name the domain name of the domain, including tld
     * @param Nameserver[] $nameservers the list of nameservers (with optional gluerecords) for this domain
     * @param WhoisContact[] $contacts the list of WhoisContacts for this domain
     * @param DnsEntry[] $dnsEntries the list of DnsEntries for this domain
     * @param DomainBranding $branding the branding for this domain, see the branding property for more info
     */
    public function __construct($name, $nameservers = array(  ), $contacts = array(  ), $dnsEntries = array(  ), $branding = null)
    {
        $this->name = $name;
        $this->nameservers = $nameservers;
        $this->contacts = $contacts;
        $this->dnsEntries = $dnsEntries;
        $this->branding = $branding;
    }
}