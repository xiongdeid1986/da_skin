<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:48
  from "/home/cloud.ddweb.com.cn/public_html/templates/spacehost/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbb1cbc7a68_78073271',
  'file_dependency' => 
  array (
    'd24e48300ec07f15de0379a1b361baef4821b606' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/spacehost/header.tpl',
      1 => 1511831796,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbb1cbc7a68_78073271 ($_smarty_tpl) {
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
<body>
    
<div id="header-holder">
    <div class="bottom-gradiant"></div>
    
    <?php echo $_smarty_tpl->tpl_vars['headeroutput']->value;?>

    
    <section id="header">
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
            </ul>

            <?php if ($_smarty_tpl->tpl_vars['assetLogoPath']->value) {?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/index.php" class="logo"><img src="<?php echo $_smarty_tpl->tpl_vars['assetLogoPath']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
"></a>
            <?php } else { ?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/index.php" class="logo logo-text"><img class="logo" src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/logo.svg" alt="<?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
"></a>
            <?php }?>

        </div>
    </section>

    <section id="main-menu">

        <nav id="nav" class="navbar navbar-default navbar-main" role="navigation">
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

                        <li><a class="chat-button" href="#">Chat now</a></li>
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
                            <div class="arrow-icon">
                                <i class="sphst sphst-arrow-down"></i>
                            </div>
                            <div class="button-text">Go to plans</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>
</div>

<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
<div class="row-title-only container-fluid more-padding">
    <div class="container">
        <div class="row">
            <div class="row-title">Why you’ll be happy with Space Host?</div>
        </div>
    </div>
</div>
<div id="features" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="feature-box">
                    <div class="feature-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/feature1.svg" alt="">
                    </div>
                    <div class="feature-title">Site Bulilder</div>
                    <div class="feature-details">
                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
                        <h4>Site Bulilder</h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="feature-box">
                    <div class="feature-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/feature2.svg" alt="">
                    </div>
                    <div class="feature-title">100% Uptime</div>
                    <div class="feature-details">
                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
                        <h4>100% Uptime</h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="feature-box">
                    <div class="feature-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/feature3.svg" alt="">
                    </div>
                    <div class="feature-title">Fast Loaded</div>
                    <div class="feature-details">
                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
                        <h4>Fast Loaded</h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="feature-box">
                    <div class="feature-icon">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/feature4.svg" alt="">
                    </div>
                    <div class="feature-title">Upload files</div>
                    <div class="feature-details">
                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
                        <h4>Upload files</h4>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<div id="partners" class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row-title">Trusted by the best</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem<br> accusantium</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="partners-slider">
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner1.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner2.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner3.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner4.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner5.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner6.png" alt=""></div>
                <div><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/partner7.png" alt=""></div>
            </div>
        </div>
    </div>
</div>
<div id="more-features" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">What we offer?</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-title">Web Hosting</div>
                    <div class="mfeature-text bg-color1">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-title">Web Design</div>
                    <div class="mfeature-text bg-color2">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mfeature-box">
                    <div class="mfeature-title">Support</div>
                    <div class="mfeature-text bg-color3">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
<div id="search-box" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Ready?<br>
Let’s get strated.</p>
                <div class="domain-search-holder">
                    <form id="domain-search2" method="post" action="domainchecker.php">
                        <input id="domain-text2" type="text" name="domain" placeholder="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['exampledomain'];?>
" />
                        <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value) {?>
                        <span class="inline-button">
                            <input id="search-btn2" type="submit" name="submit" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['search'];?>
" />
                        </span>
                        <?php }?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>
<div id="domain-pricing" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">What you will get with domain names?</div>
            </div>
        </div>
        <div class="row domain-lists-holder">
            <div class="col-sm-12">
                <div class="domain-pricing-holder">
                    <?php echo '<script'; ?>
 language="javascript" src="feeds/domainpricing.php"><?php echo '</script'; ?>
>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row-title-only container-fluid">
    <div class="container">
        <div class="row">
            <div class="row-title">What people say about Space Host?</div>
        </div>
    </div>
</div>
<div id="testimonials" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial-box right-img">
                    <div class="row">
                        <div class="col-xs-3 img-holder dot-color1"><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/person1.jpg" alt=""></div>
                        <div class="col-xs-9">
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-box right-img">
                    <div class="row">
                        <div class="col-xs-3 img-holder dot-color2"><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/person2.jpg" alt=""></div>
                        <div class="col-xs-9">
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonials-title">Grow with us<br>
See results.</div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-box left-img">
                    <div class="row">
                        <div class="col-xs-3 img-holder dot-color3"><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/person3.jpg" alt=""></div>
                        <div class="col-xs-9">
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-box left-img">
                    <div class="row">
                        <div class="col-xs-3 img-holder dot-color4"><img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/images/person4.jpg" alt=""></div>
                        <div class="col-xs-9">
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="message-with-link" class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <span class="text">Lorem ipsum dolor sit amet, consectetur adipiscing.</span> <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/contact.php" class="button-bluegrey">Request a Demo</a><a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/register.php" class="button-purple">Create free account</a>
        </div>
    </div>
</div>
<?php }?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/verifyemail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

<div id="main-body-holder" class="container-fluid <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'products') {?>pricing<?php }?>">
    <section id="main-body" class="container">

        <div class="row">
            <?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value && ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() || $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren())) {?>
                <?php if ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren()) {?>
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
                <?php if (!$_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() && !$_smarty_tpl->tpl_vars['showingLoginPage']->value && !$_smarty_tpl->tpl_vars['inShoppingCart']->value && $_smarty_tpl->tpl_vars['templatefile']->value != 'homepage') {?>
                    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/pageheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value,'showbreadcrumb'=>true), 0, true);
?>

                <?php }
}
}
