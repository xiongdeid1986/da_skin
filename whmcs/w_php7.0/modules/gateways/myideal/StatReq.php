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
require_once(dirname(__FILE__) . "/myideal_lib.php");
require_once(dirname(__FILE__) . "/ThinMPI.php");
$GATEWAY = getGatewayVariables('myideal');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$urltowhmcs = $CONFIG['SystemURL'] . '/';
$whmcslogo = $CONFIG['LogoURL'];
$data = new AcquirerStatusRequest();
$transID = $_GET['trxid'];
$transID = str_pad($transID, 16, '0');
$data->setTransactionID($transID);
$rule = new ThinMPI();
$result = $rule->ProcessRequest($data);
if( !$result->isOK() )
{
    $error_message = $result->getErrorMessage();
}
else
{
    if( !$result->isAuthenticated() )
    {
        $error_message = "Uw bestelling is helaas niet betaald, probeer het nog eens";
    }
    else
    {
        $transactionID = $result->getTransactionID();
        $invoiceid = get_query_val('mod_myideal', 'invoiceid', array( 'transid' => $transactionID ));
        $logdata = array( 'TransactionID' => $transactionID, 'InvoiceID' => $invoiceid );
        if( !$invoiceid )
        {
            logTransaction('iDEAL', $logdata, "Invoice ID Not Found");
        }
        logTransaction('iDEAL', $logdata, 'Successful');
        addInvoicePayment($invoiceid, $transactionID, '', '', 'myideal');
        redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
    }
}
if( $error_message )
{
    echo "<html>\n<head>\n  <title> iDeal Payment Failed </title>\n  <meta http-equiv=\"refresh\" content=\"10; url=";
    echo $urltowhmcs;
    echo "clientarea.php?action=invoices\">\n</head>\n<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#0000FF\" vlink=\"#800080\" alink=\"#FF0000\">\n\n<center>\n\n<img src=\"";
    echo $whmcslogo;
    echo "\"><br/><br/>\n\n<p>De betaling is niet voldaan. U kunt het wellicht nogmaals proberen of een andere betaalwijze kiezen. <br />U wordt nu teruggestuurd naar het overzicht van uw facturen.<br />\n<a href=\"";
    echo $urltowhmcs;
    echo "clientarea.php?action=invoices\">Klik hier om verder te gaan</a></p>\n\nThe payment was not made. Please try again or choose a different way to pay. <br />You will now be send back to the invoice overview.«<br/>\n<a href=\"";
    echo $urltowhmcs;
    echo "clientarea.php?action=invoices\">Please click here to continue</a><br/><br/>\n\n<p>";
    echo $error_message;
    echo "</p>\n\n</center>\n\n</body>\n</html>\n";
}