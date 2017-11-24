{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.pwreset}

{if $success}

  <div class="alert alert-success">
    <p>{$LANG.pwresetvalidationsent}</p>
  </div>

  <p>{$LANG.pwresetvalidationcheckemail}

  <br />
  <br />
  <br />
  <br />

{else}

{if $errormessage}
<div class="alert alert-danger text-center">
    <p>{$errormessage}</p>
</div>
{/if}

<form method="post" action="pwreset.php"  class="form-horizontal">
<input type="hidden" name="action" value="reset" />

{if $securityquestion}

<input type="hidden" name="email" value="{$email}" />

<p>{$LANG.pwresetsecurityquestionrequired}</p>
    <fieldset>
	    <div class="form-group">
		  <label class="col-sm-3 control-label" for="answer">{$securityquestion}:</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-4" name="answer" id="answer" type="text" value="{$answer}" />
			</div>
		</div>
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<p><input type="submit" class="btn btn-primary" value="{$LANG.pwresetsubmit}" /></p>
			</div>
		</div>
    </fieldset>
	<br /><br /><br /><br /><br /><br /><br />

{else}

<p>{$LANG.pwresetdesc}</p>
    <fieldset>
	    <div class="form-group">
		  <label class="col-sm-3 control-label" for="email">{$LANG.loginemail}:</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-4" name="email" id="email" type="text" />
			</div>
		</div>
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<p><input type="submit" class="btn btn-primary" value="{$LANG.pwresetsubmit}" /></p>
			</div>
		</div>
    </fieldset>
	<br /><br /><br /><br /><br /><br /><br />
{/if}

</form>

{/if}