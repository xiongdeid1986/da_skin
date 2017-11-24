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
 * WHMCS Clients Management Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Client
{
    private $userid = '';
    public function __construct($userid)
    {
        $this->setID($userid);
        return $this;
    }
    public function setID($userid)
    {
        $this->userid = $userid;
        return true;
    }
    public function getID()
    {
        return (int) $this->userid;
    }
    public function getUneditableClientProfileFields()
    {
        global $whmcs;
        return explode(',', $whmcs->get_config('ClientsProfileUneditableFields'));
    }
    public function isEditableField($field)
    {
        $uneditablefields = defined('CLIENTAREA') ? $this->getUneditableClientProfileFields() : array(  );
        return !in_array($field, $uneditablefields) ? true : false;
    }
    public function getDetails($contactid = '')
    {
        $countries = $countrycallingcodes = array(  );
        include(ROOTDIR . "/includes/countries.php");
        include(ROOTDIR . "/includes/countriescallingcodes.php");
        if( !function_exists('convertStateToCode') )
        {
            require(ROOTDIR . "/includes/clientfunctions.php");
        }
        if( !function_exists('getCustomFields') )
        {
            require(ROOTDIR . "/includes/customfieldfunctions.php");
        }
        $result = select_query('tblclients', '', array( 'id' => $this->getID() ));
        $data = mysql_fetch_array($result);
        if( !isset($data['id']) )
        {
            return false;
        }
        if( $contactid == 'billing' )
        {
            $contactid = $data['billingcid'];
        }
        else
        {
            $contactid = (int) $contactid;
        }
        if( 0 < $contactid )
        {
            $result = select_query('tblcontacts', '', array( 'userid' => $this->getID(), 'id' => $contactid ));
            if( isset($data['id']) )
            {
                $data = array_merge($data, mysql_fetch_array($result));
                $data['id'] = $this->getID();
            }
            else
            {
                update_query('tblclients', array( 'billingcid' => '' ), array( 'id' => $this->getID() ));
            }
        }
        $details = array(  );
        $details['userid'] = $data['id'];
        $details['id'] = $details['userid'];
        $details['firstname'] = $data['firstname'];
        $details['lastname'] = $data['lastname'];
        $details['fullname'] = $data['firstname'] . " " . $data['lastname'];
        $details['companyname'] = $data['companyname'];
        $details['email'] = $data['email'];
        $details['address1'] = $data['address1'];
        $details['address2'] = $data['address2'];
        $details['city'] = $data['city'];
        $details['fullstate'] = $data['state'];
        $details['state'] = $details['fullstate'];
        $details['postcode'] = $data['postcode'];
        $details['countrycode'] = $data['country'];
        $details['country'] = $details['countrycode'];
        if( $details['country'] == 'GB' )
        {
            $postcode = $origpostcode = $details['postcode'];
            $postcode = strtoupper($postcode);
            $postcode = preg_replace("/[^A-Z0-9]/", '', $postcode);
            if( strlen($postcode) == 5 )
            {
                $postcode = substr($postcode, 0, 2) . " " . substr($postcode, 2);
            }
            else
            {
                if( strlen($postcode) == 6 )
                {
                    $postcode = substr($postcode, 0, 3) . " " . substr($postcode, 3);
                }
                else
                {
                    if( strlen($postcode) == 7 )
                    {
                        $postcode = substr($postcode, 0, 4) . " " . substr($postcode, 4);
                    }
                    else
                    {
                        $postcode = $origpostcode;
                    }
                }
            }
            $postcode = trim($postcode);
            $details['postcode'] = $postcode;
        }
        $details['statecode'] = convertStateToCode($details['state'], $details['country']);
        $details['countryname'] = $countries[$data['country']];
        $details['phonecc'] = $countrycallingcodes[$data['country']];
        $details['phonenumber'] = $data['phonenumber'];
        $phonedigits = preg_replace("/[^0-9]/", '', $data['phonenumber']);
        $phonedigits = ltrim($phonedigits, '0');
        $details['phonenumberformatted'] = $phonedigits ? "+" . $details['phonecc'] . "." . $phonedigits : '';
        $details['billingcid'] = $data['billingcid'];
        $details['notes'] = $data['notes'];
        $details['password'] = $data['password'];
        $details['twofaenabled'] = $data['authmodule'] ? true : false;
        $details['currency'] = $data['currency'];
        $details['defaultgateway'] = $data['defaultgateway'];
        $details['cctype'] = $data['cardtype'];
        $details['cclastfour'] = $data['cardlastfour'];
        $details['securityqid'] = $data['securityqid'];
        $details['securityqans'] = decrypt($data['securityqans']);
        $details['groupid'] = $data['groupid'];
        $details['status'] = $data['status'];
        $details['credit'] = $data['credit'];
        $details['taxexempt'] = $data['taxexempt'];
        $details['latefeeoveride'] = $data['latefeeoveride'];
        $details['overideduenotices'] = $data['overideduenotices'];
        $details['separateinvoices'] = $data['separateinvoices'];
        $details['disableautocc'] = $data['disableautocc'];
        $details['emailoptout'] = $data['emailoptout'];
        $details['overrideautoclose'] = $data['overrideautoclose'];
        $details['language'] = $data['language'];
        $lastlogin = $data['lastlogin'];
        $details['lastlogin'] = $lastlogin == "0000-00-00 00:00:00" ? "No Login Logged" : "Date: " . fromMySQLDate($lastlogin, 'time') . "<br>IP Address: " . $data['ip'] . "<br>Host: " . $data['host'];
        $i = 1;
        $customfields = getCustomFields('client', '', $this->getID(), 'on');
        foreach( $customfields as $value )
        {
            $details['customfields' . $i] = $value['value'];
            $details['customfields'][] = array( 'id' => $value['id'], 'value' => $value['value'] );
            $i++;
        }
        if( $contactid )
        {
            $details['domainemails'] = $data['domainemails'];
            $details['generalemails'] = $data['generalemails'];
            $details['invoiceemails'] = $data['invoiceemails'];
            $details['productemails'] = $data['productemails'];
            $details['supportemails'] = $data['supportemails'];
        }
        return $details;
    }
    public function getCurrency()
    {
        return getCurrency($this->getID());
    }
    public function updateClient()
    {
        global $whmcs;
        $exinfo = $this->getDetails();
        if( defined('ADMINAREA') )
        {
            $updatefieldsarray = array(  );
        }
        else
        {
            $updatefieldsarray = array( 'firstname' => "First Name", 'lastname' => "Last Name", 'companyname' => "Company Name", 'email' => "Email Address", 'address1' => "Address 1", 'address2' => "Address 2", 'city' => 'City', 'state' => 'State', 'postcode' => 'Postcode', 'country' => 'Country', 'phonenumber' => "Phone Number", 'billingcid' => "Billing Contact" );
            if( $whmcs->get_config('AllowClientsEmailOptOut') )
            {
                $updatefieldsarray['emailoptout'] = "Newsletter Email Opt Out";
            }
        }
        $changelist = array(  );
        $updateqry = array(  );
        foreach( $updatefieldsarray as $field => $displayname )
        {
            if( $this->isEditableField($field) )
            {
                $value = $whmcs->get_req_var($field);
                if( $field == 'emailoptout' && !$value )
                {
                    $value = '0';
                }
                $updateqry[$field] = $value;
                if( $value != $exinfo[$field] )
                {
                    $changelist[] = $displayname . ": '" . $exinfo[$field] . "' to '" . $value . "'";
                }
            }
        }
        update_query('tblclients', $updateqry, array( 'id' => $this->getID() ));
        $old_customfieldsarray = getCustomFields('client', '', $this->getID(), '', '');
        $customfields = getCustomFields('client', '', $this->getID(), '', '');
        foreach( $customfields as $v )
        {
            $k = $v['id'];
            $customfieldsarray[$k] = $_POST['customfield'][$k];
        }
        saveCustomFields($this->getID(), $customfieldsarray);
        $paymentmethod = $whmcs->get_req_var('paymentmethod');
        clientChangeDefaultGateway($this->getID(), $paymentmethod);
        if( $paymentmethod != $exinfo['defaultgateway'] )
        {
            $changelist[] = "Default Payment Method: '" . getGatewayName($exinfo['defaultgateway']) . "' to '" . getGatewayName($paymentmethod) . "'\n";
        }
        run_hook('ClientEdit', array_merge(array( 'userid' => $this->getID(), 'olddata' => $exinfo ), $updateqry));
        if( !defined('ADMINAREA') && $whmcs->get_config('SendEmailNotificationonUserDetailsChange') )
        {
            foreach( $old_customfieldsarray as $values )
            {
                if( $values['value'] != $_POST['customfield'][$values['id']] )
                {
                    $changelist[] = $values['name'] . ": '" . $values['value'] . "' to '" . $_POST['customfield'][$values['id']] . "'";
                }
            }
            if( 0 < count($changelist) )
            {
                $adminurl = $whmcs->get_config('SystemSSLURL') ? $whmcs->get_config('SystemSSLURL') : $whmcs->get_config('SystemURL');
                $adminurl .= '/' . $whmcs->get_admin_folder_name() . "/clientssummary.php?userid=" . $this->getID();
                sendAdminNotification('account', "WHMCS User Details Change", "<p>Client ID: <a href=\"" . $adminurl . "\">" . $this->getID() . " - " . $exinfo['firstname'] . " " . $exinfo['lastname'] . "</a> has requested to change his/her details as indicated below:<br><br>" . implode("<br />\n", $changelist) . "<br>If you are unhappy with any of the changes, you need to login and revert them - this is the only record of the old details.</p><p>This change request was submitted from " . WHMCS_Utility_Environment_CurrentUser::getiphost() . " (" . WHMCS_Utility_Environment_CurrentUser::getip() . ")</p>");
                logActivity("Client Profile Modified - " . implode(", ", $changelist) . " - User ID: " . $this->getID());
            }
        }
        return true;
    }
    public function getContactsWithAddresses()
    {
        $where = array(  );
        $where['userid'] = $this->userid;
        $where['address1'] = array( 'sqltype' => 'NEQ', 'value' => '' );
        return $this->getContactsData($where);
    }
    public function getContacts()
    {
        $where = array(  );
        $where['userid'] = $this->userid;
        return $this->getContactsData($where);
    }
    private function getContactsData($where)
    {
        $contactsarray = array(  );
        $result = select_query('tblcontacts', 'id,firstname,lastname,email', $where, "firstname` ASC,`lastname", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $contactsarray[] = array( 'id' => $data['id'], 'name' => $data['firstname'] . " " . $data['lastname'], 'email' => $data['email'] );
        }
        return $contactsarray;
    }
    public function getContact($contactid)
    {
        $result = select_query('tblcontacts', '', array( 'userid' => $this->userid, 'id' => $contactid ));
        $data = mysql_fetch_assoc($result);
        $data['permissions'] = explode(',', $data['permissions']);
        return isset($data['id']) ? $data : false;
    }
    public function deleteContact($contactid)
    {
        delete_query('tblcontacts', array( 'userid' => $this->userid, 'id' => $contactid ));
        update_query('tblclients', array( 'billingcid' => '' ), array( 'billingcid' => $contactid, 'id' => $this->userid ));
        run_hook('ContactDelete', array( 'userid' => $this->userid, 'contactid' => $contactid ));
        return true;
    }
    public function getFiles()
    {
        $where = array( 'userid' => $this->userid );
        if( !defined('ADMINAREA') )
        {
            $where['adminonly'] = '';
        }
        $files = array(  );
        $result = select_query('tblclientsfiles', '', $where, 'title', 'ASC');
        while( $data = mysql_fetch_assoc($result) )
        {
            $id = $data['id'];
            $title = $data['title'];
            $adminonly = $data['adminonly'];
            $filename = $data['filename'];
            $filename = substr($filename, 11);
            $date = fromMySQLDate($data['dateadded'], 0, 1);
            $files[] = array( 'id' => $id, 'date' => $date, 'title' => $title, 'adminonly' => $adminonly, 'filename' => $filename );
        }
        return $files;
    }
    public function resetSendPW()
    {
        sendMessage("Automated Password Reset", $this->userid);
        return true;
    }
    public function sendEmailTpl($tplname)
    {
        return sendMessage($tplname, $this->userid);
    }
    public function getEmailTemplates()
    {
        $emailtpls = array(  );
        $query = "SELECT * FROM tblemailtemplates WHERE type='general' AND language='' AND name!='Password Reset Validation' ORDER BY name ASC";
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $messagename = $data['name'];
            $custom = $data['custom'];
            $emailtpls[] = array( 'name' => $messagename, 'custom' => $custom );
        }
        return $emailtpls;
    }
    public function sendCustomEmail($subject, $msg)
    {
        delete_query('tblemailtemplates', array( 'name' => "Client Custom Email Msg" ));
        insert_query('tblemailtemplates', array( 'type' => 'general', 'name' => "Client Custom Email Msg", 'subject' => $subject, 'message' => $message ));
        sendMessage("Client Custom Email Msg", $this->userid);
        delete_query('tblemailtemplates', array( 'name' => "Client Custom Email Msg" ));
        return true;
    }
}