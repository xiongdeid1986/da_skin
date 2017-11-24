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
$GATEWAY = getGatewayVariables('myideal');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
require_once(dirname(__FILE__) . "/myideal_lib.php");
require_once(dirname(__FILE__) . "/ThinMPI.php");
$conf = LoadConfiguration();
$orderNumber = $_POST['ordernumber'];
$description = $_POST['description'];
$currency = $_POST['currency'];
$amount = $_POST['grandtotal'];
$amount *= 100;
$product1number = '1';
$issuerID = $_POST['issuerID'];
if( $issuerID == 0 )
{
    print "Kies uw bank uit de lijst om met iDEAL te betalen<br>";
    exit();
}
$data = new AcquirerTrxRequest();
$data->setIssuerID($issuerID);
$directory = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$directory = substr($directory, 0, strrpos($directory, '/') + 1);
$returnURL = $directory . "StatReq.php";
$data->setMerchantReturnURL($returnURL);
$data->setPurchaseID($orderNumber);
$data->setAmount($amount);
$data->setCurrency($currency);
$data->setExpirationPeriod($conf['EXPIRATIONPERIOD']);
$data->setLanguage($conf['LANGUAGE']);
$data->setDescription($description);
$rule = new ThinMPI();
$result = new AcquirerTrxResponse();
$result = $rule->ProcessRequest($data);
if( $result->isOK() )
{
    $transactionID = $result->getTransactionID();
    if( !mysql_num_rows(full_query("SHOW TABLES LIKE 'mod_myideal'")) )
    {
        $query = "CREATE TABLE `mod_myideal` (`transid` TEXT NOT NULL ,`invoiceid` TEXT NOT NULL ,`password` TEXT NOT NULL)";
        $result = full_query($query);
    }
    delete_query('mod_myideal', array( 'transid' => $transactionID ));
    delete_query('mod_myideal', array( 'invoiceid' => $description ));
    insert_query('mod_myideal', array( 'transid' => $transactionID, 'invoiceid' => $description ));
    $amount /= 100;
    $ISSURL = $result->getIssuerAuthenticationURL();
    $ISSURL = html_entity_decode($ISSURL);
    header("Location: " . $ISSURL);
    exit();
}
echo "<p><b>Bestelling</b></p>\n";
print "Er is helaas iets misgegaan. Foutmelding van iDEAL:<br>";
$Msg = $result->getErrorMessage();
print $Msg . "<br>";