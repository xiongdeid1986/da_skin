 <div class="row">
  <div class="col-md-4 col-md-offset-4 box">
    {if $invalidlink}
    <p class="text-danger bg-danger text-alert">{$invalidlink}</p>
    {elseif $success}
    <h4>{$LANG.pwresetvalidationsuccess}</h4>
    <p class="text-success bg-success">{$LANG.pwresetsuccessdesc|sprintf2:'<a href="clientarea.php">':'</a>'}</p>
    {else}
    {if $errormessage}
    <p class="text-danger bg-danger text-alert"><strong><span aria-hidden="true" class="icon icon-ban"></span> {$LANG.warning}</strong></p><br>
    {$errormessage}
    {/if}
    <form method="post" action="{$smarty.server.PHP_SELF}?action=pwreset">
      <input type="hidden" name="key" id="key" value="{$key}" />
      <h5>{$LANG.pwresetenternewpw}</h5>
      <div class="form-group">          
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><span aria-hidden="true" class="icon icon-lock"></span></span>
          <input type="password" class="form-control" name="newpw" id="password" placeholder="{$LANG.newpassword}" />
        </div>
      </div>
      <div class="form-group">          
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><span aria-hidden="true" class="icon icon-lock"></span></span>
          <input type="password" class="form-control" name="confirmpw" id="confirmpw" placeholder="{$LANG.confirmnewpassword}" />
        </div>
      </div>
      <div class="form-group">
        {include file="$template/pwstrength.tpl"}
      </div>
      <div class="form-group">
        <input class="btn btn-primary btn-block" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
        <input class="btn btn-default btn-block" type="reset" value="{$LANG.cancel}" />
      </div>
  </form>
  {/if}
</div>
</div>
<div class="content-sm">
    <div class="row">   
      <div class="col-md-4 col-md-offset-4">