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
class WHMCS_Billing_Cycles
{
    protected $cycles = array( 'free' => "Free Account", 'onetime' => "One Time", 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'semiannually' => 'Semi-Annually', 'annually' => 'Annually', 'biennially' => 'Biennially', 'triennially' => 'Triennially' );
    public function getSystemBillingCycles()
    {
        $cycles = array(  );
        foreach( $this->cycles as $k => $v )
        {
            $cycles[] = $k;
        }
        return $cycles;
    }
    public function isValidSystemBillingCycle($cycle)
    {
        return in_array($cycle, $this->getSystemBillingCycles());
    }
    public function isValidPublicBillingCycle($cycle)
    {
        return in_array($cycle, $this->getPublicBillingCycles());
    }
    public function getPublicBillingCycles()
    {
        $cycles = array(  );
        foreach( $this->cycles as $k => $v )
        {
            $cycles[] = $v;
        }
        return $cycles;
    }
    public function getBillingCyclesArray()
    {
        return $this->$cycles;
    }
    public function getPublicBillingCycle($cycle)
    {
        $cycles = $this->getBillingCyclesArray();
        return array_key_exists($cycle, $cycles) ? $cycles[$cycle] : '';
    }
}