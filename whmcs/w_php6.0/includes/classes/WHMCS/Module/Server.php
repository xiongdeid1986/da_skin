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
class WHMCS_Module_Server extends WHMCS_Module
{
    protected $type = 'servers';
    protected $serviceID = '';
    protected $serviceModule = '';
    public function getModuleByServiceID($serviceID = '')
    {
        if( !$serviceID )
        {
            $serviceID = $this->serviceID;
        }
        $this->serviceModule = get_query_val('tblhosting', "tblproducts.servertype", array( "tblhosting.id" => (int) $serviceID ), '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
        return $this->serviceModule;
    }
    /**
     * Returns the module previously loaded via getModuleByServiceID
     *
     * Has to be called after getModuleByServiceID
     *
     * @return string
     */
    public function getServiceModule()
    {
        return $this->serviceModule;
    }
    public function loadByServiceID($serviceID)
    {
        $this->serviceID = (int) $serviceID;
        $moduleName = $this->getModuleByServiceID();
        return $this->load($moduleName);
    }
    public function buildParams()
    {
        $serviceID = (int) $this->serviceID;
        $result = select_query('tblhosting', '', array( 'id' => $serviceID ));
        $data = mysql_fetch_array($result);
        $serviceID = $data['id'];
        if( !$serviceID )
        {
        }
        $userid = $data['userid'];
        $domain = $data['domain'];
        $username = $data['username'];
        $password = $data['password'];
        $pid = $data['packageid'];
        $server = $data['server'];
        $params = array(  );
        $params['accountid'] = $serviceID;
        $params['serviceid'] = $serviceID;
        $params['userid'] = $userid;
        $params['domain'] = $domain;
        $params['username'] = $username;
        $params['password'] = WHMCS_Input_Sanitize::decode(decrypt($password));
        $params['packageid'] = $pid;
        $params['pid'] = $pid;
        $params['serverid'] = $server;
        $result = select_query('tblproducts', '', array( 'id' => $pid ));
        $data = mysql_fetch_array($result);
        $params['type'] = $data['type'];
        $params['producttype'] = $data['type'];
        $params['moduletype'] = $data['servertype'];
        if( !$params['moduletype'] )
        {
            return false;
        }
        if( !isValidforPath($params['moduletype']) )
        {
            exit( "Invalid Server Module Name" );
        }
        $counter = 1;
        while( $counter <= 24 )
        {
            $params['configoption' . $counter] = $data['configoption' . $counter];
            $counter += 1;
        }
        $customfields = array(  );
        $result = full_query("SELECT tblcustomfields.fieldname,tblcustomfieldsvalues.value FROM tblcustomfields,tblcustomfieldsvalues WHERE tblcustomfields.id=tblcustomfieldsvalues.fieldid AND tblcustomfieldsvalues.relid=" . (int) $serviceID . " AND tblcustomfields.relid=" . (int) $pid);
        while( $data = mysql_fetch_array($result) )
        {
            $customfieldname = $data[0];
            $customfieldvalue = $data[1];
            if( strpos($customfieldname, "|") )
            {
                $customfieldname = explode("|", $customfieldname);
                $customfieldname = trim($customfieldname[0]);
            }
            if( strpos($customfieldvalue, "|") )
            {
                $customfieldvalue = explode("|", $customfieldvalue);
                $customfieldvalue = trim($customfieldvalue[0]);
            }
            $customfields[$customfieldname] = $customfieldvalue;
        }
        $params['customfields'] = $customfields;
        $configoptions = array(  );
        $result = full_query("SELECT tblproductconfigoptions.optionname,tblproductconfigoptions.optiontype,tblproductconfigoptionssub.optionname,tblhostingconfigoptions.qty FROM tblproductconfigoptions,tblproductconfigoptionssub,tblhostingconfigoptions,tblproductconfiglinks WHERE tblhostingconfigoptions.configid=tblproductconfigoptions.id AND tblhostingconfigoptions.optionid=tblproductconfigoptionssub.id AND tblhostingconfigoptions.relid=" . (int) $serviceID . " AND tblproductconfiglinks.gid=tblproductconfigoptions.gid AND tblproductconfiglinks.pid=" . (int) $pid);
        while( $data = mysql_fetch_array($result) )
        {
            $configoptionname = $data[0];
            $configoptiontype = $data[1];
            $configoptionvalue = $data[2];
            $configoptionqty = $data[3];
            if( strpos($configoptionname, "|") )
            {
                $configoptionname = explode("|", $configoptionname);
                $configoptionname = trim($configoptionname[0]);
            }
            if( strpos($configoptionvalue, "|") )
            {
                $configoptionvalue = explode("|", $configoptionvalue);
                $configoptionvalue = trim($configoptionvalue[0]);
            }
            if( $configoptiontype == '3' || $configoptiontype == '4' )
            {
                $configoptionvalue = $configoptionqty;
            }
            $configoptions[$configoptionname] = $configoptionvalue;
        }
        $params['configoptions'] = $configoptions;
        $client = new WHMCS_Client($userid);
        $clientsdetails = $client->getDetails();
        $clientsdetails['state'] = $clientsdetails['statecode'];
        $clientsdetails = foreignChrReplace($clientsdetails);
        $params['clientsdetails'] = $clientsdetails;
        if( $server )
        {
            $result = select_query('tblservers', '', array( 'id' => $server ));
            $data = mysql_fetch_array($result);
            $params['server'] = true;
            $params['serverip'] = $data['ipaddress'];
            $params['serverhostname'] = $data['hostname'];
            $params['serverusername'] = WHMCS_Input_Sanitize::decode($data['username']);
            $params['serverpassword'] = WHMCS_Input_Sanitize::decode(decrypt($data['password']));
            $params['serveraccesshash'] = WHMCS_Input_Sanitize::decode($data['accesshash']);
            $params['serversecure'] = $data['secure'];
        }
        else
        {
            $params['server'] = false;
            $params['serversecure'] = '';
            $params['serveraccesshash'] = $params['serversecure'];
            $params['serverpassword'] = $params['serveraccesshash'];
            $params['serverusername'] = $params['serverpassword'];
            $params['serverhostname'] = $params['serverusername'];
            $params['serverip'] = $params['serverhostname'];
        }
        $GLOBALS['moduleparams'] = $params;
        return $params;
    }
    public function call($function, $params = array(  ))
    {
        $serviceID = (int) $this->serviceID;
        if( $serviceID )
        {
            $builtParams = $this->buildParams();
        }
        else
        {
            $builtParams = array(  );
        }
        if( !is_array($params) )
        {
            $params = array(  );
        }
        switch( $function )
        {
            case 'CreateAccount':
                $action = 'create';
                break;
            case 'SuspendAccount':
                $action = 'suspend';
                break;
            case 'UnsuspendAccount':
                $action = 'unsuspend';
                break;
            case 'TerminateAccount':
                $action = 'terminate';
                break;
            case 'ChangePassword':
                $action = 'changepw';
                break;
            case 'ChangePackage':
                $action = 'upgrade';
                break;
            default:
                $action = $function;
                break;
        }
        $params['action'] = $action;
        $builtParams = array_merge($builtParams, $params);
        return parent::call($function, $builtParams);
    }
}