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
class WHMCS_Module_Registrar extends WHMCS_Module
{
    protected $type = 'registrars';
    protected $domainID = '';
    public function getSettings()
    {
        $settings = array(  );
        $result = select_query('tblregistrars', '', array( 'registrar' => $this->getLoadedModule() ));
        while( $data = mysql_fetch_array($result) )
        {
            $setting = $data['setting'];
            $value = $data['value'];
            $settings[$setting] = decrypt($value);
        }
        return $settings;
    }
    public function setDomainID($domainID)
    {
        $this->domainID = $domainID;
    }
    protected function getDomainID()
    {
        return (int) $this->domainID;
    }
    protected function buildParams()
    {
        $data = get_query_vals('tbldomains', 'id,type,domain,registrationperiod,registrar', array( 'id' => $this->getDomainID() ));
        $domainID = $data['id'];
        $type = $data['type'];
        $domainname = $data['domain'];
        $regperiod = $data['registrationperiod'];
        $registrar = $data['registrar'];
        $params = $this->getSettings();
        $domainObj = new WHMCS_Domains_Domain($domainname);
        $params['domainObj'] = $domainObj;
        $params['domainid'] = $domainID;
        $params['domainname'] = $domainname;
        $params['sld'] = $domainObj->getSLD();
        $params['tld'] = $domainObj->getTLD();
        $params['regtype'] = $type;
        $params['regperiod'] = $regperiod;
        $params['registrar'] = $registrar;
        $additflds = new WHMCS_Domains_AdditionalFields();
        $params['additionalfields'] = $additflds->getFieldValuesFromDatabase($domainID);
        return $params;
    }
    public function call($function, $additionalParams = '')
    {
        if( $function != 'getConfigArray' && !$this->getDomainID() )
        {
            return array( 'error' => "Domain ID is required" );
        }
        $params = $this->buildParams();
        if( is_array($additionalParams) )
        {
            $params = array_merge($params, $additionalParams);
        }
        return parent::call($function, $params);
    }
}