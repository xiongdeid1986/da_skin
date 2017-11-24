{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.domainregisterns}
<p>{$LANG.domainregisternsexplanation}</p>

<div class="alert alert-info">
    <p>{$LANG.domainname}: <strong>{$domain}</strong></p>
</div>

{if $result}
    <div class="alert alert-danger">
        <p class="bold textcenter">{$result}</p>
    </div>
{/if}
<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter">{$LANG.domainregisternsreg}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#dns-box1"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="dns-box1" class="panel-collapse collapse in">
	<div class="portlet-body">
		<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns" class="form-horizontal">
			<input type="hidden" name="sub" value="register" />
			<input type="hidden" name="domainid" value="{$domainid}" />
			<div class="space-12"></div>
		<fieldset>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="ns1">{$LANG.domainregisternsns}</label>
				<div class="col-sm-9">
					<input type="text" name="ns" id="ns1" class="col-xs-12 col-sm-4" /> . {$domain}
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="ip1">{$LANG.domainregisternsip}</label>
				<div class="col-sm-9">
					<input type="text" name="ipaddress" class="col-xs-12 col-sm-4" id="ip1" />
				</div>
			</div>

			<div class="clearfix form-actions">
				<div class="col-md-offset-3 col-md-9">
					<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />
				</div>
			</div>
		</fieldset>
		</form>
	</div>
	</div>
</div>

<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter">{$LANG.domainregisternsmod}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#dns-box2"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>		
	</div>
	<div id="dns-box2" class="panel-collapse collapse in">
	<div class="portlet-body">
		<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns" class="form-horizontal">
			<input type="hidden" name="sub" value="modify" />
			<input type="hidden" name="domainid" value="{$domainid}" />
			<div class="space-12"></div>
		<fieldset>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="ns2">{$LANG.domainregisternsns}</label>
				<div class="col-sm-9">
					<input type="text" name="ns" id="ns2" class="col-xs-12 col-sm-4" /> . {$domain}
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="ip2">{$LANG.domainregisternscurrentip}</label>
				<div class="col-sm-9">
					<input type="text" name="currentipaddress" class="col-xs-12 col-sm-4" id="ip2" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="ip3">{$LANG.domainregisternsnewip}</label>
				<div class="col-sm-9">
					<input type="text" name="newipaddress" class="col-xs-12 col-sm-4" id="ip3" />
				</div>
			</div>

			<div class="clearfix form-actions">
				<div class="col-md-offset-3 col-md-9">
					<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />
				</div>
			</div>
		</fieldset>
		</form>
	</div>
	</div>
</div>

<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter">{$LANG.domainregisternsdel}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#dns-box3"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>		
	</div>
	<div id="dns-box3" class="panel-collapse collapse in">
	<div class="portlet-body">
		<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns" class="form-horizontal">
			<input type="hidden" name="sub" value="delete" />
			<input type="hidden" name="domainid" value="{$domainid}" />
			<div class="space-12"></div>
		<fieldset>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="ns3">{$LANG.domainregisternsns}</label>
				<div class="col-sm-9">
					<input type="text" name="ns" id="ns3" class="col-xs-12 col-sm-4" /> . {$domain}
				</div>
			</div>

			<div class="clearfix form-actions">
				<div class="col-md-offset-3 col-md-9">			
					<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />
				</div>
			</div>

		</fieldset>
		</form>
	</div>
	</div>
</div>

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
	<input type="hidden" name="id" value="{$domainid}" />
	<p><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-xs btn-inverse" /></p>
</form>