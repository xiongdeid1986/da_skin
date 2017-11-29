<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:25:48
  from "/home/cloud.ddweb.com.cn/public_html/templates/spacehost/footer.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cbb1cc3c0e9_44369124',
  'file_dependency' => 
  array (
    '0950fd7c62f69a633adf61ff5b6eb5b0c975ed43' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/spacehost/footer.tpl',
      1 => 1511831796,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cbb1cc3c0e9_44369124 ($_smarty_tpl) {
?>

                </div><!-- /.main-content -->
                <?php if (!$_smarty_tpl->tpl_vars['inShoppingCart']->value && $_smarty_tpl->tpl_vars['secondarySidebar']->value->hasChildren()) {?>
                    <div class="col-md-3 pull-md-left sidebar">
                        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, ((string)$_smarty_tpl->tpl_vars['template']->value)."/includes/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('sidebar'=>$_smarty_tpl->tpl_vars['secondarySidebar']->value), 0, true);
?>

                    </div>
                <?php }?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</section>
</div>
<div id="contact" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">Get in touch</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="contact-holder">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-box">
                                <h4>Get the right support</h4>
                                <p>Sed ut perspiciatis unde omnis iste natus<br>
error sit voluptatem accusantium<br>
Lorem ipsusm set amir</p>
                                <a href="#">Visit our support portal</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-box">
                                <h4>Talk to customer service</h4>
                                <p>Olim ut perspiciatis unde omnis iste natus<br>
error sit voluptatem accusantium<br>
Lorem ipsusm set amir</p>
                                <a href="#">Call us on 38-244-64-23</a>
                            </div>
                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == "contact") {?>
                    <?php } else { ?>
                    <div class="row">
                        <form id="contactform" method="post" action="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/mailer.php">
                            <div class="form-items-holder">
                                <div class="col-sm-12 col-md-6"><input type="text" id="name" name="name" placeholder="Your name" required></div>
                                <div class="col-sm-12 col-md-6"><input type="email" id="email" name="email" placeholder="Email Address" required></div>
                                <div class="col-md-12"><textarea id="message" name="message" placeholder="Write a message" required></textarea></div>
                                <div class="ajax-button col-md-12">
                                   <input id="submit" type="submit" value="Send message">
                                </div>
                                <div class="col-md-12" id="form-messages"></div>
                            </div>
                        </form>
                    </div>
                    <?php }?>
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

<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/js/slick.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['WEB_ROOT']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/js/main.js"><?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->tpl_vars['footeroutput']->value;?>


</body>
</html>
<?php }
}
