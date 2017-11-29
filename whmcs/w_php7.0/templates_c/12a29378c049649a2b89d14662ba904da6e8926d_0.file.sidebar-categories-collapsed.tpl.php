<?php
/* Smarty version 3.1.29, created on 2017-11-28 03:50:38
  from "/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/sidebar-categories-collapsed.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cdd0e798085_37711208',
  'file_dependency' => 
  array (
    '12a29378c049649a2b89d14662ba904da6e8926d' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/orderforms/NeWorld/sidebar-categories-collapsed.tpl',
      1 => 1511832030,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cdd0e798085_37711208 ($_smarty_tpl) {
?>
<div class="categories-collapsed visible-xs visible-sm clearfix">

    <div class="pull-left form-inline">
        <form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>
">
            <select name="gid" onchange="submit()" class="form-control">
                <optgroup label="Product Categories">
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
                        <option value="<?php echo $_smarty_tpl->tpl_vars['productgroup']->value['gid'];?>
"<?php if ($_smarty_tpl->tpl_vars['gid']->value == $_smarty_tpl->tpl_vars['productgroup']->value['gid']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['productgroup']->value['name'];?>
</option>
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
                </optgroup>
                <optgroup label="Actions">
                    <?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?>
                        <option value="addons"<?php if ($_smarty_tpl->tpl_vars['gid']->value == "addons") {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['LANG']->value['cartproductaddons'];?>
</option>
                        <?php if ($_smarty_tpl->tpl_vars['renewalsenabled']->value) {?>
                            <option value="renewals"<?php if ($_smarty_tpl->tpl_vars['gid']->value == "renewals") {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['LANG']->value['domainrenewals'];?>
</option>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['registerdomainenabled']->value) {?>
                        <option value="registerdomain"<?php if ($_smarty_tpl->tpl_vars['domain']->value == "register") {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['LANG']->value['navregisterdomain'];?>
</option>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['transferdomainenabled']->value) {?>
                        <option value="transferdomain"<?php if ($_smarty_tpl->tpl_vars['domain']->value == "transfer") {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['LANG']->value['transferinadomain'];?>
</option>
                    <?php }?>
                    <option value="viewcart"<?php if ($_smarty_tpl->tpl_vars['action']->value == "view") {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['LANG']->value['viewcart'];?>
</option>
                </optgroup>
            </select>
        </form>
    </div>

    <?php if (!$_smarty_tpl->tpl_vars['loggedin']->value && $_smarty_tpl->tpl_vars['currencies']->value) {?>
        <div class="pull-right form-inline">
            <form method="post" action="cart.php<?php if ($_smarty_tpl->tpl_vars['action']->value) {?>?a=<?php echo $_smarty_tpl->tpl_vars['action']->value;
} elseif ($_smarty_tpl->tpl_vars['gid']->value) {?>?gid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;
}?>">
                <select name="currency" onchange="submit()" class="form-control">
                    <option value=""><?php echo $_smarty_tpl->tpl_vars['LANG']->value['choosecurrency'];?>
</option>
                    <?php
$_from = $_smarty_tpl->tpl_vars['currencies']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_curr_1_saved_item = isset($_smarty_tpl->tpl_vars['curr']) ? $_smarty_tpl->tpl_vars['curr'] : false;
$_smarty_tpl->tpl_vars['curr'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['curr']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['curr']->value) {
$_smarty_tpl->tpl_vars['curr']->_loop = true;
$__foreach_curr_1_saved_local_item = $_smarty_tpl->tpl_vars['curr'];
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['curr']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['curr']->value['code'];?>
</option>
                    <?php
$_smarty_tpl->tpl_vars['curr'] = $__foreach_curr_1_saved_local_item;
}
if ($__foreach_curr_1_saved_item) {
$_smarty_tpl->tpl_vars['curr'] = $__foreach_curr_1_saved_item;
}
?>
                </select>
            </form>
        </div>
    <?php }?>

</div>
<?php }
}
