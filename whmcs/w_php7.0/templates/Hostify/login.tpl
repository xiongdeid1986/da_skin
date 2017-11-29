<div id="form-section" class="container-fluid signin">
    <div class="website-logo">
        {if $assetLogoPath}
            <a href="{$WEB_ROOT}/index.php" class="logo"><img src="{$assetLogoPath}" alt="{$companyname}"></a>
        {else}
            <a href="{$WEB_ROOT}/index.php" class="logo logo-text logo-holder">{$companyname}</a>
        {/if}
    </div>
    <div class="row">
        <div class="info-slider-holder">
            <div class="bg-animation"></div>
            <div class="info-holder">
                <h6>A Service you can anytime modify.</h6>
                <div class="bold-title">it’s not that hard to get<br>
    a website <span>anymore.</span></div>
                <div class="mini-testimonials-slider">
                    <div>
                        <div class="details-holder">
                            <img class="photo" src="{$WEB_ROOT}/templates/{$template}/assets/img/person1.jpg" alt="">
                            <h4>Chris Walker</h4>
                            <h5>CEO & CO-Founder @HelloBrandio</h5>
                            <p>“In hostify we trust. I am with them for over
    7 years now. It always felt like home!
    Loved their customer support”</p>
                        </div>
                    </div>
                    <div>
                        <div class="details-holder">
                            <img class="photo" src="{$WEB_ROOT}/templates/{$template}/assets/img/person1.jpg" alt="">
                            <h4>Chris Walker</h4>
                            <h5>CEO & CO-Founder @HelloBrandio</h5>
                            <p>“In hostify we trust. I am with them for over
    7 years now. It always felt like home!
    Loved their customer support”</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-holder">
            <div class="menu-holder">
                <ul class="main-links">
                    <li><a class="normal-link" href="{$WEB_ROOT}/register.php">Don’t have an account?</a></li>
                    <li><a class="sign-button" href="{$WEB_ROOT}/register.php">Sign up</a></li>
                </ul>
            </div>
            <div class="signin-signup-form">
                <div class="form-items">
                    <div class="form-title">{include file="$template/includes/pageheader.tpl" title=$LANG.login desc="{$LANG.restrictedpage}"}</div>
                    {if $incorrect}
                        {include file="$template/includes/alert.tpl" type="error" msg=$LANG.loginincorrect textcenter=true}
                    {elseif $verificationId && empty($transientDataName)}
                        {include file="$template/includes/alert.tpl" type="error" msg=$LANG.verificationKeyExpired textcenter=true}
                    {elseif $ssoredirect}
                        {include file="$template/includes/alert.tpl" type="info" msg=$LANG.sso.redirectafterlogin textcenter=true}
                    {/if}
                    <form id="signinform" method="post" action="{$systemurl}dologin.php" role="form">
                        <div class="form-text">
                            <input id="inputEmail" type="email" name="username" name="username" placeholder="{$LANG.enteremail}">
                        </div>
                        <div class="form-text">
                            <input id="inputPassword" type="password" name="password" placeholder="{$LANG.clientareapassword}" autocomplete="off">
                        </div>
                        <div class="form-text text-holder">
                            <input id="chkbox" type="checkbox" class="hno-checkbox" name="rememberme" /> <label for="chkbox">{$LANG.loginrememberme}</label>
                        </div>
                        <div class="form-button">
                            <button id="login" type="submit" class="ybtn ybtn-purple">{$LANG.loginbutton}</button>
                            <a href="pwreset.php" class="btn btn-link">{$LANG.forgotpw}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
