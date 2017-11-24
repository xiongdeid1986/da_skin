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
require("../../../init.php");
$whmcs->load_function('gateway');
$whmcs->load_function('invoice');
$GATEWAY = getGatewayVariables('pagseguro');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$PagSeguro = "Comando=validar";
$PagSeguro .= "&Token=" . $GATEWAY['callbacktoken'];
foreach( $_POST as $k => $v )
{
    $PagSeguro .= "&" . $k . "=" . urlencode(stripslashes($v));
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://pagseguro.uol.com.br/Security/NPI/Default.aspx");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $PagSeguro);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$resp = curl_exec($ch);
if( !tep_not_null($resp) )
{
    curl_setopt($ch, CURLOPT_URL, "https://pagseguro.uol.com.br/Security/NPI/Default.aspx");
    $resp = curl_exec($ch);
}
curl_close($ch);
if( strcmp($resp, 'VERIFICADO') == 0 )
{
    $VendedorEmail = addslashes($_POST['VendedorEmail']);
    $TransacaoID = addslashes($_POST['TransacaoID']);
    $Referencia = (int) $_POST['Referencia'];
    $StatusTransacao = addslashes($_POST['StatusTransacao']);
    $TipoPagamento = addslashes($_POST['TipoPagamento']);
    $CliNome = addslashes($_POST['CliNome']);
    $NumItens = addslashes($_POST['NumItens']);
    $ProdValor = number_format(str_replace(array( ',', "." ), ".", addslashes($_POST['ProdValor_1'])), 2, ".", '');
    $Taxa = 0;
    $invoiceid = checkCbInvoiceID($Referencia, 'PagSeguro');
    switch( $TipoPagamento )
    {
        case 'Boleto':
        case 'Pagamento':
            break;
        case "Pagamento Online":
            $Taxa = ($ProdValor * 2.9) / 100 + 0.4;
            break;
        case "Cartão de Crédito":
            $Taxa = ($ProdValor * 6.4) / 100 + 0.4;
    }
    $result = select_query('tblinvoices', 'userid,status', array( 'id' => $invoiceid ));
    $payments = mysql_fetch_array($result);
    $userid = $payments['userid'];
    $status = $payments['status'];
    if( $GATEWAY['convertto'] )
    {
        $currency = getCurrency($userid);
        $ProdValor = convertCurrency($ProdValor, $GATEWAY['convertto'], $currency['id']);
        $Taxa = convertCurrency($Taxa, $GATEWAY['convertto'], $currency['id']);
    }
    if( $GATEWAY['email'] != $VendedorEmail )
    {
        logTransaction('PagSeguro', $_REQUEST, "Invalid Vendor Email");
        return 1;
    }
    if( $StatusTransacao == 'Aprovado' )
    {
        if( $status == 'Unpaid' )
        {
            addInvoicePayment($invoiceid, $TransacaoID, $ProdValor, $Taxa, 'pagseguro');
        }
        logTransaction('PagSeguro', $_REQUEST, 'Incomplete');
        redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
        return 1;
    }
    if( $StatusTransacao == 'Completo' )
    {
        $result = select_query('tblinvoices', 'status', array( 'id' => $invoiceid ));
        $payments = mysql_fetch_array($result);
        $status = $payments['status'];
        if( $status == 'Unpaid' )
        {
            addInvoicePayment($invoiceid, $TransacaoID, $ProdValor, $Taxa, 'pagseguro');
        }
        logTransaction('PagSeguro', $_REQUEST, 'Completed');
        redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
        return 1;
    }
    if( $StatusTransacao == 'Cancelado' )
    {
        logTransaction('PagSeguro', $_REQUEST, 'Cancelled');
        redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
        return 1;
    }
    logTransaction('PagSeguro', $_REQUEST, 'Processing');
    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
    return 1;
    break;
}
logTransaction('PagSeguro', $_REQUEST, 'Error');
redirSystemURL("action=invoices", "clientarea.php");
function tep_not_null($value)
{
    if( is_array($value) )
    {
        if( 0 < sizeof($value) )
        {
            return true;
        }
        return false;
    }
    if( $value != '' && $value != 'NULL' && 0 < strlen(trim($value)) )
    {
        return true;
    }
    return false;
}