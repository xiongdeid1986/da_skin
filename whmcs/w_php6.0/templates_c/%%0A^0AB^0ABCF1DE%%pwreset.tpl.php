<?php /* Smarty version 2.6.28, created on 2016-12-15 08:53:11
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/pwreset.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['pwreset'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['success']): ?>

  <div class="alert alert-success">
    <p><?php echo $this->_tpl_vars['LANG']['pwresetvalidationsent']; ?>
</p>
  </div>

  <p><?php echo $this->_tpl_vars['LANG']['pwresetvalidationcheckemail']; ?>


  <br />
  <br />
  <br />
  <br />

<?php else: ?>

<?php if ($this->_tpl_vars['errormessage']): ?>
<div class="alert alert-danger text-center">
    <p><?php echo $this->_tpl_vars['errormessage']; ?>
</p>
</div>
<?php endif; ?>

<form method="post" action="pwreset.php"  class="form-horizontal">
<input type="hidden" name="action" value="reset" />

<?php if ($this->_tpl_vars['securityquestion']): ?>

<input type="hidden" name="email" value="<?php echo $this->_tpl_vars['email']; ?>
" />

<p><?php echo $this->_tpl_vars['LANG']['pwresetsecurityquestionrequired']; ?>
</p>
    <fieldset>
	    <div class="form-group">
		  <label class="col-sm-3 control-label" for="answer"><?php echo $this->_tpl_vars['securityquestion']; ?>
:</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-4" name="answer" id="answer" type="text" value="<?php echo $this->_tpl_vars['answer']; ?>
" />
			</div>
		</div>
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<p><input type="submit" class="btn btn-primary" value="<?php echo $this->_tpl_vars['LANG']['pwresetsubmit']; ?>
" /></p>
			</div>
		</div>
    </fieldset>
	<br /><br /><br /><br /><br /><br /><br />

<?php else: ?>

<p><?php echo $this->_tpl_vars['LANG']['pwresetdesc']; ?>
</p>
    <fieldset>
	    <div class="form-group">
		  <label class="col-sm-3 control-label" for="email"><?php echo $this->_tpl_vars['LANG']['loginemail']; ?>
:</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-4" name="email" id="email" type="text" />
			</div>
		</div>
		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<p><input type="submit" class="btn btn-primary" value="<?php echo $this->_tpl_vars['LANG']['pwresetsubmit']; ?>
" /></p>
			</div>
		</div>
    </fieldset>
	<br /><br /><br /><br /><br /><br /><br />
<?php endif; ?>

</form>

<?php endif; ?>