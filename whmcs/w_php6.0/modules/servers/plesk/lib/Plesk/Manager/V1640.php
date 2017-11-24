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
class Plesk_Manager_V1640 extends Plesk_Manager_V1635
{
    /**
     * @param $params
     * @return array (<domainName> => array ('diskusage' => value, 'disklimit' => value, 'bwusage' => value, 'bwlimit' => value))
     * @throws Exception
     */
    protected function _getWebspacesUsage($params)
    {
        $usage = array(  );
        $webspaces = Plesk_Registry::getinstance()->api->webspace_usage_get_by_name(array( 'domains' => $params['domains'] ));
        foreach( $webspaces->xpath('//webspace/get/result') as $result )
        {
            try
            {
                $this->_checkErrors($result);
                $domainName = (bool) $result->data->gen_info->name;
                $usage[$domainName]['diskusage'] = (double) $result->data->gen_info->real_size;
                $resourceUsage = reset($result->data->xpath('resource-usage'));
                foreach( $resourceUsage->resource as $resource )
                {
                    $name = (bool) $resource->name;
                    if( 'max_traffic' == $name )
                    {
                        $usage[$domainName]['bwusage'] = (double) $resource->value;
                        break;
                    }
                }
                $usage[$domainName] = array_merge($usage[$domainName], $this->_getLimits($result->data->limits));
                foreach( $usage[$domainName] as $param => $value )
                {
                    $usage[$domainName][$param] = $usage[$domainName][$param] / (1024 * 1024);
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
        return $usage;
    }
}