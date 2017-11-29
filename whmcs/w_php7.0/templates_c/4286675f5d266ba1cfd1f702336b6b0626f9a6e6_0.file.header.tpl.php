<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:16
  from "/home/cloud.ddweb.com.cn/public_html/templates/Hostify/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbafc8afdd9_28125014',
  'file_dependency' => 
  array (
    '4286675f5d266ba1cfd1f702336b6b0626f9a6e6' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/Hostify/header.tpl',
      1 => 1511831716,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbafc8afdd9_28125014 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php echo $_smarty_tpl->tpl_vars['charset']->value;?>
" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php if ($_smarty_tpl->tpl_vars['kbarticle']->value['title']) {
echo $_smarty_tpl->tpl_vars['kbarticle']->value['title'];?>
 - <?php }
echo $_smarty_tpl->tpl_vars['pagetitle']->value;?>
 - <?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
</title>

    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


    <?php echo $_smarty_tpl->tpl_vars['headoutput']->value;?>


</head>
<body <?php if ($_smarty_tpl->tpl_vars['loginpage']->value == 1 || $_smarty_tpl->tpl_vars['templatefile']->value == "clientregister") {?>class="fullpage"<?php }?>>
<?php if ($_smarty_tpl->tpl_vars['loginpage']->value == 0 && $_smarty_tpl->tpl_vars['templatefile']->value != "clientregister") {?>

<div id="header-holder" class="<?php if ($_smarty_tpl->tpl_vars['templatefile']->value != 'homepage') {?>inner-header<?php }?>">
    <div class="bg-animation"></div>

    <?php echo $_smarty_tpl->tpl_vars['headeroutput']->value;?>


    <section id="header" class="container-fluid">
        <div class="container">
            <ul class="top-nav">
                <?php if ($_smarty_tpl->tpl_vars['languagechangeenabled']->value && count($_smarty_tpl->tpl_vars['locales']->value) > 1) {?>
                    <li>
                        <a href="#" class="choose-language" data-toggle="popover" id="languageChooser">
                            <?php echo $_smarty_tpl->tpl_vars['activeLocale']->value['localisedName'];?>

                            <b class="caret"></b>
                        </a>
                        <div id="languageChooserContent" class="hidden">
                            <ul>
                                <?php
$_from = $_smarty_tpl->tpl_vars['locales']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_locale_0_saved_item = isset($_smarty_tpl->tpl_vars['locale']) ? $_smarty_tpl->tpl_vars['locale'] : false;
$_smarty_tpl->tpl_vars['locale'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['locale']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['locale']->value) {
$_smarty_tpl->tpl_vars['locale']->_loop = true;
$__foreach_locale_0_saved_local_item = $_smarty_tpl->tpl_vars['locale'];
?>
                                    <li>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['currentpagelinkback']->value;?>
language=<?php echo $_smarty_tpl->tpl_vars['locale']->value['language'];?>
"><?php echo $_smarty_tpl->tpl_vars['locale']->value['localisedName'];?>
</a>
                                    </li>
                                <?php
$_smarty_tpl->tpl_vars['locale'] = $__foreach_locale_0_saved_local_item;
}
if ($__foreach_locale_0_saved_item) {
$_smarty_tpl->tpl_vars['locale'] = $__foreach_locale_0_saved_item;
}
?>
                            </ul>
                        </div>
                    </li>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?>
                    <li>
                        <a href="#" data-toggle="popover" id="accountNotifications" data-placement="bottom">
                            <?php echo $_smarty_tpl->tpl_vars['LANG']->value['notifications'];?>

                            <?php if (count($_smarty_tpl->tpl_vars['clientAlerts']->value) > 0) {?><span class="label label-info">NEW</span><?php }?>
                            <b class="caret"></b>
                        </a>
                        <div id="accountNotificationsContent" class="hidden">
                            <ul class="client-alerts">
                            <?php
$_from = $_smarty_tpl->tpl_vars['clientAlerts']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_alert_1_saved_item = isset($_smarty_tpl->tpl_vars['alert']) ? $_smarty_tpl->tpl_vars['alert'] : false;
$_smarty_tpl->tpl_vars['alert'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['alert']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['alert']->value) {
$_smarty_tpl->tpl_vars['alert']->_loop = true;
$__foreach_alert_1_saved_local_item = $_smarty_tpl->tpl_vars['alert'];
?>
                                <li>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['alert']->value->getLink();?>
">
                                        <i class="fa fa-fw fa-<?php if ($_smarty_tpl->tpl_vars['alert']->value->getSeverity() == 'danger') {?>exclamation-circle<?php } elseif ($_smarty_tpl->tpl_vars['alert']->value->getSeverity() == 'warning') {?>warning<?php } elseif ($_smarty_tpl->tpl_vars['alert']->value->getSeverity() == 'info') {?>info-circle<?php } else { ?>check-circle<?php }?>"></i>
                                        <div class="message"><?php echo $_smarty_tpl->tpl_vars['alert']->value->getMessage();?>
</div>
                                    </a>
                                </li>
                            <?php
$_smarty_tpl->tpl_vars['alert'] = $__foreach_alert_1_saved_local_item;
}
if (!$_smarty_tpl->tpl_vars['alert']->_loop) {
?>
                                <li class="none">
                                    <?php echo $_smarty_tpl->tpl_vars['LANG']->value['notificationsnone'];?>

                                </li>
                            <?php
}
if ($__foreach_alert_1_saved_item) {
$_smarty_tpl->tpl_vars['alert'] = $__foreach_alert_1_saved_item;
}
?>
                            </ul>
                        </div>
                    </li>
                    <li class="primary-action">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/logout.php" class="btn btn-action">
                            <?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientareanavlogout'];?>

                        </a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/clientarea.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['login'];?>
</a>
                    </li>
                    <?php if ($_smarty_tpl->tpl_vars['condlinks']->value['allowClientRegistration']) {?>
                        <li>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/register.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['register'];?>
</a>
                        </li>
                    <?php }?>
                    <li class="primary-action">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php?a=view" class="btn btn-action">
                            <?php echo $_smarty_tpl->tpl_vars['LANG']->value['viewcart'];?>

                        </a>
                    </li>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['adminMasqueradingAsClient']->value || $_smarty_tpl->tpl_vars['adminLoggedIn']->value) {?>
                    <li>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/logout.php?returntoadmin=1" class="btn btn-logged-in-admin" data-toggle="tooltip" data-placement="bottom" title="<?php if ($_smarty_tpl->tpl_vars['adminMasqueradingAsClient']->value) {
echo $_smarty_tpl->tpl_vars['LANG']->value['adminmasqueradingasclient'];?>
 <?php echo $_smarty_tpl->tpl_vars['LANG']->value['logoutandreturntoadminarea'];
} else {
echo $_smarty_tpl->tpl_vars['LANG']->value['adminloggedin'];?>
 <?php echo $_smarty_tpl->tpl_vars['LANG']->value['returntoadminarea'];
}?>">
                            <i class="fa fa-sign-out"></i>
                        </a>
                    </li>
                <?php }?>
                <li class="support-button-holder support-dropdown">
                    <a class="support-button" href="#">Support</a>
                    <ul class="dropdown-menu">
                      <li><a href="#"><i class="fa fa-phone"></i>Toll-Free  08-197-435-01</a></li>
                      <li><a href="#"><i class="fa fa-comments"></i>Start a Live Chat</a></li>
                      <li><a href="#"><i class="fa fa-ticket"></i>Open a ticket</a></li>
                      <li><a href="#"><i class="fa fa-book"></i>Knowledge base</a></li>
                    </ul>
                </li>
            </ul>

            <?php if ($_smarty_tpl->tpl_vars['assetLogoPath']->value) {?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/index.php" class="logo"><img src="<?php echo $_smarty_tpl->tpl_vars['assetLogoPath']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
"></a>
            <?php } else { ?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/index.php" class="logo logo-text logo-holder"><?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
</a>
            <?php }?>

        </div>
    </section>
    <section id="main-menu">
        <nav id="nav" class="container-fluid navbar navbar-default navbar-main" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary-nav">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="primary-nav">

                    <ul class="nav navbar-nav">

                        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/navbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('navbar'=>$_smarty_tpl->tpl_vars['primaryNavbar']->value), 0, true);
?>


                    </ul>

                    <ul class="nav navbar-nav navbar-right">

                        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/navbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('navbar'=>$_smarty_tpl->tpl_vars['secondaryNavbar']->value), 0, true);
?>


                    </ul>

                </div><!-- /.navbar-collapse -->
            </div>
        </nav>
    </section>
    <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
    <div id="top-content" class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                    <div class="big-title">Find a space for you.<br>Start today.</div>
                    <div class="domain-search-holder">
                        <form id="domain-search" method="post" action="domainchecker.php">
                            <input id="domain-text" type="text" name="domain" placeholder="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['exampledomain'];?>
" />
                            <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value) {?>
                            <span class="inline-button">
                                <input id="search-btn" type="submit" name="submit" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['search'];?>
" />
                            </span>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                            <span class="inline-button">
                                <input id="transfer-btn" type="submit" name="transfer" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['domainstransfer'];?>
" />
                            </span>
                            <?php }?>
                        </form>
                        <div class="captcha-holder"><?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/captcha.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
</div>
                    </div>
                    <?php } else { ?>
                        <div class="toparea-space"></div>
                    <?php }?>
                </div>
                <div class="col-md-12">
                    <div class="arrow-button-holder">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php?a=view">
                            <div class="button-text">Web Hosting Plans</div>
                            <div class="arrow-icon">
                                <i class="htfy htfy-arrow-down"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>
</div>
<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
<div id="info" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="info-text">adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure.</div>

                <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/register.php" class="ybtn ybtn-purple ybtn-shadow">Create Your Account</a>
            </div>
        </div>
    </div>
</div>
<div id="services" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">Our Services</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/service-icon1.png" alt="">
                    </div>
                    <div class="service-title"><a href="#">Web Hosting</a></div>
                    <div class="service-details">
                        <p>At vero eos et accusamus et iusto odio dignissimos
ducimus qui blanditiis praesentium voluptatum div
atque corrupti quos dolores et quas molestias.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/service-icon2.png" alt="">
                    </div>
                    <div class="service-title"><a href="#">Resellers</a></div>
                    <div class="service-details">
                        <p>At vero eos et accusamus et iusto odio dignissimos
ducimus qui blanditiis praesentium voluptatum div
atque corrupti quos dolores et quas molestias.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/service-icon3.png" alt="">
                    </div>
                    <div class="service-title"><a href="#">VPS Hosting</a></div>
                    <div class="service-details">
                        <p>At vero eos et accusamus et iusto odio dignissimos
ducimus qui blanditiis praesentium voluptatum div
atque corrupti quos dolores et quas molestias.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/service-icon4.png" alt="">
                    </div>
                    <div class="service-title"><a href="#">Cloud Hosting</a></div>
                    <div class="service-details">
                        <p>At vero eos et accusamus et iusto odio dignissimos
ducimus qui blanditiis praesentium voluptatum div
atque corrupti quos dolores et quas molestias.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="message1" class="container-fluid message-area">
    <div class="bg-color"></div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="text-purple-light">Are you ready?</div>
                <div class="text-purple-dark">create an account, or contact us.</div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="buttons-holder">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/register.php" class="ybtn ybtn-purple">Create Your Account</a><a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/contact.php" class="ybtn ybtn-white ybtn-shadow">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="custom-plan" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>Custom Hosting Plan</h4>
                <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum div atque corrupti quos dolores et quas molestias.</p>
            </div>
            <div class="col-md-12">
                <div class="custom-plan-box">
                    <input id="c-plan" type="text" data-slider-min="100" data-slider-max="10000" data-slider-step="100" data-slider-value="5000" data-currency="$" data-unit="GB">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="features" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="row-title">All Features</div>
            </div>
        </div>
        <div class="row rtl-cols">
            <div class="col-sm-12 col-md-6">
                <div id="features-links-holder">
                    <div class="icons-axis">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/features-icon.png" alt="">
                    </div>
                    <div class="feature-icon-holder feature-icon-holder1 opened" data-id="1">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-worldwide"></i></div>
                        <div class="feature-title">%99 Uptime</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder2" data-id="2">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-cogwheel"></i></div>
                        <div class="feature-title">Easy control panel</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder3" data-id="3">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-location"></i></div>
                        <div class="feature-title">Email Marketing</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder4" data-id="4">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-download"></i></div>
                        <div class="feature-title">1CLICK Script Installs</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder5" data-id="5">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-like"></i></div>
                        <div class="feature-title">7/24 Support</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div id="features-holder">
                    <div class="feature-box feature-d1 show-details">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-worldwide"></i></span>
                            <span class="feature-title">%99 Uptime</span>
                        </div>
                        <div class="feature-details">
                            <p>At vero eos et accusamus et iusto odio dignissimos
                                ducimus qui blanditiis praesentium voluptatum div
                                atque corrupti quos dolores et quas molestias.</p>

                            <p>dignissimos ducimus qui blanditiis praesentium
                                voluptatum div atque corrupti quos dolores et quas
                                unimo molestias.</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d2">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-cogwheel"></i></span>
                            <span class="feature-title">Easy control panel</span>
                        </div>
                        <div class="feature-details">
                            <p>At vero eos et accusamus et iusto odio dignissimos
                                ducimus qui blanditiis praesentium voluptatum div
                                atque corrupti quos dolores et quas molestias.</p>

                            <p>dignissimos ducimus qui blanditiis praesentium
                                voluptatum div atque corrupti quos dolores et quas
                                unimo molestias.</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d3">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-location"></i></span>
                            <span class="feature-title">Email Marketing</span>
                        </div>
                        <div class="feature-details">
                            <p>At vero eos et accusamus et iusto odio dignissimos
                                ducimus qui blanditiis praesentium voluptatum div
                                atque corrupti quos dolores et quas molestias.</p>

                            <p>dignissimos ducimus qui blanditiis praesentium
                                voluptatum div atque corrupti quos dolores et quas
                                unimo molestias.</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d4">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-download"></i></span>
                            <span class="feature-title">1CLICK Script Installs</span>
                        </div>
                        <div class="feature-details">
                            <p>At vero eos et accusamus et iusto odio dignissimos
                                ducimus qui blanditiis praesentium voluptatum div
                                atque corrupti quos dolores et quas molestias.</p>

                            <p>dignissimos ducimus qui blanditiis praesentium
                                voluptatum div atque corrupti quos dolores et quas
                                unimo molestias.</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d5">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-like"></i></span>
                            <span class="feature-title">7/24 Support</span>
                        </div>
                        <div class="feature-details">
                            <p>At vero eos et accusamus et iusto odio dignissimos
                                ducimus qui blanditiis praesentium voluptatum div
                                atque corrupti quos dolores et quas molestias.</p>

                            <p>dignissimos ducimus qui blanditiis praesentium
                                voluptatum div atque corrupti quos dolores et quas
                                unimo molestias.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="testimonials" class="container-fluid">
    <div class="bg-color"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="row-title">Testimonials</div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div id="testimonials-slider">
                    <div>
                        <div class="details-holder">
                            <img class="photo" src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/person1.jpg" alt="">
                            <h4>Chris Walker</h4>
                            <h5>CEO & CO-Founder @HelloBrandio</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris egestas non ante non consequat. Aenean accumsan eros vel elit tristique, non sodales nunc luctus. Etiam vitae odio eget orci finibus auctor ut eget magna.</p>
                        </div>
                    </div>
                    <div>
                        <div class="details-holder">
                            <img class="photo" src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/img/person1.jpg" alt="">
                            <h4>Chris Walker</h4>
                            <h5>CEO & CO-Founder @HelloBrandio</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris egestas non ante non consequat. Aenean accumsan eros vel elit tristique, non sodales nunc luctus. Etiam vitae odio eget orci finibus auctor ut eget magna.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="more-features" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">Our Promise</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-icon">
                        <i class="htfy htfy-tick"></i>
                    </div>
                    <div class="mfeature-title">%99.9 Uptime</div>
                    <div class="mfeature-details">Mauris at libero sed justo pretium maximus ac non ex. Donec sit amet ultrices dolo.</div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-icon">
                        <i class="htfy htfy-tick"></i>
                    </div>
                    <div class="mfeature-title">Money Back Guarantee</div>
                    <div class="mfeature-details">Mauris at libero sed justo pretium maximus ac non ex. Donec sit amet ultrices dolo.</div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-icon">
                        <i class="htfy htfy-tick"></i>
                    </div>
                    <div class="mfeature-title">Best Support</div>
                    <div class="mfeature-details">Mauris at libero sed justo pretium maximus ac non ex. Donec sit amet ultrices dolo.</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="message2" class="container-fluid message-area normal-bg">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="text-purple-light">Are you ready?</div>
                <div class="text-purple-dark">create an account, or contact us.</div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="buttons-holder">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/register.php" class="ybtn ybtn-purple">Create Your Account</a><a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/contact.php" class="ybtn ybtn-white ybtn-shadow">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/verifyemail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

	<?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value) {?>
	<div id="main-body-holder" class="container-fluid">
	<section id="main-body">
	    <div class="container<?php if ($_smarty_tpl->tpl_vars['skipMainBodyContainer']->value) {?>-fluid without-padding<?php }?>">
	        <div class="row">
	
	        <?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value && ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() || $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren())) {?>
	            <?php if ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() && !$_smarty_tpl->tpl_vars['skipMainBodyContainer']->value) {?>
	                <div class="col-md-9 pull-md-right">
	                    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/pageheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value,'showbreadcrumb'=>true), 0, true);
?>

	                </div>
	            <?php }?>
	            <div class="col-md-3 pull-md-left sidebar">
	                <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('sidebar'=>$_smarty_tpl->tpl_vars['primarySidebar']->value), 0, true);
?>

	            </div>
	        <?php }?>
	        <!-- Container for main page display content -->
	        <div class="<?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value && ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() || $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren())) {?>col-md-9 pull-md-right<?php } else { ?>col-xs-12<?php }?> main-content">
	            <?php if (!$_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() && !$_smarty_tpl->tpl_vars['showingLoginPage']->value && !$_smarty_tpl->tpl_vars['inShoppingCart']->value && $_smarty_tpl->tpl_vars['templatefile']->value != 'homepage' && !$_smarty_tpl->tpl_vars['skipMainBodyContainer']->value) {?>
	                <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/pageheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value,'showbreadcrumb'=>true), 0, true);
?>

	            <?php }?>
	<?php }
}
}
}
