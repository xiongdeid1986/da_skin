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
$GATEWAY = getGatewayVariables('tco');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
if( $GATEWAY['secretword'] )
{
    $string_to_hash = $GATEWAY['secretword'] . $GATEWAY['vendornumber'] . $_REQUEST['x_trans_id'] . $_REQUEST['x_amount'];
    $check_key = strtoupper(md5($string_to_hash));
    if( $check_key != $_REQUEST['x_MD5_Hash'] )
    {
        logTransaction($GATEWAY['name'], $_REQUEST, "MD5 Hash Failure");
        redirSystemURL("action=invoices", "clientarea.php");
    }
}
echo "<html>\n<head>\n<title>" . $CONFIG['CompanyName'] . "</title>\n</head>\n<body>\n<p>Payment Processing Completed. However it may take a while for 2CheckOut fraud verification to complete and the payment to be reflected on your account. Please wait while you are redirected back to the client area...</p>\n";
if( $_POST['x_response_code'] == '1' )
{
    $invoiceid = checkCbInvoiceID($_POST['x_invoice_num'], '2CheckOut');
    if( $GATEWAY['skipfraudcheck'] )
    {
        echo "<meta http-equiv=\"refresh\" content=\"2;url=" . $CONFIG['SystemURL'] . "/viewinvoice.php?id=" . $invoiceid . "&paymentsuccess=true\">";
    }
    else
    {
        echo "<meta http-equiv=\"refresh\" content=\"2;url=" . $CONFIG['SystemURL'] . "/viewinvoice.php?id=" . $invoiceid . "&pendingreview=true\">";
    }
}
else
{
    logTransaction('2CheckOut', $_REQUEST, 'Unsuccessful');
    echo "<meta http-equiv=\"refresh\" content=\"2;url=" . $CONFIG['SystemURL'] . "/clientarea.php?action=invoices\">";
}
echo "\n</body>\n</html>";