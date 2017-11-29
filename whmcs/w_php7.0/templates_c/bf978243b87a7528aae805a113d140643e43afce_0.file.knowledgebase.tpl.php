<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:34
  from "/home/cloud.ddweb.com.cn/public_html/templates/Hostify/knowledgebase.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbb0e72f1a1_21814738',
  'file_dependency' => 
  array (
    'bf978243b87a7528aae805a113d140643e43afce' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/Hostify/knowledgebase.tpl',
      1 => 1511831716,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbb0e72f1a1_21814738 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/home/cloud.ddweb.com.cn/public_html/vendor/smarty/smarty/libs/plugins/modifier.truncate.php';
?>
<form role="form" method="post" action="<?php echo routePath('knowledgebase-search');?>
">
    <div class="input-group input-group-lg kb-search">
        <input type="text" id="inputKnowledgebaseSearch" name="search" class="form-control" placeholder="What can we help you with?" />
        <span class="input-group-btn">
            <input type="submit" id="btnKnowledgebaseSearch" class="btn btn-primary btn-input-padded-responsive" value="<?php echo $_smarty_tpl->tpl_vars['LANG']->value['search'];?>
" />
        </span>
    </div>
</form>

<h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['knowledgebasecategories'];?>
</h2>

<?php if ($_smarty_tpl->tpl_vars['kbcats']->value) {?>
    <div class="row">
        <?php
$_from = $_smarty_tpl->tpl_vars['kbcats']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_kbcats_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_foreach_kbcats']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_kbcats'] : false;
$__foreach_kbcats_0_saved_item = isset($_smarty_tpl->tpl_vars['kbcat']) ? $_smarty_tpl->tpl_vars['kbcat'] : false;
$_smarty_tpl->tpl_vars['kbcat'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['__smarty_foreach_kbcats'] = new Smarty_Variable(array('iteration' => 0));
$_smarty_tpl->tpl_vars['kbcat']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['kbcat']->value) {
$_smarty_tpl->tpl_vars['kbcat']->_loop = true;
$_smarty_tpl->tpl_vars['__smarty_foreach_kbcats']->value['iteration']++;
$__foreach_kbcats_0_saved_local_item = $_smarty_tpl->tpl_vars['kbcat'];
?>
            <div class="col-sm-4">
                <a href="<?php ob_start();
echo $_smarty_tpl->tpl_vars['kbcat']->value['id'];
$_tmp1=ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['kbcat']->value['urlfriendlyname'];
$_tmp2=ob_get_clean();
echo routePath('knowledgebase-category-view',$_tmp1,$_tmp2);?>
">
                    <i class="fa fa-folder-open-o"></i>
                    <?php echo $_smarty_tpl->tpl_vars['kbcat']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['kbcat']->value['numarticles'];?>
)
                </a>
                <p><?php echo $_smarty_tpl->tpl_vars['kbcat']->value['description'];?>
</p>
            </div>
            <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_kbcats']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_kbcats']->value['iteration'] : null) % 2 == 0) {?>
                </div><div class="row">
            <?php }?>
        <?php
$_smarty_tpl->tpl_vars['kbcat'] = $__foreach_kbcats_0_saved_local_item;
}
if ($__foreach_kbcats_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_foreach_kbcats'] = $__foreach_kbcats_0_saved;
}
if ($__foreach_kbcats_0_saved_item) {
$_smarty_tpl->tpl_vars['kbcat'] = $__foreach_kbcats_0_saved_item;
}
?>
    </div>
<?php } else { ?>
    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/alert.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"info",'msg'=>$_smarty_tpl->tpl_vars['LANG']->value['knowledgebasenoarticles'],'textcenter'=>true), 0, true);
?>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['kbmostviews']->value) {?>

    <h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['knowledgebasepopular'];?>
</h2>

    <div class="kbarticles">
        <?php
$_from = $_smarty_tpl->tpl_vars['kbmostviews']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_kbarticle_1_saved_item = isset($_smarty_tpl->tpl_vars['kbarticle']) ? $_smarty_tpl->tpl_vars['kbarticle'] : false;
$_smarty_tpl->tpl_vars['kbarticle'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['kbarticle']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['kbarticle']->value) {
$_smarty_tpl->tpl_vars['kbarticle']->_loop = true;
$__foreach_kbarticle_1_saved_local_item = $_smarty_tpl->tpl_vars['kbarticle'];
?>
            <a href="<?php ob_start();
echo $_smarty_tpl->tpl_vars['kbarticle']->value['id'];
$_tmp3=ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['kbarticle']->value['urlfriendlytitle'];
$_tmp4=ob_get_clean();
echo routePath('knowledgebase-article-view',$_tmp3,$_tmp4);?>
">
                <span class="glyphicon glyphicon-file"></span>&nbsp;<?php echo $_smarty_tpl->tpl_vars['kbarticle']->value['title'];?>

            </a>
            <p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['kbarticle']->value['article'],100,"...");?>
</p>
        <?php
$_smarty_tpl->tpl_vars['kbarticle'] = $__foreach_kbarticle_1_saved_local_item;
}
if ($__foreach_kbarticle_1_saved_item) {
$_smarty_tpl->tpl_vars['kbarticle'] = $__foreach_kbarticle_1_saved_item;
}
?>
    </div>

<?php }
}
}
