{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{if $inactive}
{include file="$template/pageheader.tpl" title=$LANG.affiliatestitle}

<div class="alert alert-danger">
    <p>{$LANG.affiliatesdisabled}</p>
</div>
<br />
<br />
<br />
{else}
{include file="$template/pageheader.tpl" title=$LANG.affiliatestitle desc=$LANG.affiliatesrealtime}

<button type="button" value="{$LANG.affiliatesreferallink}" class="btn btn-success btn-sm" onclick="jQuery('#affiliatelink').slideToggle()" /><i class="fa fa-info"></i> {$LANG.affiliatesreferallink}</button>
	<div id="affiliatelink" style="display:none">
		<br />
		<div class="note">
			<input type="text" value="{$referrallink}" class="input-xxlarge" />
		</div>
	</div>

<div class="ticketdetailscontainer">
    <div class="row">
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliatesvisitorsreferred}
				<div class="detail">{$visitors}</div>
			</div>
        </div>
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliatessignups}
				<div class="detail">{$signups}</div>
			</div>
        </div>
		
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliatesconversionrate}
				<div class="detail">{$conversionrate}%</div>
			</div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliatescommissionspending}
				<div class="detail">{$pendingcommissions}</div>
			</div>
        </div>
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliatescommissionsavailable}
				<div class="detail">{$balance}</div>
			</div>
        </div>
		
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.affiliateswithdrawn}
				<div class="detail">{$withdrawn}</div>
			</div>
        </div>
    </div>	
</div>

<br />

{if $withdrawrequestsent}
<div class="alert alert-success">
    <p>{$LANG.affiliateswithdrawalrequestsuccessful}</p>
</div>
{else}
{if $withdrawlevel}
<p>
  <input type="button" class="btn btn-primary btn-sm" value="{$LANG.affiliatesrequestwithdrawal}" onclick="window.location='{$smarty.server.PHP_SELF}?action=withdrawrequest'" />
</p>
<div class="space-12"></div>
{/if}
{/if}

<p><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>

<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4><i class="fa fa-list"></i> {$LANG.affiliatesreferals}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#affiliates-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>		
	</div>
	<div id="affiliates-box" class="panel-collapse collapse in">
	<div class="portlet-body">
		<table id=sample-table-2" class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th{if $orderby eq "date"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="affiliates.php?orderby=date">{$LANG.affiliatessignupdate}</a></th>
					<th{if $orderby eq "product"} class="sorting_{$sort}"{/if} class="sorting"><a href="affiliates.php?orderby=product">{$LANG.orderproduct}</a></th>
					<th{if $orderby eq "amount"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="affiliates.php?orderby=amount">{$LANG.affiliatesamount}</a></th>
					<th>{$LANG.affiliatescommission}</th>
					<th{if $orderby eq "status"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="affiliates.php?orderby=status">{$LANG.affiliatesstatus}</a></th>
				</tr>
			</thead>
			{foreach key=num item=referral from=$referrals}
			<tr>
				<td class="hidden-sm hidden-xs visible-lg visible-md" data-title="{$LANG.affiliatessignupdate}">{$referral.date}</td>
				<td data-title="{$LANG.orderproduct}">{$referral.service}</td>
					<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
						<li><i class="icon-angle-right bigger-110 text-primary"></i>{$LANG.affiliatessignupdate}: {$referral.date}</li>
						<li><i class="icon-angle-right bigger-110 text-primary"></i>{$LANG.affiliatesamount}: {$referral.amountdesc}</li>
						<li><i class="icon-angle-right bigger-110 text-primary"></i>{$LANG.affiliatesstatus}: {$referral.amountdesc}</li>
					</ul>
				<td class="hidden-sm hidden-xs visible-lg visible-md" data-title="{$LANG.affiliatesamount}">{$referral.status}</td>
				<td data-title="{$LANG.affiliatescommission}">{$referral.commission}</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md" data-title="{$LANG.affiliatesstatus}">{$referral.status}</td>
			</tr>
			{foreachelse}
				<tr>
					<td colspan="5" class="textcenter">{$LANG.norecordsfound}</td>
				</tr>
			{/foreach}
	</table>
	</div>
	</div>
</div>

<ul class="pagination">
	<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}affiliates.php?page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
	<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}affiliates.php?page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>

{if $affiliatelinkscode}
<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4><i class="fa fa-picture-o"></i> {$LANG.affiliateslinktous}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#banners-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="banners-box" class="panel-collapse collapse in">
	<div class="portlet-body">
			<div class="widget-main">
				<div>{$affiliatelinkscode}</div>
			</div>
	</div>
	</div>
</div>
{/if}

{/if}