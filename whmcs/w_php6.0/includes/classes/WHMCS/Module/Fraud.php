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
class WHMCS_Module_Fraud extends WHMCS_Module
{
    protected $type = 'fraud';
    public function getSettings()
    {
        $settings = array(  );
        $result = select_query('tblfraud', '', array( 'fraud' => $this->getLoadedModule() ));
        while( $data = mysql_fetch_array($result) )
        {
            $setting = $data['setting'];
            $value = $data['value'];
            $settings[$setting] = $value;
        }
        return $settings;
    }
    public function call($function, $params = array(  ))
    {
        if( !is_array($params) )
        {
            $params = array(  );
        }
        $params = array_merge($params, $this->getSettings());
        return parent::call($function, $params);
    }
    public function doFraudCheck($orderid, $userid = '', $ip = '')
    {
        include(ROOTDIR . "/includes/countriescallingcodes.php");
        $params = array(  );
        $whmcs = WHMCS_Application::getinstance();
        $params['ip'] = $ip ? $ip : $whmcs->getRemoteIp();
        $params['forwardedip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $userid = (int) $userid;
        if( !$userid )
        {
            $userid = $_SESSION['uid'];
        }
        $clientsdetails = getClientsDetails($userid);
        $countrycode = $clientsdetails['country'];
        $phonenumber = preg_replace("/[^0-9]/", '', $clientsdetails['phonenumber']);
        $params['clientsdetails'] = $clientsdetails;
        $params['clientsdetails']['countrycode'] = $countrycallingcodes[$countrycode];
        $params['clientsdetails']['phonenumber'] = $phonenumber;
        $results = $this->call('doFraudCheck', $params);
        $fraudoutput = '';
        if( $results )
        {
            foreach( $results as $key => $value )
            {
                if( $key != 'userinput' && $key != 'title' && $key != 'description' && $key != 'error' )
                {
                    $fraudoutput .= $key . " => " . $value . "\n";
                }
            }
        }
        update_query('tblorders', array( 'fraudmodule' => $this->getLoadedModule(), 'fraudoutput' => $fraudoutput ), array( 'id' => (int) $orderid ));
        $results['fraudoutput'] = $fraudoutput;
        return $results;
    }
    public function processResultsForDisplay($orderid, $fraudoutput = '')
    {
        if( $orderid && !$fraudoutput )
        {
            $data = get_query_vals('tblorders', 'fraudoutput', array( 'id' => $orderid, 'fraudmodule' => $this->getLoadedModule() ));
            $fraudoutput = $data['fraudoutput'];
        }
        $results = $this->call('processResultsForDisplay', array( 'data' => $fraudoutput ));
        return WHMCS_Input_Sanitize::makesafeforoutput($results);
    }
}