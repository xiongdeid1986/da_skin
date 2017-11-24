{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.login}

{if $incorrect}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$LANG.loginincorrect}</p>
</div>
{/if}

<form method="post" action="{$systemsslurl}dologin.php" class="form-horizontal">

    <fieldset>
	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="username">{$LANG.loginemail}:</label>
			<div class="col-sm-9">
			    <input class="col-xs-12 col-sm-3" name="username" id="username" type="text" />
			</div>
		</div>

		<div class="form-group">
		    <label class="col-sm-3 control-label" for="password">{$LANG.loginpassword}:</label>
			<div class="col-sm-9">
			    <input class="col-xs-12 col-sm-3" name="password" id="password" type="password"/>
			</div>
		</div>

		<div class="form-group">
		    <label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
			    <input type="checkbox" name="rememberme"{if $rememberme} checked="checked"{/if} /> {$LANG.loginrememberme}
			</div>
		</div>
		
		
		
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<input type="submit" class="btn btn-success" value="{$LANG.loginbutton}" /> 
			</div>
		</div>
	</fieldset>
	<div class="col-md-offset-3 col-md-9">
		<p><a href="pwreset.php" >{$LANG.loginforgotteninstructions}</a></p>
		<br /><br />
	</div>
</form>

<script type="text/javascript">
$("#username").focus();
</script>
