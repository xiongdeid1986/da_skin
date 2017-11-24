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
if( isset($_GET['invoiceid']) )
{
    require("../../init.php");
    $whmcs->load_function('gateway');
    $whmcs->load_function('invoice');
    global $_LANG;
    $GATEWAY = getGatewayVariables('directdebit');
    if( !$GATEWAY['type'] )
    {
        exit( "Module Not Activated" );
    }
    $invoiceID = (int) $whmcs->get_req_var('invoiceid');
    if( WHMCS_Session::get('adminid') )
    {
        $result = select_query('tblinvoices', "id, userid", array( 'id' => $invoiceID ));
    }
    else
    {
        $result = select_query('tblinvoices', "id, userid", array( 'id' => $invoiceID, 'userid' => (int) WHMCS_Session::get('uid') ));
    }
    $data = mysql_fetch_array($result);
    $invoiceID = $data['id'];
    $userID = $data['userid'];
    if( !$invoiceID )
    {
        exit( "Access Denied" );
    }
    echo "<!DOCTYPE html>\n<html lang=\"en\">\n    <head>\n        <meta http-equiv=\"content-type\" content=\"text/html; charset=";
    echo $CONFIG['Charset'];
    echo "\" />\n        <title>\n            ";
    echo $_LANG['directDebitPageTitle'];
    echo "        </title>\n        <link href=\"../../templates/default/css/invoice.css\" rel=\"stylesheet\">\n    </head>\n    <body>\n        <div class=\"wrapper\">\n            <p>\n                <img src=\"";
    echo $CONFIG['LogoURL'];
    echo "\" title=\"";
    echo $CONFIG['CompanyName'];
    echo "\" />\n            </p>\n            <h1>\n                ";
    echo $_LANG['directDebitHeader'];
    echo "            </h1>\n        ";
    if( $submit )
    {
        $errorMessage = '';
        if( !$bankName )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorNoBankName'];
        }
        if( !in_array($bankAccType, array( 'Checking', 'Savings' )) )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorAccountType'];
        }
        if( !$bankABACode )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorNoABA'];
        }
        if( !$bankAccNumber )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorAccNumber'];
        }
        if( !$bankAccNumber2 )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorConfirmAccNumber'];
        }
        if( $bankAcctNumber != $bankAcctNumber2 )
        {
            $errorMessage .= "<li>" . $_LANG['directDebitErrorAccNumberMismatch'];
        }
        if( !$errorMessage )
        {
            update_query('tblclients', array( 'bankname' => $bankName, 'banktype' => $bankAccType, 'bankcode' => $bankABACode, 'bankacct' => $bankAccNumber ), array( 'id' => $userID ));
            echo "<p align=\"center\">" . $_LANG['directDebitThanks'] . "</p>\n        <p align=\"center\"><a href=\"#\" onclick=\"window.close()\">" . $_LANG['closewindow'] . "</a></p>\n        ";
        }
    }
    if( !$submit || $errorMessage )
    {
        echo "            <p>\n                ";
        echo $_LANG['directDebitPleaseSubmit'];
        echo "            </p>\n            <form method=\"post\" action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "?invoiceid=";
        echo $invoiceID;
        echo "\">\n                <input type=\"hidden\" name=\"submit\" value=\"true\" />\n                ";
        if( $errorMessage )
        {
            echo "<div class=\"creditbox\" style=\"text-align:left;\"><b>" . $_LANG['directDebitFollowingError'] . "</b></p><ul>" . $errorMessage . "</ul></div>";
        }
        if( !$bankAccType || $bankAccType == 'Checking' )
        {
            $checkingChecked = " checked";
            $savingsChecked = '';
        }
        else
        {
            $checkingChecked = '';
            $savingsChecked = " checked";
        }
        echo "                <table>\n                    <tr>\n                        <td>\n                            ";
        echo $_LANG['directDebitBankName'];
        echo "                        </td>\n                        <td>\n                            <input type=\"text\" name=\"bankName\" size=\"30\" value=\"";
        echo $bankName;
        echo "\" />\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                            ";
        echo $_LANG['directDebitAccountType'];
        echo "                        </td>\n                        <td>\n                            <label>\n                                <input\n                                    type=\"radio\" name=\"bankAccType\" value=\"Checking\"";
        echo $checkingChecked;
        echo " />\n                                ";
        echo $_LANG['directDebitChecking'];
        echo "                            </label>\n                            <label>\n                                <input type=\"radio\" name=\"bankAccType\" value=\"Savings\"";
        echo $savingsChecked;
        echo " />\n                                ";
        echo $_LANG['directDebitSavings'];
        echo "                            </label>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                            ";
        echo $_LANG['directDebitABA'];
        echo "                        </td>\n                        <td>\n                            <input type=\"text\" name=\"bankABACode\" size=\"20\" value=\"";
        echo $bankABACode;
        echo "\" />\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                            ";
        echo $_LANG['directDebitAccNumber'];
        echo "                        </td>\n                        <td>\n                            <input type=\"text\" name=\"bankAccNumber\" size=\"20\" value=\"";
        echo $bankAccNumber;
        echo "\" />\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                            ";
        echo $_LANG['directDebitConfirmAccNumber'];
        echo "                        </td>\n                        <td>\n                            <input type=\"text\" name=\"bankAccNumber2\" size=\"20\" value=\"";
        echo $bankAccNumber2;
        echo "\" />\n                        </td>\n                    </tr>\n                </table>\n                <p align=\"center\">\n                    <img src=\"http://cdn.whmcs.com/assets/img/achinfographic.gif\" />\n                </p>\n                <p align=\"center\">\n                    <input type=\"submit\" value=\"";
        echo $_LANG['directDebitSubmit'];
        echo "\" />\n                </p>\n            </form>\n        ";
    }
    echo "        </div>\n    </body>\n</html>\n";
}
/**
 * The configuration of the Direct Debit module which will return the options
 * for the client to configure.
 *
 * @return array The configuration array
 */
function directdebit_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "Direct Debit" ) );
    return $configarray;
}
/**
 * Used to generate a form for the client to assist in making a payment.
 *
 * @param array $params The parameters for the payment
 * @return string The code to display a payment link to the client
 */
function directdebit_link($params)
{
    $code = "<form method=\"post\" action=\"modules/gateways/directdebit.php?invoiceid=" . $params['invoiceid'] . "\">\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\" />\n</form>";
    return $code;
}