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
class Plesk_Manager_V1635 extends Plesk_Manager_V1632
{
    protected function _createSession($params)
    {
        $ownerInfo = $this->_getAccountInfo($params);
        if( !isset($ownerInfo['login']) )
        {
            return null;
        }
        $result = Plesk_Registry::getinstance()->api->session_create(array( 'login' => $ownerInfo['login'], 'userIp' => base64_encode($_SERVER['REMOTE_ADDR']) ));
        return $result->server->create_session->result->id;
    }
    protected function _getClientAreaForm($params)
    {
        $address = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
        $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
        $secure = $params['serversecure'] ? 'https' : 'http';
        if( empty($address) )
        {
            return '';
        }
        $sessionId = $this->_createSession($params);
        if( is_null($sessionId) )
        {
            return '';
        }
        $form = sprintf("<form action=\"%s://%s:%s/enterprise/rsession_init.php\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"PLESKSESSID\" value=\"%s\" />" . "<input type=\"hidden\" name=\"PHPSESSID\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($address), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($sessionId), WHMCS_Input_Sanitize::encode($sessionId), Plesk_Registry::getinstance()->translator->translate('BUTTON_CONTROL_PANEL'));
        return $form;
    }
}