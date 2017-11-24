{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.pwreset}

{if $invalidlink}

  <div class="alert alert-danger">
    <p class="text-center">{$invalidlink}</p>
  </div>
  <br /><br /><br /><br />

{elseif $success}

  <br />
  <div class="alert alert-success">
    <p class="text-center bold">{$LANG.pwresetvalidationsuccess}</p>
  </div>

  <p class="text-center">{$LANG.pwresetsuccessdesc|sprintf2:'<a href="clientarea.php">':'</a>'}</p>

  <br /><br /><br /><br />

{else}

{if $errormessage}

  <div class="alert alert-danger">
    <p class="text-center">{$errormessage}</p>
  </div>

{/if}

<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=pwreset">
<input type="hidden" name="key" id="key" value="{$key}" />

<br /><h4 class="lighter">{$LANG.pwresetenternewpw}</h4><br />
  <fieldset>

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
		<input class="btn btn-primary" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
		<input class="btn btn-inverse" type="reset" value="{$LANG.cancel}" />
	</div>
</div>
</form>
{/if}