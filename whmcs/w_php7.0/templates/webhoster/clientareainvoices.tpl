{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{include file="$template/pageheader.tpl" title=$LANG.invoices desc=$LANG.invoicesintro}


<div class="row">
	<div class="col-xs-12 col-sm-12">
		<p class="pull-left"><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>
		<p class="pull-right">{$LANG.invoicesoutstandingbalance}: <span class="label label-lg arrowed-right label-{if $nobalance}success{else}danger{/if}">{$totalbalance}</span>{if $masspay}&nbsp; <a href="clientarea.php?action=masspay&all=true" class="btn btn-success"><i class="fa fa-check-circle-o"></i> {$LANG.masspayall}</a>{/if}</p>
	</div>
</div>

<p>{$LANG.invoicescredit} {$LANG.invoicesbalance}:<span class="label label-inverse label-xlg arrowed-in-right arrowed-in"> {$clientsstats.creditbalance}</span></p>

<form method="post" action="clientarea.php?action=masspay">
	<table class="table table-bordered table-striped table-hover dataTable tc-table">
		<thead>
			<tr>
				{if $masspay}
				<th class="col-small center">
					<input type="checkbox" class="tc"/>
					<span class="labels"></span>
				</th>{/if}
				<th{if $orderby eq "id"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=invoices&orderby=id">{$LANG.invoicestitle}</a></th>
				<th{if $orderby eq "date"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=date">{$LANG.invoicesdatecreated}</a></th>
				<th{if $orderby eq "duedate"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=duedate">{$LANG.invoicesdatedue}</a></th>
				<th{if $orderby eq "status"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=status">{$LANG.invoicesstatus}</a></th>						
				<th{if $orderby eq "total"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=total">{$LANG.invoicestotal}</a></th>
				<th class="col-small center">&nbsp;</th>
			</tr>
		</thead>				
		{foreach from=$invoices item=invoice}
			<tr>
				{if $masspay}
				<td class="col-small center">
					<input type="checkbox" class="invoiceids tc" name="invoiceids[]" value="{$invoice.id}" {if $invoice.rawstatus != "unpaid"} disabled{/if} />
					<span class="labels"></span>
				</td>{/if}
				<td><a href="viewinvoice.php?id={$invoice.id}" target="_blank">{$invoice.invoicenum}</a>
					<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
						<li><span class="label label-{$invoice.rawstatus} arrowed-in-right arrowed-in">{$invoice.statustext}</span></li>
						<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.invoicesdatecreated}: {$invoice.datecreated}</li>
						<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.invoicesdatedue}: {$invoice.datedue}</li>
						<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.invoicestotal}: {$invoice.total}</li> 
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
				<td colspan="{if $masspay}7{else}6{/if}" class="text-center">{$LANG.norecordsfound}</td>
			</tr>
		{/foreach}
				
        {if $masspay}
        <tfoot>
            <tr>
                <td class="col-small center"></td>
                <td colspan="5"><input type="submit" name="masspayselected" value="{$LANG.masspayselected}" class="btn btn-sm btn-inverse">&nbsp;&nbsp;<a href="clientarea.php?action=masspay&amp;all=true" class="btn btn-sm btn-success"><i class="fa fa-check-circle-o"></i> {$LANG.masspayall}</a>
                <td class="hidden-sm hidden-xs visible-lg visible-md"></td>
            </tr>
         </tfoot>{/if}
	</table>			
</form>
{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

<ul class="pagination">
	<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=invoices{if $q}&q={$q}{/if}&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
	<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=invoices{if $q}&q={$q}{/if}&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>