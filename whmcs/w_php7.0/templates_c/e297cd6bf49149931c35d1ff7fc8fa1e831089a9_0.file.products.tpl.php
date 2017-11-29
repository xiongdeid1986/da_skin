<?php
/* Smarty version 3.1.29, created on 2017-11-28 03:50:38
  from "/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/products.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cdd0e74e667_85881030',
  'file_dependency' => 
  array (
    'e297cd6bf49149931c35d1ff7fc8fa1e831089a9' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/products.tpl',
      1 => 1511832030,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:orderforms/".((string)$_smarty_tpl->tpl_vars[\'carttpl\']->value)."/order-top.tpl' => 1,
    'file:orderforms/".((string)$_smarty_tpl->tpl_vars[\'carttpl\']->value)."/common.tpl' => 1,
    'file:orderforms/".((string)$_smarty_tpl->tpl_vars[\'carttpl\']->value)."/sidebar-categories-collapsed.tpl' => 1,
  ),
),false)) {
function content_5a1cdd0e74e667_85881030 ($_smarty_tpl) {
?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:orderforms/".((string)$_smarty_tpl->tpl_vars['carttpl']->value)."/order-top.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

	
<section class="order-home space2x">
	<div class="container">
		
	<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:orderforms/".((string)$_smarty_tpl->tpl_vars['carttpl']->value)."/common.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


		<div id="order-standard_cart">
			
			<div class="choosecat">
			    <ul class="row list-unstyled">
			        <?php
$_from = $_smarty_tpl->tpl_vars['productgroups']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_productgroup_0_saved_item = isset($_smarty_tpl->tpl_vars['productgroup']) ? $_smarty_tpl->tpl_vars['productgroup'] : false;
$__foreach_productgroup_0_saved_key = isset($_smarty_tpl->tpl_vars['num']) ? $_smarty_tpl->tpl_vars['num'] : false;
$_smarty_tpl->tpl_vars['productgroup'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['num'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['productgroup']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['num']->value => $_smarty_tpl->tpl_vars['productgroup']->value) {
$_smarty_tpl->tpl_vars['productgroup']->_loop = true;
$__foreach_productgroup_0_saved_local_item = $_smarty_tpl->tpl_vars['productgroup'];
?>
			            <li class="col-xs-6 col-sm-3 <?php if ($_GET['gid'] == ((string)$_smarty_tpl->tpl_vars['productgroup']->value['gid'])) {?>active<?php }?>">
			            	<a href="cart.php?gid=<?php echo $_smarty_tpl->tpl_vars['productgroup']->value['gid'];?>
"><?php echo $_smarty_tpl->tpl_vars['productgroup']->value['name'];?>
</a>
			            </li>
			        <?php
$_smarty_tpl->tpl_vars['productgroup'] = $__foreach_productgroup_0_saved_local_item;
}
if ($__foreach_productgroup_0_saved_item) {
$_smarty_tpl->tpl_vars['productgroup'] = $__foreach_productgroup_0_saved_item;
}
if ($__foreach_productgroup_0_saved_key) {
$_smarty_tpl->tpl_vars['num'] = $__foreach_productgroup_0_saved_key;
}
?>
			        <?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?>
			            <li class="col-xs-6 col-sm-3"><a href="cart.php?a=addons"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['cartproductaddons'];?>
</a></li>
				        <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
				            <?php if ($_smarty_tpl->tpl_vars['renewalsenabled']->value) {?>
				                <li class="col-xs-3"><a href="cart.php?a=renewals"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['domainrenewals'];?>
</a></li>
				            <?php }?>
				        <?php }?>
			        <?php }?>
			        <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value) {?>
			            <li class="col-xs-6 col-sm-3">
			            	<a href="cart.php?a=add&domain=register"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['registerdomain'];?>
</a>
			            </li>
			        <?php }?>
			        <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value || $_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
			            <li class="col-xs-6 col-sm-3">
			            	<a href="cart.php?a=add&domain=transfer"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['transferdomain'];?>
</a>
			            </li>
			        <?php }?>
			    </ul>
			</div>
			
            <div class="header-lined text-center">
                <h2 class="title-head">
                    <?php if ($_smarty_tpl->tpl_vars['productGroup']->value['headline']) {?>
                        <?php echo $_smarty_tpl->tpl_vars['productGroup']->value['headline'];?>

                    <?php } else { ?>
                        <?php echo $_smarty_tpl->tpl_vars['productGroup']->value['name'];?>

                    <?php }?>
                </h2>
                <?php if ($_smarty_tpl->tpl_vars['productGroup']->value['tagline']) {?>
                    <p><?php echo $_smarty_tpl->tpl_vars['productGroup']->value['tagline'];?>
</p>
                <?php }?>
            </div>
            
            <?php if ($_smarty_tpl->tpl_vars['errormessage']->value) {?>
                <div class="alert alert-danger">
                    <?php echo $_smarty_tpl->tpl_vars['errormessage']->value;?>

                </div>
            <?php }?>
		
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:orderforms/".((string)$_smarty_tpl->tpl_vars['carttpl']->value)."/sidebar-categories-collapsed.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


            <div class="products" id="products">
                <div class="row row-eq-height">
                    <?php
$_from = $_smarty_tpl->tpl_vars['products']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_product_1_saved_item = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product'] : false;
$_smarty_tpl->tpl_vars['product'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['product']->iteration=0;
$_smarty_tpl->tpl_vars['product']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
$_smarty_tpl->tpl_vars['product']->iteration++;
$__foreach_product_1_saved_local_item = $_smarty_tpl->tpl_vars['product'];
?>
                        <div class="col-md-6">
                            <div class="product clearfix" id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
">
                                <header>
                                    <span id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
-name"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</span>
                                    <?php if ($_smarty_tpl->tpl_vars['product']->value['qty']) {?>
                                        <span class="qty">
                                            <?php echo $_smarty_tpl->tpl_vars['product']->value['qty'];?>
 <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderavailable'];?>

                                        </span>
                                    <?php }?>
                                </header>
                                <div class="product-desc">
                                    <?php if ($_smarty_tpl->tpl_vars['product']->value['featuresdesc']) {?>
                                        <p id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
-description">
                                            <?php echo $_smarty_tpl->tpl_vars['product']->value['featuresdesc'];?>

                                        </p>
                                    <?php }?>
                                    <ul>
                                        <?php
$_from = $_smarty_tpl->tpl_vars['product']->value['features'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_2_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$__foreach_value_2_saved_key = isset($_smarty_tpl->tpl_vars['feature']) ? $_smarty_tpl->tpl_vars['feature'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['feature'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['value']->iteration=0;
$_smarty_tpl->tpl_vars['value']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['feature']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$_smarty_tpl->tpl_vars['value']->iteration++;
$__foreach_value_2_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                                            <li id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
-feature<?php echo $_smarty_tpl->tpl_vars['value']->iteration;?>
">
                                                <span class="feature-value"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</span>
                                                <?php echo $_smarty_tpl->tpl_vars['feature']->value;?>

                                            </li>
                                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_local_item;
}
if ($__foreach_value_2_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved_item;
}
if ($__foreach_value_2_saved_key) {
$_smarty_tpl->tpl_vars['feature'] = $__foreach_value_2_saved_key;
}
?>
                                    </ul>
                                </div>
                                <footer>
                                    <div class="product-pricing" id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
-price">
                                        <?php if ($_smarty_tpl->tpl_vars['product']->value['bid']) {?>
                                            <?php echo $_smarty_tpl->tpl_vars['LANG']->value['bundledeal'];?>
<br />
                                            <?php if ($_smarty_tpl->tpl_vars['product']->value['displayprice']) {?>
                                                <span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['displayprice'];?>
</span>
                                            <?php }?>
                                        <?php } else { ?>
                                            <?php if ($_smarty_tpl->tpl_vars['product']->value['pricing']['hasconfigoptions']) {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['startingfrom'];?>

                                                <br />
                                            <?php }?>
                                            <span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['price'];?>
</span>
                                            <br />
                                            <?php if ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "monthly") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermmonthly'];?>

                                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "quarterly") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermquarterly'];?>

                                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "semiannually") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermsemiannually'];?>

                                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "annually") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermannually'];?>

                                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "biennially") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermbiennially'];?>

                                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['pricing']['minprice']['cycle'] == "triennially") {?>
                                                <?php echo $_smarty_tpl->tpl_vars['LANG']->value['orderpaymenttermtriennially'];?>

                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <a href="cart.php?a=add&<?php if ($_smarty_tpl->tpl_vars['product']->value['bid']) {?>bid=<?php echo $_smarty_tpl->tpl_vars['product']->value['bid'];
} else { ?>pid=<?php echo $_smarty_tpl->tpl_vars['product']->value['pid'];
}?>" class="btn btn-success btn-sm" id="product<?php echo $_smarty_tpl->tpl_vars['product']->iteration;?>
-order-button">
                                        <i class="fa fa-shopping-cart"></i>
                                        <?php echo $_smarty_tpl->tpl_vars['LANG']->value['ordernowbutton'];?>

                                    </a>
                                </footer>
                            </div>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['product']->iteration%2 == 0) {?>
                            </div>
                            <div class="row row-eq-height">
                        <?php }?>
                    <?php
$_smarty_tpl->tpl_vars['product'] = $__foreach_product_1_saved_local_item;
}
if ($__foreach_product_1_saved_item) {
$_smarty_tpl->tpl_vars['product'] = $__foreach_product_1_saved_item;
}
?>
                </div>
            </div>
		</div>
	</div>
</section><?php }
}
