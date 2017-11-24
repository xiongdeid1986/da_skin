<!DOCTYPE html>
<html lang="en">
<head>
<title>{$companyname} - {$id}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="templates/{$template}/css/quote.css">
<link href="includes/jscript/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/jscript/jquery.js"></script>
<script type="text/javascript" src="includes/jscript/jqueryui.js"></script>
{literal}<script>
$(document).ready(function(){

$("#quoteaccept").dialog({
    autoOpen: false,
    resizable: false,
    width: 500,
    modal: true,
	buttons: {'Agree & Accept': function() {
		    $("#quoteacceptfrm").submit();
		}}
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
<div class="col-lg-9 col-lg-offset-1">    
<div class="panel panel-default">
<div class="panel-heading">
<div class="row">
<div class="col-lg-6">
{if $logo}<p><img src="{$logo}"></p>{else}<h2>{$companyname}</h2>{/if}
</div>
<div class="col-lg-6 text-right" style="padding-bottom:20px;">
{if $stage eq "Delivered"}

<p><span class="label label-warning">{$LANG.quotestagedelivered}</span></p>

<form style="display:inline"><input type="button" class="btn btn-success btn-small hidden-print" value="{$LANG.quoteacceptbtn}" {if $accepttos}onclick="$('#quoteaccept').dialog('open')"{else}onclick="location.href='viewquote.php?id={$quoteid}&action=accept'" {/if} /></form>
<form style="display:inline" method="post" action="dl.php?type=q&amp;id={$quoteid}"><input type="submit" class="btn btn-default btn-small hidden-print" value="{$LANG.quotedlpdfbtn}" /></form>
{elseif $stage eq "Accepted"}
<p><span class="label label-success">{$LANG.quotestageaccepted}</span></p>
{elseif $stage eq "On Hold"}
<p><span class="label label-info">{$LANG.quotestageonhold}</span></p>
<form style="display:inline"><input type="button" value="{$LANG.quoteacceptbtn}" {if $accepttos}onclick="$('#quoteaccept').dialog('open')"{else}onclick="location.href='viewquote.php?id={$quoteid}&action=accept'" {/if} /></form>
<form style="display:inline" method="post" action="dl.php?type=q&amp;id={$quoteid}"><input type="submit" value="{$LANG.quotedlpdfbtn}" /></form>
{elseif $stage eq "Lost"}
<p><span class="label label-warning">{$LANG.quotestagelost}</span></p>
{elseif $stage eq "Dead"}
<p><span class="label label-inverse">{$LANG.quotestagedead}</span></p>
{/if}
</div>
</div>

</div>
<div class="panel-body">

{if $agreetosrequired}<p align="center" class="unpaid">{$LANG.ordererroraccepttos}</p>{/if}

<div class="row">
<div class="col-xs-4">
<h4>{$LANG.quotenumber}: <span class="badge">{$id}</span></h4>
<p>{$LANG.quotedatecreated}: {$datecreated}<br />
{$LANG.quotevaliduntil}: {$validuntil}</p>
</div>
<div class="col-xs-4">
<h4>{$LANG.quoterecipient}</h4>
<p>{if $clientsdetails.companyname}{$clientsdetails.companyname}<br />{/if}
{$clientsdetails.firstname} {$clientsdetails.lastname}<br />
{$clientsdetails.address1}, {$clientsdetails.address2}<br />
{$clientsdetails.city}, {$clientsdetails.state}, {$clientsdetails.postcode}<br />
{$clientsdetails.country}</p>
</div>
<div class="col-xs-4">
<h4>{$LANG.invoicespayto}</h4>
<p>{$payto}</p>
</div>
</div>

<div class="row">
<div class="col-lg-12">
<div class="well" style="background-color:#fff;">  
<p>{$proposal}</p>
</div></div></div>
<table class="table">
<tr>
	<td><strong>{$LANG.invoicesdescription}</strong></td>
    <td><strong>{$LANG.quotediscountheading}</strong></td>
    <td style="width: 105px;"><strong>{$LANG.invoicesamount}</strong></td>
</tr>
{foreach key=num item=quoteitem from=$quoteitems}
<tr>
	<td>{$quoteitem.description}{if $quoteitem.taxed eq "true"} *{/if}</td>
    <td>{if $quoteitem.discount>0}{$quoteitem.discount} ({$quoteitem.discountpc}%){else} - {/if}</td>
    <td>{$quoteitem.amount}</td>
</tr>
{/foreach}
<tr>
	<td>{$LANG.invoicessubtotal}:&nbsp;</td>
    <td>&nbsp;</td>
    <td><strong>{$subtotal}</strong></td>
</tr>
{if $taxrate}
<tr>
	<td>{$taxrate}% {$taxname}:&nbsp;</td>
    <td>&nbsp;</td>
    <td><strong>{$tax}</strong></td>
</tr>
{/if}
{if $taxrate2}
<tr>
	<td>{$taxrate2}% {$taxname2}:&nbsp;</td>
    <td>&nbsp;</td>
    <td><strong>{$tax2}</strong></td>
</tr>
{/if}
<tr  class="info">
	<td>{$LANG.quotelinetotal}:&nbsp;</td>
    <td>&nbsp;</td>
	<td><strong>{$total}</strong></td>
</tr>
</table>

{if $taxrate}<p>* {$LANG.invoicestaxindicator}</p>{/if}

{if $notes}
<div class="row">
<div class="col-lg-12">
<div class="well" style="background-color:#fff;">  
<p>{$LANG.invoicesnotes}: {$notes}</p>
</div></div></div>
{/if}
<br /><br />
{/if}

<div id="quoteaccept" title="{$LANG.quoteacceptancetitle}" style="display:none;">

<form method="post" action="{$smarty.server.PHP_SELF}?id={$quoteid}&action=accept" id="quoteacceptfrm">

<p>{$LANG.quoteacceptancehowto} <a href="{$tosurl}" target="_blank">{$tosurl}</a></p>

<p><label><input type="checkbox" name="agreetos" /> {$LANG.ordertosagreement} <a href="{$tosurl}" target="_blank">{$LANG.ordertos}</a></label></p>

<p>{$LANG.quoteacceptancewarning}</p>

</form>

</div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-9 col-lg-offset-1 text-right hidden-print">
<p><a href="clientarea.php" class="btn btn-default btn-sm">{$LANG.invoicesbacktoclientarea}</a> <a href="dl.php?type=q&amp;id={$quoteid}" class="btn btn-default btn-sm">{$LANG.quotedlpdfbtn}</a> <a href="javascript:window.close()" class="btn btn-danger btn-sm">{$LANG.closewindow}</a></p>
</div>
</div>

</div>
</body>
</html>