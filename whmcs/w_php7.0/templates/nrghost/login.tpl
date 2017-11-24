<section>
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 margin-top margin-bottom">
                <div class="login-halfwidthcontaine halfwidthcontainer">

{if $incorrect}

<div class="alert alert-warning" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
       <p>{$LANG.loginincorrect}</p>
</div>

{/if}

<div class="form-block login-form-block ">
      <img class="img-circle form-icon" src="templates/{$template}/assets/nrghosts/img/icon-118.png" alt="">
      <div class="form-wrapper">
        <div class="block-header">
            <h2 class="title">Login Form</h2>
        </div>
        <form method="post" action="{$systemsslurl}dologin.php" class="form-stacked">
        <div class="logincontainer">
            <fieldset class="control-group">

                <div class="control-group field-entry">
                    <label class="control-label" for="username">{$LANG.loginemail}:</label>
                    <div class="controls">
                        <input class="input-xlarge" name="username" id="username" type="text" />
                    </div>
                </div>
                <div class="control-group field-entry">
                    <label class="control-label" for="password">{$LANG.loginpassword}:</label>
                    <div class="controls">
                        <input class="input-xlarge" name="password" id="password" type="password"/>
                    </div>
                </div>


                  <div class="rememberme checkbox-entry checkbox"><input type="checkbox" name="rememberme"{if $rememberme} checked="checked"{/if} /> <label>{$LANG.loginrememberme}</label></div>

                  <a class="simple-link" href="pwreset.php"><span class="glyphicon glyphicon-chevron-right"></span>{$LANG.loginforgotteninstructions}</a>

                <div class="button">
                    <div class="loginbtn">Login<input id="login" type="submit" class="" value="{$LANG.loginbutton}" /></div>
                </div>
            </fieldset>
        </div>
      </form>
      </div>
 </div>
<script type="text/javascript">
$("#username").focus();
</script>

</div>          </div>
        </div>
    </div>
</section>
