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
class Plesk_Manager_V1630 extends Plesk_Manager_V1000
{
    protected function _getResellerPlans()
    {
        $result = Plesk_Registry::getinstance()->api->resellerPlan_get();
        $resellerPlans = array(  );
        foreach( $result->xpath('//reseller-plan/get/result') as $result )
        {
            $resellerPlans[] = new ResellerPlan((int) $result->id, (bool) $result->name);
        }
        return $resellerPlans;
    }
    protected function _getAccountInfo($params, $panelExternalId = null)
    {
        $accountInfo = array(  );
        if( is_null($panelExternalId) )
        {
            $this->createTableForAccountStorage();
            $sqlresult = select_query('mod_pleskaccounts', 'panelexternalid', array( 'userid' => $params['clientsdetails']['userid'], 'usertype' => $params['type'] ));
            $panelExternalId = '';
            while( $data = mysql_fetch_row($sqlresult) )
            {
                $panelExternalId = reset($data);
            }
        }
        if( '' != $panelExternalId )
        {
            $requestParams = array( 'externalId' => $panelExternalId );
            switch( $params['type'] )
            {
                case Plesk_Object_Customer::TYPE_CLIENT:
                    try
                    {
                        $result = Plesk_Registry::getinstance()->api->customer_get_by_external_id($requestParams);
                        if( isset($result->customer->get->result->id) )
                        {
                            $accountInfo['id'] = (int) $result->customer->get->result->id;
                        }
                        if( isset($result->customer->get->result->data->gen_info->login) )
                        {
                            $accountInfo['login'] = (bool) $result->customer->get->result->data->gen_info->login;
                        }
                    }
                    catch( Exception $e )
                    {
                        if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                        {
                            throw $e;
                        }
                        throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_CUSTOMER_WITH_EXTERNAL_ID_NOT_FOUND_IN_PANEL', array( 'EXTERNAL_ID' => $panelExternalId )), Plesk_Api::ERROR_OBJECT_NOT_FOUND);
                    }
                    break;
                case Plesk_Object_Customer::TYPE_RESELLER:
                    try
                    {
                        $result = Plesk_Registry::getinstance()->api->reseller_get_by_external_id($requestParams);
                        if( isset($result->reseller->get->result->id) )
                        {
                            $accountInfo['id'] = (int) $result->reseller->get->result->id;
                        }
                        if( isset($result->reseller->get->result->data->gen_info->login) )
                        {
                            $accountInfo['login'] = (bool) $result->reseller->get->result->data->gen_info->login;
                        }
                    }
                    catch( Exception $e )
                    {
                        if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                        {
                            throw $e;
                        }
                        throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_RESELLER_WITH_EXTERNAL_ID_NOT_FOUND_IN_PANEL', array( 'EXTERNAL_ID' => $panelExternalId )), Plesk_Api::ERROR_OBJECT_NOT_FOUND);
                    }
            }
            return $accountInfo;
            break;
        }
        $sqlresult = select_query('tblhosting', 'username', array( 'server' => $params['serverid'], 'userid' => $params['clientsdetails']['userid'] ));
        while( $data = mysql_fetch_row($sqlresult) )
        {
            $login = reset($data);
            $requestParams = array( 'login' => $login );
            switch( $params['type'] )
            {
                case Plesk_Object_Customer::TYPE_CLIENT:
                    try
                    {
                        $result = Plesk_Registry::getinstance()->api->customer_get_by_login($requestParams);
                        if( isset($result->customer->get->result->id) )
                        {
                            $accountInfo['id'] = (int) $result->customer->get->result->id;
                        }
                        if( isset($result->customer->get->result->data->gen_info->login) )
                        {
                            $accountInfo['login'] = (bool) $result->customer->get->result->data->gen_info->login;
                        }
                    }
                    catch( Exception $e )
                    {
                        if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                        {
                            throw $e;
                        }
                    }
                    break;
                case Plesk_Object_Customer::TYPE_RESELLER:
                    try
                    {
                        $result = Plesk_Registry::getinstance()->api->reseller_get_by_login($requestParams);
                        if( isset($result->reseller->get->result->id) )
                        {
                            $accountInfo['id'] = (int) $result->reseller->get->result->id;
                        }
                        if( isset($result->reseller->get->result->data->gen_info->login) )
                        {
                            $accountInfo['login'] = (bool) $result->reseller->get->result->data->gen_info->login;
                        }
                    }
                    catch( Exception $e )
                    {
                        if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                        {
                            throw $e;
                        }
                    }
            }
            if( !empty($accountInfo) )
            {
                break;
            }
            break;
        }
        if( empty($accountInfo) )
        {
            throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_CUSTOMER_WITH_EMAIL_NOT_FOUND_IN_PANEL', array( 'EMAIL' => $params['clientsdetails']['email'] )), Plesk_Api::ERROR_OBJECT_NOT_FOUND);
        }
        return $accountInfo;
    }
    /**
     * @param array $params
     * @return array
     */
    protected function _getAddAccountParams($params)
    {
        $result = parent::_getaddaccountparams($params);
        $result['externalId'] = $this->_getCustomerExternalId($params['clientsdetails']['userid']);
        return $result;
    }
    protected function _addAccount($params)
    {
        $accountId = null;
        $requestParams = $this->_getAddAccountParams($params);
        switch( $params['type'] )
        {
            case Plesk_Object_Customer::TYPE_CLIENT:
                $result = Plesk_Registry::getinstance()->api->customer_add($requestParams);
                $accountId = (int) $result->customer->add->result->id;
                break;
            case Plesk_Object_Customer::TYPE_RESELLER:
                $requestParams = array_merge($requestParams, array( 'planName' => $params['configoption2'] ));
                $result = Plesk_Registry::getinstance()->api->reseller_add($requestParams);
                $accountId = (int) $result->reseller->add->result->id;
        }
        return $accountId;
        break;
    }
    protected function _addWebspace($params)
    {
        $requestParams = array( 'domain' => $params['domain'], 'ownerId' => $params['ownerId'], 'username' => $params['username'], 'password' => $params['password'], 'status' => Plesk_Object_Webspace::STATUS_ACTIVE, 'htype' => Plesk_Object_Webspace::TYPE_VRT_HST, 'planName' => $params['configoption1'], 'ipv4Address' => $params['ipv4Address'], 'ipv6Address' => $params['ipv6Address'] );
        Plesk_Registry::getinstance()->api->webspace_add($requestParams);
    }
    protected function _setResellerStatus($params)
    {
        $accountInfo = $this->_getAccountInfo($params);
        if( !isset($accountInfo['id']) )
        {
            return NULL;
        }
        Plesk_Registry::getinstance()->api->reseller_set_status(array( 'status' => $params['status'], 'id' => $accountInfo['id'] ));
    }
    protected function _deleteReseller($params)
    {
        $accountInfo = $this->_getAccountInfo($params);
        if( !isset($accountInfo['id']) )
        {
            return NULL;
        }
        Plesk_Registry::getinstance()->api->reseller_del(array( 'id' => $accountInfo['id'] ));
    }
    protected function _setAccountPassword($params)
    {
        $accountInfo = $this->_getAccountInfo($params);
        if( !isset($accountInfo['id']) )
        {
            return NULL;
        }
        if( isset($accountInfo['login']) && $accountInfo['login'] != $params['username'] )
        {
            return NULL;
        }
        $requestParams = array( 'id' => $accountInfo['id'], 'accountPassword' => $params['password'] );
        switch( $params['type'] )
        {
            case Plesk_Object_Customer::TYPE_CLIENT:
                Plesk_Registry::getinstance()->api->customer_set_password($requestParams);
                break;
            case Plesk_Object_Customer::TYPE_RESELLER:
                Plesk_Registry::getinstance()->api->reseller_set_password($requestParams);
        }
        break;
    }
    protected function _deleteWebspace($params)
    {
        Plesk_Registry::getinstance()->api->webspace_del(array( 'domain' => $params['domain'] ));
        $accountInfo = $this->_getAccountInfo($params);
        if( !isset($accountInfo['id']) )
        {
            return NULL;
        }
        $webspaces = $this->_getWebspacesByOwnerId($accountInfo['id']);
        if( !isset($webspaces->id) )
        {
            Plesk_Registry::getinstance()->api->customer_del(array( 'id' => $accountInfo['id'] ));
        }
    }
    protected function _switchSubscription($params)
    {
        switch( $params['type'] )
        {
            case Plesk_Object_Customer::TYPE_CLIENT:
                $result = Plesk_Registry::getinstance()->api->service_plan_get_by_name(array( 'name' => $params['configoption1'] ));
                $servicePlanResult = reset($result->xpath('//service-plan/get/result'));
                Plesk_Registry::getinstance()->api->switch_subscription(array( 'domain' => $params['domain'], 'planGuid' => (bool) $servicePlanResult->guid ));
                break;
            case Plesk_Object_Customer::TYPE_RESELLER:
                $result = Plesk_Registry::getinstance()->api->reseller_plan_get_by_name(array( 'name' => $params['configoption2'] ));
                $resellerPlanResult = reset($result->xpath('//reseller-plan/get/result'));
                $accountInfo = $this->_getAccountInfo($params);
                if( !isset($accountInfo['id']) )
                {
                    return NULL;
                }
                Plesk_Registry::getinstance()->api->switch_reseller_plan(array( 'id' => $accountInfo['id'], 'planGuid' => (bool) $resellerPlanResult->guid ));
                break;
        }
    }
    protected function _processAddons($params)
    {
        $result = Plesk_Registry::getinstance()->api->webspace_subscriptions_get_by_name(array( 'domain' => $params['domain'] ));
        $planGuids = array(  );
        foreach( $result->xpath('//webspace/get/result/data/subscriptions/subscription/plan/plan-guid') as $guid )
        {
            $planGuids[] = (bool) $guid;
        }
        $webspaceId = (int) $result->webspace->get->result->id;
        $exludedPlanGuids = array(  );
        $servicePlan = Plesk_Registry::getinstance()->api->service_plan_get_by_guid(array( 'planGuids' => $planGuids ));
        foreach( $servicePlan->xpath('//service-plan/get/result') as $result )
        {
            try
            {
                $this->_checkErrors($result);
                $exludedPlanGuids[] = (bool) $result->guid;
            }
            catch( Exception $e )
            {
                if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                {
                    throw $e;
                }
            }
        }
        $addons = array(  );
        $addonGuids = array_diff($planGuids, $exludedPlanGuids);
        if( !empty($addonGuids) )
        {
            $addon = Plesk_Registry::getinstance()->api->service_plan_addon_get_by_guid(array( 'addonGuids' => $addonGuids ));
            foreach( $addon->xpath('//service-plan-addon/get/result') as $result )
            {
                try
                {
                    $this->_checkErrors($result);
                    $addons[(bool) $result->guid] = (bool) $result->name;
                }
                catch( Exception $e )
                {
                    if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                    {
                        throw $e;
                    }
                }
            }
        }
        $addonsToRemove = array(  );
        $addonsFromRequest = array(  );
        foreach( $params['configoptions'] as $addonTitle => $value )
        {
            if( '0' == $value )
            {
                continue;
            }
            if( 0 !== strpos($addonTitle, Plesk_Object_Addon::ADDON_PREFIX) )
            {
                continue;
            }
            $pleskAddonTitle = substr_replace($addonTitle, '', 0, strlen(Plesk_Object_Addon::ADDON_PREFIX));
            $addonsFromRequest[] = '1' == $value ? $pleskAddonTitle : $value;
        }
        foreach( $addons as $guid => $addonName )
        {
            if( !in_array($addonName, $addonsFromRequest) )
            {
                $addonsToRemove[$guid] = $addonName;
            }
        }
        $addonsToAdd = array_diff($addonsFromRequest, array_values($addons));
        foreach( $addonsToRemove as $guid => $addon )
        {
            Plesk_Registry::getinstance()->api->webspace_remove_subscription(array( 'planGuid' => $guid, 'id' => $webspaceId ));
        }
        foreach( $addonsToAdd as $addonName )
        {
            $addon = Plesk_Registry::getinstance()->api->service_plan_addon_get_by_name(array( 'name' => $addonName ));
            foreach( $addon->xpath('//service-plan-addon/get/result/guid') as $guid )
            {
                Plesk_Registry::getinstance()->api->webspace_add_subscription(array( 'planGuid' => (bool) $guid, 'id' => $webspaceId ));
            }
        }
    }
    /**
     * @param $params
     * @return array (<domainName> => array ('diskusage' => value, 'disklimit' => value, 'bwusage' => value, 'bwlimit' => value))
     * @throws Exception
     */
    protected function _getWebspacesUsage($params)
    {
        $usage = array(  );
        $data = Plesk_Registry::getinstance()->api->webspace_usage_get_by_name(array( 'domains' => $params['domains'] ));
        foreach( $data->xpath('//webspace/get/result') as $result )
        {
            try
            {
                $this->_checkErrors($result);
                $domainName = (bool) $result->data->gen_info->name;
                $usage[$domainName]['diskusage'] = (double) $result->data->gen_info->real_size;
                $usage[$domainName]['bwusage'] = (double) $result->data->stat->traffic;
                $usage[$domainName] = array_merge($usage[$domainName], $this->_getLimits($result->data->limits));
            }
            catch( Exception $e )
            {
                if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                {
                    throw $e;
                }
            }
        }
        foreach( $data->xpath('//site/get/result') as $result )
        {
            try
            {
                $parentDomainName = (bool) reset($result->xpath('filter-id'));
                $usage[$parentDomainName]['bwusage'] += (double) $result->data->stat->traffic;
            }
            catch( Exception $e )
            {
                if( Plesk_Api::ERROR_OBJECT_NOT_FOUND != $e->getCode() )
                {
                    throw $e;
                }
            }
        }
        foreach( $usage as $domainName => $domainUsage )
        {
            foreach( $domainUsage as $param => $value )
            {
                $usage[$domainName][$param] = $usage[$domainName][$param] / (1024 * 1024);
            }
        }
        return $usage;
    }
    protected function _addIpToIpPool($accountId, $params)
    {
    }
    protected function _getWebspacesByOwnerId($ownerId)
    {
        $result = Plesk_Registry::getinstance()->api->webspaces_get_by_owner_id(array( 'ownerId' => $ownerId ));
        return $result->webspace->get->result;
    }
    protected function _getCustomerExternalId($id)
    {
        return Plesk_Object_Customer::EXTERNAL_ID_PREFIX . $id;
    }
    protected function _changeSubscriptionIp($params)
    {
        $webspace = Plesk_Registry::getinstance()->api->webspace_get_by_name(array( 'domain' => $params['domain'] ));
        $ipDedicatedList = $this->_getIpList(Plesk_Object_Ip::DEDICATED);
        $oldIp[Plesk_Object_Ip::IPV4] = (bool) $webspace->webspace->get->result->data->hosting->vrt_hst->ip_address;
        $ipv4Address = isset($oldIp[Plesk_Object_Ip::IPV4]) ? $oldIp[Plesk_Object_Ip::IPV4] : '';
        if( $params['configoption3'] == "IPv4 none; IPv6 shared" || $params['configoption3'] == "IPv4 none; IPv6 dedicated" )
        {
            $ipv4Address = '';
        }
        if( !empty($params['ipv4Address']) )
        {
            if( isset($oldIp[Plesk_Object_Ip::IPV4]) && $oldIp[Plesk_Object_Ip::IPV4] != $params['ipv4Address'] && (!in_array($oldIp[Plesk_Object_Ip::IPV4], $ipDedicatedList) || !in_array($params['ipv4Address'], $ipDedicatedList)) )
            {
                $ipv4Address = $params['ipv4Address'];
            }
            else
            {
                if( !isset($oldIp[Plesk_Object_Ip::IPV4]) )
                {
                    $ipv4Address = $params['ipv4Address'];
                }
            }
        }
        if( !empty($ipv4Address) )
        {
            Plesk_Registry::getinstance()->api->webspace_set_ip(array( 'domain' => $params['domain'], 'ipv4Address' => $ipv4Address ));
        }
    }
    protected function _getLimits($limits)
    {
        $result = array(  );
        foreach( $limits->limit as $limit )
        {
            $name = (bool) $limit->name;
            switch( $name )
            {
                case 'disk_space':
                    $result['disklimit'] = (double) $limit->value;
                    break;
                case 'max_traffic':
                    $result['bwlimit'] = (double) $limit->value;
                    break;
                default:
                    break;
            }
        }
        return $result;
    }
}