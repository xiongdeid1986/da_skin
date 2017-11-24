{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{include file="$template/pageheader.tpl" title=$LANG.quotestitle desc=$LANG.quotesintro}

<p><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>

<table id=sample-table-2" class="table table-bordered table-striped table-hover dataTable tc-table">
    <thead>
        <tr>
            <th{if $orderby eq "id"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=quotes&orderby=id">{$LANG.quotenumber}</a></th>
            <th{if $orderby eq "subject"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=quotes&orderby=subject">{$LANG.quotesubject}</a></th>
            <th{if $orderby eq "datecreated"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=quotes&orderby=datecreated">{$LANG.quotedatecreated}</a></th>
            <th{if $orderby eq "validuntil"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=quotes&orderby=validuntil">{$LANG.quotevaliduntil}</a></th>
            <th{if $orderby eq "stage"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=quotes&orderby=stage">{$LANG.quotestage}</a></th>
            <th class="col-medium">&nbsp;</th>
        </tr>
    </thead>
    {foreach from=$quotes item=quote}
        <tr>
            <td class="hidden-sm hidden-xs visible-lg visible-md">{$quote.id}</td>
            <td><a href="dl.php?type=q&id={$quote.id}" target="_blank">{$quote.subject}</a>
				<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.quotenumber}: {$quote.id}</li>
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.quotedatecreated}: {$quote.datecreated}</li>
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.quotevaliduntil}: {$quote.validuntil}</li>
					<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.quotestage}: {$quote.stage}</li>
				</ul>			
			</td>
            <td class="hidden-sm hidden-xs visible-lg visible-md">{$quote.datecreated}</td>
            <td class="hidden-sm hidden-xs visible-lg visible-md">{$quote.validuntil}</td>
            <td class="hidden-sm hidden-xs visible-lg visible-md">{$quote.stage}</td>
            <td class="col-medium">
				<div class="btn-group btn-group-xs">
					<input type="button" class="btn btn-inverse btn-xs" value="{$LANG.quoteview}" onclick="window.location='viewquote.php?id={$quote.id}'" />
					<input type="button" class="btn btn-primary btn-xs" value="{$LANG.quotedownload}" onclick="window.location='dl.php?type=q&id={$quote.id}'" />
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

<ul class="pagination">
	<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=quotes&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
	<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=quotes&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>