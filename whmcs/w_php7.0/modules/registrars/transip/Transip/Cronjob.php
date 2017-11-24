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
 * This class models a cronjob
 * that will be run on the Webhosting package for a domain name.
 *
 * @package Transip
 * @class Cronjob
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_Cronjob
{
    public $name = NULL;
    public $url = NULL;
    public $email = NULL;
    public $minuteTrigger = NULL;
    public $hourTrigger = NULL;
    public $dayTrigger = NULL;
    public $monthTrigger = NULL;
    public $weekdayTrigger = NULL;
    /**
     * Create a new cronjob
     *
     * @param string $name Cronjob name
     * @param string $url Cronjob url to fetch
     * @param string $email Mail address to send cron output to
     * @param string $minuteTrigger Minute field for cronjob
     * @param string $hourTrigger Hour field for cronjob
     * @param string $dayTrigger Day field for cronjob
     * @param string $monthTrigger Month field for cronjob
     * @param string $weekdayTrigger Weekday field for cronjob
     */
    public function __construct($name, $url, $email, $minuteTrigger, $hourTrigger, $dayTrigger, $monthTrigger, $weekdayTrigger)
    {
        $this->name = $name;
        $this->url = $url;
        $this->email = $email;
        $this->minuteTrigger = $minuteTrigger;
        $this->hourTrigger = $hourTrigger;
        $this->dayTrigger = $dayTrigger;
        $this->monthTrigger = $monthTrigger;
        $this->weekdayTrigger = $weekdayTrigger;
    }
}