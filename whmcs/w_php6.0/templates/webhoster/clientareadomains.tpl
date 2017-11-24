{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.clientareanavdomains desc=$LANG.clientareadomainsintro}

<div class="row clearfix">
	<div class="col-lg-3 col-md-4 col-sm-4 pull-right">
		<form method="post" action="clientarea.php?action=domains">
			<div class="input-group">
				<input type="text" placeholder="{$LANG.searchenterdomain}" name="q" value="{if $q}{$q}{else}{$LANG.searchenterdomain}{/if}" class="form-control search-query" onfocus="if(this.value=='{$LANG.searchenterdomain}')this.value=''" /><span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search icon-only"></i></button></span>
			</div>
		</form>
	</div>
</div>

<div class="space-12"></div>

{literal}
<script>
$(document).ready(function() {
	$(".setbulkaction").click(function(event) {
	  event.preventDefault();
	  $("#bulkaction").val($(this).attr('id'));
	  $("#bulkactionform").submit();
	});
});
</script>
{/literal}


<p><span class="badge badge-primary">{$numitems}</span> {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>
<form method="post" id="bulkactionform" action="clientarea.php?action=bulkdomain">
<input id="bulkaction" name="update" type="hidden" />
<table class="table table-bordered table-hover dataTable tc-table">
	<thead>
		<tr>
			<th class="col-small center">
				<input type="Checkbox" class="tc" />
				<span class="labels"></span>
			</th>
			<th{if $orderby eq "domain"} class="sorting_{$sort}"{/if} class="sorting"><a href="clientarea.php?action=domains{if $q}&q={$q}{/if}&orderby=domain">{$LANG.clientareahostingdomain}</a></th>
			<th{if $orderby eq "regdate"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains{if $q}&q={$q}{/if}&orderby=regdate">{$LANG.clientareahostingregdate}</a></th>
			<th{if $orderby eq "nextduedate"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains{if $q}&q={$q}{/if}&orderby=nextduedate">{$LANG.clientareahostingnextduedate}</a></th>
			<th{if $orderby eq "status"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains{if $q}&q={$q}{/if}&orderby=status">{$LANG.clientareastatus}</a></th>
			<th{if $orderby eq "autorenew"} class="sorting_{$sort} hidden-sm hidden-xs visible-lg visible-md"{/if} class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains{if $q}&q={$q}{/if}&orderby=autorenew">{$LANG.domainsautorenew}</a></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	{foreach key=num item=domain from=$domains}
		<tr>
			<td class="col-small center">
				<input type="Checkbox" class="tc" name="domids[]" class="domids" value="{$domain.id}" />
				<span class="labels"></span>
			</td>
			<td><a href="clientarea.php?action=domaindetails&id={$domain.id}">{$domain.domain}</a>						
				<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small>{$LANG.clientareahostingregdate}: {$domain.registrationdate}</i></small></li>
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small>{$LANG.clientareahostingnextduedate}: {$domain.nextduedate}</i></small></li>
					<li><span class="label label-{$domain.rawstatus} arrowed-in-right arrowed-in">{$domain.statustext}</span></li>
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small>{$LANG.domainsautorenew}: {if $domain.autorenew}{$LANG.domainsautorenewenabled}{else}{$LANG.domainsautorenewdisabled}{/if}</i></small></li>
				</ul>					
			</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$domain.registrationdate}</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{$domain.nextduedate}</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md"><span class="label label-{$domain.rawstatus} arrowed-in-right arrowed-in">{$domain.statustext}</span></td>
			<td class="hidden-sm hidden-xs visible-lg visible-md">{if $domain.autorenew}{$LANG.domainsautorenewenabled}{else}{$LANG.domainsautorenewdisabled}{/if}</td>
			<td class="col-small center">
				<div class="action-buttons">
					<a href="clientarea.php?action=domaindetails&id={$domain.id}" class="tooltip-primary" data-rel="tooltip" data-placement="left" title="{$LANG.managedomain}"><i class="fa fa-edit bigger-130"></i></a>
				</div>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="7" class="text-center">{$LANG.norecordsfound}</td>
		</tr>
	{/foreach}
	<tfoot>
		<tr>
			<td class="col-small center"></td>
			<td colspan="6">
				<div class="btn-group dropup">
					<a class="btn btn-default btn-sm" href="#" data-toggle="dropdown">{$LANG.withselected}<i class="fa fa-angle-down icon-on-right"></i></a>
						<ul class="dropdown-menu  dropdown-caret dropdown-menu-right">
							<li><a href="#" id="nameservers" class="setbulkaction">{$LANG.domainmanagens}</a></li>
							<li><a href="#" id="autorenew" class="setbulkaction">{$LANG.domainautorenewstatus}</a></li>
							<li><a href="#" id="reglock" class="setbulkaction">{$LANG.domainreglockstatus}</a></li>
							<li><a href="#" id="contactinfo" class="setbulkaction">{$LANG.domaincontactinfo}</a></li>
							<li class="divider"></li>
							{if $allowrenew}<li><a href="#" id="renew" class="setbulkaction">{$LANG.domainmassrenew}</a></li>{/if}
						</ul>
				</div>
			</td>
		</tr>
	</tfoot>
</table>

</form>

{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

<ul class="pagination no-margin">
	<li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=domains{if $q}&q={$q}{/if}&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
	<li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=domains{if $q}&q={$q}{/if}&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>

