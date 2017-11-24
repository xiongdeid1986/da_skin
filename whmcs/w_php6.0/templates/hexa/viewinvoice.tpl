<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset={$charset}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$companyname} - {* This code should be uncommented for EU companies using the sequential invoice numbering so that when unpaid it is shown as a proforma invoice {if $status eq "Paid"}*}{$LANG.invoicenumber}{*{else}{$LANG.proformainvoicenumber}{/if}*}{$invoicenum}</title>
    <link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link href="templates/{$template}/css/invoice.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-lg-offset-1">    
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {if $error}
                        <div class="alert alert-danger">{$LANG.invoiceserror}</div>
                        {else}
                        <div class="row">
                            <div class="col-lg-8">
                                {if $logo}<p><img src="{$logo}" title="{$companyname}" /></p>{else}<h2>{$companyname}</h2>{/if}
                            </div>
                            <div class="col-lg-4 text-right">
                                {if $status eq "Unpaid"}
                                <p><span class="label label-danger">{$LANG.invoicesunpaid}</span></p>
                                {if $allowchangegateway}
                                <form method="post" class="hidden-print" action="{$smarty.server.PHP_SELF}?id={$invoiceid}">{$gatewaydropdown}</form>
                                {else}
                                {$paymentmethod}
                                {/if}
                                <div class="hidden-print" style="float:right;">{$paymentbutton}</div>
                                {elseif $status eq "Paid"}
                                <p><span class="label label-success">{$LANG.invoicespaid}</span></p>
                                {$paymentmethod}
                                ({$datepaid})
                                {elseif $status eq "Refunded"}
                                <p><span class="label label-info">{$LANG.invoicesrefunded}</span></p>
                                {elseif $status eq "Cancelled"}
                                <p><span class="label label-info">{$LANG.invoicescancelled}</span></p>
                                {elseif $status eq "Collections"}
                                <p><span class="label label-info">{$LANG.invoicescollections}</span></p>
                                {/if}
                            </div>
                        </div>
                        {if $smarty.get.paymentsuccess}
                        <p class="paid">{$LANG.invoicepaymentsuccessconfirmation}</p>
                        {elseif $smarty.get.pendingreview}
                        <p class="paid">{$LANG.invoicepaymentpendingreview}</p>
                        {elseif $smarty.get.paymentfailed}
                        <p class="unpaid">{$LANG.invoicepaymentfailedconfirmation}</p>
                        {elseif $offlinepaid}
                        <p class="refunded">{$LANG.invoiceofflinepaid}</p>
                        {/if}
                    </div>
                    <div class="panel-body">
                        {if $manualapplycredit}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                <div class="panel-body panel-success">
                                        <h4>{$LANG.invoiceaddcreditdesc1} <span class="label label-success">{$totalcredit}</span></h4><p>{$LANG.invoiceaddcreditdesc2}{$LANG.invoiceaddcreditamount}.</p>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <form  method="post" action="{$smarty.server.PHP_SELF}?id={$invoiceid}">
                                                    <input type="hidden" name="applycredit" value="true" />
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="creditamount" value="{$creditamount}" />
                                                        <span class="input-group-btn">
                                                            <input type="submit" class="btn btn-default" value="{$LANG.invoiceaddcreditapply}" />
                                                        </span>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/if}

                        <div class="row">
                            <div class="col-xs-4">
                                <h4>{* This code should be uncommented for EU companies using the sequential invoice numbering so that when unpaid it is shown as a proforma invoice {if $status eq "Paid"}*}{$LANG.invoicenumber}{*{else}{$LANG.proformainvoicenumber}{/if}*} <span class="badge">{$invoicenum}</span></h4>
                                <p>{$LANG.invoicesdatecreated}: {$datecreated}<br />
                                    {$LANG.invoicesdatedue}: {$datedue}</p>
                                </div>
                                <div class="col-xs-4">
                                    <h4>{$LANG.invoicesinvoicedto}</h4>
                                    <p>
                                        {if $clientsdetails.companyname}{$clientsdetails.companyname}<br />{/if}
                                        {$clientsdetails.firstname} {$clientsdetails.lastname}<br />
                                        {$clientsdetails.address1}, {$clientsdetails.address2}<br />
                                        {$clientsdetails.city}, {$clientsdetails.state}, {$clientsdetails.postcode}<br />
                                        {$clientsdetails.country}
                                        {if $customfields}
                                        <br /><br />
                                        {foreach from=$customfields item=customfield}
                                        {$customfield.fieldname}: {$customfield.value}<br />
                                        {/foreach}
                                        {/if}
                                    </p>
                                </div>
                                <div class="col-xs-4">
                                    <h4>{$LANG.invoicespayto}</h4>
                                    <p>{$payto}</p>
                                </div>
                            </div>

                            <table class="table">
                                <tr>
                                    <td>{$LANG.invoicesdescription}</td>
                                    <td>{$LANG.invoicesamount}</td>
                                </tr>
                                {foreach from=$invoiceitems item=item}
                                <tr>
                                    <td>{$item.description}{if $item.taxed eq "true"} *{/if}</td>
                                    <td>{$item.amount}</td>
                                </tr>
                                {/foreach}
                                <tr>
                                    <td>{$LANG.invoicessubtotal}:</td>
                                    <td>{$subtotal}</td>
                                </tr>
                                {if $taxrate}
                                <tr>
                                    <td>{$taxrate}% {$taxname}:</td>
                                    <td>{$tax}</td>
                                </tr>
                                {/if}
                                {if $taxrate2}
                                <tr>
                                    <td>{$taxrate2}% {$taxname2}:</td>
                                    <td>{$tax2}</td>
                                </tr>
                                {/if}
                                <tr>
                                    <td>{$LANG.invoicescredit}:</td>
                                    <td>{$credit}</td>
                                </tr>
                                <tr class="info">
                                    <td>{$LANG.invoicestotal}:</td>
                                    <td>{$total}</td>
                                </tr>
                            </table>

                            {if $taxrate}<p>* {$LANG.invoicestaxindicator}</p>{/if}

                            <h4>{$LANG.invoicestransactions}</h4>

                            <table class="table table-striped">
                                <tr>
                                    <td>{$LANG.invoicestransdate}</td>
                                    <td>{$LANG.invoicestransgateway}</td>
                                    <td>{$LANG.invoicestransid}</td>
                                    <td>{$LANG.invoicestransamount}</td>
                                </tr>
                                {foreach from=$transactions item=transaction}
                                <tr>
                                    <td>{$transaction.date}</td>
                                    <td>{$transaction.gateway}</td>
                                    <td>{$transaction.transid}</td>
                                    <td>{$transaction.amount}</td>
                                </tr>
                                {foreachelse}
                                <tr>
                                    <td colspan="4">{$LANG.invoicestransnonefound}</td>
                                </tr>
                                {/foreach}
                                <tr class="info">
                                    <td colspan="3">{$LANG.invoicesbalance}:</td>
                                    <td>{$balance}</td>
                                </tr>
                            </table>

                            {if $notes}
                            <p>{$LANG.invoicesnotes}: {$notes}</p>
                            {/if}

                            {/if}

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-9 col-lg-offset-1 text-right hidden-print">
                    <a href="clientarea.php" class="btn btn-default btn-sm">{$LANG.invoicesbacktoclientarea}</a> <a href="dl.php?type=i&amp;id={$invoiceid}" class="btn btn-default btn-sm">{$LANG.invoicesdownload}</a> <a href="javascript:window.close()" class="btn btn-default btn-sm btn-danger">{$LANG.closewindow}</a>
                </div>
            </div>
        </div>
    </body>
    </html>