{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.clientareanavchangepw}
{include file="$template/clientareadetailslinks.tpl"}
{if $successful}
<div class="alert alert-success">
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $errormessage}
<div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}
<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=changepw">

  <fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="existingpw">{$LANG.existingpassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="existingpw" id="existingpw" />
		</div>
	</div>
	
	<div class="hr hr-16 hr-dotted"></div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password">{$LANG.newpassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="newpw" id="password" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="confirmpw">{$LANG.confirmnewpassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="confirmpw" id="confirmpw" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="passstrength">{$LANG.pwstrength}</label>
		<div class="col-sm-9">
            {include file="$template/pwstrength.tpl"}
		</div>
	</div>

  </fieldset>

  <div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
		<input class="btn" type="reset" value="{$LANG.cancel}" />
	</div>
</div>

</form>