{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.clientareaemails desc=$LANG.emailstagline}
<p class="pull-right"><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>
<div class="clearfix"></div>

<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter"><i class="fa fa-envelope text-orange"></i> {$LANG.clientareaemails}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#email-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="email-box" class="panel-collapse collapse in">
	<div class="portlet-body">
		<table id=sample-table-2" class="table table-bordered table-hover dataTable">
			
    <thead>
        <tr>
            <th{if $orderby eq "date"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=emails&orderby=date">{$LANG.clientareaemailsdate}</a></th>
            <th{if $orderby eq "subject"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=emails&orderby=subject">{$LANG.clientareaemailssubject}</a></th>
            <th width="35px">&nbsp;</th>
        </tr>
    </thead>
	{foreach from=$emails item=email}
	
        <tr>
            <td class="hidden-sm hidden-xs visible-lg visible-md">{$email.date}</td>
            <td><a href="clientarea.php?action=emails" onclick="popupWindow('viewemail.php?id={$email.id}','emlmsg',650,400)">{$email.subject}</a>
				<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
					<li><i class="fa fa-angle-right bigger-110 text-green"></i> {$LANG.clientareaemailsdate}: {$email.date}</li>
				</ul>						
			</td>
            <td width="35px"><div class="action-buttons"><a href="clientarea.php?action=emails" onclick="popupWindow('viewemail.php?id={$email.id}','emlmsg',650,400)"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
        </tr>
	{foreachelse}
				<tr>
					<td colspan="7" class="text-center">{$LANG.norecordsfound}</td>
				</tr>
	{/foreach}
			
		</table>
	</div>
	</div>
</div>

<ul class="pagination">
        <li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=emails&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
        <li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=emails&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>
