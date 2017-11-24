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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
if( !function_exists('resellerclub_GetConfigArray') )
{
    require(ROOTDIR . "/modules/registrars/resellerclub/resellerclub.php");
}
function stargate_GetConfigArray()
{
    $vals = resellerclub_GetConfigArray();
    $vals['FriendlyName']['Value'] = 'StarGate/UK2';
    unset($vals['Description']);
    return $vals;
}
function stargate_GetNameservers($params)
{
    return resellerclub_GetNameservers($params);
}
function stargate_SaveNameservers($params)
{
    return resellerclub_SaveNameservers($params);
}
function stargate_GetRegistrarLock($params)
{
    return resellerclub_GetRegistrarLock($params);
}
function stargate_SaveRegistrarLock($params)
{
    return resellerclub_SaveRegistrarLock($params);
}
function stargate_RegisterDomain($params)
{
    return resellerclub_RegisterDomain($params);
}
function stargate_TransferDomain($params)
{
    return resellerclub_TransferDomain($params);
}
function stargate_RenewDomain($params)
{
    return resellerclub_RenewDomain($params);
}
function stargate_GetContactDetails($params)
{
    return resellerclub_GetContactDetails($params);
}
function stargate_SaveContactDetails($params)
{
    return resellerclub_SaveContactDetails($params);
}
function stargate_GetEPPCode($params)
{
    return resellerclub_GetEPPCode($params);
}
function stargate_RegisterNameserver($params)
{
    return resellerclub_RegisterNameserver($params);
}
function stargate_ModifyNameserver($params)
{
    return resellerclub_ModifyNameserver($params);
}
function stargate_DeleteNameserver($params)
{
    return resellerclub_DeleteNameserver($params);
}
function stargate_RequestDelete($params)
{
    return resellerclub_RequestDelete($params);
}
function stargate_GetDNS($params)
{
    return resellerclub_GetDNS($params);
}
function stargate_SaveDNS($params)
{
    return resellerclub_SaveDNS($params);
}
function stargate_GetEmailForwarding($params)
{
    return resellerclub_GetEmailForwarding($params);
}
function stargate_SaveEmailForwarding($params)
{
    return resellerclub_SaveEmailForwarding($params);
}
function stargate_ReleaseDomain($params)
{
    return resellerclub_ReleaseDomain($params);
}
function stargate_IDProtectToggle($params)
{
    return resellerclub_IDProtectToggle($params);
}
function stargate_Sync($params)
{
    return resellerclub_Sync($params);
}
function stargate_TransferSync($params)
{
    return resellerclub_TransferSync($params);
}