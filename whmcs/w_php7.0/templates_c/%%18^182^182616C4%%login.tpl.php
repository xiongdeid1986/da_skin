<?php /* Smarty version 2.6.28, created on 2016-12-13 23:50:20
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/login.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['login'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['incorrect']): ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p><?php echo $this->_tpl_vars['LANG']['loginincorrect']; ?>
</p>
</div>
<?php endif; ?>

<form method="post" action="<?php echo $this->_tpl_vars['systemsslurl']; ?>
dologin.php" class="form-horizontal">

    <fieldset>
	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="username"><?php echo $this->_tpl_vars['LANG']['loginemail']; ?>
:</label>
			<div class="col-sm-9">
			    <input class="col-xs-12 col-sm-3" name="username" id="username" type="text" />
			</div>
		</div>

		<div class="form-group">
		    <label class="col-sm-3 control-label" for="password"><?php echo $this->_tpl_vars['LANG']['loginpassword']; ?>
:</label>
			<div class="col-sm-9">
			    <input class="col-xs-12 col-sm-3" name="password" id="password" type="password"/>
			</div>
		</div>

		<div class="form-group">
		    <label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
			    <input type="checkbox" name="rememberme"<?php if ($this->_tpl_vars['rememberme']): ?> checked="checked"<?php endif; ?> /> <?php echo $this->_tpl_vars['LANG']['loginrememberme']; ?>

			</div>
		</div>
		
		
		
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<input type="submit" class="btn btn-success" value="<?php echo $this->_tpl_vars['LANG']['loginbutton']; ?>
" /> 
			</div>
		</div>
	</fieldset>
	<div class="col-md-offset-3 col-md-9">
		<p><a href="pwreset.php" ><?php echo $this->_tpl_vars['LANG']['loginforgotteninstructions']; ?>
</a></p>
		<br /><br />
	</div>
</form>

<script type="text/javascript">
$("#username").focus();
</script>