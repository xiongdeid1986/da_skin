{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<div class="page-header no-margin-top">
	<h3>{$pagetitle} <small><span class="toggle" data-toggle="dash-intro"><i class="fa fa-question-circle"></i></span></small></h3>
</div>


<div class="note hide" id="dash-intro">
	{$LANG.clientareaheader}
</div>

{if $ccexpiringsoon}
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p><strong>{$LANG.ccexpiringsoon}:</strong></p><p>{$LANG.ccexpiringsoondesc|sprintf2:'</p><p><a href="clientarea.php?action=creditcard" class="btn btn-mini">':'</a>'}</p>
	</div>
{/if}
{if $clientsstats.incredit}
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p>{$LANG.availcreditbaldesc|sprintf2:$clientsstats.creditbalance}</p>
	</div>
{/if}
{if $clientsstats.numoverdueinvoices>0}
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p><strong>{$LANG.youhaveoverdueinvoices|sprintf2:$clientsstats.numoverdueinvoices}:</strong> {$LANG.overdueinvoicesdesc|sprintf2:' <a href="clientarea.php?action=masspay&all=true">':'</a>'}</p>
	</div>
{/if}

<div class="row">
	<div class="{if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}col-lg-3 col-sm-6{else}col-lg-4{/if}">
		<a class="tile-button btn btn-white" href="{if $clientsstats.productsnumtotal>0}clientarea.php?action=products{else}cart.php{/if}">
			<div class="tile-content-wrapper">
				<i class="fa fa-cogs text-primary"></i>				
				<div class="tile-content text-primary">
					{$clientsstats.productsnumactive}<span>({$clientsstats.productsnumtotal})</span>
				</div>
				<small>{$LANG.clientareanavservices}</small>				
			</div>
		</a>
	</div>
	{if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}		
	<div class="col-lg-3 col-sm-6">
		<a class="tile-button btn btn-white" href="{if $clientsstats.numdomains>0}clientarea.php?action=domains{else}domainchecker.php{/if}">
			<div class="tile-content-wrapper">
				<i class="fa fa-globe"></i>	
				<div class="tile-content">
					{$clientsstats.numactivedomains}<span>({$clientsstats.numdomains})</span>
				</div>
				<small>{$LANG.clientareanavdomains}</small>
			</div>
		</a>
	</div>
	{/if}
	  
	<div class="{if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}col-lg-3 col-sm-6{else}col-lg-4{/if}">
		<a class="tile-button btn btn-white" href="{if $clientsstats.numtickets>0}supporttickets.php{else}submitticket.php{/if}">
			<div class="tile-content-wrapper">
				<i class="fa fa-comments text-success"></i>
				<div class="tile-content text-success">
					{$clientsstats.numtickets}
				</div>
				<small>{$LANG.navtickets}</small>
			</div>
		</a>
	</div>
		  
	{if $condlinks.affiliates}
	<div class="{if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}col-lg-3 col-sm-6{else}col-lg-4{/if}">
		<a class="tile-button btn btn-white" href="affiliates.php">
			<div class="tile-content-wrapper">
				<i class="fa fa-users text-warning"></i>
				<div class="tile-content text-warning">
					{$clientsstats.numaffiliatesignups}
				</div>
				<small>{$LANG.affiliatestitle}</small>
			</div>
		</a>
	</div>
	{else}
	<div class="{if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}col-lg-3 col-sm-6{else}col-lg-4{/if}">
		<a class="tile-button btn btn-white" href="clientarea.php?action=invoices">
			<div class="tile-content-wrapper">
				<i class="fa fa-warning text-danger"></i>
				<div class="tile-content text-danger">
					{$clientsstats.numdueinvoices}
				</div>
				<small>{$LANG.invoicesdue}</small>
			</div>
		</a>
	</div>
	{/if}		  
</div><!-- /.row -->

{if $announcements}
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-list"></i> {$LANG.ourlatestnews}</h4>
		</div>
		<div class="portlet-widgets">
			<a class="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
		<span class="divider"></span>
	</div>
	<div class="portlet-body">
		<div id="owl-example" class="owl-carousel">
			<div><i class="fa fa-clock-o"></i> <a href="announcements.php?id={$announcements.0.id}">{$announcements.0.date}</a> {$announcements.0.text|strip_tags|truncate:500:'...'}</div>
			{if $announcements.1.text}<div><i class="fa fa-clock-o"></i> <a href="announcements.php?id={$announcements.1.id}">{$announcements.1.date}</a> {$announcements.1.text|strip_tags|truncate:500:'...'}</div>{/if}
			{if $announcements.2.text}<div><i class="fa fa-clock-o"></i> <a href="announcements.php?id={$announcements.2.id}">{$announcements.2.date}</a> {$announcements.2.text|strip_tags|truncate:500:'...'}</div>{/if}
		</div>
	</div>
</div>

{literal}<script>$(document).ready(function() {
  var owl = $("#owl-example");owl.owlCarousel({autoHeight : true, singleItem:true, pagination: false, transitionStyle: "fade" });
  $(".next").click(function(){owl.trigger('owl.next');})
  $(".prev").click(function(){owl.trigger('owl.prev');})
});</script>{/literal}
{/if}

{foreach from=$addons_html item=addon_html}
<div style="margin:15px 0 15px 0;">{$addon_html}</div>
{/foreach}
{if in_array('tickets',$contactpermissions)}
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-comments"></i> {$LANG.supportticketsopentickets}</h4>
		</div>
		<div class="portlet-widgets">
			<a href="submitticket.php" class="tooltip-primary" data-placement="top" data-rel="tooltip" title="" data-original-title="{$LANG.opennewticket}"><i class="fa fa-plus"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion" href="#ticket-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="ticket-box" class="panel-collapse collapse in">
		<div class="portlet-body no-padding">
			<table class="table table-bordered table-hover tc-table">
				<thead>
					<tr>
						<th>{$LANG.supportticketssubject}</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.supportticketsstatus}</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.supportticketsdepartment}</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.supportticketsticketlastupdated}</th>
						<th class="col-small center">&nbsp;</th>
					</tr>
				</thead>
			{foreach key=num item=ticket from=$tickets}
				<tr>
					<td><a href="viewticket.php?tid={$ticket.tid}&amp;c={$ticket.c}">{if $ticket.unread}<strong>{/if}#{$ticket.tid} - {$ticket.subject}{if $ticket.unread}</strong>{/if}</a>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.supportticketsticketlastupdated}: {$ticket.lastreply}</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.supportticketsdepartment}: {$ticket.department}</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.supportticketsstatus}: {$ticket.status}</li>
						</ul>																							
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.status}</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.department}</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.lastreply}</td>
					<td class="col-small center"><div class="action-buttons"><a href="viewticket.php?tid={$ticket.tid}&c={$ticket.c}"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="6" class="text-center">{$LANG.supportticketsnoneopen}</td>
				</tr>
			{/foreach}
			</table>
		</div>
	</div>
</div>
<div style="margin-top:15px;"></div>
{/if}




{if in_array('invoices',$contactpermissions)}
<form method="post" action="clientarea.php?action=masspay">
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-{if $clientsstats.numdueinvoices>0}warning{else}check{/if}"></i> {$LANG.invoicesdue}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#invoice-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="invoice-box" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-bordered table-hover tc-table">
			<thead>
				<tr>
					{if $masspay}
					<th class="col-small center">
						<input type="checkbox" class="tc" />
						<span class="labels"></span>
					</th>{/if}
					<th>{$LANG.invoicenumber}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.invoicesdatecreated}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.invoicesdatedue}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.invoicesstatus}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.invoicestotal}</th>
					<th class="col-small center">&nbsp;</th>
				</tr>
			</thead>
		{foreach key=num item=invoice from=$invoices}			
				<tr>
					{if $masspay}
					<td class="col-small center">
						<input type="checkbox" class="tc" name="invoiceids[]" value="{$invoice.id}" {if $invoice.rawstatus != "unpaid"} disabled{/if} />
						<span class="labels"></span>
					</td>{/if}
					<td><a href="viewinvoice.php?id={$invoice.id}" target="_blank">{$invoice.invoicenum}</a>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><span class="label label-{$invoice.rawstatus} arrowed-in-right arrowed-in">{$invoice.statustext}</span></li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.invoicesdatecreated}: {$invoice.datecreated}</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.invoicesdatedue}: {$invoice.datedue}</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i>{$LANG.invoicestotal}: {$invoice.total}</li>
						</ul>
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$invoice.datecreated}</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$invoice.datedue}</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><span class="label label-{$invoice.rawstatus} arrowed-in-right arrowed-in">{$invoice.statustext}</span></td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$invoice.total}</td>
					<td class="col-small center"><div class="action-buttons"><a href="viewinvoice.php?id={$invoice.id}" target="_blank"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
				</tr>
		{foreachelse}
				<tr>
					<td class="text-center" colspan="{if $masspay}8{else}7{/if}">{$LANG.invoicesnoneunpaid}</td>
				</tr>
		{/foreach}			
		{if $clientsstats.numoverdueinvoices>0}
		{if $masspay}
			<tfoot>
				<tr>
					<td class="col-small center"></td>
					<td colspan="5"><input type="submit" name="masspayselected" value="{$LANG.masspayselected}" class="btn btn-inverse">&nbsp;&nbsp;<a href="clientarea.php?action=masspay&amp;all=true" class="btn btn-success"><i class="fa fa-check-circle-o"></i> {$LANG.masspayall}</a>
					<td class="hidden-sm hidden-xs visible-lg visible-md"></td>
				</tr>
			</tfoot>
		{/if}
		{else}
			{/if}
		</table>
	</div>
	</div>
</div>
</form>
{/if}

{if $files}
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-paperclip"></i> {$LANG.clientareafiles}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#file-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="file-box" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-striped table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th class="col-medium">{$LANG.clientareafilesdate}</th>
					<th>{$LANG.clientareafilesfilename}</th>
					</tr>
			</thead>
			{foreach key=num item=file from=$files}
				<tr>
					<td class="col-medium" data-title="{$LANG.clientareafilesdate}">{$file.date}</td>
					<td data-title="{$LANG.clientareafilesfilename}"><div class="action-buttons"><a href="dl.php?type=f&id={$file.id}"><i class="fa fa-download"></i> {$file.title}</a></div></td>
				</tr>
			{/foreach}
		</table>
	</div>
	</div>
</div>
{/if}