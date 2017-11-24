{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.networkstatustitle desc=$LANG.networkstatusintro}

<div class="text-center">
	<div class="btn-group" style="margin-bottom: 25px;">
		<a class="btn{if $view == 'open'} active{/if}" href="{$smarty.server.PHP_SELF}?view=open">{$opencount} {$LANG.networkissuesstatusopen}</a>
		<a class="btn{if $view == 'scheduled'} active{/if}" href="{$smarty.server.PHP_SELF}?view=scheduled">{$scheduledcount} {$LANG.networkissuesstatusscheduled}</a>
		<a class="btn{if $view == 'resolved'} active{/if}" href="{$smarty.server.PHP_SELF}?view=resolved">{$resolvedcount} {$LANG.networkissuesstatusresolved}</a>
	</div>
</div>

{foreach from=$issues item=issue}
			<div class="block-s3">
			<h3>{$issue.title|truncate:80} - <small class="text-primary">({$issue.status})</small></h3>
			<ul>
				{if $issue.clientaffected}<li><span class="label label-sm label-warning">Attention</span></li>{/if}
				<li><strong>{$LANG.networkissuesdate}</strong> - {$issue.startdate}{if $issue.enddate} - {$issue.enddate}{/if}</li> 
				<li><strong>{$LANG.networkissuesaffecting} {$issue.type}</strong> - {if $issue.type eq $LANG.networkissuestypeserver}{$issue.server}{else}{$issue.affecting}{/if}</li>
				<li><strong>{$LANG.networkissuespriority}</strong> - {$issue.priority}</li>
				<li><strong>{$LANG.networkissueslastupdated}</strong>- {$issue.lastupdate}</li>
			</ul>
			
			<p>{$issue.description}</p>
			
			
			</div>
			<hr class="separator" />
			
{foreachelse}
			<p class="text-center">{$noissuesmsg}</p>

{/foreach}
	
	<ul class="pagination">
		<li{if !$prevpage} class="disabled"{/if}>
			<a href="{if $prevpage}{$smarty.server.PHP_SELF}?{if $view}view={$view}&amp;{/if}page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a>
		</li>
		<li{if !$nextpage} class="disabled"{/if}>
			<a href="{if $nextpage}{$smarty.server.PHP_SELF}?{if $view}view={$view}&amp;{/if}page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a>
		</li>
	</ul>

{if $servers}
<script type="text/javascript">
{literal}
	function getStats(num) {
		$.post('serverstatus.php', 'getstats=1&num='+num, function(data) {
			$("#load"+num).html(data.load);
			$("#uptime"+num).html(data.uptime);
		},'json');
	}
	function checkPort(num,port) {
		$.post('serverstatus.php', 'ping=1&num='+num+'&port='+port, function(data) {
			$("#port"+port+"_"+num).html(data);
		});
	}
{/literal}
</script>

{if $servers}{literal}<script type="text/javascript">
function getStats(num) {
    $.post('serverstatus.php', 'getstats=1&num='+num, function(data) {
        $("#load"+num).html(data.load); $("#load2"+num).html(data.load);
        $("#uptime"+num).html(data.uptime); $("#uptime2"+num).html(data.uptime);
    },'json');
}
function checkPort(num,port) {
    $.post('serverstatus.php', 'ping=1&num='+num+'&port='+port, function(data) {
        $("#port"+port+"_"+num).html(data);
    });
}
</script>{/literal}{/if}

<div class="portlet">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter"><i class="fa fa-signal"></i> {$LANG.serverstatustitle}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#network-box"><i class="fa fa-chevron-down"></i></a>
		</div>
	<div class="clearfix"></div>		
	</div>
	<div id="network-box" class="panel-collapse collapse in">
	<div class="portlet-body">
		<div class="table-responsive"><table class="table table-bordered table-hover">
				<p>{$LANG.serverstatusheadingtext}</p>
			<thead class="thin-border-bottom">
				<tr>
					<th>{$LANG.servername}</th>
					<th>HTTP</th>
					<th>FTP</th>
					<th>POP3</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.serverstatusphpinfo}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.serverstatusserverload}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.serverstatusuptime}</th>
				</tr>
			</thead>
		{foreach from=$servers key=num item=server}
				<tr>
					<td>{$server.name}
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md" style="clear:both; padding-top:5px">
							<li><i class="fa fa-angle-right bigger-110 text-green"></i> {$LANG.serverstatusserverload} : <span id="load2{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}" /></span></li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i> {$LANG.serverstatusuptime} : <span id="uptime2{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}" /></span></li>
						</ul>										
					</td>
					<td id="port80_{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}"></td>
					<td id="port21_{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}"></td>
					<td id="port110_{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}"></td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><a href="{$server.phpinfourl}" target="_blank">{$LANG.serverstatusphpinfo}</a></td>
					<td class="hidden-sm hidden-xs visible-lg visible-md" id="load{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}"></td>
					<td class="hidden-sm hidden-xs visible-lg visible-md" id="uptime{$num}"><img src="images/loadingsml.gif" alt="{$LANG.loading}"><script type="text/javascript">checkPort({$num},80);checkPort({$num},21);checkPort({$num},110);getStats({$num});</script></td>
			</tr>
		{foreachelse}
				<tr>
					<td colspan="7">{$LANG.serverstatusnoservers}</td>
				</tr>
		{/foreach}
		</table></div>
	</div>
	</div>
</div>

{/if}

<div class="action-buttons text-center">
	<a href="networkissuesrss.php"><i class="fa fa-rss text-warning"></i> {$LANG.announcementsrss}</a>
</div>

<br />