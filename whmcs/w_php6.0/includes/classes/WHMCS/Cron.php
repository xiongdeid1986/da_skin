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
class WHMCS_Cron
{
    private $incli = false;
    private $debugmode = false;
    private $lasttime = '';
    private $lastmemory = '';
    private $lastaction = '';
    private $log = array(  );
    private $emaillog = array(  );
    private $emailsublog = array(  );
    private $args = array(  );
    private $doonly = false;
    private $validactions = array(  );
    private $starttime = '';
    private $sendreport = true;
    public function __construct()
    {
    }
    public static function init()
    {
        $obj = new WHMCS_Cron();
        $obj->incli = $obj->isRunningInCLI();
        $obj->validactions = $obj->getValidActions();
        $args = $obj->fetchArgs();
        if( in_array('debug', $args) )
        {
            $obj->setDebugMode(true);
        }
        else
        {
            $obj->setDebugMode(false);
        }
        if( in_array('skip_report', $args) )
        {
            $obj->sendreport = false;
        }
        $obj->determineRunMode();
        $obj->starttime = time();
        return $obj;
    }
    public function getValidActions()
    {
        $validactions = array( 'updaterates' => "Updating Currency Exchange Rates", 'updatepricing' => "Updating Product Pricing for Current Exchange Rates", 'invoices' => "Generating Invoices", 'latefees' => "Applying Late Fees", 'ccprocessing' => "Processing Credit Card Charges", 'invoicereminders' => "Processing Invoice Reminder Notices", 'domainrenewalnotices' => "Processing Domain Renewal Notices", 'suspensions' => "Processing Overdue Suspensions", 'terminations' => "Processing Overdue Terminations", 'fixedtermterminations' => "Performing Automated Fixed Term Service Terminations", 'cancelrequests' => "Processing Cancellation Requests", 'closetickets' => "Auto Closing Inactive Tickets", 'affcommissions' => "Processing Delayed Affiliate Commissions", 'affreports' => "Sending Affiliate Reports", 'emailmarketing' => "Processing Email Marketer Rules", 'ccexpirynotices' => "Sending Credit Card Expiry Reminders", 'usagestats' => "Updating Disk & Bandwidth Usage Stats", 'overagesbilling' => "Processing Overage Billing Charges", 'clientstatussync' => "Performing Client Status Sync", 'backups' => "Database Backup", 'report' => "Sending Email Report" );
        return $validactions;
    }
    public function isRunningInCLI()
    {
        return php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']);
    }
    public function fetchArgs()
    {
        if( $this->incli )
        {
            $this->args = $_SERVER['argv'];
        }
        else
        {
            foreach( $this->validactions as $action => $name )
            {
                if( array_key_exists('skip_' . $action, $_REQUEST) )
                {
                    $this->args[] = 'skip_' . $action;
                }
                if( array_key_exists('do_' . $action, $_REQUEST) )
                {
                    $this->args[] = 'do_' . $action;
                }
            }
        }
        return $this->args;
    }
    public function setDebugMode($state = false)
    {
        $this->debugmode = $state ? true : false;
        if( $state )
        {
            error_reporting(E_ALL ^ E_NOTICE);
        }
        else
        {
            error_reporting(0);
        }
    }
    public function determineRunMode()
    {
        foreach( $this->args as $arg )
        {
            if( substr($arg, 0, 3) == 'do_' )
            {
                $this->doonly = true;
                return true;
            }
        }
        return false;
    }
    public function raiseLimits()
    {
        $minimumMemoryLimit = '512M';
        $currentMemoryLimit = ini_get('memory_limit');
        if( preg_match("/G\$/", $currentMemoryLimit) )
        {
            $currentMemoryLimit = $currentMemoryLimit * 1024 * 1024;
        }
        else
        {
            if( preg_match("/M\$/", $currentMemoryLimit) )
            {
                $currentMemoryLimit = $currentMemoryLimit * 1024;
            }
            else
            {
                if( !preg_match("/K\$/", $currentMemoryLimit) )
                {
                    $currentMemoryLimit = round($currentMemoryLimit / 1024, 0);
                }
            }
        }
        if( (int) $currentMemoryLimit < (int) $minimumMemoryLimit * 1024 )
        {
            @ini_set('memory_limit', $minimumMemoryLimit);
        }
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
    }
    public function isScheduled($action)
    {
        if( !array_key_exists($action, $this->validactions) )
        {
            return false;
        }
        $this->emailsublog = array(  );
        $this->lastaction = $action;
        if( $this->doonly )
        {
            if( in_array('do_' . $action, $this->args) )
            {
                $this->logAction();
                return true;
            }
            $this->logAction(false, true);
            return false;
        }
        if( in_array('skip_' . $action, $this->args) )
        {
            $this->logAction(false, true);
            return false;
        }
        $this->logAction();
        return true;
    }
    private function logAction($end = false, $skip = false)
    {
        $action = $this->validactions[$this->lastaction];
        $prefix = 'Starting';
        if( $end )
        {
            $prefix = 'Completed';
        }
        if( $skip )
        {
            $prefix = 'Skipping';
        }
        $this->logActivity($prefix . " " . $action);
        return true;
    }
    public function logActivity($msg, $sub = false)
    {
        logActivity("Cron Job: " . $msg);
        if( $sub )
        {
            $msg = " - " . $msg;
        }
        $this->log($msg);
        return true;
    }
    public function logActivityDebug($msg)
    {
        $this->log($msg, 1);
        return true;
    }
    public function log($msg, $verbose = 0)
    {
        if( $this->debugmode )
        {
            $time = microtime();
            $memory = $this->getMemUsage();
            $timediff = round($time - $this->lasttime, 2);
            $memdiff = round($memory - $this->lastmemory, 2);
            $msg .= " (Time: " . $timediff . " Memory: " . $memory . ")";
            $this->lasttime = $time;
            $this->lastmemory = $memory;
        }
        if( $this->incli )
        {
            echo $msg . "\n";
        }
        if( !$verbose )
        {
            $this->log[] = $msg;
        }
    }
    private function getMemUsage()
    {
        return round(memory_get_peak_usage() / (1024 * 1024), 2);
    }
    public function logmemusage($line)
    {
        $this->log("Memory Usage @ Line " . $line . ": " . $this->getMemUsage());
    }
    public function emailLog($msg)
    {
        $this->emaillog[] = $msg;
        if( count($this->emailsublog) )
        {
            foreach( $this->emailsublog as $entry )
            {
                $this->emaillog[] = " - " . $entry;
            }
        }
        $this->emaillog[] = '';
    }
    public function emailLogSub($msg)
    {
        $this->emailsublog[] = $msg;
        $this->logActivity($msg, true);
    }
    public function emailReport()
    {
        if( $this->sendreport )
        {
            $cronreport = "Cron Job Report for " . date("l jS F Y @ H:i:s", $this->starttime) . "<br /><br />";
            foreach( $this->emaillog as $logentry )
            {
                $cronreport .= $logentry . "<br />";
            }
            sendAdminNotification('system', "WHMCS Cron Job Activity", $cronreport);
        }
        else
        {
            $this->logActivity("Skipped sending email report due to skip_report flag");
        }
    }
}