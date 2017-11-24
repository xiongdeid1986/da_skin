{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.clientareacancelrequest}

{if $invalid}

<div class="alert alert-warning">
    <p>{$LANG.clientareacancelinvalid}</p>
</div>

<div>
    <input type="button" value="{$LANG.clientareabacklink}" class="btn btn-sm btn-info" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" />
</div>

<br /><br /><br />

{elseif $requested}

<div class="alert alert-success">
    <p>{$LANG.clientareacancelconfirmation}</p>
</div>

<div>
    <input type="button" value="{$LANG.clientareabacklink}" class="btn btn-sm btn-info" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" />
</div>

<br /><br /><br />

{else}

{if $error}
<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        <li>{$LANG.clientareacancelreasonrequired}</li>
    </ul>
</div>
{/if}

<div class="alert alert-block alert-info">
    <p>{$LANG.clientareacancelproduct}: <strong>{$groupname} - {$productname}</strong>{if $domain} ({$domain}){/if}</p>
</div>
<div class="row">
	<div class="col-xs-12">
		<form method="post" action="{$smarty.server.PHP_SELF}?action=cancel&amp;id={$id}" class="form-horizontal">
		<input type="hidden" name="sub" value="submit" />

			<fieldset>
				<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="cancellationreason">{$LANG.clientareacancelreason}</label>
					<div class="col-sm-9">
						<textarea class="col-xs-12 col-sm-8" name="cancellationreason" id="cancellationreason" rows="6"></textarea>
					</div>
				</div>
				<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="type">{$LANG.clientareacancellationtype}</label>
					<div class="col-sm-9">
						<select class="col-xs-12 col-sm-3" name="type" id="type">
						<option value="Immediate">{$LANG.clientareacancellationimmediate}</option>
						<option value="End of Billing Period">{$LANG.clientareacancellationendofbillingperiod}</option>
						</select>
					</div>
				</div>		
				{if $domainid}
					<br />
				<div class="alert alert-block alert-warning">
					<p><strong>{$LANG.cancelrequestdomain}</strong></p>
					<p>{$LANG.cancelrequestdomaindesc|sprintf2:$domainnextduedate:$domainprice:$domainregperiod}</p>
					<p><label class="checkbox"><input type="checkbox" name="canceldomain" id="canceldomain" /> {$LANG.cancelrequestdomainconfirm}</label></p>
				</div>
				{/if}
				<div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<input type="submit" value="{$LANG.clientareacancelrequestbutton}" class="btn btn-danger btn-sm" />
						<input type="button" value="{$LANG.cancel}" class="btn btn-sm btn-info" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" />
					</div>
				</div>

			</fieldset>

		</form>
	</div>
</div>

{/if}