 <div class="row">
  <div class="col-md-4 col-md-offset-4 box">      
    <div class="content-wrap">
      <h6>{$LANG.login}</h6>
      <p class="text-danger bg-danger text-alert {if !$incorrect} displaynone{/if}" id="login-error"><strong><span aria-hidden="true" class="icon icon-ban"></span> {$LANG.warning}</strong><br/>{$LANG.loginincorrect}</p>
      <form method="post" action="{$systemsslurl}dologin.php">
        <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-envelope"></span></span>
            <input class="form-control" name="username" type="text" placeholder="{$LANG.loginemail}">
          </div>
        </div>             
        <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-lock"></span></span>
            <input class="form-control" name="password" type="password" placeholder="{$LANG.loginpassword}">
          </div>
        </div>             
        <a href="pwreset.php" class="forgot">{$LANG.loginforgotteninstructions}</a>
        <div class="remember">
          <label class="checkbox-inline rememberme"><input type="checkbox" name="rememberme" id="rememberme">{$LANG.loginrememberme}</label>        
        </div>
        <div class="row">
         <div class="col-md-12">
          <p><input type="submit" name="submit" class="btn btn-primary btn-lg btn-block" value="Login"></p>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
<div class="row">
  <div class="col-md-4 col-md-offset-4 no-account">
    <p>Don't have an account?</p>
    <a href="register.php">Sign up</a>
  </div>
</div>
<div class="content-sm">
  <div class="row">   
    <div class="col-md-4 col-md-offset-4">