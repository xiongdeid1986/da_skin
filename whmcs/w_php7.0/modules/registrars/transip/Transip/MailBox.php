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
 * This class models a mailbox
 *
 * @package Transip
 * @class MailBox
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_MailBox
{
    public $address = NULL;
    public $spamCheckerStrength = NULL;
    public $maxDiskUsage = NULL;
    public $hasVacationReply = NULL;
    public $vacationReplySubject = NULL;
    public $vacationReplyMessage = NULL;
    const SPAMCHECKER_STRENGTH_AVERAGE = 'AVERAGE';
    const SPAMCHECKER_STRENGTH_OFF = 'OFF';
    const SPAMCHECKER_STRENGTH_LOW = 'LOW';
    const SPAMCHECKER_STRENGTH_HIGH = 'HIGH';
    /**
     * Create new mailbox
     *
     * @param string $address the address of this MailBox
     * @param string $spamCheckerStrength One of the Transip_MailBox::SPAMCHECKER_STRENGTH_* constants.
     * @param int $maxDiskUsage max mailbox size in megabytes
     * @param boolean $hasVacationReply does MailBox has vacationreply
     * @param string $vacationReplySubject Subject of vacation reply
     * @param string $vacationReplyMessage Message of vacation reply
     */
    public function __construct($address, $spamCheckerStrength = 'AVERAGE', $maxDiskUsage = 20, $hasVacationReply = false, $vacationReplySubject = '', $vacationReplyMessage = '')
    {
        $this->address = $address;
        $this->spamCheckerStrength = $spamCheckerStrength;
        $this->maxDiskUsage = $maxDiskUsage;
        $this->hasVacationReply = $hasVacationReply;
        $this->vacationReplySubject = $vacationReplySubject;
        $this->vacationReplyMessage = $vacationReplyMessage;
    }
}