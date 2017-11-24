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
class WHMCS_Order
{
    private $orderid = '';
    private $data = array(  );
    public function __construct()
    {
    }
    public function setID($orderid)
    {
        $this->orderid = (int) $orderid;
        return $this->loadData();
    }
    public function loadData()
    {
        $result = select_query('tblorders', "tblorders.*,tblclients.firstname,tblclients.lastname,tblclients.email,tblclients.companyname,tblclients.address1,tblclients.address2,tblclients.city,tblclients.state,tblclients.postcode,tblclients.country,tblclients.groupid,(SELECT status FROM tblinvoices WHERE id=tblorders.invoiceid) AS invoicestatus", array( "tblorders.id" => $this->orderid ), '', '', '', "tblclients ON tblclients.id=tblorders.userid");
        $data = mysql_fetch_array($result);
        if( !$data['id'] )
        {
            return false;
        }
        $this->data = $data;
        return true;
    }
    public function getData($var = '')
    {
        return array_key_exists($var, $this->data) ? $this->data[$var] : '';
    }
    public function createOrder($userid, $paymentmethod, $contactid = '')
    {
        global $whmcs;
        $order_number = generateUniqueID();
        $this->orderid = insert_query('tblorders', array( 'ordernum' => $order_number, 'userid' => $userid, 'contactid' => $contactid, 'date' => "now()", 'status' => 'Pending', 'paymentmethod' => $paymentmethod, 'ipaddress' => WHMCS_Utility_Environment_CurrentUser::getip() ));
        logActivity("New Order Created - Order ID: " . $orderid . " - User ID: " . $userid);
        return $this->orderid;
    }
    public function updateOrder($data)
    {
    }
}