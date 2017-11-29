<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:16
  from "/home/cloud.ddweb.com.cn/public_html/templates/Hostify/footer.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbafc92f9b8_26999575',
  'file_dependency' => 
  array (
    'a0cf53ae726eccbff45932a74279b127b70d6c89' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/Hostify/footer.tpl',
      1 => 1511831716,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbafc92f9b8_26999575 ($_smarty_tpl) {
?>

<?php if ($_smarty_tpl->tpl_vars['loginpage']->value == 0 && $_smarty_tpl->tpl_vars['templatefile']->value != "clientregister") {
if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value) {?>
                            </div><!-- /.main-content -->
                    <div class="col-md-3 pull-md-left sidebar">
                    <?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value && $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren()) {?>
                        <div>
                            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('sidebar'=>$_smarty_tpl->tpl_vars['secondarySidebar']->value), 0, true);
?>

                        </div>
                    <?php }?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
</div>
<?php }?>
<div id="footer" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-5">
                <div class="footer-menu-holder">
                    <h4>About</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="address-holder">
                    <div class="phone"><i class="fa fa-phone"></i> 00 852 95606583</div>
                    <div class="email"><i class="fa fa-envelope"></i> hello@neworld.org</div>
                    <div class="address">
                        <i class="fa fa-map-marker"></i>
                        <div>NeWorld Cloud Ltd<br/>
                            20-22 Wenlock Road,<br/>
                            London, England, N1 7GU
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="footer-menu-holder">
                    <?php if (!$_smarty_tpl->tpl_vars['loggedin']->value) {?>
                    <h4>Links</h4>
                    <?php } else { ?>
                    <h4>Client details</h4>
                    <?php }?>
                    <ul class="footer-menu">
                        <?php if (!$_smarty_tpl->tpl_vars['loggedin']->value) {?>
                            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/navbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('navbar'=>$_smarty_tpl->tpl_vars['primaryNavbar']->value), 0, true);
?>

                        <?php } else { ?>
                            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/customnavbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('navbar'=>$_smarty_tpl->tpl_vars['secondaryNavbar']->value), 0, true);
?>

                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                <div class="social-menu-holder">
                    <ul class="social-menu">
                        <li><a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal system-modal fade" id="modalAjax" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Title</h4>
            </div>
            <div class="modal-body panel-body">
                Loading...
            </div>
            <div class="modal-footer panel-footer">
                <div class="pull-left loader">
                    <i class="fa fa-circle-o-notch fa-spin"></i> Loading...
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary modal-submit">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
<?php }
if ($_smarty_tpl->tpl_vars['templatefile']->value == "clientregister") {
echo '<script'; ?>
>
    $(window).on("load", function() {
        $("select").addClass("selectpicker");
    });
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/js/bootstrap-select.min.js"><?php echo '</script'; ?>
>
<?php }
echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/js/bootstrap-slider.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/js/slick.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/assets/js/docs.js?v=1"><?php echo '</script'; ?>
>

<?php echo $_smarty_tpl->tpl_vars['footeroutput']->value;?>

</body>
</html>
<?php }
}
