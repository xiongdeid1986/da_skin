{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{include file="$template/pageheader.tpl" title=$product}
{if $modulechangepwresult eq "success"}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	{$LANG.serverchangepasswordsuccessful}
</div>
{elseif $modulechangepwresult eq "error"}
<div class="alert alert-danger>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	{$modulechangepasswordmessage}
</div>
{elseif $modulecustombuttonresult=="success"}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	{$LANG.moduleactionsuccess}
</div>
{elseif $modulecustombuttonresult}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	<strong>{$LANG.moduleactionfailed}:</strong> {$modulecustombuttonresult}
</div>
{/if}

<div class="tc-tabsbar arrow" id="tabs-1">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-information" data-toggle="tab" title="{$LANG.information}"><i class="icon-info-sign"></i> {$LANG.information}</a></li>
		{if $modulechangepassword}<li><a href="#tab-changepw" data-toggle="tab" title="{$LANG.serverchangepassword}"><i class="icon-key"></i> {$LANG.serverchangepassword}</a></li>{/if}
		{if $downloads}<li><a href="#tab-downloads" data-toggle="tab" title="{$LANG.downloadstitle}">{$LANG.downloadstitle}</a></li>{/if}
		{if $addons || $addonsavailable}<li><a href="#tab-addons" data-toggle="tab" title="{$LANG.clientareahostingaddons}"><i class="icon-plus"></i> {$LANG.clientareahostingaddons}</a></li>{/if}
	</ul>

<div class="tab-content">
	<div class="tab-pane active" id="tab-information">
		<div class="row">
			<div class="col-sm-4">
				<h2>{$LANG.information}</h2>
				{if $groupname}{$groupname} - {/if}{$product} <span class="label label-{$rawstatus} arrowed-right">{$status}</span>{if $domain}<br /><span class="text-primary">{$domain}</span>{/if}<br /><br />
				<p>{$LANG.clientareaproductdetailsintro}</p>
				{if $suspendreason}<span class="text-warning"><strong>{$LANG.suspendreason}</strong>: {$suspendreason}</span>{/if}
				
				<div class="btn-toolbar">
					{if $packagesupgrade || $configoptionsupgrade || $showcancelbutton || $modulecustombuttons}
						<div class="btn-group">
							<a data-toggle="dropdown" class="btn btn-sm btn-primary dropdown-toggle">{$LANG.productmanagementactions} <i class="fa fa-angle-down"></i></a>
							<ul class="dropdown-menu dropdown-primary">
								{foreach from=$modulecustombuttons key=label item=command}
								<li><a href="clientarea.php?action=productdetails&amp;id={$id}&amp;modop=custom&amp;a={$command}">{$label}</a></li>
								{/foreach}
								{if $packagesupgrade}
								<li><a href="upgrade.php?type=package&amp;id={$id}">{$LANG.upgradedowngradepackage}</a></li>
								{/if}
								{if $configoptionsupgrade}
								<li><a href="upgrade.php?type=configoptions&amp;id={$id}">{$LANG.upgradedowngradeconfigoptions}</a></li>
								{/if}
								{if $showcancelbutton}
								<li><a href="clientarea.php?action=cancel&amp;id={$id}">{$LANG.clientareacancelrequestbutton}</a></li>
								{/if}
								<li class="divider"></li>
								<li><a href="clientarea.php?action=products">{$LANG.backtoserviceslist|replace:'&laquo; ':''}</a>
							</ul>
						</div>
					{else}
					{if $clientareaaction == 'productdetails'}
							<a href="clientarea.php?action=products" class="btn btn-sm btn-info">{$LANG.backtoserviceslist|replace:'&laquo; ':''}</a>
					{else}
						<form method="post" action="{$smarty.server.PHP_SELF}?action=productdetails" style="margin-bottom:0"><input type="hidden" name="id" value="{$id}"><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-sm btn-info"></form>
					{/if}
					{/if}<br /></br /></br />
				</div>
			</div>
			<div class="col-sm-8">
				<div class="portlet">
					<div class="portlet-heading dark">
						<div class="portlet-title">
							<h4><i class="fa fa-th"></i> {$LANG.orderproduct}</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box1"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box1" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<thead>
							</thead>
								{if $dedicatedip}
								<tr>
									<td>{$LANG.domainregisternsip}</td>
									<td>{$dedicatedip}</td>
								</tr>
								{/if}
								<tr>
									<td>{$LANG.firstpaymentamount}</td>
									<td>{$firstpaymentamount}</td>								
								</tr>
								<tr>
									<td>{$LANG.clientareahostingregdate}</td>
									<td>{$regdate}</td>
								</tr>
								<tr>
									<td>{$LANG.recurringamount}</td>
									<td>{$recurringamount}</td>
								</tr>
								<tr>
									<td>{$LANG.clientareahostingnextduedate}</td>
									<td>{$nextduedate}</td>
								</tr>
								<tr>
									<td>{$LANG.orderbillingcycle}</td>
									<td>{$billingcycle}</td>
								</tr>
								<tr>
									<td>{$LANG.orderpaymentmethod}</td>
									<td>{$paymentmethod}</td>
								</tr>
						</table>
					</div>
					</div>
				</div>
				{if $username}
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4><i class="fa fa-key"></i> {$LANG.orderlogininfo}</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box2"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box2" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<tr>
								<td>{$LANG.serverusername} <i class="fa fa-angle-right text-blue"></i> {$username}</td>
								<td>{if $password}{$LANG.serverpassword} <i class="fa fa-angle-right text-blue"></i> {$password}{/if}</td>
							</tr>
						</table>
					</div>
					</div>
				</div>
				{/if}								
				{if $lastupdate}
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4></h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box3"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box3" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<tr>
								<td><p>{$LANG.clientareadiskusage}</p>{$diskusage}MB / {$disklimit}MB ({$diskpercent})<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all progress progress-striped active"><span class="ui-progressbar-value ui-widget-header ui-corner-left progress-bar progress-bar-success" style="width:{$diskpercent}"></span></div></td>
								<td><p>{$LANG.clientareabwusage}</p>{$bwusage}MB / {$bwlimit}MB ({$bwpercent})<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all progress progress-striped active"><span class="ui-progressbar-value ui-widget-header ui-corner-left progress-bar progress-bar-success" style="width:{$bwpercent}"></span></div></td>
							</tr>
						</table>
					</div>
					</div>
				</div>
				{/if}					
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4><i class="fa fa-th"></i> {$LANG.cartconfigurationoptions}</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box4"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box4" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
						{foreach from=$configurableoptions item=configoption}
							<tr>
								<td>{$configoption.optionname}</td>	
								<td>{if $configoption.optiontype eq 3}{if $configoption.selectedqty}{$LANG.yes}{else}{$LANG.no}{/if}{elseif $configoption.optiontype eq 4}{$configoption.selectedqty} x {$configoption.selectedoption}{else}{$configoption.selectedoption}{/if}</td>
							</tr>
						{/foreach}
						{foreach from=$productcustomfields item=customfield}
							<tr>
								<td>{$customfield.name} - ({$customfield.description})</td>		
								<td>{$customfield.value}</td>
							</tr>
						{/foreach}
						</table>
					</div>
					</div>
				</div>				
			</div>
		</div>
		{if $moduleclientarea}{$moduleclientarea|replace:'modulebutton':'btn btn-info btn-sm'}{/if}
	</div>

		
	<div class="tab-pane" id="tab-changepw">		
		<h2>{$LANG.serverchangepassword}</h2><div class="alert alert-info">{$LANG.serverchangepasswordintro}</div>
		<form method="post" action="{$smarty.server.PHP_SELF}" class="form-horizontal">
		<input type="hidden" name="id" value="{$id}">
		<input type="hidden" name="modulechangepassword" value="true">
			<fieldset>
				<input type="hidden" name="action" value="productdetails">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="password">{$LANG.newpassword}</label>
						<div class="col-sm-9">
							<input class="col-xs-10 col-sm-5" type="password" name="newpw" id="password">							
						</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="confirmpw">{$LANG.confirmnewpassword}</label>
						<div class="col-sm-9">
							<input class="col-xs-10 col-sm-5" type="password" name="confirmpw" id="confirmpw" class="">
						</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="passstrength">{$LANG.pwstrength}</label>
							<div class="col-sm-9">
								{include file="$template/pwstrength.tpl"}
							</div>
				</div>
				<div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<button class="btn btn-info btn-sm">{$LANG.clientareasavechanges}</button>
						<button class="btn btn-danger btn-sm" type="reset">{$LANG.cancel}</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>

	<div class="tab-pane" id="tab-downloads">
		<div class="row">
			<div class="col-sm-4">
				<h2>{$LANG.downloadstitle}</h2>
				<p>There are the following downloads associated with this product</p><br /></br />
			</div>
			<div class="col-sm-8">
				{foreach from=$downloads item=download}
						<h4>{$download.title} - <a href="{$download.link}" title="{$LANG.downloadname} {$download.title}" class="btn btn-xs btn-inverse"><i class="fa fa-download"></i> {$LANG.downloadname}</a></h4>
						<p>{$download.description}</p>
				{/foreach}
			</div>
		</div>
	</div>

	<div class="tab-pane" id="tab-addons">
			<h2>{$LANG.clientareahostingaddons}</h2>
			<p>{$LANG.yourclientareahostingaddons}</p>
		<table class="table table-striped table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th>{$LANG.clientareaaddon}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.clientareaaddonpricing}</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md">{$LANG.clientareahostingnextduedate}</th>
				</tr>
			</thead>
			{foreach key=num item=addon from=$addons}
				<tr>
					<td>{$addon.name} &nbsp; <span class="label {$addon.rawstatus}">{$addon.status}</span>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.clientareaaddonpricing} : {$addon.pricing}
							<li><i class="fa fa-angle-right bigger-110"></i> {$LANG.clientareahostingnextduedate} : {$addon.nextduedate}</li>
						</ul>							
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$addon.pricing}</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md">{$addon.nextduedate}</td>
				</tr>
			{foreachelse}
				<tr>
					<td class="text-center" colspan="3">{$LANG.clientareanoaddons}</td>
				</tr>
			{/foreach}
		</table>
		
		{if $addonsavailable}<p><a class="btn btn-success btn-sm" href="cart.php?gid=addons&amp;pid={$id}">{$LANG.orderavailableaddons}</a></p>{/if}
	</div>	

</div>
</div>