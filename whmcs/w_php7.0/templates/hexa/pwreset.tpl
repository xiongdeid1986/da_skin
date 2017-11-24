 <div class="row">
  <div class="col-md-4 col-md-offset-4 box">
    <p class="text-danger bg-danger text-alert {if !$incorrect} displaynone{/if}" id="login-error"><strong><span aria-hidden="true" class="icon icon-ban"></span> {$LANG.warning}</strong><br/>{$LANG.loginincorrect}</p>         
    <div class="content-wrap">
      <h6>{$LANG.pwreset}</h6>
      {if $success}
      <p class="text-success bg-success text-alert"><span aria-hidden="true" class="icon icon-paper-plane"></span> {$LANG.pwresetvalidationsent}</p><p><small>{$LANG.pwresetvalidationcheckemail}</small></p>
      {else}
      {if $errormessage}
      <p class="text-danger bg-danger text-alert"><strong><span aria-hidden="true" class="icon icon-ban"></span> {$LANG.warning}</strong><br/>{$errormessage}</p>
      {elseif !$securityquestion}
      <p><small>{$LANG.pwresetdesc}</small></p>
      {/if}   
      <form method="post" action="pwreset.php"  name="frmpwreset">
        <input type="hidden" name="action" value="reset" />
        {if $securityquestion}
        <input type="hidden" name="email" value="{$email}" />
        <p><small>{$LANG.pwresetsecurityquestionrequired}</small></p> 
        <div class="form-group">          
          <div class="input-group input-group-lg">          
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-question"></span></span>
            <input class="form-control" name="answer" id="answer" type="text" value="{$answer}" placeholder="{$securityquestion}" />
          </div>
        </div>         
        {else}
        <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-envelope"></span></span>
            <input class="form-control" name="email" id="email" type="email" placeholder="{$LANG.loginemail}" />
          </div>
        </div>    
        {/if}          
        <div class="row">
         <div class="col-md-12">
          <p><input type="submit" class="btn btn-primary btn-lg btn-block" value="{$LANG.pwresetsubmit}" /></p>
        </div>
        </div>
      </form>
    </div>
    {/if} 
  </div>
  </div>
  <div class="content-sm">
    <div class="row">   
      <div class="col-md-4 col-md-offset-4">