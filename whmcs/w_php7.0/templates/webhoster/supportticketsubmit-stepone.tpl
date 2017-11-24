{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{include file="$template/pageheader.tpl" title=$LANG.navopenticket}
<p>{$LANG.supportticketsheader}</p>

<br />
{foreach from=$departments item=department}
	<ul class="list-unstyled" style="padding: 0 25px;">
		<li><div class="action-buttons"><h5><a href="{$smarty.server.PHP_SELF}?step=2&amp;deptid={$department.id}"><i class="fa fa-envelope text-warning"></i> {$department.name}</a></h5></div>
				{if $department.description}<p class="grey">{$department.description}</p>{/if}</li>
	</ul>
<div class="hr hr8 hr-dotted"></div>

{foreachelse}

<div class="alert alert-info">
    {$LANG.nosupportdepartments}
</div>
{/foreach}
<br />
<br />
<br />