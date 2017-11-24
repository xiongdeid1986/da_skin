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
		<title>{$companyname} - {$id}</title>
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
		<link rel="stylesheet" type="text/css" href="templates/{$template}/assets/css/quote.css">
		<link rel="stylesheet" href="templates/{$template}/assets/css/plugins/jqueryui/jquery-ui-1.10.4.full.min.css" />
		<script type="text/javascript" src="includes/jscript/jquery.js"></script>
		<script type="text/javascript" src="includes/jscript/jqueryui.js"></script>
		
{literal}<script>
$(document).ready(function(){

$("#quoteaccept").dialog({
    autoOpen: false,
    resizable: false,
	title: "<h4 class='lighter'><i class='fa fa-info text-danger'></i> Quote Acceptance</h4>",
    modal: true,
	buttons: [ { text: "Agree & Accept", "class": "btn btn-success", click: function() {
		    $("#quoteacceptfrm").submit(); 
			
		}} ]
});

});
</script>{/literal}
</head>
<body>

{if $error}
<div class="alert alert-danger">{$LANG.invoiceserror}</div>
{else}
<div class="container">
	<div class="row">
		<div class="col-md-8 col-lg-offset-2">    
			<div class="padding-25 white" style="margin-bottom: 15px;">				
				<div class="row">
					<div class="col-md-6">
						{if $logo}<p><img src="{$logo}"></p>{else}<h3 class="lighter grey"><i class="icon-leaf green"></i> {$companyname}</h3>{/if}
					</div>
					<div class="col-md-6 text-right" style="padding-bottom:20px;">
						{if $stage eq "Delivered"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-important">{$LANG.quotestagedelivered}</span></p>
						<form style="display:inline"><input type="button" class="btn btn-success btn-sm" value="{$LANG.quoteacceptbtn}" {if $accepttos}onclick="$('#quoteaccept').dialog('open')"{else}onclick="location.href='viewquote.php?id={$quoteid}&action=accept'" {/if} /></form>
						<form style="display:inline" method="post" action="dl.php?type=q&amp;id={$quoteid}"><input type="submit" class="btn btn-default btn-sm" value="{$LANG.quotedlpdfbtn}" /></form>
						{elseif $stage eq "Accepted"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-success">{$LANG.quotestageaccepted}</span></p>
						{elseif $stage eq "On Hold"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-warning">{$LANG.quotestageonhold}</span></p>
						<form style="display:inline"><input type="button" class="btn btn-success btn-sm" value="{$LANG.quoteacceptbtn}" {if $accepttos}onclick="$('#quoteaccept').dialog('open')"{else}onclick="location.href='viewquote.php?id={$quoteid}&action=accept'" {/if} /></form>
						<form style="display:inline" method="post" action="dl.php?type=q&amp;id={$quoteid}"><input class="btn btn-default btn-sm" type="submit" value="{$LANG.quotedlpdfbtn}" /></form>
						{elseif $stage eq "Lost"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-warning">{$LANG.quotestagelost}</span></p>
						{elseif $stage eq "Dead"}
						<p><span class="label label-xlg arrowed-in-right arrowed-in label-inverse">{$LANG.quotestagedead}</span></p>
					{/if}
				</div>
			</div>
			
			<div class="hr hr-6 dotted hr-double"></div>
				<div class="row">
					<div class="col-md-12">
							<p class="pull-left text-primary">{$LANG.quotenumber}: {$id}</p>
                            <p class="pull-right">{$LANG.quotedatecreated}: {$datecreated}<br />{$LANG.quotevaliduntil}: {$validuntil}</p>
					</div>
				</div>
			<div class="hr hr-6 dotted hr-double"></div>
								
			{if $agreetosrequired}<p align="center" class="unpaid">{$LANG.ordererroraccepttos}</p>{/if}

			<div class="row">
					<div class="col-sm-6">
					<div class="portlet no-border">
						<div class="portlet-heading">
							<div class="portlet-title">
								<h4>{$LANG.quoterecipient}</h4>
							</div>
						</div>
						<div class="portlet-body"> 						
							{if $clientsdetails.companyname}{$clientsdetails.companyname}<br />{/if}
							<strong>{$clientsdetails.firstname} {$clientsdetails.lastname}</strong>
							<br />{$clientsdetails.address1}, {$clientsdetails.address2}
							<br />{$clientsdetails.city}, {$clientsdetails.state}, {$clientsdetails.postcode}
							<br />{$clientsdetails.country}
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
			
			<div class="row">
				<div class="col-md-12">
					<div class="well white">  
						<p>{$proposal}</p>
					</div>
				</div>
			</div>
			
			<table class="table table-striped table-bordered tc-table table-primary">
				<thead>
				<tr>
					<th>{$LANG.invoicesdescription}</th>
					<th>{$LANG.quotediscountheading}</th>
					<th class="col-medium">{$LANG.invoicesamount}</th>
				</tr>
				</thead>
				{foreach key=num item=quoteitem from=$quoteitems}
				<tr>
					<td>{$quoteitem.description}{if $quoteitem.taxed eq "true"} *{/if}</td>
					<td>{if $quoteitem.discount}{$quoteitem.discount} ({$quoteitem.discountpc}%){else} - {/if}</td>
					<td>{$quoteitem.amount}</td>
				</tr>
				{/foreach}
				<tr>
					<td>{$LANG.invoicessubtotal}:&nbsp;</td>
					<td>&nbsp;</td>
					<td>{$subtotal}</td>
				</tr>
				{if $taxrate}
				<tr>
					<td>{$taxrate}% {$taxname}:&nbsp;</td>
					<td>&nbsp;</td>
					<td>{$tax}</td>
				</tr>
				{/if}
				{if $taxrate2}
				<tr>
					<td>{$taxrate2}% {$taxname2}:&nbsp;</td>
					<td>&nbsp;</td>
					<td>{$tax2}</td>
				</tr>
				{/if}
				<tr class="text-danger">
					<td>{$LANG.quotelinetotal}:&nbsp;</td>
					<td>&nbsp;</td>
					<td>{$total}</td>
				</tr>
			</table>

			{if $taxrate}<p><span class="text-danger">*</span>* {$LANG.invoicestaxindicator}</p>{/if}

			{if $notes}
			<div class="row">
				<div class="col-md-12">
					<div class="well white">  
						<p>{$LANG.invoicesnotes}: {$notes}</p>
					</div>
				</div>
			</div>
			{/if}

		</div>
		</div>
	</div>
{/if}

<div id="quoteaccept" title="{$LANG.quoteacceptancetitle}" style="display:none;">
<form method="post" action="{$smarty.server.PHP_SELF}?id={$quoteid}&action=accept" id="quoteacceptfrm">
	<p>{$LANG.quoteacceptancehowto} <a href="{$tosurl}" target="_blank">{$tosurl}</a></p>
	<p><label><input type="checkbox" name="agreetos" /> {$LANG.ordertosagreement} <a href="{$tosurl}" target="_blank">{$LANG.ordertos}</a></label></p>
	<p>{$LANG.quoteacceptancewarning}</p>
</form>
</div>
			
	<div class="row">
		<div class="col-md-8 col-lg-offset-2 text-center">
			<div class="btn-group btn-group-xs">
				<a href="clientarea.php" class="btn btn-primary">{$LANG.invoicesbacktoclientarea}</a> <a href="dl.php?type=q&amp;id={$quoteid}" class="btn btn-inverse"><i class="fa fa-print icon-only"></i></a> <a href="javascript:window.close()" class="btn btn-danger"><i class="fa fa-times icon-only"></i></a>
			</div>
		</div>
	</div>
	
</div>
</body>
</html>