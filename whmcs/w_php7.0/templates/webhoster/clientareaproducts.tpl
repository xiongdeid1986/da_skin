{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.clientareaproducts desc=$LANG.clientareaproductsintro}

<div class="row clearfix">
	<div class="col-lg-3 col-md-4 col-sm-4 pull-right">
		<form method="post" action="clientarea.php?action=products">
			<div class="input-group">
				<input type="text" name="q" placeholder="{$LANG.searchenterdomain}" value="{if $q}{$q}{else}{$LANG.searchenterdomain}{/if}" class="form-control search-query" onfocus="if(this.value=='{$LANG.searchenterdomain}')this.value=''" /><span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search icon-only"></i></button></span>
			</div>
		</form>
	</div>
</div>

<div class="space-12"></div>


	<p><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>

	<table class="table table-bordered table-hover dataTable tc-table">
		<thead>
			<tr>
				<th{if $orderby eq "product"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=product">{$LANG.orderproduct}</a></th>
				<th{if $orderby eq "price"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=price">{$LANG.orderprice}</a></th>
				<th{if $orderby eq "billingcycle"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=billingcycle">{$LANG.orderbillingcycle}</a></th>
				<th{if $orderby eq "nextduedate"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=nextduedate">{$LANG.clientareahostingnextduedate}</a></th>
				<th{if $orderby eq "status"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=status">{$LANG.clientareastatus}</a></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		{foreach from=$services item=service}
			<tr>
				<td><a href="clientarea.php?action=productdetails&id={$service.id}">{$service.group} - {$service.product}</a>{if $service.domain}<br /><i><small>{$service.domain}</i></small>{/if}
					<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small>{$LANG.orderprice}: {$service.amount}</i></small></li>
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small>{$LANG.orderbillingcycle}: {$service.billingcycle}</i></small></li>
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small>{$LANG.clientareahostingnextduedate}: {$service.nextduedate}</i></small></li>
					</ul>					
				</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md">{$service.amount}</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md">{$service.billingcycle}</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md">{$service.nextduedate}</td>
				<td><span class="label label-{$service.rawstatus} arrowed-in-right arrowed-in">{$service.statustext}</span></td>
				<td class="col-small center">					
					<div class="action-buttons">
						<a href="clientarea.php?action=productdetails&id={$service.id}" class="tooltip-primary" data-rel="tooltip" data-placement="left" title="{$LANG.clientareaviewdetails}"><i class="fa fa-search-plus bigger-130"></i></a>	
					</div>
				</td>								
			</tr>
			{foreachelse}
		<tr>
			<td colspan="6" class="text-center">{$LANG.norecordsfound}</td>
		</tr>
	{/foreach}
	</table>

	{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

	<ul class="pagination no-margin">
		<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=products{if $q}&q={$q}{/if}&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
		<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=products{if $q}&q={$q}{/if}&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
	</ul>

