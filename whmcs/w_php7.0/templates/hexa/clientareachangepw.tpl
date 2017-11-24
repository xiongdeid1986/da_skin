{include file="$template/pageheader.tpl" title=$LANG.clientareanavchangepw}

{include file="$template/clientareadetailslinks.tpl"}

{if $successful}
<div class="alert alert-success">
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $errormessage}
<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}
<div class="titleline"></div>
<form  method="post" action="{$smarty.server.PHP_SELF}?action=changepw">

<div class="row">
<div class="col-md-3">
<div class="form-group">
<label for="existingpw">{$LANG.existingpassword}</label>
<input type="password" class="form-control" name="existingpw" id="existingpw" />
</div>
</div>
<div class="col-md-3">
<div class="form-group">
<label for="password">{$LANG.newpassword}</label>
<input type="password" name="newpw" class="form-control" id="password" />
</div>
</div>
<div class="col-md-3">          
<div class="form-group">
<label for="confirmpw">{$LANG.confirmnewpassword}</label>
<input type="password" class="form-control" name="confirmpw" id="confirmpw" />
</div>
</div>
<div class="col-md-3">
<div class="form-group" style="padding-top:15px;">
{include file="$template/pwstrength.tpl"}
</div>
</div>
</div>
<div class="btn-toolbar pull-right" role="toolbar">    
<input class="btn btn-primary btn-sm pull-right" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />  
<input class="btn btn-link btn-sm pull-right" type="reset" value="{$LANG.cancel}" />
</div>    
</form>