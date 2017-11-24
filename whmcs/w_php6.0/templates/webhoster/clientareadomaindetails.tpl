{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.managing|cat:' '|cat:$domain}
{if $updatesuccess}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{elseif $registrarcustombuttonresult=="success"}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$LANG.moduleactionsuccess}</p>
</div>
{elseif $error}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$error}</p>
</div>
{elseif $registrarcustombuttonresult}
<div class="alert alert-danger">
    <p><strong>{$LANG.moduleactionfailed}:</strong> {$registrarcustombuttonresult}</p>
</div>
{elseif $lockstatus=="unlocked"}
<div class="alert alert-danger">
    <p><strong>{$LANG.domaincurrentlyunlocked}</strong> {$LANG.domaincurrentlyunlockedexp}</p>
</div>
{/if}

<div class="tc-tabsbar arrow" id="tabs-1">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-information" data-toggle="tab" title="{$LANG.information}">{$LANG.information}</a></li>
        <li><a href="#tab-autorenew" data-toggle="tab" title="{$LANG.domainsautorenew}">{$LANG.domainsautorenew}</a></li>
        {if $rawstatus == "active" && $managens}<li><a href="#tab-nameservers" data-toggle="tab" title="{$LANG.domainnameservers}">{$LANG.domainnameservers}</a></li>{/if}
        {if $lockstatus}{if $tld neq "co.uk" && $tld neq "org.uk" && $tld neq "ltd.uk" && $tld neq "plc.uk" && $tld neq "me.uk"}<li><a href="#tab-lock" data-toggle="tab" title="{$LANG.domainregistrarlock}">{$LANG.domainregistrarlock}</a></li>{/if}{/if}
        {if $releasedomain}<li><a href="#tab-release" data-toggle="tab" title="{$LANG.domainrelease}">{$LANG.domainrelease}</a></li>{/if}
        {if $addonscount}<li><a href="#tab-addons" data-toggle="tab" title="{$LANG.clientareahostingaddons}">{$LANG.clientareahostingaddons}</a></li>{/if}
    </ul>	
	
<div class="tab-content">
	<div class="tab-pane active" id="tab-information">
		<div class="row">
			<div class="col-sm-4">
				<h2>{$LANG.information}</h2>
				<p>{$LANG.domaininfoexp}</p>

				<div class="btn-toolbar">
					{if $managecontacts || $registerns || $dnsmanagement || $emailforwarding || $getepp}
					<div class="btn-group">
						<a data-toggle="dropdown" href="#" class="btn btn-sm btn-primary dropdown-toggle">{$LANG.domainmanagementtools} <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu dropdown-primary">
							{if $managecontacts}<li><a href="clientarea.php?action=domaincontacts&domainid={$domainid}">{$LANG.domaincontactinfo}</a></li>{/if}
							{if $registerns}<li><a href="clientarea.php?action=domainregisterns&domainid={$domainid}">{$LANG.domainregisterns}</a></li>{/if}
							{if $dnsmanagement}<li><a href="clientarea.php?action=domaindns&domainid={$domainid}">{$LANG.clientareadomainmanagedns}</a></li>{/if}
							{if $emailforwarding}<li><a href="clientarea.php?action=domainemailforwarding&domainid={$domainid}">{$LANG.clientareadomainmanageemailfwds}</a></li>{/if}
							{if $getepp}
							<li><a href="clientarea.php?action=domaingetepp&domainid={$domainid}">{$LANG.domaingeteppcode}</a></li>{/if}
							{if $registrarcustombuttons}<li class="divider"></li>
							{foreach from=$registrarcustombuttons key=label item=command}
							<li><a href="clientarea.php?action=domaindetails&amp;id={$domainid}&amp;modop=custom&amp;a={$command}">{$label}</a></li>
							{/foreach}{/if}
							<li class="divider"></li>
							<li><a href="clientarea.php?action=domains">{$LANG.backtodomainslist|replace:'&laquo; ':''}</a></li>
						</ul>
					</div>{/if}<br /></br /><br /></br />
				</div>	
            </div>
			<div class="col-sm-8">
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4 class="lighter">{$domain} <span class="label label-{$rawstatus} arrowed-right">{$status}</span></h4>
						</div>
						<div class="clearfix"></div>
					</div>						
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
								<tr>
									<td>{$LANG.firstpaymentamount}</td>
									<td>{$firstpaymentamount}</td>								
								</tr>
								<tr>
									<td>{$LANG.clientareahostingregdate}</td>
									<td>{$registrationdate}</td>
								</tr>
								<tr>
									<td>{$LANG.recurringamount}</td>
									<td>{$recurringamount} {$LANG.every} {$registrationperiod} {$LANG.orderyears}{if $renew} &nbsp; <a href="cart.php?gid=renewals" class="btn btn-yellow btn-xs">{$LANG.domainsrenewnow}</a>{/if}</td>
								</tr>
								<tr>
									<td>{$LANG.clientareahostingnextduedate}</td>
									<td>{$nextduedate}</td>
								</tr>
								<tr>
									<td>{$LANG.orderpaymentmethod}</td>
									<td>{$paymentmethod}</td>
								</tr>
								{if $registrarclientarea}
								<tr>
									<td><div class="moduleoutput">{$registrarclientarea|replace:'modulebutton':'btn'}</div></td>
								</tr>
								{/if}
						</table>					
					</div>
				</div><br /><br /><br /><br />			
			</div>
		</div>
	</div>
	
	
	<div class="tab-pane" id="tab-autorenew">
		<div class="row">
			<div class="col-sm-4">
                <h2>{$LANG.domainsautorenew}</h2>
                <p>{$LANG.domainrenewexp}</p>
			</div>
			<div class="col-sm-8">
				<h4><strong>{$LANG.domainautorenewstatus}</strong></h4>
				<p>{if $autorenew}<span class="label label-success">{$LANG.domainsautorenewenabled}</span>{else}<span class="label label-important">{$LANG.domainsautorenewdisabled}</span>{/if}</p><hr /><br />
				<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails" class="form-horizontal">
					<input type="hidden" name="id" value="{$domainid}">
					{if $autorenew}
					<input type="hidden" name="autorenew" value="disable">
						<p><input type="submit" class="btn btn-danger" value="{$LANG.domainsautorenewdisable}" /></p>
					{else}
					<input type="hidden" name="autorenew" value="enable">
						<p><input type="submit" class="btn btn-success" value="{$LANG.domainsautorenewenable}" /></p>
					{/if}
				</form><br /><br /><br /><br />
			</div>
        </div>
    </div>

	<div class="tab-pane" id="tab-nameservers">
		<h2>{$LANG.domainnameservers}</h2>
        <p>{$LANG.domainnsexp}</p><hr />
        <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails" class="form-horizontal">
                <input type="hidden" name="id" value="{$domainid}" />
                <input type="hidden" name="sub" value="savens" />
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-9">				
							<label class="radio"><input type="radio" name="nschoice" value="default" onclick="disableFields('domnsinputs',true)"{if $defaultns} checked{/if} /> {$LANG.nschoicedefault}</label>
							<label class="radio"><input type="radio" name="nschoice" value="custom" onclick="disableFields('domnsinputs','')"{if !$defaultns} checked{/if} /> {$LANG.nschoicecustom}</label>
						</div>
					</div>
					
				<div class="space-12"></div>
				
                <fieldset>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ns1">{$LANG.domainnameserver1}</label>
                        <div class="col-sm-9">
                            <input class="col-xs-10 col-sm-4 domnsinputs" id="ns1" name="ns1" type="text" value="{$ns1}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ns2">{$LANG.domainnameserver2}</label>
                        <div class="col-sm-9">
                            <input class="col-xs-10 col-sm-4 domnsinputs" id="ns2" name="ns2" type="text" value="{$ns2}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ns3">{$LANG.domainnameserver3}</label>
                        <div class="col-sm-9">
                            <input class="col-xs-10 col-sm-4 domnsinputs" id="ns3" name="ns3" type="text" value="{$ns3}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ns4">{$LANG.domainnameserver4}</label>
                        <div class="col-sm-9">
                            <input class="col-xs-10 col-sm-4 domnsinputs" id="ns4" name="ns4" type="text" value="{$ns4}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ns5">{$LANG.domainnameserver5}</label>
                        <div class="col-sm-9">
                            <input class="col-xs-10 col-sm-4 domnsinputs" id="ns5" name="ns5" type="text" value="{$ns5}" />
                        </div>
                    </div>
                </fieldset>
                    <div class="clearfix form-actions">
						<div class="col-md-offset-3 col-md-9">
							<input type="submit" class="btn btn-success btn-sm" value="{$LANG.changenameservers}" />
						</div>
					</div>
        </form>
    </div>

	<div class="tab-pane" id="tab-lock">
		<div class="row">
			<div class="col-sm-4">
                <h2>{$LANG.domainregistrarlock}</h2>
                <p>{$LANG.domainlockingexp}</p>
            </div>
			<div class="col-sm-8">
                <h4><strong>{$LANG.domainreglockstatus}:</strong></h4>
                <p>{if $lockstatus=="locked"}<span class="label label-success">{$LANG.domainsautorenewenabled}</span>{else}<span class="label label-important">{$LANG.domainsautorenewdisabled}</span>{/if}</p>
                <hr />
                <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
                <input type="hidden" name="id" value="{$domainid}" />
                <input type="hidden" name="sub" value="savereglock" />
                {if $lockstatus=="locked"}
                <p><input type="submit" class="btn red" value="{$LANG.domainreglockdisable}" /></p>
                {else}
                <p><input type="submit" class="btn green" name="reglock" value="{$LANG.domainreglockenable}" /></p>
                {/if}
                </form><br /><br /><br /><br />
			</div>

		</div>
	</div>
	
	
	<div class="tab-pane" id="tab-release">
		<div class="row">
			<div class="col-sm-4">
                <h2>{$LANG.domainrelease}</h2>
                <p>{$LANG.domainreleasedescription}</p>
            </div>
			<div class="col-sm-8">
                {if $releasedomain}
                <p><strong>&nbsp;&raquo;&nbsp;&nbsp;{$LANG.domainrelease}</strong></p>
                <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
                <input type="hidden" name="sub" value="releasedomain">
                <input type="hidden" name="id" value="{$domainid}">
                {$LANG.domainreleasetag}: <input type="text" name="transtag" size="20" />
                <p align="center"><input type="submit" value="{$LANG.domainrelease}" class="buttonwarn" /></p>
                </form>
                {/if}
			</div>
		</div><br /><br /><br /><br />
	</div>
	
	
	<div class="tab-pane" id="tab-addons">
		<div class="row">
			<div class="col-sm-4">
				<div class="internalpadding">
					<h2>{$LANG.domainaddons}</h2>
					<p>{$LANG.domainaddonsinfo}</p>
				</div>
			</div>
			<div class="col-sm-8">
			<br />
                {if $addons.idprotection}
                <div class="row">
                    <div class="col-sm-8">
						<ul class="media-list">
							<li class="media">
								<a class="pull-left" href="#"><img src="images/idprotect.png" /></a>
								<div class="media-body">
									<h5 class="media-heading"><strong>{$LANG.domainidprotection}</strong></h5>
									{$LANG.domainaddonsidprotectioninfo}<br /><br />
									{if $addonstatus.idprotection}
									<a href="clientarea.php?action=domainaddons&id={$domainid}&disable=idprotect&token={$token}">{$LANG.disable}</a>
									{else}
									<a href="clientarea.php?action=domainaddons&id={$domainid}&buy=idprotect&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.idprotection}</a>
									{/if}
								</div>
							</li>
						</ul>
                    </div>
                </div>
                {/if}
                {if $addons.dnsmanagement}
                <div class="row">
                    <div class="col-sm-8">
						<ul class="media-list">
							<li class="media">
								<a class="pull-left" href="#"><img src="images/dnsmanagement.png" /></a>
								<div class="media-body">
									<h5 class="media-heading"><strong>{$LANG.domainaddonsdnsmanagement}</strong></h5>
									{$LANG.domainaddonsdnsmanagementinfo}<br /><br />
									{if $addonstatus.dnsmanagement}
									<a href="clientarea.php?action=domaindns&domainid={$domainid}">{$LANG.manage}</a> | <a href="clientarea.php?action=domainaddons&id={$domainid}&disable=dnsmanagement&token={$token}">{$LANG.disable}</a>
									{else}
									<a href="clientarea.php?action=domainaddons&id={$domainid}&buy=dnsmanagement&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.dnsmanagement}</a>
									{/if}
								</div>
							</li>
						</ul>
                    </div>
                </div>
                {/if}
                {if $addons.emailforwarding}
                <div class="row">
                    <div class="col-sm-8">
						<ul class="media-list">
							<li class="media">
								<a class="pull-left" href="#"><img src="images/emailfwd.png"/></a>
								<div class="media-body">
									<h5 class="media-heading"><strong>{$LANG.domainemailforwarding}</strong></h5>
									{$LANG.domainaddonsemailforwardinginfo}<br /><br />
									{if $addonstatus.emailforwarding}
									<a href="clientarea.php?action=domainemailforwarding&domainid={$domainid}">{$LANG.manage}</a> | <a href="clientarea.php?action=domainaddons&id={$domainid}&disable=emailfwd&token={$token}">{$LANG.disable}</a>
									{else}
									<a href="clientarea.php?action=domainaddons&id={$domainid}&buy=emailfwd&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.emailforwarding}</a>
									{/if}
								</div>
							</li>
						</ul>
                    </div>
                </div>
                {/if}
			</div>
		</div>
	</div>


	</div>

</div>