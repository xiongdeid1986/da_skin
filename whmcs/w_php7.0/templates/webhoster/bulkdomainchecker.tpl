{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.domaintitle desc=$LANG.domaincheckerintro}

{if $inccode}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	{$LANG.captchaverifyincorrect}
</div>
{/if}

<div class="tc-tabsbar center-tabs arrow">
	<ul class="nav nav-tabs">
		<li><a href="domainchecker.php">{$LANG.domainsimplesearch}</a></li>
		{if $bulkdomainsearchenabled}
		<li  class="active"><a href="domainchecker.php?search=bulkregister">{$LANG.domainbulksearch}</a></li>
		{if $condlinks.domaintrans}<li><a href="domainchecker.php?search=bulktransfer">{$LANG.domainbulktransfersearch}</a></li>{/if}
		{/if}
	</ul>
</div>

<div class="tab-content padding-16 text-center">
	<form method="post" action="domainchecker.php" class="form-horizontal">
		<input type="hidden" name="search" value="bulk">
		<p>{$LANG.domainbulksearchintro}</p>
		<div class="text-center">
			<textarea name="bulkdomains" rows="8" class="input-xxlarge" placeholder="The bulk real-time domain name search allows you to search up to 20 domains at once.  Enter the domains in the field below, one per line - do not enter www. or http:// in front.">{$bulkdomains}</textarea>
		</div>
	{if $capatacha}
			<p><i class="fa fa-info-circle text-info"></i> {$LANG.captchaverify}</p>
		{if $capatacha eq "recaptcha"}
			<p>{$recapatchahtml}</p>
		{else}
			<img src="includes/verifyimage.php" alt="captcha"> <input type="text" name="code" class="input-sm" style="margin-bottom:0" maxlength="5"><br /><br />
		{/if}
		<hr>
	{/if}
	<div class="space-6"></div>	
		<input type="submit" value="{$LANG.checkavailability}" class="btn btn-success" onclick="$('#modalpleasewait').modal();">
	</form>
</div>

{if $invalid}
<div class="alert alert-danger text-center">
	<h4>{$LANG.domaincheckerbulkinvaliddomain}</h4>
</div>
{/if}

{if $availabilityresults}
<div class="row">
	<div class="col-sm-10 col-sm-offset-1">
		<form method="post" action="{$systemsslurl}cart.php" class="form-horizontal">
			<input type="hidden" name="a" value="add">
			<input type="hidden" name="domain" value="register">
			<table class="table table-bordered table-hover tc-table">
				{foreach from=$availabilityresults key=num item=result}
					<tr>
						<td class="col-small center">
						{if $result.status eq "available"}
							<input type="checkbox" name="domains[]" value="{$result.domain}" {if $num eq "0" && $available}checked {/if}/>
							<input type="hidden" name="domainsregperiod[{$result.domain}]" value="{$result.period}" />
						{else}
							<input type="checkbox" disabled>
						{/if}
						</td>
						<td>{$result.domain}
						
							{if $result.status eq "available"}
								<div class="space-4 visible-xs"></div>
                                <ul class="list-unstyled visible-xs">
									<li><select name="domainsregperiod[{$result.domain}]">{foreach key=period item=regoption from=$result.regoptions}<option value="{$period}">{$period} {$LANG.orderyears} @ {$regoption.register}</option>{/foreach}</select></li>
								</ul>{/if}
								{if $result.status eq "unavailable"}
								<div class="space-4 visible-xs"></div>
								<ul class="list-unstyled visible-xs">
									<li><a href="#" onclick="popupWindow('whois.php?domain={$result.domain}','whois',650,420);return false">Whois Lookup</a></li>
								</ul>
							{/if}
						
						</td>
						<td class="col-small center">
						{if $result.status eq "available"}
							<i class="fa fa-check text-success bigger-110"></i>
						{else}
							<i class="fa fa-times text-danger bigger-110"></i>
						{/if}
						</td>
						<td class="text-center hidden-xs">
						{if $result.status eq "unavailable"} 
							<a href="#" onclick="popupWindow('whois.php?domain={$result.domain}','whois',650,420);return false">Whois Lookup</a>
						{else}
							<select name="domainsregperiod[{$result.domain}]">
							{foreach key=period item=regoption from=$result.regoptions}
								<option value="{$period}">{$period} {$LANG.orderyears} @ {$regoption.register}</option>
							{/foreach}
							</select>
						{/if}
						</td>
					</tr>
				{/foreach}
			</table>
			
			<div class="padding-all text-center">
				<input type="submit" value="{$LANG.ordernowbutton} &raquo;" class="btn btn-success">
			</div>
			
			<div class="space-16"></div>
			
		</form>
	</div>
</div>

{else}

<div class="portlet">
	<div class="portlet-heading inverse">
		<div class="portlet-title">
			<h4><i class="fa fa-tags"></i> {$LANG.domainspricing}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#domain-price"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="domain-price" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th>{$LANG.domaintld}</th>
					<th>{$LANG.domainminyears}</th>
					<th>{$LANG.domainsregister}</th>
					<th>{$LANG.domainstransfer}</th>
					<th>{$LANG.domainsrenew}</td>
				</tr>
			</thead>
	{foreach from=$tldpricelist item=tldpricelist}
				<tr>
					<td data-title="{$LANG.domaintld}">{$tldpricelist.tld}</td>
					<td data-title="{$LANG.domainminyears}">{$tldpricelist.period}</td>
					<td data-title="{$LANG.domainsregister}">{if $tldpricelist.register}{$tldpricelist.register}{else}{$LANG.domainregnotavailable}{/if}</td>
					<td data-title="{$LANG.domainstransfer}">{if $tldpricelist.transfer}{$tldpricelist.transfer}{else}{$LANG.domainregnotavailable}{/if}</td>
					<td data-title="{$LANG.domainsrenew}">{if $tldpricelist.renew}{$tldpricelist.renew}{else}{$LANG.domainregnotavailable}{/if}</td>
				</tr>
		{/foreach}
		</table>
	</div>
	</div>
</div>

	{if !$loggedin && $currencies}
		<form method="post" action="domainchecker.php" class="form-horizontal">
			{$LANG.choosecurrency}: <select class="input-sm" name="currency" onchange="submit()" style="width:76px;">
			{foreach from=$currencies item=curr}
				<option value="{$curr.id}"{if $curr.id eq $currency.id} selected="selected"{/if}>{$curr.code}</option>
			{/foreach}
			</select>
		</form>
		{/if}
{/if}

<div class="modal fade in" id="modalpleasewait">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header text-center">
                 <h4><i class="fa fa-spinner fa-pulse text-warning"></i> {$LANG.pleasewait}</h4>
            </div>
        </div>
    </div>
</div>

