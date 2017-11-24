<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:30
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareachangepw.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['clientareanavchangepw'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/clientareadetailslinks.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['successful']): ?>
<div class="alert alert-success">
    <p><?php echo $this->_tpl_vars['LANG']['changessavedsuccessfully']; ?>
</p>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['errormessage']): ?>
<div class="alert alert-danger">
    <p><?php echo $this->_tpl_vars['LANG']['clientareaerrors']; ?>
</p>
    <ul>
        <?php echo $this->_tpl_vars['errormessage']; ?>

    </ul>
</div>
<?php endif; ?>
<form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=changepw">

  <fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="existingpw"><?php echo $this->_tpl_vars['LANG']['existingpassword']; ?>
</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="existingpw" id="existingpw" />
		</div>
	</div>
	
	<div class="hr hr-16 hr-dotted"></div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password"><?php echo $this->_tpl_vars['LANG']['newpassword']; ?>
</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="newpw" id="password" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="confirmpw"><?php echo $this->_tpl_vars['LANG']['confirmnewpassword']; ?>
</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="confirmpw" id="confirmpw" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="passstrength"><?php echo $this->_tpl_vars['LANG']['pwstrength']; ?>
</label>
		<div class="col-sm-9">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pwstrength.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>

  </fieldset>

  <div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="submit" value="<?php echo $this->_tpl_vars['LANG']['clientareasavechanges']; ?>
" />
		<input class="btn" type="reset" value="<?php echo $this->_tpl_vars['LANG']['cancel']; ?>
" />
	</div>
</div>

</form>