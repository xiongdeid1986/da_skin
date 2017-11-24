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
 * This class models a ForwardHost
 *
 * @package Transip
 * @class Forward
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_Forward
{
    public $domainName = NULL;
    public $forwardTo = NULL;
    public $forwardMethod = NULL;
    public $frameTitle = NULL;
    public $frameIcon = NULL;
    public $forwardEverything = NULL;
    public $forwardSubdomains = NULL;
    public $forwardEmailTo = NULL;
    const FORWARDMETHOD_DIRECT = 'direct';
    const FORWARDMETHOD_FRAME = 'frame';
    /**
     * Constructs a Forward object.
     *
     * @param string   $domainName         Domain name to forward
     * @param string   $forwardTo          URL to forward to
     * @param string   $forwardMethod      OPTIONAL Method of forwarding; either Forward::FORWARDMETHOD_DIRECT or Forward::FORWARDMETHOD_FRAME
     * @param string   $frameTitle         OPTIONAL Frame title if forwardMethod is set to Forward::FORWARDMETHOD_FRAME
     * @param string   $frameIcon          OPTIONAL Frame favicon if forwardMethod is set to Forward::FORWARDMETHOD_FRAME
     * @param boolean  $forwardEveryThing  OPTIONAL Set to true to forward to preserve the URL info after the domain.
     * @param string   $forwardSubdomains  OPTIONAL Set to true if subdomains should be appended to the target URL.
     * @param string   $forwardEmailTo     OPTIONAL The e-mailaddress all emails to this forward are forwarded to.
     */
    public function __construct($domainName, $forwardTo, $forwardMethod = 'direct', $frameTitle = '', $frameIcon = '', $forwardEveryThing = true, $forwardSubdomains = '', $forwardEmailTo = '')
    {
        $this->domainName = $domainName;
        $this->forwardTo = $forwardTo;
        $this->forwardMethod = $forwardMethod;
        $this->frameTitle = $frameTitle;
        $this->frameIcon = $frameIcon;
        $this->forwardEveryThing = $forwardEveryThing;
        $this->forwardSubdomains = $forwardSubdomains;
        $this->forwardEmailTo = $forwardEmailTo;
    }
}