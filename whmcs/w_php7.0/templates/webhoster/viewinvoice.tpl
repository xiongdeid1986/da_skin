{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset={$charset}" />
    <title>{$companyname} - {* This code should be uncommented for EU companies using the sequential invoice numbering so that when unpaid it is shown as a proforma invoice {if $status eq "Paid"}*}{$LANG.invoicenumber}{*{else}{$LANG.proformainvoicenumber}{/if}*}{$invoicenum}</title>
		<!-- basic styles -->
		<link href="templates/{$template}/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
		<link href='//fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="templates/{$template}/assets/css/themes/style.css" />
		<link rel="stylesheet" href="templates/{$template}/assets/css/whmcs.css" />
		<link href="templates/{$template}/assets/css/invoice.css" rel="stylesheet">
		
		<!--[if lt IE 9]>
		<script src="templates/{$template}/assets/js/html5shiv.js"></script>
		<script src="templates/{$template}/assets/js/respond.min.js"></script>
		<![endif]-->
  </head>
  <body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-lg-offset-2">
            <div class="padding-25 white" style="margin-bottom: 15px;">
				{if $error}
                <div class="alert alert-danger">{$LANG.invoiceserror}</div>
				{else}
                <div class="row">
                    <div class="col-md-8">{if $logo}
                        <p><img src="{$logo}" title="{$companyname}" class="img-responsive" /></p>
						{else}
                        <h3 class="text-gray"><i class="fa fa-leaf text-primary"></i> {$companyname}</h3>{/if}
					</div>
                    <div class="col-md-4 text-right">
						{if $status eq "Unpaid"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-important">{$LANG.invoicesunpaid}</span></p>{if $allowchangegateway}
						<form method="post" class="form-horizontal" action="{$smarty.server.PHP_SELF}?id={$invoiceid}">{$gatewaydropdown}</form>{else}{$paymentmethod}{/if}
						<div style="float: right;">{$paymentbutton}</div>
						{elseif $status eq "Paid"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-success">{$LANG.invoicespaid}</span></p>
						{$paymentmethod} ({$datepaid}) 
						{elseif $status eq "Refunded"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-inverse">{$LANG.invoicesrefunded}</span></p>
						{elseif $status eq "Cancelled"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-inverse">{$LANG.invoicescancelled}</span></p>
						{elseif $status eq "Collections"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-warning">{$LANG.invoicescollections}</span></p>
						{/if}
					</div>
				</div>
				<div class="space-8"></div>
				<div class="hr hr-6 dotted hr-double"></div>
				<div class="row">
					<div class="col-md-12">
							<p class="pull-left text-primary">{* This code should be uncommented for EU companies using the sequential invoice numbering so that when unpaid it is shown as a proforma invoice {if $status eq "Paid"}*}{$LANG.invoicenumber}{*{else}{$LANG.proformainvoicenumber}{/if}*}{$invoicenum}</p>
                            <p class="pull-right">{$LANG.invoicesdatecreated}: {$datecreated} <br /> {$LANG.invoicesdatedue}: {$datedue}</p>
					</div>
				</div>
				<div class="hr hr-6 dotted hr-double"></div>
				<div class="space-8"></div>
				<div class="row">
					<div class="col-md-12">
					{if $smarty.get.paymentsuccess}
						<p class="paid">{$LANG.invoicepaymentsuccessconfirmation}</p>
						{elseif $smarty.get.pendingreview}
						<p class="paid">{$LANG.invoicepaymentpendingreview}</p>
						{elseif $smarty.get.paymentfailed}
						<p class="unpaid">{$LANG.invoicepaymentfailedconfirmation}</p>
						{elseif $offlinepaid}
						<p class="refunded">{$LANG.invoiceofflinepaid}</p>
					{/if} 					
					{if $manualapplycredit}
							<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?id={$invoiceid}">
							<input type="hidden" name="applycredit" value="true" />
								<div class="alert alert-success">
									<small>{$LANG.invoiceaddcreditdesc1} {$totalcredit}. {$LANG.invoiceaddcreditdesc2}{$LANG.invoiceaddcreditamount}:</small>
								</div>
								<div class="row">
									<div class="col-sm-4 pull-right">
										<div class="input-group">
											<input type="text" class="form-control" name="creditamount" value="{$creditamount}" />
											<span class="input-group-btn"><input type="submit" class="btn btn-purple btn-sm" value="{$LANG.invoiceaddcreditapply}" /></span>
										</div>
									</div>
								</div>
							</form>
					{/if}
					</div>
				</div>
				<div class="space-8"></div>
				<div class="row">
					<div class="col-sm-6">
					<div class="portlet no-border">
						<div class="portlet-heading">
							<div class="portlet-title">
								<h4>{$LANG.invoicesinvoicedto}</h4>
							</div>
						</div>
						<div class="portlet-body"> 						
							{if $clientsdetails.companyname}{$clientsdetails.companyname}<br />{/if}
							<strong>{$clientsdetails.firstname} {$clientsdetails.lastname}</strong>
							<br />{$clientsdetails.address1}, {$clientsdetails.address2}
							<br />{$clientsdetails.city}, {$clientsdetails.state}, {$clientsdetails.postcode}
							<br />{$clientsdetails.country} {if $customfields}<br />
							<br />{foreach from=$customfields item=customfield} {$customfield.fieldname}: {$customfield.value}
							<br />{/foreach}{/if}
						</div>
						</div>
					</div>
					<div class="col-sm-6">
					<div class="portlet no-border">
						<div class="portlet-heading">
							<div class="portlet-title">
								<h4>{$LANG.invoicespayto}</h4>
							</div>
						</div>
						<div class="portlet-body">
							{$payto}
						</div>
						</div>
					</div>
				</div>				
				<div class="space-8"></div>				
											
				
				<table class="table table-striped table-bordered tc-table table-primary">
					<thead>
						<tr>
							<th>{$LANG.invoicesdescription}</th>
							<th style="width: 105px;">{$LANG.invoicesamount}</th>
						</tr>
					</thead>
					{foreach from=$invoiceitems item=item}
						<tr>
							<td>{$item.description}{if $item.taxed eq "true"} *{/if}</td>
							<td>{$item.amount}</td>
						</tr>
					{/foreach}
						<tr>
							<td class="text-right">{$LANG.invoicessubtotal}:</td>
							<td>{$subtotal}</td>
						</tr>
						{if $taxrate}
						<tr>
							<td class="text-right">{$taxrate}% {$taxname}:</td>
							<td>{$tax}</td>
						</tr>
						{/if}
						{if $taxrate2}
						<tr>
							<td class="text-right">{$taxrate2}% {$taxname2}:</td>
							<td>{$tax2}</td>
						</tr>
						{/if}
						<tr>
							<td class="text-right">{$LANG.invoicescredit}:</td>
							<td>{$credit}</td>
						</tr>
						<tr class="text-danger">
							<td class="text-right">{$LANG.invoicestotal}:</td>
							<td>{$total}</td>
						</tr>
				</table>				

				{if $taxrate}<p><span class="text-danger">*</span> {$LANG.invoicestaxindicator}</p>{/if}
				<p class="label label-sm label-default arrowed arrowed-right">{$LANG.invoicestransactions}</p>

				<div class="table-responsive"><table class="table table-striped table-bordered tc-table">
					<thead class="thin-border-bottom">
						<tr>
							<th>{$LANG.invoicestransdate}</th>
							<th>{$LANG.invoicestransgateway}</th>
							<th>{$LANG.invoicestransid}</th>
							<th class="col-medium">{$LANG.invoicestransamount}</th>
						</tr>
					</thead>
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
						<tr>
							<td colspan="3">{$LANG.invoicesbalance}:</td>
							<td class="text-danger">{$balance}</td>
						</tr>
				</table></div>
				{if $notes}<p>{$LANG.invoicesnotes}: {$notes}</p>{/if}
				{/if}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 col-lg-offset-2 text-center">
			<div class="btn-group btn-group-xs">
				<a href="clientarea.php" class="btn btn-primary">{$LANG.invoicesbacktoclientarea}</a>
				<a href="dl.php?type=i&amp;id={$invoiceid}" class="btn btn-inverse"><i class="fa fa-print icon-only"></i></a> <a href="javascript:window.close()" class="btn btn-danger"><i class="fa fa-times icon-only"></i></a>
			</div>
		</div>
	</div>
	<div class="space-8"></div>	
</div>
</body>
</html>