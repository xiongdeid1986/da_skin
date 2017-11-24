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
if( !function_exists('opensrs_GetConfigArray') )
{
    include_once(ROOTDIR . "/modules/registrars/opensrs/opensrs.php");
}
function resellone_getConfigArray()
{
    $configArray = opensrs_getConfigArray();
    $configArray['FriendlyName']['Value'] = 'ResellOne';
    return $configArray;
}
function resellone_GetNameservers($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_GetNameservers($params, $O, 'resellone');
}
function resellone_SaveNameservers($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_SaveNameservers($params, $O, 'resellone');
}
function resellone_GetRegistrarLock($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_GetRegistrarLock($params, $O, 'resellone');
}
function resellone_SaveRegistrarLock($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_SaveRegistrarLock($params, $O, 'resellone');
}
function resellone_RegisterDomain($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_RegisterDomain($params, $O, 'resellone');
}
function resellone_TransferDomain($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_TransferDomain($params, $O, 'resellone');
}
function resellone_RenewDomain($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_RenewDomain($params, $O, 'resellone');
}
function resellone_GetContactDetails($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_GetContactDetails($params, $O, 'resellone');
}
function resellone_SaveContactDetails($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_SaveContactDetails($params, $O, 'resellone');
}
function resellone_GetEPPCode($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_GetEPPCode($params, $O, 'resellone');
}
function resellone_RegisterNameserver($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_RegisterNameserver($params, $O, 'resellone');
}
function resellone_DeleteNameserver($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_DeleteNameserver($params, $O, 'resellone');
}
function resellone_ModifyNameserver($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_ModifyNameserver($params, $O, 'resellone');
}
function resellone_Sync($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_Sync($params, $O, 'resellone');
}
function resellone_TransferSync($params)
{
    try
    {
        $O = resellone_Connect($params['Username'], $params['PrivateKey'], $params['TestMode']);
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return opensrs_TransferSync($params, $O, 'resellone');
}
function resellone_AdminDomainsTabFields($params)
{
    return opensrs_AdminDomainsTabFields($params);
}
function resellone_AdminDomainsTabFieldsSave($params)
{
    return opensrs_AdminDomainsTabFieldsSave($params);
}
/**
 * Connect to resellOne
 *
 * @throws Exception
 *
 * @param $username
 * @param $privateKey
 * @param bool $testMode
 *
 * @return resellone_base
 */
function resellone_Connect($username, $privateKey, $testMode = false)
{
    $mode = 'live';
    if( $testMode )
    {
        $mode = 'test';
    }
    require_once(dirname(__FILE__) . "/resellone_base.php");
    if( !class_exists('PEAR') )
    {
        $error = "OpenSRS/ResellOne Class Files Missing. Visit <a href=\"http://nullrefer.com/?http://docs.whmcs.com/" . "OpenSRS#Additional_Registrar_Module_Files_Requirement\" target=\"_blank\">" . "http://docs.whmcs.com/OpenSRS#Additional_Registrar_Module_Files_Requirement</a> to resolve";
        throw new Exception($error);
    }
    $connection = new resellone_base($mode, 'XCP', $username, $privateKey);
    return $connection;
}