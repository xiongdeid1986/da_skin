<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:32
  from "/home/cloud.ddweb.com.cn/public_html/templates/Hostify/includes/sidebar.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbb0c3c5a74_09601429',
  'file_dependency' => 
  array (
    'c832bf4f201b8f070c6f3ca041d6ce718bc84607' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/Hostify/includes/sidebar.tpl',
      1 => 1511831719,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbb0c3c5a74_09601429 ($_smarty_tpl) {
$_from = $_smarty_tpl->tpl_vars['sidebar']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_0_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_0_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
    <div menuItemName="<?php echo $_smarty_tpl->tpl_vars['item']->value->getName();?>
" class="panel panel-sidebar <?php if ($_smarty_tpl->tpl_vars['item']->value->getClass()) {
echo $_smarty_tpl->tpl_vars['item']->value->getClass();
} else { ?>panel-sidebar<?php }
if ($_smarty_tpl->tpl_vars['item']->value->getExtra('mobileSelect') && $_smarty_tpl->tpl_vars['item']->value->hasChildren()) {?> hidden-sm hidden-xs<?php }?>"<?php if ($_smarty_tpl->tpl_vars['item']->value->getAttribute('id')) {?> id="<?php echo $_smarty_tpl->tpl_vars['item']->value->getAttribute('id');?>
"<?php }?>>
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php if ($_smarty_tpl->tpl_vars['item']->value->hasIcon()) {?><i class="<?php echo $_smarty_tpl->tpl_vars['item']->value->getIcon();?>
"></i>&nbsp;<?php }?>
                <?php echo $_smarty_tpl->tpl_vars['item']->value->getLabel();?>

                <?php if ($_smarty_tpl->tpl_vars['item']->value->hasBadge()) {?>&nbsp;<span class="badge"><?php echo $_smarty_tpl->tpl_vars['item']->value->getBadge();?>
</span><?php }?>
                <i class="fa fa-chevron-up panel-minimise pull-right"></i>
            </h3>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->hasBodyHtml()) {?>
            <div class="panel-body">
                <?php echo $_smarty_tpl->tpl_vars['item']->value->getBodyHtml();?>

            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->hasChildren()) {?>
            <div class="list-group<?php if ($_smarty_tpl->tpl_vars['item']->value->getChildrenAttribute('class')) {?> <?php echo $_smarty_tpl->tpl_vars['item']->value->getChildrenAttribute('class');
}?>">
                <?php
$_from = $_smarty_tpl->tpl_vars['item']->value->getChildren();
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_childItem_1_saved_item = isset($_smarty_tpl->tpl_vars['childItem']) ? $_smarty_tpl->tpl_vars['childItem'] : false;
$_smarty_tpl->tpl_vars['childItem'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['childItem']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['childItem']->value) {
$_smarty_tpl->tpl_vars['childItem']->_loop = true;
$__foreach_childItem_1_saved_local_item = $_smarty_tpl->tpl_vars['childItem'];
?>
                    <?php if ($_smarty_tpl->tpl_vars['childItem']->value->getUri()) {?>
                        <a menuItemName="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getName();?>
" href="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getUri();?>
" class="list-group-item<?php if ($_smarty_tpl->tpl_vars['childItem']->value->isDisabled()) {?> disabled<?php }
if ($_smarty_tpl->tpl_vars['childItem']->value->getClass()) {?> <?php echo $_smarty_tpl->tpl_vars['childItem']->value->getClass();
}
if ($_smarty_tpl->tpl_vars['childItem']->value->isCurrent()) {?> active<?php }?>"<?php if ($_smarty_tpl->tpl_vars['childItem']->value->getAttribute('dataToggleTab')) {?> data-toggle="tab"<?php }
if ($_smarty_tpl->tpl_vars['childItem']->value->getAttribute('target')) {?> target="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getAttribute('target');?>
"<?php }?> id="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getId();?>
">
                            <?php if ($_smarty_tpl->tpl_vars['childItem']->value->hasBadge()) {?><span class="badge"><?php echo $_smarty_tpl->tpl_vars['childItem']->value->getBadge();?>
</span><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['childItem']->value->hasIcon()) {?><i class="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getIcon();?>
"></i>&nbsp;<?php }?>
                            <?php echo $_smarty_tpl->tpl_vars['childItem']->value->getLabel();?>

                        </a>
                    <?php } else { ?>
                        <div menuItemName="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getName();?>
" class="list-group-item<?php if ($_smarty_tpl->tpl_vars['childItem']->value->getClass()) {?> <?php echo $_smarty_tpl->tpl_vars['childItem']->value->getClass();
}?>" id="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getId();?>
">
                            <?php if ($_smarty_tpl->tpl_vars['childItem']->value->hasBadge()) {?><span class="badge"><?php echo $_smarty_tpl->tpl_vars['childItem']->value->getBadge();?>
</span><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['childItem']->value->hasIcon()) {?><i class="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getIcon();?>
"></i>&nbsp;<?php }?>
                            <?php echo $_smarty_tpl->tpl_vars['childItem']->value->getLabel();?>

                        </div>
                    <?php }?>
                <?php
$_smarty_tpl->tpl_vars['childItem'] = $__foreach_childItem_1_saved_local_item;
}
if ($__foreach_childItem_1_saved_item) {
$_smarty_tpl->tpl_vars['childItem'] = $__foreach_childItem_1_saved_item;
}
?>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->hasFooterHtml()) {?>
            <div class="panel-footer clearfix">
                <?php echo $_smarty_tpl->tpl_vars['item']->value->getFooterHtml();?>

            </div>
        <?php }?>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['item']->value->getExtra('mobileSelect') && $_smarty_tpl->tpl_vars['item']->value->hasChildren()) {?>
        
        <div class="panel hidden-lg hidden-md <?php if ($_smarty_tpl->tpl_vars['item']->value->getClass()) {
echo $_smarty_tpl->tpl_vars['item']->value->getClass();
} else { ?>panel-default<?php }?>"<?php if ($_smarty_tpl->tpl_vars['item']->value->getAttribute('id')) {?> id="<?php echo $_smarty_tpl->tpl_vars['item']->value->getAttribute('id');?>
"<?php }?>>
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php if ($_smarty_tpl->tpl_vars['item']->value->hasIcon()) {?><i class="<?php echo $_smarty_tpl->tpl_vars['item']->value->getIcon();?>
"></i>&nbsp;<?php }?>
                    <?php echo $_smarty_tpl->tpl_vars['item']->value->getLabel();?>

                    <?php if ($_smarty_tpl->tpl_vars['item']->value->hasBadge()) {?>&nbsp;<span class="badge"><?php echo $_smarty_tpl->tpl_vars['item']->value->getBadge();?>
</span><?php }?>
                </h3>
            </div>
            <div class="panel-body">
                <form role="form">
                    <select class="form-control" onchange="selectChangeNavigate(this)">
                        <?php
$_from = $_smarty_tpl->tpl_vars['item']->value->getChildren();
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_childItem_2_saved_item = isset($_smarty_tpl->tpl_vars['childItem']) ? $_smarty_tpl->tpl_vars['childItem'] : false;
$_smarty_tpl->tpl_vars['childItem'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['childItem']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['childItem']->value) {
$_smarty_tpl->tpl_vars['childItem']->_loop = true;
$__foreach_childItem_2_saved_local_item = $_smarty_tpl->tpl_vars['childItem'];
?>
                            <option menuItemName="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getUri();?>
" class="list-group-item" <?php if ($_smarty_tpl->tpl_vars['childItem']->value->isCurrent()) {?>selected="selected"<?php }?>>
                                <?php echo $_smarty_tpl->tpl_vars['childItem']->value->getLabel();?>

                                <?php if ($_smarty_tpl->tpl_vars['childItem']->value->hasBadge()) {?>(<?php echo $_smarty_tpl->tpl_vars['childItem']->value->getBadge();?>
)<?php }?>
                            </option>
                        <?php
$_smarty_tpl->tpl_vars['childItem'] = $__foreach_childItem_2_saved_local_item;
}
if ($__foreach_childItem_2_saved_item) {
$_smarty_tpl->tpl_vars['childItem'] = $__foreach_childItem_2_saved_item;
}
?>
                    </select>
                </form>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['item']->value->hasFooterHtml()) {?>
                <div class="panel-footer">
                    <?php echo $_smarty_tpl->tpl_vars['item']->value->getFooterHtml();?>

                </div>
            <?php }?>
        </div>
    <?php }
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_0_saved_local_item;
}
if ($__foreach_item_0_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_0_saved_item;
}
}
}
