{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{include file="$template/pageheader.tpl" title=$LANG.clientareanavsupporttickets desc=$LANG.supportticketsintro}
<p>{$LANG.supportticketssystemdescription}</p>

<div class="row">
	<div class="col-xs-12 col-sm-3 pull-right">
		<form method="post" action="supporttickets.php">
			<div class="input-group">
				<input type="text" name="searchterm" placeholder="{$LANG.searchtickets}" value="{if $q}{$q}{else}{$LANG.searchtickets}{/if}" class="form-control search-query" onfocus="if(this.value=='{$LANG.searchtickets}')this.value=''" /><span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search icon-only"></i></button></span>
			</div>
		</form>
	</div>
</div>

<div class="space-18"></div>

<div class="row clearfix">
	<div class="col-sm-6">
		<span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}
	</div>
	<div class="col-sm-6">	
		<p class="pull-right"><a class="btn btn-sm btn-success" href="submitticket.php"><i class="fa fa-plus"></i> {$LANG.opennewticket}</a></p>
	</div>
</div>

<table class="table table-bordered table-striped table-hover dataTable tc-table">
	<thead>
		<tr>
			<th{if $orderby eq "date"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=date">{$LANG.supportticketsdate}</a></th>
			<th{if $orderby eq "dept"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=dept">{$LANG.supportticketsdepartment}</a></th>
			<th{if $orderby eq "subject"} class="sorting_{$sort}"{/if} class="sorting"><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=subject">{$LANG.supportticketssubject}</a></th>
			<th{if $orderby eq "status"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=status">{$LANG.supportticketsstatus}</a></th>
			<th{if $orderby eq "lastreply"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=lastreply">{$LANG.supportticketsticketlastupdated}</a></th>
			<th class="col-small center">&nbsp;</th>
		</tr>
	</thead>
	{foreach key=num item=ticket from=$tickets}
		<tr>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.date}</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.department}</td>
			<td><a href="viewticket.php?tid={$ticket.tid}&amp;c={$ticket.c}">{if $ticket.unread}<strong>{/if}#{$ticket.tid} - {$ticket.subject}{if $ticket.unread}</strong>{/if}</a>						
				<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.supportticketsticketlastupdated}: {$ticket.lastreply}</li>
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.supportticketsdepartment}: {$ticket.department}</li>
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.supportticketsstatus}: {$ticket.status}</li>
				</ul>										
			</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.status}</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$ticket.lastreply}</td>
			<td class="col-small center"><div class="action-buttons"><a href="viewticket.php?tid={$ticket.tid}&c={$ticket.c}"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="7" class="text-center">{$LANG.norecordsfound}</td>
		</tr>
	{/foreach}
</table>
{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

<ul class="pagination">
	<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
	<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>