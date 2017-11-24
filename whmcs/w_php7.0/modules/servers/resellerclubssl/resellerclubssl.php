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
 * Provide a display name and which internal API version to use.
 * The API version will determine how the html entity characters are decoded
 * before being provided to the module functions.
 *
 * @return array
 */
function resellerclubssl_MetaData()
{
    return array( 'DisplayName' => "ResellerClub SSL Certificates", 'APIVersion' => "1.1" );
}
function resellerclubssl_ConfigOptions()
{
    $data = get_query_val('tblemailtemplates', "COUNT(*)", array( 'name' => "SSL Certificate Configuration Required" ));
    if( !$data )
    {
        $message = "&lt;p&gt;Dear {\$client_name},&lt;/p&gt;" . PHP_EOL . "&lt;p&gt;Thank you for your order for an SSL Certificate. " . "Before you can use your certificate, it requires configuration which" . " can be done at the URL below.&lt;/p&gt;" . PHP_EOL . "&lt;p&gt;{\$ssl_configuration_link}&lt;/p&gt;" . PHP_EOL . "&lt;p&gt;Instructions are provided throughout the process but if you experience " . "any problems or have any questions, please open a ticket for assistance.&lt;/p&gt;" . PHP_EOL . "&lt;p&gt;{\$signature}&lt;/p&gt;";
        insert_query('tblemailtemplates', array( 'type' => 'product', 'name' => "SSL Certificate Configuration Required", 'subject' => "SSL Certificate Configuration Required", 'message' => $message, 'plaintext' => 0 ));
    }
    $pid = $_GET['id'];
    $customfieldid = get_query_val('tblcustomfields', 'id', "type='product' AND relid=" . (int) $pid . " AND fieldname LIKE 'Domain Name%'");
    if( !$customfieldid )
    {
        insert_query('tblcustomfields', array( 'type' => 'product', 'relid' => $pid, 'fieldname' => "Domain Name", 'fieldtype' => 'text', 'description' => "Enter the domain name you want to protect", 'required' => 'on', 'showorder' => 'on', 'showinvoice' => 'on' ));
    }
    $params = get_query_vals('tblproducts', 'configoption1,configoption2,configoption3,configoption4', array( 'id' => $pid ));
    $certificateTypes = $certificateTypeDescription = '';
    if( $params['configoption1'] && $params['configoption2'] )
    {
        $certificates = resellerclubssl_getSSLPlans($params);
        foreach( $certificates as $planID => $certificateName )
        {
            if( $planID )
            {
                $certificateTypes .= $planID . "|" . $certificateName . ',';
            }
            else
            {
                $certificateTypes .= $certificateName . ',';
            }
        }
        $certificateTypes = substr($certificateTypes, 0, 0 - 1);
    }
    if( !$certificateTypes )
    {
        $certificateTypes = "Please enter your Reseller ID and API-Key and click on Save Changes.";
    }
    if( $params['configoption3'] != '' && !strpos($params['configoption3'], "|") )
    {
        $certificateTypeDescription = "The current saved value for Certificate Type is invalid, press Save Changes";
    }
    $configarray = array( "Reseller ID" => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Obtained from ResellerClub Settings > Personal Information > Primary Profile" ), 'API-Key' => array( 'Type' => 'password', 'Size' => '20', 'Description' => "Your API Key. You can get this from the LogicBoxes Control Panel in Settings -> API" ), "Certificate Type" => array( 'Type' => 'dropdown', 'Options' => $certificateTypes, 'Description' => $certificateTypeDescription ), "Test Mode" => array( 'Type' => 'yesno' ) );
    return $configarray;
}
/**
 * Send the initial sslcert/add command to create the SSL Order.
 *
 * @param array $params - The variables from WHMCS_Module_Server::buildParams()
 *
 * @return string - Containing an error if applicable or 'success'.
 */
function resellerclubssl_CreateAccount($params)
{
    $existingSSLOrder = get_query_val('tblsslorders', "count(*)", array( 'serviceid' => $params['serviceid'] ));
    if( $existingSSLOrder )
    {
        return "An SSL Order already exists for this service";
    }
    $domainName = $params['domain'];
    if( $params['customfields']["Domain Name"] )
    {
        $domainName = $params['customfields']["Domain Name"];
    }
    updateService(array( 'domain' => $domainName, 'username' => '', 'password' => '' ));
    $certificateType = (int) current(explode("|", $params['configoption3']));
    if( $params['configoptions']["Certificate Type"] && is_int($params['configoptions']["Certificate Type"]) )
    {
        $certificateType = (int) $params['configoptions']["Certificate Type"];
        $plans = resellerclubssl_getSSLPlans($params);
        $params['configoption3'] = $certificateType . "|" . $plans[$certificateType];
    }
    if( !is_int($certificateType) || $certificateType <= 0 )
    {
        return "The certificate type is not correctly set, please check the Module Settings and click Save Changes";
    }
    $purchaseMonths = 12;
    if( isset($params['configoptions']['Years']) )
    {
        $purchaseMonths = $params['configoptions']['Years'] * 12;
    }
    else
    {
        $billingCycle = get_query_val('tblhosting', 'billingcycle', array( 'id' => $params['serviceid'] ));
        if( $billingCycle == 'Biennially' )
        {
            $purchaseMonths = 24;
        }
        else
        {
            if( $billingCycle == 'Triennially' )
            {
                $purchaseMonths = 36;
            }
        }
    }
    $postFields = array(  );
    $postFields['auth-userid'] = $params['configoption1'];
    $postFields['api-key'] = $params['configoption2'];
    $postFields['username'] = $params['clientsdetails']['email'];
    $result = resellerclubssl_SendJsonCommand('details', 'customers', $postFields, $params, 'GET');
    unset($postFields['username']);
    if( strtoupper($result['response']['status']) == 'ERROR' )
    {
        if( !$result['response']['message'] )
        {
            $result['response']['message'] = $result['response']['error'];
        }
        return $result['response']['message'];
    }
    if( strtoupper($result['status']) == 'ERROR' )
    {
        $postFields['lang-pref'] = resellerclubssl_Language($params['clientsdetails']['language']);
        $postFields['username'] = $params['clientsdetails']['email'];
        $postFields['passwd'] = resellerclubssl_genLBRandomPW();
        $postFields['name'] = $params['clientsdetails']['fullname'];
        $postFields['company'] = $params['clientsdetails']['companyname'] ? $params['clientsdetails']['companyname'] : 'N/A';
        $postFields['address-line-1'] = $params['clientsdetails']['address1'];
        $postFields['address-line-2'] = $params['clientsdetails']['address2'];
        $postFields['city'] = $params['clientsdetails']['city'];
        if( $params['country'] != 'US' )
        {
            $postFields['state'] = $params['clientsdetails']['state'];
        }
        else
        {
            $postFields['state'] = $params['clientsdetails']['statecode'];
        }
        $postFields['state'] = $params['clientsdetails']['state'];
        $postFields['zipcode'] = $params['clientsdetails']['postcode'];
        $postFields['country'] = $params['clientsdetails']['country'];
        $postFields['phone'] = preg_replace("/[^0-9]/", '', $params['clientsdetails']['phonenumber']);
        $postFields['phone-cc'] = $params['clientsdetails']['phonecc'];
        $result = resellerclubssl_SendJsonCommand('signup', 'customers', $postFields, $params, 'POST');
        if( $result['response']['status'] == 'ERROR' )
        {
            return $result['response']['message'];
        }
        $customerID = $result;
    }
    else
    {
        $customerID = $result['customerid'];
    }
    if( !$customerID )
    {
        return "Error obtaining customer id";
    }
    if( is_array($customerID) )
    {
        return $result['response']['message'];
    }
    unset($postFields);
    $postFields = array(  );
    $postFields['auth-userid'] = $params['configoption1'];
    $postFields['api-key'] = $params['configoption2'];
    $postFields['domain-name'] = $domainName;
    $postFields['months'] = $purchaseMonths;
    $postFields['customer-id'] = $customerID;
    $postFields['plan-id'] = $certificateType;
    $postFields['invoice-option'] = 'NoInvoice';
    $result = resellerclubssl_SendJsonCommand('add', 'sslcert', $postFields, $params, 'POST');
    if( $result['response']['status'] == 'ERROR' )
    {
        if( !$result['response']['message'] )
        {
            $result['response']['message'] = $result['response']['error'];
        }
        return $result['response']['message'];
    }
    $orderID = $result['entityid'];
    if( !$orderID )
    {
        return "Unable to obtain Order-ID";
    }
    $sslOrderID = insert_query('tblsslorders', array( 'userid' => $params['clientsdetails']['userid'], 'serviceid' => $params['serviceid'], 'remoteid' => $orderID, 'module' => 'resellerclubssl', 'certtype' => $params['configoption3'], 'status' => "Awaiting Configuration" ));
    $whmcs = WHMCS_Application::getinstance();
    $systemURL = $whmcs->getSystemSSLURL() ? $whmcs->getSystemSSLURL() : $whmcs->getSystemURL();
    $sslConfigurationLink = $systemURL . "/configuressl.php?cert=" . md5($sslOrderID);
    $sslConfigurationLink = "<a href='" . $sslConfigurationLink . "'>" . $sslConfigurationLink . "</a>";
    sendMessage("SSL Certificate Configuration Required", $params['serviceid'], array( 'ssl_configuration_link' => $sslConfigurationLink ));
    return 'success';
}
function resellerclubssl_TerminateAccount($params)
{
    $sslexists = get_query_val('tblsslorders', "COUNT(*)", array( 'serviceid' => $params['serviceid'], 'status' => "Awaiting Configuration" ));
    if( !$sslexists )
    {
        return "SSL Either not Provisioned or Not Awaiting Configuration so unable to cancel";
    }
    update_query('tblsslorders', array( 'status' => 'Cancelled' ), array( 'serviceid' => $params['serviceid'] ));
    $postfields = array(  );
    $postfields['auth-userid'] = $params['configoption1'];
    $postFields['api-key'] = $params['configoption2'];
    $params['remoteid'] = get_query_val('tblsslorders', 'remoteid', array( 'serviceid' => $params['serviceid'] ));
    $postfields['order-id'] = $params['remoteid'];
    if( strpos(get_query_val('tblsslorders', 'certtype', array( 'serviceid' => $params['serviceid'] )), "|") )
    {
        resellerclubssl_SendCommand('delete', 'sslcert', $postfields, $params, 'post');
    }
    else
    {
        resellerclubssl_SendCommand('cancel', 'digitalcertificate', $postfields, $params, 'POST');
        resellerclubssl_SendCommand('delete', 'digitalcertificate', $postfields, $params, 'POST');
    }
    return 'success';
}
function resellerclubssl_AdminCustomButtonArray()
{
    $buttonarray = array( "Resend Configuration Email" => 'resend', "Prepare for Reissue" => 'Reissue', 'Renew' => 'Renew' );
    return $buttonarray;
}
function resellerclubssl_resend($params)
{
    $id = get_query_val('tblsslorders', 'id', array( 'serviceid' => $params['serviceid'] ));
    if( !$id )
    {
        return "No SSL Order exists for this product";
    }
    global $CONFIG;
    $sslconfigurationlink = $CONFIG['SystemURL'] . "/configuressl.php?cert=" . md5($id);
    $sslconfigurationlink = "<a href=\"" . $sslconfigurationlink . "\">" . $sslconfigurationlink . "</a>";
    sendMessage("SSL Certificate Configuration Required", $params['serviceid'], array( 'ssl_configuration_link' => $sslconfigurationlink ));
    return 'success';
}
function resellerclubssl_ClientArea($params)
{
    global $_LANG;
    $data = get_query_vals('tblsslorders', '', array( 'serviceid' => $params['serviceid'] ));
    $id = $data['id'];
    $orderid = $data['orderid'];
    $serviceid = $data['serviceid'];
    $remoteid = $data['remoteid'];
    $module = $data['module'];
    $certtype = $data['certtype'];
    $domain = $data['domain'];
    $provisiondate = $data['provisiondate'];
    $completiondate = $data['completiondate'];
    $expirydate = $data['expirydate'];
    $status = $data['status'];
    if( $id )
    {
        if( !$provisiondate )
        {
            $provisiondate = get_query_val('tblhosting', 'regdate', array( 'id' => $params['serviceid'] ));
        }
        $provisiondate = $provisiondate == '0000-00-00' ? '-' : fromMySQLDate($provisiondate);
        if( $status == "Awaiting Configuration" )
        {
            $status .= " - <a href=\"configuressl.php?cert=" . md5($id) . "\">" . $_LANG['sslconfigurenow'] . "</a>";
        }
        $output = "<div align=\"left\">\n<table width=\"100%\">\n<tr><td width=\"150\" class=\"fieldlabel\">" . $_LANG['sslprovisioningdate'] . ":</td><td>" . $provisiondate . "</td></tr>\n<tr><td class=\"fieldlabel\">" . $_LANG['sslstatus'] . ":</td><td>" . $status . "</td></tr>\n</table>\n</div>";
        return $output;
    }
}
function resellerclubssl_AdminServicesTabFields($params)
{
    $data = get_query_vals('tblsslorders', '', array( 'serviceid' => $params['serviceid'] ));
    $id = $data['id'];
    $orderid = $data['orderid'];
    $serviceid = $data['serviceid'];
    $remoteid = $data['remoteid'];
    $module = $data['module'];
    $certtype = $data['certtype'];
    $domain = $data['domain'];
    $provisiondate = $data['provisiondate'];
    $completiondate = $data['completiondate'];
    $expirydate = $data['expirydate'];
    $status = $data['status'];
    if( !$id )
    {
        $remoteid = '-';
        $status = "Not Yet Provisioned";
    }
    $fieldsarray = array( "ResellerClub Order ID" => $remoteid, "SSL Configuration Status" => $status );
    return $fieldsarray;
}
function resellerclubssl_SSLStepOne($params)
{
    if( $params['remoteid'] && !strpos($params['certtype'], "|") )
    {
        $certdata = resellerclubssl_getCertDetails($params);
        if( is_array($certdata) )
        {
            if( $certdata['certificateEnrolled'] == 'true' )
            {
                update_query('tblsslorders', array( 'completiondate' => "now()", 'status' => 'Completed' ), array( 'serviceid' => $params['serviceid'], 'status' => array( 'sqltype' => 'NEQ', 'value' => 'Completed' ) ));
                return NULL;
            }
            update_query('tblsslorders', array( 'completiondate' => '', 'status' => "Awaiting Configuration" ), array( 'serviceid' => $params['serviceid'] ));
            return NULL;
        }
    }
    else
    {
        if( $params['remoteid'] )
        {
            $postFields = array(  );
            $postFields['auth-userid'] = $params['configoption1'];
            $postFields['api-key'] = $params['configoption2'];
            $postFields['order-id'] = $params['remoteid'];
            $certificateData = resellerclubssl_sendJsonCommand('details', 'sslcert', $postFields, $params, 'GET');
            if( $certificateData['response']['status'] == 'ERROR' )
            {
                if( !$certificateData['response']['message'] )
                {
                    $certificateData['response']['message'] = $certificateData['response']['error'];
                }
                return $certificateData['response']['message'];
            }
            if( (string) $certificateData['actioncompleted'] == true )
            {
                update_query('tblsslorders', array( 'completiondate' => "now()", 'status' => 'Completed' ), array( 'serviceid' => $params['serviceid'], 'status' => array( 'sqltype' => 'NEQ', 'value' => 'Completed' ) ));
                return NULL;
            }
            update_query('tblsslorders', array( 'completiondate' => '', 'status' => "Awaiting Configuration" ), array( 'serviceid' => $params['serviceid'] ));
        }
    }
}
function resellerclubssl_SSLStepTwo($params)
{
    $domain = strtolower(trim($params['domain']));
    if( substr($domain, 0, 7) == "http://" )
    {
        $domain = substr($domain, 7);
    }
    if( substr($domain, 0, 4) == "www." )
    {
        $domain = substr($domain, 4);
    }
    $approveremails = array( 'admin', 'administrator', 'hostmaster', 'root', 'postmaster' );
    foreach( $approveremails as $email )
    {
        $approveremailsarray[] = $email . "@" . $domain;
    }
    $values['approveremails'] = $approveremailsarray;
    return $values;
}
function resellerclubssl_SSLStepThree($params)
{
    if( !strpos($params['certtype'], "|") )
    {
        $countrycallingcodes = array(  );
        require(ROOTDIR . "/includes/countriescallingcodes.php");
        $postfields = array(  );
        $postfields['auth-userid'] = $params['configoption1'];
        $postFields['api-key'] = $params['configoption2'];
        $postfields['order-id'] = $params['remoteid'];
        $certdata = resellerclubssl_getCertDetails($params);
        if( $certdata['isenrolled'] == 'false' )
        {
            $phoneCC = $countrycallingcodes[$params['configdata']['country']];
            $phoneNumber = preg_replace("/[^0-9]/", '', $params['configdata']['phonenumber']);
            $postfields['attr-name1'] = 'org_name';
            $postfields['attr-name2'] = 'org_street1';
            $postfields['attr-name3'] = 'org_city';
            $postfields['attr-name4'] = 'org_state';
            $postfields['attr-name5'] = 'org_postalcode';
            $postfields['attr-name6'] = 'org_country';
            $postfields['attr-name7'] = 'org_phone';
            $postfields['attr-name8'] = 'org_fax';
            $postfields['attr-name9'] = 'admin_firstname';
            $postfields['attr-name10'] = 'admin_lastname';
            $postfields['attr-name11'] = 'admin_jobtitle';
            $postfields['attr-name12'] = 'admin_telephone';
            $postfields['attr-name13'] = 'admin_email';
            $postfields['attr-name14'] = 'tech_firstname';
            $postfields['attr-name15'] = 'tech_lastname';
            $postfields['attr-name16'] = 'tech_jobtitle';
            $postfields['attr-name17'] = 'tech_telephone';
            $postfields['attr-name18'] = 'tech_email';
            $postfields['attr-name19'] = 'approveremail';
            $postfields['attr-name20'] = 'software';
            $postfields['attr-name21'] = 'csrString';
            $postfields['attr-value1'] = $params['configdata']['company'] ? $params['configdata']['company'] : 'N/A';
            $postfields['attr-value2'] = $params['configdata']['address1'];
            $postfields['attr-value3'] = $params['configdata']['city'];
            $postfields['attr-value4'] = $params['configdata']['state'];
            $postfields['attr-value5'] = $params['configdata']['postcode'];
            $postfields['attr-value6'] = $params['configdata']['country'];
            $postfields['attr-value7'] = $phoneCC . $phoneNumber;
            $postfields['attr-value8'] = $phoneCC . $phoneNumber;
            $postfields['attr-value14'] = $params['configdata']['firstname'];
            $postfields['attr-value9'] = $postfields['attr-value14'];
            $postfields['attr-value15'] = $params['configdata']['lastname'];
            $postfields['attr-value10'] = $postfields['attr-value15'];
            $postfields['attr-value11'] = 'Administrator';
            $postfields['attr-value12'] = $phoneCC . $phoneNumber;
            $postfields['attr-value13'] = $params['configdata']['email'];
            $postfields['attr-value16'] = "IT Admin";
            $postfields['attr-value17'] = $phoneCC . $phoneNumber;
            $postfields['attr-value18'] = $params['configdata']['email'];
            $postfields['attr-value19'] = $params['approveremail'];
            $postfields['attr-value20'] = $params['servertype'] == '1013' || $params['servertype'] == '1014' ? 'IIS' : 'Other';
            $postfields['attr-value21'] = $params['csr'];
            $result = resellerclubssl_SendCommand('enroll-for-thawtecertificate', 'digitalcertificate', $postfields, $params, 'POST');
        }
        else
        {
            $postfields['csr-string'] = $params['csr'];
            $postfields['csr-software'] = $params['servertype'] == '1013' || $params['servertype'] == '1014' ? 'IIS' : 'Other';
            $postfields['approver-email'] = $params['approveremail'];
            $result = resellerclubssl_SendCommand('reissue', 'digitalcertificate', $postfields, $params, 'POST');
        }
        if( $result['response']['status'] == 'ERROR' )
        {
            return array( 'error' => $result['response']['message'] );
        }
        if( $result['hashtable']['entry'][0]['string'][1] != 'success' )
        {
            return array( 'error' => $result['hashtable']['entry'][1]['string'][1] );
        }
    }
    else
    {
        $postFields = array(  );
        $postFields['auth-userid'] = $params['configoption1'];
        $postFields['api-key'] = $params['configoption2'];
        $postFields['order-id'] = $params['remoteid'];
        $certificateData = resellerclubssl_sendJsonCommand('details', 'sslcert', $postFields, $params, 'GET');
        if( $certificateData['response']['status'] == 'ERROR' )
        {
            if( !$certificateData['response']['message'] )
            {
                $certificateData['response']['message'] = $certificateData['response']['error'];
            }
            return array( 'error' => $certificateData['response']['message'] );
        }
        $postFields['csr'] = $params['csr'];
        $postFields['verification-email'] = $params['approveremail'];
        if( (string) $certificateData['actioncompleted'] != true )
        {
            $result = resellerclubssl_SendJsonCommand('enroll', 'sslcert', $postFields, $params, 'POST');
        }
        else
        {
            $result = resellerclubssl_SendJsonCommand('reissue', 'sslcert', $postFields, $params, 'POST');
        }
        if( $result['response']['status'] == 'ERROR' )
        {
            if( !$result['response']['message'] )
            {
                $result['response']['message'] = $result['response']['error'];
            }
            return array( 'error' => $result['response']['message'] );
        }
    }
    return array( 'provisioned' => true );
}
function resellerclubssl_Reissue($params)
{
    $id = get_query_val('tblsslorders', 'id', array( 'serviceid' => $params['serviceid'] ));
    if( !$id )
    {
        return "No SSL Order exists for this product";
    }
    update_query('tblsslorders', array( 'status' => "Awaiting Configuration" ), array( 'serviceid' => $params['serviceid'] ));
    global $CONFIG;
    $sslconfigurationlink = $CONFIG['SystemURL'] . "/configuressl.php?cert=" . md5($id);
    $sslconfigurationlink = "<a href=\"" . $sslconfigurationlink . "\">" . $sslconfigurationlink . "</a>";
    sendMessage("SSL Certificate Configuration Required", $params['serviceid'], array( 'ssl_configuration_link' => $sslconfigurationlink ));
    return 'success';
}
function resellerclubssl_Renew($params)
{
    $certificateData = get_query_vals('tblsslorders', '', array( 'serviceid' => $params['serviceid'] ));
    if( strpos($certificateData['certtype'], "|") )
    {
        $postFields = array(  );
        $postFields['auth-userid'] = $params['configoption1'];
        $postFields['api-key'] = $params['configoption2'];
        $postFields['order-id'] = $params['remoteid'];
        $postFields['invoice-option'] = 'NoInvoice';
        $purchaseMonths = 12;
        if( isset($params['configoptions']['Years']) )
        {
            $purchaseMonths = $params['configoptions']['Years'] * 12;
        }
        else
        {
            $billingCycle = get_query_val('tblhosting', 'billingcycle', array( 'id' => $params['serviceid'] ));
            if( $billingCycle == 'Biennially' )
            {
                $purchaseMonths = 24;
            }
            else
            {
                if( $billingCycle == 'Triennially' )
                {
                    $purchaseMonths = 36;
                }
            }
        }
        $postFields['months'] = $purchaseMonths;
        $result = resellerclubssl_SendJsonCommand('renew', 'sslcert', $postFields, $params, 'POST');
        if( $result['response']['status'] == 'ERROR' )
        {
            if( !$result['response']['message'] )
            {
                $result['response']['message'] = $result['response']['error'];
            }
            return $result['response']['message'];
        }
        return 'success';
    }
    return "This certificate type has been discontinued. Please place a new order for your SSL Certificate.";
}
function resellerclubssl_SendCommand($command, $type, $postfields, $params, $method)
{
    if( $params['configoption4'] )
    {
        $url = "https://test.httpapi.com/api/" . $type . '/' . $command . ".xml";
    }
    else
    {
        $url = "https://httpapi.com/api/" . $type . '/' . $command . ".xml";
    }
    $ch = curl_init();
    if( $method == 'GET' )
    {
        $url .= "?";
        foreach( $postfields as $field => $data )
        {
            $url .= $field . "=" . rawurlencode($data) . "&";
        }
        $url = substr($url, 0, 0 - 1);
    }
    else
    {
        $query_string = '';
        foreach( $postfields as $field => $data )
        {
            if( $field != 'ns' )
            {
                $data = rawurlencode($data);
            }
            $query_string .= $field . "=" . $data . "&";
        }
        $postfield = substr($postfield, 0, 0 - 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $ip = resellerclubssl_GetIP();
        $ip2 = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
        $result['response']['status'] = 'ERROR';
        $result['response']['message'] = "CURL Error: " . curl_errno($ch) . " - " . curl_error($ch) . " (IP: " . $ip . " & " . $ip2 . ")";
    }
    else
    {
        $result = resellerclubssl_xml2array($data);
    }
    curl_close($ch);
    logModuleCall('logicboxes', $command, $postfields, $data, $result, array( $params['configoption1'], $params['configoption2'] ));
    if( $result['response']['message'] == "An unexpected error has occurred" )
    {
        $result['response']['message'] = "Login Failure or Unexpected Error";
    }
    return $result;
}
function resellerclubssl_getCertDetails($params, $option = 'All')
{
    $postfields = array(  );
    $postfields['auth-userid'] = $params['configoption1'];
    $postFields['api-key'] = $params['configoption2'];
    $postfields['order-id'] = $params['remoteid'];
    $postfields['option'] = $option;
    $result = resellerclubssl_sendcommand('details', 'digitalcertificate', $postfields, $params, 'GET');
    if( $result['response']['status'] == 'ERROR' )
    {
        return $result['response']['message'];
    }
    if( $option != 'All' )
    {
        $result = $result['hashtable']['entry'][0];
    }
    foreach( $result['hashtable']['entry'] as $entry => $value )
    {
        $certdata[$value['string'][0]] = $value['string'][1];
    }
    return $certdata;
}
function resellerclubssl_getOrderID($postfields, $params)
{
    $domain = $postfields['domain-name'];
    if( isset($GLOBALS['logicboxesorderids'][$domain]) )
    {
        $result = $GLOBALS['logicboxesorderids'][$domain];
    }
    else
    {
        $result = resellerclubssl_sendcommand('orderid', 'digitalcertificate', $postfields, $params, 'GET');
        $GLOBALS['logicboxesorderids'][$domain] = $result;
    }
    if( $result['response']['status'] == 'ERROR' )
    {
        return $result['response']['message'];
    }
    $orderid = $result['int'];
    if( !$orderid )
    {
        return "Unable to obtain Order-ID";
    }
    return $orderid;
}
function resellerclubssl_genLBRandomPW()
{
    $letters = 'ABCDEFGHIJKLMNPQRSTUVYXYZabcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $letterscount = strlen($letters) - 1;
    $numberscount = strlen($numbers) - 1;
    $password = '';
    for( $i = 0; $i < 5; $i++ )
    {
        $password .= $letters[rand(0, $letterscount)] . $numbers[rand(0, $numberscount)];
    }
    return $password;
}
function resellerclubssl_xml2array($contents, $get_attributes = 1, $priority = 'tag')
{
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if( !$xml_values )
    {
        return NULL;
    }
    $xml_array = array(  );
    $parents = array(  );
    $opened_tags = array(  );
    $arr = array(  );
    $current =& $xml_array;
    $repeated_tag_index = array(  );
    foreach( $xml_values as $data )
    {
        unset($attributes);
        unset($value);
        extract($data);
        $result = array(  );
        $attributes_data = array(  );
        if( isset($value) )
        {
            if( $priority == 'tag' )
            {
                $result = $value;
            }
            else
            {
                $result['value'] = $value;
            }
        }
        if( isset($attributes) && $get_attributes )
        {
            foreach( $attributes as $attr => $val )
            {
                if( $priority == 'tag' )
                {
                    $attributes_data[$attr] = $val;
                }
                else
                {
                    $result['attr'][$attr] = $val;
                }
            }
        }
        if( $type == 'open' )
        {
            $parent[$level - 1] =& $current;
            if( !is_array($current) || !in_array($tag, array_keys($current)) )
            {
                $current[$tag] = $result;
                if( $attributes_data )
                {
                    $current[$tag . '_attr'] = $attributes_data;
                }
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current =& $current[$tag];
            }
            else
            {
                if( isset($current[$tag][0]) )
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array( $current[$tag], $result );
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if( isset($current[$tag . '_attr']) )
                    {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current =& $current[$tag][$last_item_index];
            }
        }
        else
        {
            if( $type == 'complete' )
            {
                if( !isset($current[$tag]) )
                {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if( $priority == 'tag' && $attributes_data )
                    {
                        $current[$tag . '_attr'] = $attributes_data;
                    }
                }
                else
                {
                    if( isset($current[$tag][0]) && is_array($current[$tag]) )
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if( $priority == 'tag' && $get_attributes && $attributes_data )
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    {
                        $current[$tag] = array( $current[$tag], $result );
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if( $priority == 'tag' && $get_attributes )
                        {
                            if( isset($current[$tag . '_attr']) )
                            {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if( $attributes_data )
                            {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                }
            }
            else
            {
                if( $type == 'close' )
                {
                    $current =& $parent[$level - 1];
                }
            }
        }
    }
    return $xml_array;
}
function resellerclubssl_GetIP()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.whmcs.com/getip/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;
}
function resellerclubssl_Language($language)
{
    $language = strtolower($language);
    switch( $language )
    {
        case 'dutch':
            $language = 'nl';
            break;
        case 'german':
            $language = 'de';
            break;
        case 'italian':
            $language = 'it';
            break;
        case 'portuguese-br':
            $language = 'pt';
            break;
        case 'portuguese-pt':
            $language = 'pt';
            break;
        case 'spanish':
            $language = 'es';
            break;
        case 'turkish':
            $language = 'tr';
            break;
        case 'english':
            break;
        default:
            $language = 'en';
            break;
    }
    if( strlen($language) == 2 )
    {
        return $language;
    }
    return 'en';
}
/**
 * Obtain a list of SSL Plans with the product ID and name as at ResellerClub.
 *
 * @param array $params
 *
 * @return array - an array of ID -> Name values.
 */
function resellerclubssl_getSSLPlans($params)
{
    $postFields = array(  );
    $postFields['auth-userid'] = $params['configoption1'];
    $postFields['api-key'] = $params['configoption2'];
    $result = resellerclubssl_sendJsonCommand('plan-details', 'products', $postFields, $params, 'GET');
    if( $result['status'] == 'ERROR' )
    {
        return array( str_replace(',', '', $result['message']) );
    }
    if( $result['response']['status'] == 'ERROR' )
    {
        if( !$result['response']['message'] )
        {
            $result['response']['message'] = $result['response']['error'];
        }
        return array( str_replace(',', '', $result['response']['message']) );
    }
    $sslProducts = $result['sslcert'];
    $return = array(  );
    foreach( $sslProducts as $productID => $sslProduct )
    {
        if( $sslProduct['plan_status'] == 'Active' )
        {
            $return[$productID] = $sslProduct['plan_name'];
        }
    }
    return $return;
}
/**
 * Send data to the ResellerClub Json API and parse the response received.
 *
 * Example URL: https://test.httpapi.com/api/$type/$command.json
 *
 * @param string $command - The API command being called.
 * @param string $type - The category of the API command being called.
 * @param array $postFields - The data to be sent to the API URL.
 * @param array $params - An array of parameters from the product configuration.
 * @param string $method - The method of call - GET or POST.
 * @param bool $noDecode - Whether to not decode the response from the API call.
 *
 * @return array|int
 */
function resellerclubssl_sendJsonCommand($command, $type, $postFields, $params, $method, $noDecode = false)
{
    $testURL = '';
    if( $params['configoption4'] )
    {
        $testURL = "test.";
    }
    $url = "https://" . $testURL . "httpapi.com/api/" . $type . '/' . $command . ".json";
    $ch = curl_init();
    switch( $method )
    {
        case 'GET':
            $url .= "?";
            foreach( $postFields as $field => $value )
            {
                $url .= $field . "=" . rawurlencode($value) . "&";
            }
            $url = substr($url, 0, 0 - 1);
            break;
        default:
            $query_string = '';
            foreach( $postFields as $field => $value )
            {
                if( $field != 'ns' )
                {
                    $value = rawurlencode($value);
                }
                $query_string .= $field . "=" . $value . "&";
            }
            $query_string = substr($query_string, 0, 0 - 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
            break;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $ip = resellerclubssl_getip();
        $ip2 = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
        $result['response']['status'] = 'ERROR';
        $result['response']['message'] = "CURL Error: " . curl_errno($ch) . " - " . curl_error($ch) . " (IP: " . $ip . " & " . $ip2 . ")";
    }
    else
    {
        if( $noDecode && is_numeric($data) )
        {
            $result = $data;
        }
        else
        {
            $result = json_decode($data, true);
        }
    }
    curl_close($ch);
    logModuleCall('logicboxes', $type . '/' . $command, $postFields, $data, $result, array( $params['configoption1'], $params['configoption2'] ));
    if( $result['response']['message'] == "An unexpected error has occurred" )
    {
        $result['response']['message'] = "Login Failure or Unexpected Error";
    }
    return $result;
}