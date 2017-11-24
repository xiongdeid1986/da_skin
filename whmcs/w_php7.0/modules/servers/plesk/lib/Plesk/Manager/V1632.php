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
class Plesk_Manager_V1632 extends Plesk_Manager_V1630
{
    protected function _processAddons($params)
    {
        parent::_processaddons($params);
    }
    protected function _addWebspace($params)
    {
        parent::_addwebspace($params);
    }
    protected function _getSharedIpv4($params)
    {
        return $this->_getIp($params);
    }
    protected function _getSharedIpv6($params)
    {
        return $this->_getIp($params, Plesk_Object_Ip::IPV6);
    }
    protected function _getFreeDedicatedIpv4()
    {
        return $this->_getFreeDedicatedIp();
    }
    protected function _getFreeDedicatedIpv6()
    {
        return $this->_getFreeDedicatedIp(Plesk_Object_Ip::IPV6);
    }
    protected function _getIpList($type = Plesk_Object_Ip::SHARED, $version = null)
    {
        $ipList = array(  );
        static $result;
        if( is_null($result) )
        {
            $result = Plesk_Registry::getinstance()->api->ip_get();
        }
        foreach( $result->ip->get->result->addresses->ip_info as $item )
        {
            if( $type !== (bool) $item->type )
            {
                continue;
            }
            $ip = (bool) $item->ip_address;
            if( Plesk_Object_Ip::IPV6 == $version && !$this->_isIpv6($ip) )
            {
                continue;
            }
            if( Plesk_Object_Ip::IPV4 == $version && $this->_isIpv6($ip) )
            {
                continue;
            }
            $ipList[] = $ip;
        }
        return $ipList;
    }
    protected function _getFreeDedicatedIp($version = Plesk_Object_Ip::IPV4)
    {
        static $domains;
        $ipListUse = array(  );
        $ipListFree = array(  );
        $ipList = $this->_getIpList(Plesk_Object_Ip::DEDICATED, $version);
        if( is_null($domains) )
        {
            $domains = Plesk_Registry::getinstance()->api->webspaces_get();
        }
        foreach( $domains->xpath('//webspace/get/result') as $item )
        {
            try
            {
                $this->_checkErrors($item);
                foreach( $item->data->hosting->vrt_hst->ip_address as $ip )
                {
                    $ipListUse[(bool) $ip] = (bool) $ip;
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
        foreach( $ipList as $ip )
        {
            if( !in_array($ip, $ipListUse) )
            {
                $ipListFree[] = $ip;
            }
        }
        $freeIp = reset($ipListFree);
        if( empty($freeIp) )
        {
            throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_NO_FREE_DEDICATED_IPTYPE', array( 'TYPE' => Plesk_Object_Ip::IPV6 == $version ? 'IPv6' : 'IPv4' )));
        }
        return $freeIp;
    }
    /**
     * @param $params
     * @return array (<domainName> => array ('diskusage' => value, 'disklimit' => value, 'bwusage' => value, 'bwlimit' => value))
     */
    protected function _getWebspacesUsage($params)
    {
        return parent::_getwebspacesusage($params);
    }
    protected function _changeSubscriptionIp($params)
    {
        $webspace = Plesk_Registry::getinstance()->api->webspace_get_by_name(array( 'domain' => $params['domain'] ));
        $ipDedicatedList = $this->_getIpList(Plesk_Object_Ip::DEDICATED);
        foreach( $webspace->webspace->get->result->data->hosting->vrt_hst->ip_address as $ip )
        {
            $ip = (bool) $ip;
            $oldIp[$this->_isIpv6($ip) ? Plesk_Object_Ip::IPV6 : Plesk_Object_Ip::IPV4] = $ip;
        }
        $ipv4Address = isset($oldIp[Plesk_Object_Ip::IPV4]) ? $oldIp[Plesk_Object_Ip::IPV4] : '';
        $ipv6Address = isset($oldIp[Plesk_Object_Ip::IPV6]) ? $oldIp[Plesk_Object_Ip::IPV6] : '';
        if( $params['configoption3'] == "IPv4 none; IPv6 shared" || $params['configoption3'] == "IPv4 none; IPv6 dedicated" )
        {
            $ipv4Address = '';
        }
        if( $params['configoption3'] == "IPv4 shared; IPv6 none" || $params['configoption3'] == "IPv4 dedicated; IPv6 none" )
        {
            $ipv6Address = '';
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
        if( !empty($params['ipv6Address']) )
        {
            if( isset($oldIp[Plesk_Object_Ip::IPV6]) && $oldIp[Plesk_Object_Ip::IPV6] != $params['ipv6Address'] && (!in_array($oldIp[Plesk_Object_Ip::IPV6], $ipDedicatedList) || !in_array($params['ipv6Address'], $ipDedicatedList)) )
            {
                $ipv6Address = $params['ipv6Address'];
            }
            else
            {
                if( !isset($oldIp[Plesk_Object_Ip::IPV6]) )
                {
                    $ipv6Address = $params['ipv6Address'];
                }
            }
        }
        if( !empty($ipv4Address) || !empty($ipv6Address) )
        {
            Plesk_Registry::getinstance()->api->webspace_set_ip(array( 'domain' => $params['domain'], 'ipv4Address' => $ipv4Address, 'ipv6Address' => $ipv6Address ));
        }
    }
}