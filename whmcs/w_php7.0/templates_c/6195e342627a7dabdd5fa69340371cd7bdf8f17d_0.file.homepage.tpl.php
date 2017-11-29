<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:48
  from "/home/cloud.ddweb.com.cn/public_html/templates/spacehost/homepage.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbb1cc5fc92_49088875',
  'file_dependency' => 
  array (
    '6195e342627a7dabdd5fa69340371cd7bdf8f17d' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/spacehost/homepage.tpl',
      1 => 1511831796,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbb1cc5fc92_49088875 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/home/cloud.ddweb.com.cn/public_html/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
if ($_smarty_tpl->tpl_vars['twitterusername']->value) {?>

    <h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['twitterlatesttweets'];?>
</h2>

    <div id="twitterFeedOutput">
        <p class="text-center"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/loading.gif" /></p>
    </div>

    <?php echo '<script'; ?>
 type="text/javascript" src="templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/js/twitter.js"><?php echo '</script'; ?>
>

<?php } elseif ($_smarty_tpl->tpl_vars['announcements']->value) {?>

    <h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['news'];?>
</h2>

    <?php
$_from = $_smarty_tpl->tpl_vars['announcements']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_announcement_0_saved_item = isset($_smarty_tpl->tpl_vars['announcement']) ? $_smarty_tpl->tpl_vars['announcement'] : false;
$_smarty_tpl->tpl_vars['announcement'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['announcement']->index=-1;
$_smarty_tpl->tpl_vars['announcement']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['announcement']->value) {
$_smarty_tpl->tpl_vars['announcement']->_loop = true;
$_smarty_tpl->tpl_vars['announcement']->index++;
$__foreach_announcement_0_saved_local_item = $_smarty_tpl->tpl_vars['announcement'];
?>
        <?php if ($_smarty_tpl->tpl_vars['announcement']->index < 2) {?>
            <div class="announcement-single">
                <h3>
                    <span class="label label-default">
                        <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['announcement']->value['rawDate'],"M jS");?>

                    </span>
                    <a href="<?php echo routePath('announcement-view',$_smarty_tpl->tpl_vars['announcement']->value['id'],$_smarty_tpl->tpl_vars['announcement']->value['urlfriendlytitle']);?>
"><?php echo $_smarty_tpl->tpl_vars['announcement']->value['title'];?>
</a>
                </h3>

                <blockquote>
                    <p>
                        <?php if (strlen(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['announcement']->value['text'])) < 350) {?>
                            <?php echo $_smarty_tpl->tpl_vars['announcement']->value['text'];?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->tpl_vars['announcement']->value['summary'];?>

                            <a href="<?php echo routePath('announcement-view',$_smarty_tpl->tpl_vars['announcement']->value['id'],$_smarty_tpl->tpl_vars['announcement']->value['urlfriendlytitle']);?>
" class="label label-warning"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['readmore'];?>
 &raquo;</a>
                        <?php }?>
                    </p>
                </blockquote>

                <?php if ($_smarty_tpl->tpl_vars['announcementsFbRecommend']->value) {?>
                    <?php echo '<script'; ?>
>
                        (function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) {
                                return;
                            }
                            js = d.createElement(s); js.id = id;
                            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                            fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));
                    <?php echo '</script'; ?>
>
                    <div class="fb-like hidden-sm hidden-xs" data-layout="standard" data-href="<?php echo fqdnRoutePath('announcement-view',$_smarty_tpl->tpl_vars['announcement']->value['id'],$_smarty_tpl->tpl_vars['announcement']->value['urlfriendlytitle']);?>
" data-send="true" data-width="450" data-show-faces="true" data-action="recommend"></div>
                    <div class="fb-like hidden-lg hidden-md" data-layout="button_count" data-href="<?php echo fqdnRoutePath('announcement-view',$_smarty_tpl->tpl_vars['announcement']->value['id'],$_smarty_tpl->tpl_vars['announcement']->value['urlfriendlytitle']);?>
" data-send="true" data-width="450" data-show-faces="true" data-action="recommend"></div>
                <?php }?>
            </div>
        <?php }?>
    <?php
$_smarty_tpl->tpl_vars['announcement'] = $__foreach_announcement_0_saved_local_item;
}
if ($__foreach_announcement_0_saved_item) {
$_smarty_tpl->tpl_vars['announcement'] = $__foreach_announcement_0_saved_item;
}
}
}
}
