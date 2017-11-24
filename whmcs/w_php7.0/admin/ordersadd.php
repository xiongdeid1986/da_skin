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
define('ADMINAREA', true);
require("../init.php");
$aInt = new WHMCS_Admin("Add New Order", false);
$aInt->title = $aInt->lang('orders', 'addnew');
$aInt->sidebar = 'orders';
$aInt->icon = 'orders';
$aInt->requiredFiles(array( 'orderfunctions', 'domainfunctions', 'whoisfunctions', 'configoptionsfunctions', 'customfieldfunctions', 'clientfunctions', 'invoicefunctions', 'processinvoices', 'gatewayfunctions', 'modulefunctions', 'cartfunctions' ));
$action = $whmcs->get_req_var('action');
$userid = $whmcs->get_req_var('userid');
$currency = getCurrency($userid);
if( $action == 'getcontacts' )
{
    $contacts = array(  );
    $result = select_query('tblcontacts', 'id,firstname,lastname,companyname,email', array( 'userid' => (int) $whmcs->get_req_var('userid') ), 'firstname', 'ASC');
    while( $data = mysqli_fetch_array($result) )
    {
        $contacts[$data['id']] = $data['firstname'] . " " . $data['lastname'];
    }
    echo json_encode($contacts);
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'createpromo' )
{
    check_token("WHMCS.admin.default");
    if( !checkPermission("Create/Edit Promotions", true) )
    {
        throw new WHMCS_Exception_Fatal("You do not have permission to create promotional codes. If you feel this message to be an error, please contact the administrator.");
    }
    if( !$code )
    {
        exit( "Promotion Code is Required" );
    }
    if( $pvalue <= 0 )
    {
        exit( "Promotion Value must be greater than zero" );
    }
    $result = select_query('tblpromotions', "COUNT(*)", array( 'code' => $code ));
    $data = mysqli_fetch_array($result);
    $duplicates = $data[0];
    if( $duplicates )
    {
        exit( "Promotion Code already exists. Please try another." );
    }
    $promoid = insert_query('tblpromotions', array( 'code' => $code, 'type' => $type, 'recurring' => $recurring, 'value' => $pvalue, 'maxuses' => '1', 'recurfor' => $recurfor, 'expirationdate' => '0000-00-00', 'notes' => "Order Process One Off Custom Promo" ));
    $promo_type = $type;
    $promo_value = $pvalue;
    $promo_recurring = $recurring;
    $promo_code = $code;
    if( $promo_type == 'Percentage' )
    {
        $promo_value .= "%";
    }
    else
    {
        $promo_value = formatCurrency($promo_value);
    }
    $promo_recurring = $promo_recurring ? 'Recurring' : "One Time";
    echo "<option value=\"" . $promo_code . "\">" . $promo_code . " - " . $promo_value . " " . $promo_recurring . "</option>";
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'getconfigoptions' )
{
    check_token("WHMCS.admin.default");
    WHMCS_Session::release();
    if( !trim($pid) )
    {
        exit( dirname(__FILE__) . " | line".__LINE__ );
    }
    $options = '';
    $configoptions = getCartConfigOptions($pid, '', $cycle);
    if( count($configoptions) )
    {
        $options .= "<p><b>" . $aInt->lang('setup', 'configoptions') . "</b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">";
        foreach( $configoptions as $configoption )
        {
            $options .= "<tr><td width=\"130\" class=\"fieldlabel\">" . $configoption['optionname'] . "</td><td class=\"fieldarea\">";
            if( $configoption['optiontype'] == '1' )
            {
                $options .= "<select onchange=\"updatesummary()\" name=\"configoption[" . $orderid . "][" . $configoption['id'] . "]\">";
                foreach( $configoption['options'] as $optiondata )
                {
                    $options .= "<option value=\"" . $optiondata['id'] . "\"";
                    if( $optiondata['id'] == $configoption['selectedvalue'] )
                    {
                        $options .= " selected";
                    }
                    $options .= ">" . $optiondata['name'] . "</option>";
                }
                $options .= "</select>";
            }
            else
            {
                if( $configoption['optiontype'] == '2' )
                {
                    foreach( $configoption['options'] as $optiondata )
                    {
                        $options .= "<input type=\"radio\" onclick=\"updatesummary()\" name=\"configoption[" . $orderid . "][" . $configoption['id'] . "]\" value=\"" . $optiondata['id'] . "\"";
                        if( $optiondata['id'] == $configoption['selectedvalue'] )
                        {
                            $options .= " checked=\"checked\"";
                        }
                        $options .= "> " . $optiondata['name'] . "<br />";
                    }
                }
                else
                {
                    if( $configoption['optiontype'] == '3' )
                    {
                        $options .= "<input type=\"checkbox\" onclick=\"updatesummary()\" name=\"configoption[" . $orderid . "][" . $configoption['id'] . "]\" value=\"1\"";
                        if( $configoption['selectedqty'] )
                        {
                            $options .= " checked=\"checked\"";
                        }
                        $options .= "> " . $configoption['options'][0]['name'];
                    }
                    else
                    {
                        if( $configoption['optiontype'] == '4' )
                        {
                            $options .= "<input type=\"text\" onchange=\"updatesummary()\" name=\"configoption[" . $orderid . "][" . $configoption['id'] . "]\" value=\"" . $configoption['selectedqty'] . "\" size=\"5\"> x " . $configoption['options'][0]['name'];
                        }
                    }
                }
            }
            $options .= "</td></tr>";
        }
        $options .= "</table>";
    }
    $customfields = getCustomFields('product', $pid, '', '', 'on', $customfields);
    if( count($customfields) )
    {
        $options .= "<p><b>" . $aInt->lang('setup', 'customfields') . "</b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">";
        foreach( $customfields as $customfield )
        {
            $inputfield = str_replace("name=\"customfield", "name=\"customfield[" . $orderid . "]", $customfield['input']);
            $options .= "<tr><td width=\"130\" class=\"fieldlabel\">" . $customfield['name'] . "</td><td class=\"fieldarea\">" . $inputfield . "</td></tr>";
        }
        $options .= "</table>";
    }
    $addonshtml = '';
    $addonsarray = getAddons($pid);
    if( count($addonsarray) )
    {
        foreach( $addonsarray as $addon )
        {
            $addonshtml .= "<label>" . str_replace("<input type=\"checkbox\" name=\"addons", "<input type=\"checkbox\" onclick=\"updatesummary()\" name=\"addons[" . $orderid . "]", $addon['checkbox']) . " " . $addon['name'] . " (" . $addon['pricing'] . ")";
            if( $addon['description'] )
            {
                $addonshtml .= " - " . $addon['description'];
            }
            $addonshtml .= "</label><br />";
        }
    }
    echo json_encode(array( 'options' => $options, 'addons' => $addonshtml ));
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $action == 'getdomainaddlfields' )
{
    check_token("WHMCS.admin.default");
    $additflds = new WHMCS_Domains_AdditionalFields();
    $additflds->setDomain($domain);
    $addlfieldscode = '';
    foreach( $additflds->getFieldsForOutput($order) as $fieldLabel => $inputHTML )
    {
        $addlfieldscode .= "<tr class=\"domainaddlfields" . $order . "\"><td width=\"130\" class=\"fieldlabel\">" . $fieldLabel . "</td><td class=\"fieldarea\">" . $inputHTML . "</td></tr>";
    }
    echo $addlfieldscode;
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
if( $whmcs->get_req_var('submitorder') )
{
    check_token("WHMCS.admin.default");
    $userid = get_query_val('tblclients', 'id', array( 'id' => $userid ));
    if( !$userid && !$calconly )
    {
        infoBox("Invalid Client ID", "Please enter or select a valid client to add the order to");
    }
    else
    {
        $_SESSION['uid'] = $userid;
        getUsersLang($userid);
        $_SESSION['cart'] = array(  );
        $_SESSION['cart']['paymentmethod'] = $paymentmethod;
        foreach( $pid as $k => $prodid )
        {
            if( $prodid )
            {
                $addons[$k] = array_keys($addons[$k]);
                if( !$qty[$k] )
                {
                    $qty[$k] = 1;
                }
                $productarray = array( 'pid' => $prodid, 'domain' => $domain[$k], 'billingcycle' => str_replace(array( '-', " " ), '', strtolower($billingcycle[$k])), 'server' => '', 'configoptions' => $configoption[$k], 'customfields' => $customfield[$k], 'addons' => $addons[$k] );
                if( strlen($_POST['priceoverride'][$k]) )
                {
                    $productarray['priceoverride'] = $_POST['priceoverride'][$k];
                }
                for( $count = 1; $count <= $qty[$k]; $count++ )
                {
                    $_SESSION['cart']['products'][] = $productarray;
                }
            }
        }
        $validtlds = array(  );
        $result = select_query('tbldomainpricing', 'extension', '');
        while( $data = mysqli_fetch_array($result) )
        {
            $validtlds[] = $data[0];
        }
        foreach( $regaction as $k => $regact )
        {
            $domainparts = explode(".", $regdomain[$k], 2);
            if( $regact && in_array("." . $domainparts[1], $validtlds) )
            {
                $_SESSION['cart']['domains'][] = array( 'type' => $regact, 'domain' => $regdomain[$k], 'regperiod' => $regperiod[$k], 'dnsmanagement' => $dnsmanagement[$k], 'emailforwarding' => $emailforwarding[$k], 'idprotection' => $idprotection[$k], 'eppcode' => $eppcode[$k], 'fields' => $domainfield[$k] );
            }
        }
        if( $promocode )
        {
            $_SESSION['cart']['promo'] = $promocode;
        }
        $_SESSION['cart']['orderconfdisabled'] = $adminorderconf ? false : true;
        $_SESSION['cart']['geninvoicedisabled'] = $admingenerateinvoice ? false : true;
        if( !$adminsendinvoice )
        {
            $CONFIG['NoInvoiceEmailOnOrder'] = true;
        }
        $contactid = $whmcs->get_req_var('contactid');
        if( $contactid )
        {
            $_SESSION['cart']['contact'] = $contactid;
        }
        if( $calconly )
        {
            $ordervals = calcCartTotals();
            echo "<div class=\"ordersummarytitle\">Order Summary</div>\n<div id=\"ordersummary\">\n<table>\n";
            if( is_array($ordervals['products']) )
            {
                foreach( $ordervals['products'] as $cartprod )
                {
                    echo "<tr class=\"item\"><td colspan=\"2\"><div class=\"itemtitle\">" . $cartprod['productinfo']['groupname'] . " - " . $cartprod['productinfo']['name'] . "</div>";
                    echo $aInt->lang('billingcycles', $cartprod['billingcycle']);
                    if( $cartprod['domain'] )
                    {
                        echo " - " . $cartprod['domain'];
                    }
                    echo "<div class=\"itempricing\">";
                    if( $cartprod['priceoverride'] )
                    {
                        echo formatCurrency($cartprod['priceoverride']) . "*";
                    }
                    else
                    {
                        echo $cartprod['pricingtext'];
                    }
                    echo "</div>";
                    if( $cartprod['configoptions'] )
                    {
                        foreach( $cartprod['configoptions'] as $cartcoption )
                        {
                            if( !empty($cartcoption['optionname']) && empty($cartcoption['value']) )
                            {
                                $cartcoption['value'] = $cartcoption['optionname'];
                            }
                            if( $cartcoption['type'] == '1' || $cartcoption['type'] == '2' )
                            {
                                echo "<br />&nbsp;&raquo;&nbsp;" . $cartcoption['name'] . ": " . $cartcoption['value'];
                            }
                            else
                            {
                                if( $cartcoption['type'] == '3' )
                                {
                                    echo "<br />&nbsp;&raquo;&nbsp;" . $cartcoption['name'] . ": ";
                                    if( $cartcoption['qty'] )
                                    {
                                        echo $aInt->lang('global', 'yes');
                                    }
                                    else
                                    {
                                        echo $aInt->lang('global', 'no');
                                    }
                                }
                                else
                                {
                                    if( $cartcoption['type'] == '4' )
                                    {
                                        echo "<br />&nbsp;&raquo;&nbsp;" . $cartcoption['name'] . ": " . $cartcoption['qty'] . " x " . $cartcoption['option'];
                                    }
                                }
                            }
                        }
                    }
                    echo "</td></tr>";
                    if( $cartprod['addons'] )
                    {
                        foreach( $cartprod['addons'] as $addondata )
                        {
                            echo "<tr class=\"item\"><td colspan=\"2\"><div class=\"itemtitle\">" . $addondata['name'] . "</div><div class=\"itempricing\">" . $addondata['pricingtext'] . "</div></td></tr>";
                        }
                    }
                }
            }
            if( is_array($ordervals['domains']) )
            {
                foreach( $ordervals['domains'] as $cartdom )
                {
                    echo "<tr class=\"item\"><td colspan=\"2\"><div class=\"itemtitle\">" . $aInt->lang('fields', 'domain') . " " . $aInt->lang('domains', $cartdom['type']) . "</div>" . $cartdom['domain'] . " (" . $cartdom['regperiod'] . " " . $aInt->lang('domains', 'years') . ")";
                    if( $cartdom['dnsmanagement'] )
                    {
                        echo "<br />&nbsp;&raquo;&nbsp;" . $aInt->lang('domains', 'dnsmanagement');
                    }
                    if( $cartdom['emailforwarding'] )
                    {
                        echo "<br />&nbsp;&raquo;&nbsp;" . $aInt->lang('domains', 'emailforwarding');
                    }
                    if( $cartdom['idprotection'] )
                    {
                        echo "<br />&nbsp;&raquo;&nbsp;" . $aInt->lang('domains', 'idprotection');
                    }
                    echo "<div class=\"itempricing\">";
                    if( $cartdom['priceoverride'] )
                    {
                        echo formatCurrency($cartdom['priceoverride']) . "*";
                    }
                    else
                    {
                        echo $cartdom['price'];
                    }
                    echo "</div>";
                }
            }
            $cartitems = 0;
            foreach( array( 'products', 'addons', 'domains', 'renewals' ) as $k )
            {
                if( array_key_exists($k, $ordervals) )
                {
                    $cartitems += count($ordervals[$k]);
                }
            }
            if( !$cartitems )
            {
                echo "<tr class=\"item\"><td colspan=\"2\"><div class=\"itemtitle\" align=\"center\">No Items Selected</div></td></tr>";
            }
            echo "<tr class=\"subtotal\"><td>Subtotal</td><td class=\"alnright\">" . $ordervals['subtotal'] . "</td></tr>";
            if( $ordervals['promotype'] )
            {
                echo "<tr class=\"promo\"><td>Promo Discount</td><td class=\"alnright\">" . $ordervals['discount'] . "</td></tr>";
            }
            if( $ordervals['taxrate'] )
            {
                echo "<tr class=\"tax\"><td>" . $ordervals['taxname'] . " @ " . $ordervals['taxrate'] . "%</td><td class=\"alnright\">" . $ordervals['taxtotal'] . "</td></tr>";
            }
            if( $ordervals['taxrate2'] )
            {
                echo "<tr class=\"tax\"><td>" . $ordervals['taxname2'] . " @ " . $ordervals['taxrate2'] . "%</td><td class=\"alnright\">" . $ordervals['taxtotal2'] . "</td></tr>";
            }
            echo "<tr class=\"total\"><td width=\"140\">Total</td><td class=\"alnright\">" . $ordervals['total'] . "</td></tr>";
            if( $ordervals['totalrecurringmonthly'] || $ordervals['totalrecurringquarterly'] || $ordervals['totalrecurringsemiannually'] || $ordervals['totalrecurringannually'] || $ordervals['totalrecurringbiennially'] || $ordervals['totalrecurringtriennially'] )
            {
                echo "<tr class=\"recurring\"><td>Recurring</td><td class=\"alnright\">";
                if( $ordervals['totalrecurringmonthly'] )
                {
                    echo $ordervals['totalrecurringmonthly'] . " Monthly<br />";
                }
                if( $ordervals['totalrecurringquarterly'] )
                {
                    echo $ordervals['totalrecurringquarterly'] . " Quarterly<br />";
                }
                if( $ordervals['totalrecurringsemiannually'] )
                {
                    echo $ordervals['totalrecurringsemiannually'] . " Semi-Annually<br />";
                }
                if( $ordervals['totalrecurringannually'] )
                {
                    echo $ordervals['totalrecurringannually'] . " Annually<br />";
                }
                if( $ordervals['totalrecurringbiennially'] )
                {
                    echo $ordervals['totalrecurringbiennially'] . " Biennially<br />";
                }
                if( $ordervals['totalrecurringtriennially'] )
                {
                    echo $ordervals['totalrecurringtriennially'] . " Triennially<br />";
                }
                echo "</td></tr>";
            }
            echo "</table>\n</div>";
            exit( dirname(__FILE__) . " | line".__LINE__ );
        }
        $cartitems = count($_SESSION['cart']['products']) + count($_SESSION['cart']['addons']) + count($_SESSION['cart']['domains']) + count($_SESSION['cart']['renewals']);
        if( !$cartitems )
        {
            redir("noselections=1");
        }
        calcCartTotals(true);
        unset($_SESSION['uid']);
        if( $orderstatus == 'Active' )
        {
            update_query('tblorders', array( 'status' => 'Active' ), array( 'id' => $_SESSION['orderdetails']['OrderID'] ));
            if( is_array($_SESSION['orderdetails']['Products']) )
            {
                foreach( $_SESSION['orderdetails']['Products'] as $productid )
                {
                    update_query('tblhosting', array( 'domainstatus' => 'Active' ), array( 'id' => $productid ));
                }
            }
            if( is_array($_SESSION['orderdetails']['Domains']) )
            {
                foreach( $_SESSION['orderdetails']['Domains'] as $domainid )
                {
                    update_query('tbldomains', array( 'status' => 'Active' ), array( 'id' => $domainid ));
                }
            }
        }
        getUsersLang(0);
        redir("action=view&id=" . $_SESSION['orderdetails']['OrderID'], "orders.php");
    }
}
WHMCS_Session::release();
$regperiods = $regperiodss = '';
for( $regperiod = 1; $regperiod <= 10; $regperiod++ )
{
    $regperiods .= "<option value=\"" . $regperiod . "\">" . $regperiod . " " . $aInt->lang('domains', 'year' . $regperiodss) . "</option>";
    $regperiodss = 's';
}
$jquerycode = "\n\$(function(){\n    var prodtemplate = \$(\"#products .product:first\").clone();\n    var productsCount = 0;\n    window.addProduct = function(){\n        productsCount++;\n        var order = prodtemplate.clone().find(\"*\").each(function(){\n            var newId = this.id.substring(0, this.id.length-1) + productsCount;\n\n            \$(this).prev().attr(\"for\", newId); // update label for\n            this.id = newId; // update id\n\n        }).end()\n        .attr(\"id\", \"ord\" + productsCount)\n        .appendTo(\"#products\");\n        return false;\n    }\n    \$(\".addproduct\").click(addProduct);\n\n    var domainsCount = 0;\n    window.addDomain = function(){\n        domainsCount++;\n        \$('<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\" style=\"margin-top:10px;\"><tr><td width=\"130\" class=\"fieldlabel\">" . $aInt->lang('domains', 'regtype', 1) . "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"regaction['+domainsCount+']\" id=\"domnon'+domainsCount+'\" value=\"\" onclick=\"loaddomainoptions(this,0);updatesummary()\" checked /> <label for=\"domnon'+domainsCount+'\">" . $aInt->lang('global', 'none', 1) . "</label> <input type=\"radio\" name=\"regaction['+domainsCount+']\" value=\"register\" id=\"domreg'+domainsCount+'\" onclick=\"loaddomainoptions(this,1);updatesummary()\" /> <label for=\"domreg'+domainsCount+'\">" . $aInt->lang('domains', 'register', 1) . "</label> <input type=\"radio\" name=\"regaction['+domainsCount+']\" value=\"transfer\" id=\"domtrf'+domainsCount+'\" onclick=\"loaddomainoptions(this,2);updatesummary()\" /> <label for=\"domtrf'+domainsCount+'\">" . $aInt->lang('domains', 'transfer', 1) . "</label></td></tr><tr class=\"hiddenrow\" id=\"domrowdn'+domainsCount+'\" style=\"display:none;\"><td class=\"fieldlabel\">" . $aInt->lang('fields', 'domain', 1) . "</td><td class=\"fieldarea\"><input type=\"text\" class=\"regdomain\" id=\"regdomain'+domainsCount+'\" name=\"regdomain['+domainsCount+']\" size=\"40\" onkeyup=\"loaddomfields('+domainsCount+');updatesummary()\" /></td></tr><tr class=\"hiddenrow\" id=\"domrowrp'+domainsCount+'\" style=\"display:none;\"><td class=\"fieldlabel\">" . $aInt->lang('domains', 'regperiod', 1) . "</td><td class=\"fieldarea\"><select name=\"regperiod['+domainsCount+']\" onchange=\"updatesummary()\">" . $regperiods . "</select></td></tr><tr class=\"hiddentransrow\" id=\"domrowep'+domainsCount+'\" style=\"display:none;\"><td class=\"fieldlabel\">" . $aInt->lang('domains', 'eppcode', 1) . "</td><td class=\"fieldarea\"><input type=\"text\" name=\"eppcode['+domainsCount+']\" size=\"20\" /></td></tr><tr class=\"hiddenrow\" id=\"domrowad'+domainsCount+'\" style=\"display:none;\"><td class=\"fieldlabel\">" . $aInt->lang('domains', 'addons', 1) . "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"dnsmanagement['+domainsCount+']\" onclick=\"updatesummary()\" /> " . $aInt->lang('domains', 'dnsmanagement', 1) . "</label> <label><input type=\"checkbox\" name=\"emailforwarding['+domainsCount+']\" onclick=\"updatesummary()\" /> " . $aInt->lang('domains', 'emailforwarding', 1) . "</label> <label><input type=\"checkbox\" name=\"idprotection['+domainsCount+']\" onclick=\"updatesummary()\" /> " . $aInt->lang('domains', 'idprotection', 1) . "</label></td></tr><tr id=\"domainaddlfieldserase'+domainsCount+'\" style=\"display:none\"></tr></table>').appendTo(\"#domains\");\n        return false;\n    }\n    \$(\".adddomain\").click(addDomain);\n\n    \$(\"#domain0\").keyup(function() {\n      \$(\"#regdomain0\").val(\$(\"#domain0\").val());\n    });\n\n});\n\n\$(\"#inputUserId\").change(function() {\n    \$(\"#linkAddContact\").attr(\"href\", \"clientscontacts.php?userid=\" + \$(this).val() + \"&contactid=addnew\");\n    loadDomainContactOptions();\n});\n\$(\"#domnon0\").click(function() {\n    loadDomainContactOptions();\n});\n\$(\"#domreg0\").click(function() {\n    loadDomainContactOptions();\n});\n\$(\"#domtrf0\").click(function() {\n    loadDomainContactOptions();\n});\n\n";
$jscode = "\nfunction loadDomainContactOptions() {\n    if (!\$(\"#domreg0\").is(\":checked\") && !\$(\"#domtrf0\").is(\":checked\")) {\n        \$(\"#domainContactContainer\").hide();\n        return false;\n    }\n    \$.getJSON(\"ordersadd.php\", \"action=getcontacts&userid=\" + \$(\"#inputUserId\").val(), function(data){\n        var numberOfElements = data.length;\n        if (numberOfElements === 0) {\n            \$(\"#domainContactContainer\").hide();\n        } else {\n            \$(\"#inputContactID\").empty();\n            \$(\"#inputContactID\").append(\"<option value=\\\"0\\\">" . $aInt->lang('domains', 'domaincontactuseprimary', 1) . "</option>\");\n            \$.each(data, function(key, value) {\n               \$(\"#inputContactID\").append(\"<option value=\\\"\" + key + \"\\\">\" + value + \"</option>\");\n            });\n            \$(\"#domainContactContainer\").show();\n        }\n    });\n}\nfunction loadproductoptions(piddd) {\n    var ord = piddd.id.substring(3);\n    var pid = piddd.value;\n    var billingcycle = \$(\"#billingcycle option:selected\").val();\n    if (pid==0) {\n        \$(\"#productconfigoptions\"+ord).html(\"\");\n        \$(\"#addonsrow\"+ord).hide();\n        updatesummary();\n    } else {\n    \$(\"#productconfigoptions\"+ord).html(\"<p align=\\\"center\\\">" . $aInt->lang('global', 'loading') . "<br><img src=\\\"../images/loading.gif\\\"></p>\");\n    \$.post(\"ordersadd.php\", { action: \"getconfigoptions\", pid: pid, cycle: billingcycle, orderid: ord, token: \"" . generate_token('plain') . "\" },\n    function(data){\n        if (data.addons) {\n            \$(\"#addonsrow\"+ord).show();\n            \$(\"#addonscont\"+ord).html(data.addons);\n        } else {\n            \$(\"#addonsrow\"+ord).hide();\n        }\n        \$(\"#productconfigoptions\"+ord).html(data.options);\n        updatesummary();\n    },\"json\");\n    }\n}\nfunction loaddomainoptions(domrd,type) {\n    var ord = domrd.id.substring(6);\n    if (type==1) {\n        \$(\"#domrowdn\"+ord).css(\"display\",\"\");\n        \$(\"#domrowrp\"+ord).css(\"display\",\"\");\n        \$(\"#domrowep\"+ord).css(\"display\",\"none\");\n        \$(\"#domrowad\"+ord).css(\"display\",\"\");\n    } else if (type==2) {\n        \$(\"#domrowdn\"+ord).css(\"display\",\"\");\n        \$(\"#domrowrp\"+ord).css(\"display\",\"\");\n        \$(\"#domrowep\"+ord).css(\"display\",\"\");\n        \$(\"#domrowad\"+ord).css(\"display\",\"\");\n    } else {\n        \$(\"#domrowdn\"+ord).css(\"display\",\"none\");\n        \$(\"#domrowrp\"+ord).css(\"display\",\"none\");\n        \$(\"#domrowep\"+ord).css(\"display\",\"none\");\n        \$(\"#domrowad\"+ord).css(\"display\",\"none\");\n    }\n    loaddomfields(ord);\n}\nfunction updatesummary() {\n   domainchangenumber=0;\n    \$.post(\"ordersadd.php\", \"submitorder=1&calconly=1&\"+\$(\"#orderfrm\").serialize(),\n    function(data){\n        \$(\"#ordersumm\").html(data);\n    });\n}\nfunction loaddomfields(num) {\n    var domainname = \$(\"#regdomain\"+num).val();\n    if (domainname.length>=5) {\n        \$.post(\"ordersadd.php\", { action: \"getdomainaddlfields\", domain: domainname, order:num, token: \"" . generate_token('plain') . "\" },\n        function(data){\n            \$(\".domainaddlfields\"+num).remove();\n            \$(\"#domainaddlfieldserase\"+num).after(data);\n        });\n    }\n}\n";
ob_start();
if( !checkActiveGateway() )
{
    $aInt->gracefulExit($aInt->lang('gateways', 'nonesetup'));
}
if( $userid && !$paymentmethod )
{
    $paymentmethod = getClientsPaymentMethod($userid);
}
if( $whmcs->get_req_var('noselections') )
{
    infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('orders', 'noselections'));
}
echo $infobox;
echo "\n<form method=\"post\" onsubmit=\"return ifServerType()\" action=\"";
echo $_SERVER['PHP_SELF'];
echo "\" id=\"orderfrm\">\n<input type=\"hidden\" name=\"submitorder\" value=\"true\" />\n\n<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"top\" class=\"ordersummaryleftcol\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"130\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'client');
echo "</td><td class=\"fieldarea\">";
echo $aInt->clientsDropDown($userid);
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'paymentmethod');
echo "</td><td class=\"fieldarea\">";
echo paymentMethodsSelection();
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'promocode');
echo "</td><td class=\"fieldarea\"><select name=\"promocode\" id=\"promodd\" onchange=\"updatesummary()\"><option value=\"\">";
echo $aInt->lang('global', 'none');
echo "</option><optgroup label=\"Active Promotions\">";
$result = select_query('tblpromotions', '', "(maxuses<=0 OR uses<maxuses) AND (expirationdate='0000-00-00' OR expirationdate>='" . date('Ymd') . "')", 'code', 'ASC');
while( $data = mysqli_fetch_array($result) )
{
    $promo_id = $data['id'];
    $promo_code = $data['code'];
    $promo_type = $data['type'];
    $promo_recurring = $data['recurring'];
    $promo_value = $data['value'];
    if( $promo_type == 'Percentage' )
    {
        $promo_value .= "%";
    }
    else
    {
        $promo_value = formatCurrency($promo_value);
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_value = $aInt->lang('promos', 'freesetup');
    }
    $promo_recurring = $promo_recurring ? $aInt->lang('status', 'recurring') : $aInt->lang('status', 'onetime');
    if( $promo_type == "Price Override" )
    {
        $promo_recurring = $aInt->lang('promos', 'priceoverride');
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_recurring = '';
    }
    echo "<option value=\"" . $promo_code . "\">" . $promo_code . " - " . $promo_value . " " . $promo_recurring . "</option>";
}
echo "</optgroup><optgroup label=\"Expired Promotions\">";
$result = select_query('tblpromotions', '', "(maxuses>0 AND uses>=maxuses) OR (expirationdate!='0000-00-00' AND expirationdate<'" . date('Ymd') . "')", 'code', 'ASC');
while( $data = mysqli_fetch_array($result) )
{
    $promo_id = $data['id'];
    $promo_code = $data['code'];
    $promo_type = $data['type'];
    $promo_recurring = $data['recurring'];
    $promo_value = $data['value'];
    if( $promo_type == 'Percentage' )
    {
        $promo_value .= "%";
    }
    else
    {
        $promo_value = formatCurrency($promo_value);
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_value = $aInt->lang('promos', 'freesetup');
    }
    $promo_recurring = $promo_recurring ? $aInt->lang('status', 'recurring') : $aInt->lang('status', 'onetime');
    if( $promo_type == "Price Override" )
    {
        $promo_recurring = $aInt->lang('promos', 'priceoverride');
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_recurring = '';
    }
    echo "<option value=\"" . $promo_code . "\">" . $promo_code . " - " . $promo_value . " " . $promo_recurring . "</option>";
}
echo "</optgroup></select>";
if( checkPermission("Create/Edit Promotions", true) )
{
    echo " <a href=\"#\" onclick=\"showDialog('createpromo');return false\"><img src=\"images/icons/add.png\" border=\"0\" align=\"absmiddle\" /> " . $aInt->lang('orders', 'createpromo');
}
//该函数判断是否是IIS主机,如果是IIS主机则绑定多个域名


echo "</a></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('orders', 'status');
echo "</td><td class=\"fieldarea\"><select name=\"orderstatus\">\n<option value=\"Active\">";
echo $aInt->lang('status', 'active');
echo "</option>\n<option value=\"Pending\">";
echo $aInt->lang('status', 'pending');
echo "</option>\n</select></td></tr>\n<tr><td width=\"130\" class=\"fieldlabel\"></td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"adminorderconf\" id=\"adminorderconf\" checked /> <label for=\"adminorderconf\">";
echo $aInt->lang('orders', 'orderconfirmation');
echo "</label> <input type=\"checkbox\" name=\"admingenerateinvoice\" id=\"admingenerateinvoice\" checked /> <label for=\"admingenerateinvoice\">";
echo $aInt->lang('orders', 'geninvoice');
echo "</label> <input type=\"checkbox\" name=\"adminsendinvoice\" id=\"adminsendinvoice\" checked /> <label for=\"adminsendinvoice\">";
echo $aInt->lang('global', 'sendemail');
echo "</label></td></tr>\n</table>\n\n<div id=\"products\">\n<div id=\"ord0\" class=\"product\">\n\n<p><b>";
echo $aInt->lang('fields', 'product');
echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"130\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'product');
echo "</td><td class=\"fieldarea\"><select name=\"pid[]\" id=\"pid0\" onchange=\"loadproductoptions(this)\">";
echo $aInt->productDropDown(0, true);
echo "</select><span style=\"color:red;padding-left:8px\">请选择</span></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'domain');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"domain[]\" size=\"40\" id=\"domain0\" onkeyup=\"updatesummary()\" /> <span id=\"whoisresult0\"></span><span style=\"color:red;padding-left:8px\">请输入</span></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'billingcycle');
echo "</td><td class=\"fieldarea\">";
if( !$billingcycle )
{
    $billingcycle = 'Annually';
}
echo $aInt->cyclesDropDown($billingcycle, '', '', "billingcycle[]", "updatesummary()");
echo "</td></tr>\n<tr id=\"addonsrow0\" style=\"display:none;\"><td class=\"fieldlabel\">";
echo $aInt->lang('addons', 'title');
echo "</td><td class=\"fieldarea\" id=\"addonscont0\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'quantity');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"qty[]\" value=\"1\" size=\"5\" onkeyup=\"updatesummary()\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'priceoverride');
echo "</td><td class=\"fieldarea\"><input type=\"text\" value=\"0\" name=\"priceoverride[]\" size=\"10\" onkeyup=\"updatesummary()\" /> ";
echo $aInt->lang('orders', 'priceoverridedesc');
echo "</td></tr>\n</table>\n\n<div id=\"productconfigoptions0\"></div>\n\n</div>\n</div>\n\n<p style=\"padding-left:20px;\"><a href=\"#\" class=\"addproduct\"><img src=\"images/icons/add.png\" border=\"0\" align=\"absmiddle\" /> ";
echo $aInt->lang('orders', 'anotherproduct');
echo "</a></p>\n\n<p><b>";
echo $aInt->lang('domains', 'domainreg');
echo "</b></p>\n\n<div id=\"domains\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"130\" class=\"fieldlabel\">";
echo $aInt->lang('domains', 'regtype');
echo "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"regaction[0]\" id=\"domnon0\" value=\"\" onclick=\"loaddomainoptions(this,0);updatesummary()\" checked /> <label for=\"domnon0\">";
echo $aInt->lang('global', 'none');
echo "</label> <input type=\"radio\" name=\"regaction[0]\" value=\"register\" id=\"domreg0\" onclick=\"loaddomainoptions(this,1);updatesummary()\" /> <label for=\"domreg0\">";
echo $aInt->lang('domains', 'register');
echo "</label> <input type=\"radio\" name=\"regaction[0]\" value=\"transfer\" id=\"domtrf0\" onclick=\"loaddomainoptions(this,2);updatesummary()\" /> <label for=\"domtrf0\">";
echo $aInt->lang('domains', 'transfer');
echo "</label></td></tr>\n<tr class=\"hiddenrow\" id=\"domrowdn0\" style=\"display:none;\"><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'domain');
echo "</td><td class=\"fieldarea\"><input type=\"text\" class=\"regdomain\" name=\"regdomain[0]\" size=\"40\" id=\"regdomain0\" onkeyup=\"loaddomfields(0);updatesummary()\" /></td></tr>\n<tr class=\"hiddenrow\" id=\"domrowrp0\" style=\"display:none;\"><td class=\"fieldlabel\">";
echo $aInt->lang('domains', 'regperiod');
echo "</td><td class=\"fieldarea\"><select id=\"regperiod1\" name=\"regperiod[0]\" onchange=\"updatesummary()\">";
echo $regperiods;
echo "</select></td></tr>\n<tr class=\"hiddentransrow\" id=\"domrowep0\" style=\"display:none;\"><td class=\"fieldlabel\">";
echo $aInt->lang('domains', 'eppcode');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"eppcode[0]\" size=\"20\" /></td></tr>\n<tr class=\"hiddenrow\" id=\"domrowad0\" style=\"display:none;\"><td class=\"fieldlabel\">";
echo $aInt->lang('domains', 'addons');
echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"dnsmanagement[0]\" onclick=\"updatesummary()\" /> ";
echo $aInt->lang('domains', 'dnsmanagement');
echo "</label> <label><input type=\"checkbox\" name=\"emailforwarding[0]\" onclick=\"updatesummary()\" /> ";
echo $aInt->lang('domains', 'emailforwarding');
echo "</label> <label><input type=\"checkbox\" name=\"idprotection[0]\" onclick=\"updatesummary()\" /> ";
echo $aInt->lang('domains', 'idprotection');
echo "</label></td></tr>\n<tr id=\"domainaddlfieldserase0\" style=\"display:none;\"></tr>\n</table>\n\n</div>\n\n<p style=\"padding-left:20px;\"><a href=\"#\" class=\"adddomain\"><img src=\"images/icons/add.png\" border=\"0\" align=\"absmiddle\" /> ";
echo $aInt->lang('orders', 'anotherdomain');
echo "</a></p>\n\n<div id=\"domainContactContainer\" style=\"display:none;\">\n\n<p><b>";
echo $aInt->lang('domains', 'domainregcontact');
echo "</b></p>\n\n<p>";
echo sprintf($aInt->lang('domains', 'domainregcontactorderinfo'), "<a href=\"#\" id=\"linkAddContact\">", "</a>");
echo "</p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"130\" class=\"fieldlabel\">";
echo $aInt->lang('domains', 'domaincontactchoose');
echo "</td><td class=\"fieldarea\"><select name=\"contactid\" id=\"inputContactID\"></select></td></tr>\n</table>\n\n</div>\n\n</td><td valign=\"top\">\n\n<div id=\"ordersumm\" style=\"padding:15px;\"></div>\n\n<div class=\"ordersummarytitle\"><input type=\"submit\" value=\"";
echo $aInt->lang('orders', 'submit');
echo " &raquo;\" class=\"btn-primary\" style=\"font-size:20px;padding:12px 30px ;\" /></div>\n\n</td></tr></table>\n\n</form>\n\n<script> updatesummary();\n";
echo "var domainchangenumber=0;\n";
echo "function ifServerType(){\n";
echo "var type=$(\"#pid0 option:selected\").attr('servertype');\n";
echo "if(type == 'easypanel' && domainchangenumber < 1){\ndomainchangenumber++;\n";
echo "var val=$('#domain0').val();\n";
echo "if(val.indexOf('www.')!=-1){\n";
echo "$('#domain0').val(val+','+('domain'+val).split('www.')[1]);\n";
echo "}else{\n";
echo "$('#domain0').val(val+',www.'+val);\n";
echo "}\n";
echo "}\n";
//echo "alert($('#domain0').val());\n";
//echo "return false;\n";
echo "}\n";
echo "</script>\n";
echo $aInt->jqueryDialog('createpromo', $aInt->lang('orders', 'createpromo'), "<form id=\"createpromofrm\">\n" . generate_token('form') . "\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\" width=\"110\">" . $aInt->lang('fields', 'promocode') . "</td><td class=\"fieldarea\"><input type=\"text\" name=\"code\" id=\"promocode\" /></td></tr>\n<tr><td class=\"fieldlabel\">" . $aInt->lang('fields', 'type') . "</td><td class=\"fieldarea\"><select name=\"type\">\n<option value=\"Percentage\">" . $aInt->lang('promos', 'percentage') . "</option>\n<option value=\"Fixed Amount\">" . $aInt->lang('promos', 'fixedamount') . "</option>\n<option value=\"Price Override\">" . $aInt->lang('promos', 'priceoverride') . "</option>\n<option value=\"Free Setup\">" . $aInt->lang('promos', 'freesetup') . "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">" . $aInt->lang('promos', 'value') . "</td><td class=\"fieldarea\"><input type=\"text\" name=\"pvalue\" size=\"10\" /></td></tr>\n<tr><td class=\"fieldlabel\">" . $aInt->lang('promos', 'recurring') . "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"recurring\" id=\"recurring\" value=\"1\" /> <label for=\"recurring\">" . $aInt->lang('promos', 'recurenable') . "</label> <input type=\"text\" name=\"recurfor\" size=\"3\" value=\"0\" /> " . $aInt->lang('promos', 'recurenable2') . "</td></tr>\n</table>\n<p>* " . $aInt->lang('orders', 'createpromoinfo') . "</p>\n</form>", array( $aInt->lang('global', 'ok') => "savePromo()", $aInt->lang('global', 'cancel') => '' ), '', '500', '');
$jscode .= "function savePromo() {\n    jQuery.post(\"ordersadd.php\", \"action=createpromo&\"+jQuery(\"#createpromofrm\").serialize(),\n    function(data){\n        if (data.substr(0,1)==\"<\") {\n            \$(\"#promodd\").append(data);\n            \$(\"#promodd\").val(\$(\"#promocode\").val());\n            \$(\"#createpromo\").dialog(\"close\");\n        } else {\n            alert(data);\n        }\n    });\n}";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();