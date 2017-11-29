<?php
/* Smarty version 3.1.29, created on 2017-11-28 03:50:38
  from "/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/order-top.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cdd0e76d3d2_44440207',
  'file_dependency' => 
  array (
    '6ad050d8b56a5a0b134a73e8b7ea564af9c246cd' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/order-top.tpl',
      1 => 1511832030,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cdd0e76d3d2_44440207 ($_smarty_tpl) {
?>

	<section class="order-top">
		<div class="container">
			<ul class="row">
				<li class="col-xs-6 col-sm-3 <?php if ($_smarty_tpl->tpl_vars['filename']->value == "cart" && $_GET['a'] == '') {?>active<?php }?>">
					<a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php">
						<img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/orderforms/<?php echo $_smarty_tpl->tpl_vars['carttpl']->value;?>
/img/review.svg" class="theme-gray size-md">
						<?php echo $_smarty_tpl->tpl_vars['LANG']->value['chooseproduct'];?>

			        </a>
				</li>
				<li class="col-xs-6 col-sm-3 <?php if ($_smarty_tpl->tpl_vars['filename']->value == "cart" && $_GET['a'] == "view") {?>active<?php }?>">
					<a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php?a=view">
						<img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/orderforms/<?php echo $_smarty_tpl->tpl_vars['carttpl']->value;?>
/img/choose.svg" class="theme-gray size-md">
				        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['cartreviewcheckout'];?>

				    </a>
				</li>
				<li class="col-xs-6 col-sm-3 <?php if ($_smarty_tpl->tpl_vars['filename']->value == "cart" && $_GET['a'] == "checkout") {?>active<?php }?>">
					<a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php?a=checkout">
						<img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/orderforms/<?php echo $_smarty_tpl->tpl_vars['carttpl']->value;?>
/img/checkout.svg" class="theme-gray size-md">
						<?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderForm']['checkout'];?>

					</a>
				</li>
				<li class="col-xs-6 col-sm-3 <?php if ($_smarty_tpl->tpl_vars['filename']->value == "cart" && $_GET['a'] == "complete") {?>active<?php }?>">
					<a href="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/cart.php?a=checkout">
						<img src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/orderforms/<?php echo $_smarty_tpl->tpl_vars['carttpl']->value;?>
/img/confirm.svg" class="theme-gray size-md">
						<?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderconfirmation'];?>

					</a>
				</li>
			</ul>
		</div>
	</section><?php }
}
