{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.contacttitle desc=$LANG.contactheader}

{if $sent}

<br />

<div class="alert alert-success">
    <p><strong>{$LANG.contactsent}</strong></p>
</div>

{else}

{if $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}
<form method="post" action="contact.php?action=send" class="form-horizontal">
    <fieldset>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="name">{$LANG.supportticketsclientname}</label>
				<div class="col-sm-9">
        			<input class="col-xs-12 col-sm-3" type="text" name="name" id="name" value="{$name}" />
				</div>
        </div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="email">{$LANG.supportticketsclientemail}</label>
				<div class="col-sm-9">
					<input class="col-xs-12 col-sm-3" type="text" name="email" id="email" value="{$email}" />
				</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="subject">{$LANG.supportticketsticketsubject}</label>
				<div class="col-sm-9">
					<input class="col-xs-12 col-sm-6" type="text" name="subject" id="subject" value="{$subject}" />
				</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="message">{$LANG.contactmessage}</label>
				<div class="col-sm-9">
					<textarea name="message" id="message" rows="12" class="col-xs-12 col-sm-6">{$message}</textarea>
				</div>
		</div>
    </fieldset>

		{if $capatacha}
		<div class="hr hr32 hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label">{$LANG.captchatitle}</label>
				<div class="col-xs-12 col-sm-6">
					<p>{$LANG.captchaverify}</p>
				{if $capatacha eq "recaptcha"}
					<div align="center">{$recapatchahtml}</div>
				{else}
				<p><img src="includes/verifyimage.php" align="middle" /> <input type="text" name="code" size="10" maxlength="5" class="input-small" /></p>
				{/if}
				</div>
		</div>
		{/if}
	
	
<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input type="submit" value="{$LANG.contactsend}" class="btn btn-success" />
	</div>
</div>

</form>
{/if}
<br />
<br />
<br />