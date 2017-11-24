<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>

{include file="$template/pageheader.tpl" title=$LANG.clientregistertitle desc=$LANG.registerintro}

{if $noregistration}

    <div class="alert alert-danger">
        <p>{$LANG.registerdisablednotice}</p>
    </div>

{else}

{if $errormessage}
<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<form method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="register" value="true" />

<div class="row">
<div class="form-group">
<div class="col-lg-6">

    <div class="form-group">
	    <label class="control-label" for="firstname">{$LANG.clientareafirstname}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="firstname" id="firstname" value="{$clientfirstname}"{if in_array('firstname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="lastname">{$LANG.clientarealastname}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="lastname" id="lastname" value="{$clientlastname}"{if in_array('lastname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="companyname">{$LANG.clientareacompanyname}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="companyname" id="companyname" value="{$clientcompanyname}"{if in_array('companyname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="email">{$LANG.clientareaemail}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="email" id="email" value="{$clientemail}"{if in_array('email',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="password">{$LANG.clientareapassword}</label>
		<div class="controls">
		    <input type="password" class="form-control" name="password" id="password" value="{$clientpassword}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="password2">{$LANG.clientareaconfirmpassword}</label>
		<div class="controls">
		    <input type="password" class="form-control" name="password2" id="password2" value="{$clientpassword2}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="sr-only" for="passstrength">{$LANG.pwstrength}</label>
		<div class="controls">
            {include file="$template/pwstrength.tpl"}
		</div>
	</div>

</div>
<div class="col-lg-6">
    <div class="form-group">
	    <label class="control-label" for="address1">{$LANG.clientareaaddress1}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="address1" id="address1" value="{$clientaddress1}"{if in_array('address1',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="city">{$LANG.clientareacity}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="city" id="city" value="{$clientcity}"{if in_array('city',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="postcode">{$LANG.clientareapostcode}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="postcode" id="postcode" value="{$clientpostcode}"{if in_array('postcode',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="country">{$LANG.clientareacountry}</label>
		<div class="controls">
		   {$clientcountriesdropdown|replace:'name="country"':'name="country" style="width:100%; height: 34px; padding: 6px 12px; font-size: 14px; border-radius: 4px; vertical-align: middle; border: 1px solid #ccc; color: #555; line-height: 1.428571429;"'}
		</div>
	</div>

    <div class="form-group">
	    <label class="control-label" for="phonenumber">{$LANG.clientareaphonenumber}</label>
		<div class="controls">
		    <input type="text" class="form-control" name="phonenumber" id="phonenumber" value="{$clientphonenumber}"{if in_array('phonenumber',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

			</div>
		</div>
	</div>
	<div class="row">
	<div class="col-lg-12">
		{if $currencies}
		<div class="form-group">
			<label class="control-label" for="currency">{$LANG.choosecurrency}</label>
			<div class="controls" id="currency">
				<select name="currency" class="form-control">
					{foreach from=$currencies item=curr}
					<option value="{$curr.id}"{if !$smarty.post.currency && $curr.default || $smarty.post.currency eq $curr.id } selected{/if}>{$curr.code}</option>
					{/foreach}
				</select>
			</div>
		</div>
		{/if}

{foreach key=num item=customfield from=$customfields}
    <div class="form-group">
	    <label class="control-label" for="customfield{$customfield.id}">{$customfield.name}</label>
		<div class="controls">
		    {$customfield.input} {$customfield.description}
		</div>
	</div>
{/foreach}

{if $securityquestions}
    <div class="form-group">
	    <label class="control-label" for="securityqans">{$LANG.clientareasecurityquestion}</label>
		<div class="controls">
		    <select name="securityqid" id="securityqid">
            {foreach key=num item=question from=$securityquestions}
            	<option value={$question.id}>{$question.question}</option>
            {/foreach}
            </select>
		</div>
	</div>
    <div class="form-group">
	    <label class="control-label" for="securityqans">{$LANG.clientareasecurityanswer}</label>
		<div class="controls">
		    <input type="password" class="form-control" name="securityqans" id="securityqans" />
		</div>
	</div>
{/if}

	</div>
	</div>
	
	{if $capatacha}
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>{$LANG.captchatitle}</h4><small>{$LANG.captchaverify}</small>
				</div>
				<div class="panel-body">
					{if $capatacha eq "recaptcha"}
					<div align="center">{$recapatchahtml}</div>
					{else}
					<div class="col-lg-2">
						<img src="includes/verifyimage.php" /> 
					</div>
					<div class="col-lg-3">
						<input type="text" class="form-control input-sm" name="code" maxlength="5" />
					</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
	{/if}
	{if $accepttos}
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="checkbox">
					<label id="tosagree"></label>
					<input type="checkbox" name="accepttos" id="accepttos" value="on" >
					{$LANG.ordertosagreement} <a href="{$tosurl}" target="_blank">{$LANG.ordertos}</a>
				</div>
			</div>
		</div>
	</div>
	{/if}
	<input class="btn btn-primary" type="submit" value="{$LANG.clientregistertitle}">
</form>

{/if}
