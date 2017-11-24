<?php /* Smarty version 2.6.28, created on 2016-12-17 21:17:03
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/logout.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['logouttitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="alert alert-success">
        <p><?php echo $this->_tpl_vars['LANG']['logoutsuccessful']; ?>
</p>
</div>

 <p><a href="login.php" class="btn btn-xs btn-inverse"><?php echo $this->_tpl_vars['LANG']['logoutcontinuetext']; ?>
</a></p>