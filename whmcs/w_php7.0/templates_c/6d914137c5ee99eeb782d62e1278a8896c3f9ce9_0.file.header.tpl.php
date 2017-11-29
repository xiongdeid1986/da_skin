<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:21:23
  from "/home/cloud.ddweb.com.cn/public_html/templates/NeWorld/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cba1322a548_73497514',
  'file_dependency' => 
  array (
    '6d914137c5ee99eeb782d62e1278a8896c3f9ce9' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/NeWorld/header.tpl',
      1 => 1511832022,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cba1322a548_73497514 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<!--
_/_/_/_/_/  _/_/_/_/  _/      _/    _/_/_/  _/_/_/    _/_/    _/      _/
   _/      _/        _/_/    _/  _/          _/    _/    _/  _/_/    _/
  _/      _/_/_/    _/  _/  _/    _/_/      _/    _/    _/  _/  _/  _/
 _/      _/        _/    _/_/        _/    _/    _/    _/  _/    _/_/
_/      _/_/_/_/  _/      _/  _/_/_/    _/_/_/    _/_/    _/      _/

承接大型网站建设、设计、制作、规划工作！

TEL: 18910030001 MAIL:tension@me.com -->
<html lang="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['locale'];?>
">
<head>
    <meta charset="<?php echo $_smarty_tpl->tpl_vars['charset']->value;?>
" />
    <meta content="tension,tension@me.com" name="author" />
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

<body <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'login' || $_smarty_tpl->tpl_vars['filename']->value == 'logout' || $_smarty_tpl->tpl_vars['filename']->value == 'pwreset') {?>class="login"<?php } elseif ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage' || $_smarty_tpl->tpl_vars['templatefile']->value == 'vps' || $_smarty_tpl->tpl_vars['templatefile']->value == 'pricing' || $_smarty_tpl->tpl_vars['templatefile']->value == 'tos' || $_smarty_tpl->tpl_vars['templatefile']->value == 'features' || $_smarty_tpl->tpl_vars['filename']->value == "cart" || $_smarty_tpl->tpl_vars['filename']->value == 'contact' && !$_smarty_tpl->tpl_vars['loggedin']->value) {?> class="nowhmcs"<?php }?>>

<?php echo $_smarty_tpl->tpl_vars['headeroutput']->value;?>


<?php if ($_smarty_tpl->tpl_vars['adminMasqueradingAsClient']->value) {?>
    <!-- Return to admin link -->
    <div class="alert alert-danger admin-masquerade-notice">
        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['adminmasqueradingasclient'];?>

        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/logout.php?returntoadmin=1" class="alert-link"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['logoutandreturntoadminarea'];?>
</a>
    </div>
<?php } elseif ($_smarty_tpl->tpl_vars['adminLoggedIn']->value) {?>
    <!-- Return to admin link -->
    <div class="alert alert-danger admin-masquerade-notice">
        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['adminloggedin'];?>

        <a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/logout.php?returntoadmin=1" class="alert-link"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['returntoadminarea'];?>
</a>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'login' || $_smarty_tpl->tpl_vars['filename']->value == 'logout' || $_smarty_tpl->tpl_vars['filename']->value == 'pwreset') {?>

<?php } elseif ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage' || $_smarty_tpl->tpl_vars['templatefile']->value == 'vps' || $_smarty_tpl->tpl_vars['templatefile']->value == 'pricing' || $_smarty_tpl->tpl_vars['templatefile']->value == 'tos' || $_smarty_tpl->tpl_vars['templatefile']->value == 'features' || $_smarty_tpl->tpl_vars['filename']->value == "cart" || $_smarty_tpl->tpl_vars['filename']->value == 'contact' && !$_smarty_tpl->tpl_vars['loggedin']->value) {?>

	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/NeWorld/NeWorld-header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value), 0, true);
?>


	<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/NeWorld/index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value), 0, true);
?>

	<?php }?>

<?php } else { ?>

	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/NeWorld/whmcs-header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value), 0, true);
?>


<?php }?>

<?php if ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() || $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren()) {?>
    <?php if ($_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren()) {?>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/pageheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value,'showbreadcrumb'=>true), 0, true);
?>

    <?php }
}?>

<?php if (!$_smarty_tpl->tpl_vars['primarySidebar']->value->hasChildren() && !$_smarty_tpl->tpl_vars['showingLoginPage']->value && $_smarty_tpl->tpl_vars['templatefile']->value != 'homepage' && $_smarty_tpl->tpl_vars['templatefile']->value != 'vps' && $_smarty_tpl->tpl_vars['templatefile']->value != 'pricing' && $_smarty_tpl->tpl_vars['templatefile']->value != 'features' && $_smarty_tpl->tpl_vars['filename']->value != "cart") {?>
    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/pageheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->tpl_vars['displayTitle']->value,'desc'=>$_smarty_tpl->tpl_vars['tagline']->value,'showbreadcrumb'=>true), 0, true);
?>

<?php }?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/verifyemail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


<?php if ($_smarty_tpl->tpl_vars['templatefile']->value != 'login' && $_smarty_tpl->tpl_vars['filename']->value != 'logout' && $_smarty_tpl->tpl_vars['filename']->value != 'pwreset' && $_smarty_tpl->tpl_vars['templatefile']->value != 'homepage' && $_smarty_tpl->tpl_vars['templatefile']->value != 'vps' && $_smarty_tpl->tpl_vars['templatefile']->value != 'features' && $_smarty_tpl->tpl_vars['templatefile']->value != 'pricing' && $_smarty_tpl->tpl_vars['filename']->value != "cart") {?>
	<?php if ($_smarty_tpl->tpl_vars['templatefile']->value != 'clientareahome' || $_smarty_tpl->tpl_vars['templatefile']->value != 'downloads' || $_smarty_tpl->tpl_vars['templatefile']->value != 'affiliates' || $_smarty_tpl->tpl_vars['templatefile']->value != 'supportticketsubmit-stepone') {?>
		<div class="navbar-collapse-inner">
			<div class="navbar-collapse-bg"></div>
			<div class="navbar-collapse-icon">
				<i class="alico icon-list-open"></i>
				<i class="alico icon-list-close"></i>
			</div>
		</div>
	    <div class="sub-menu">
	        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('sidebar'=>$_smarty_tpl->tpl_vars['primarySidebar']->value), 0, true);
?>

	    </div>
	<?php }?>
	    <div class="main-content">
		<section id="main-body" class="content content--border">
<?php }
}
}
