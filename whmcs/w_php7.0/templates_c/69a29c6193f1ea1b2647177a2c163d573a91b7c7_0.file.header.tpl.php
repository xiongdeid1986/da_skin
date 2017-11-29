<?php
/* Smarty version 3.1.29, created on 2017-11-28 00:56:18
  from "/home/cloud.ddweb.com.cn/public_html/templates/six/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cb432506ae0_81207439',
  'file_dependency' => 
  array (
    '69a29c6193f1ea1b2647177a2c163d573a91b7c7' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/six/header.tpl',
      1 => 1510648882,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cb432506ae0_81207439 ($_smarty_tpl) {
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
<body data-phone-cc-input="<?php echo $_smarty_tpl->tpl_vars['phoneNumberInputStyle']->value;?>
">

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
/logout.php" class="btn">
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
/cart.php?a=view" class="btn">
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
/index.php" class="logo logo-text"><?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
</a>
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


                </ul>

            </div><!-- /.navbar-collapse -->
        </div>
    </nav>

</section>

<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
    <section id="home-banner">
        <div class="container text-center">
            <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                <h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['homebegin'];?>
</h2>
                <form method="post" action="domainchecker.php">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="domain" placeholder="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['exampledomain'];?>
" autocapitalize="none" />
                                <span class="input-group-btn">
                                    <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value) {?>
                                        <input type="submit" class="btn search" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['search'];?>
" />
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                                        <input type="submit" name="transfer" class="btn transfer" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['domainstransfer'];?>
" />
                                    <?php }?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/captcha.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

                </form>
            <?php } else { ?>
                <h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['doToday'];?>
</h2>
            <?php }?>
        </div>
    </section>
    <div class="home-shortcuts">
        <div class="container">
            <div class="row">
                <div class="col-md-4 hidden-sm hidden-xs text-center">
                    <p class="lead">
                        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['howcanwehelp'];?>

                    </p>
                </div>
                <div class="col-sm-12 col-md-8">
                    <ul>
                        <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                            <li>
                                <a id="btnBuyADomain" href="domainchecker.php">
                                    <i class="fa fa-globe"></i>
                                    <p>
                                        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['buyadomain'];?>
 <span>&raquo;</span>
                                    </p>
                                </a>
                            </li>
                        <?php }?>
                        <li>
                            <a id="btnOrderHosting" href="cart.php">
                                <i class="fa fa-hdd-o"></i>
                                <p>
                                    <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderhosting'];?>
 <span>&raquo;</span>
                                </p>
                            </a>
                        </li>
                        <li>
                            <a id="btnMakePayment" href="clientarea.php">
                                <i class="fa fa-credit-card"></i>
                                <p>
                                    <?php echo $_smarty_tpl->tpl_vars['LANG']->value['makepayment'];?>
 <span>&raquo;</span>
                                </p>
                            </a>
                        </li>
                        <li>
                            <a id="btnGetSupport" href="submitticket.php">
                                <i class="fa fa-envelope-o"></i>
                                <p>
                                    <?php echo $_smarty_tpl->tpl_vars['LANG']->value['getsupport'];?>
 <span>&raquo;</span>
                                </p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/verifyemail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


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

            <?php }
}
}
