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
$GATEWAYMODULE['pagseguroname'] = 'pagseguro';
$GATEWAYMODULE['pagsegurovisiblename'] = 'PagSeguro';
$GATEWAYMODULE['pagsegurotype'] = 'Invoices';
function pagseguro_activate()
{
    defineGatewayField('pagseguro', 'text', 'email', '', "Email Address", '50', '');
    defineGatewayField('pagseguro', 'text', 'callbacktoken', '', "Callback Token", '30', '');
}
function pagseguro_link($params)
{
    $number = preg_replace("/[^0-9]/", '', $params['clientdetails']['phonenumber']);
    if( 12 < strlen($number) )
    {
        $number = substr($number, strlen($number) - 12, strlen($number));
    }
    $formatednumber = substr_replace('000000000000', $number, strlen($mask) - strlen($number));
    $cliente_tel = substr($formatednumber, 4, 8);
    $cliente_ddd = substr($formatednumber, 2, 2);
    $code = "<form target=\"pagseguro\" action=\"https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx\" method=\"post\">\n<input type=\"hidden\" name=\"email_cobranca\" value=\"" . $params['email'] . "\">\n<input type=\"hidden\" name=\"tipo\" value=\"CP\">\n<input type=\"hidden\" name=\"moeda\" value=\"BRL\">\n<input type=\"hidden\" name=\"item_id_1\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"item_descr_1\" value=\"" . $params['description'] . "\">\n<input type=\"hidden\" name=\"item_quant_1\" value=\"1\">\n<input type=\"hidden\" name=\"item_valor_1\" value=\"" . $params['amount'] * 100 . "\">\n<input type=\"hidden\" name=\"ref_transacao\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"cliente_nome\" value=\"" . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\" />\n<input type=\"hidden\" name=\"cliente_cep\" value=\"" . $params['clientdetails']['postcode'] . "\" />\n<input type=\"hidden\" name=\"cliente_end\" value=\"" . $params['clientdetails']['address1'] . "\" />\n<input type=\"hidden\" name=\"cliente_bairro\" value=\"" . $params['clientdetails']['address2'] . "\" />\n<input type=\"hidden\" name=\"cliente_cidade\" value=\"" . $params['clientdetails']['city'] . "\" />\n<input type=\"hidden\" name=\"cliente_uf\" value=\"" . $params['clientdetails']['state'] . "\" />\n<input type=\"hidden\" name=\"cliente_pais\" value=\"BRA\" />\n<input type=\"hidden\" name=\"cliente_ddd\" value=\"" . $cliente_ddd . "\" />\n<input type=\"hidden\" name=\"cliente_tel\" value=\"" . $cliente_tel . "\" />\n<input type=\"hidden\" name=\"cliente_email\" value=\"" . $params['clientdetails']['email'] . "\" />\n<input type=\"hidden\" name=\"cliente_num\" value=\"s\\n\" />\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}