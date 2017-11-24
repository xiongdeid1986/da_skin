<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:25
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareadetails.tpl */ ?>

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['clientareanavdetails'])));
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
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p><?php echo $this->_tpl_vars['LANG']['changessavedsuccessfully']; ?>
</p>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['errormessage']): ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p class="bold"><?php echo $this->_tpl_vars['LANG']['clientareaerrors']; ?>
</p>
    <ul>
        <?php echo $this->_tpl_vars['errormessage']; ?>

    </ul>
</div>
<?php endif; ?>

<form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=details">

<fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="firstname"><?php echo $this->_tpl_vars['LANG']['clientareafirstname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="firstname" id="firstname" value="<?php echo $this->_tpl_vars['clientfirstname']; ?>
"<?php if (in_array ( 'firstname' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="lastname"><?php echo $this->_tpl_vars['LANG']['clientarealastname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="lastname" id="lastname" value="<?php echo $this->_tpl_vars['clientlastname']; ?>
"<?php if (in_array ( 'lastname' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="companyname"><?php echo $this->_tpl_vars['LANG']['clientareacompanyname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="companyname" id="companyname" value="<?php echo $this->_tpl_vars['clientcompanyname']; ?>
"<?php if (in_array ( 'companyname' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="email"><?php echo $this->_tpl_vars['LANG']['clientareaemail']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="email" id="email" value="<?php echo $this->_tpl_vars['clientemail']; ?>
"<?php if (in_array ( 'email' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>
	
	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="blank">&nbsp;</label>
		<div class="col-sm-9">
		    &nbsp;
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="paymentmethod"><?php echo $this->_tpl_vars['LANG']['paymentmethod']; ?>
</label>
		<div class="col-sm-9">
		    <select class="col-xs-12 col-sm-4" name="paymentmethod" id="paymentmethod">
            <option value="none"><?php echo $this->_tpl_vars['LANG']['paymentmethoddefault']; ?>
</option>
            <?php $_from = $this->_tpl_vars['paymentmethods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['method']):
?>
            <option value="<?php echo $this->_tpl_vars['method']['sysname']; ?>
"<?php if ($this->_tpl_vars['method']['sysname'] == $this->_tpl_vars['defaultpaymentmethod']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['method']['name']; ?>
</option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="billingcontact"><?php echo $this->_tpl_vars['LANG']['defaultbillingcontact']; ?>
</label>
		<div class="col-sm-9">
		    <select class="col-xs-12 col-sm-4" name="billingcid" id="billingcontact">
            <option value="0"><?php echo $this->_tpl_vars['LANG']['usedefaultcontact']; ?>
</option>
            <?php $_from = $this->_tpl_vars['contacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['contact']):
?>
            <option value="<?php echo $this->_tpl_vars['contact']['id']; ?>
"<?php if ($this->_tpl_vars['contact']['id'] == $this->_tpl_vars['billingcid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['contact']['name']; ?>
</option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address1"><?php echo $this->_tpl_vars['LANG']['clientareaaddress1']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="address1" id="address1" value="<?php echo $this->_tpl_vars['clientaddress1']; ?>
"<?php if (in_array ( 'address1' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address2"><?php echo $this->_tpl_vars['LANG']['clientareaaddress2']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="address2" id="address2" value="<?php echo $this->_tpl_vars['clientaddress2']; ?>
"<?php if (in_array ( 'address2' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="city"><?php echo $this->_tpl_vars['LANG']['clientareacity']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="city" id="city" value="<?php echo $this->_tpl_vars['clientcity']; ?>
"<?php if (in_array ( 'city' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="state"><?php echo $this->_tpl_vars['LANG']['clientareastate']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="state" id="state" value="<?php echo $this->_tpl_vars['clientstate']; ?>
"<?php if (in_array ( 'state' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="postcode"><?php echo $this->_tpl_vars['LANG']['clientareapostcode']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="postcode" id="postcode" value="<?php echo $this->_tpl_vars['clientpostcode']; ?>
"<?php if (in_array ( 'postcode' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="country"><?php echo $this->_tpl_vars['LANG']['clientareacountry']; ?>
</label>
		<div class="col-sm-9">
		    <?php echo $this->_tpl_vars['clientcountriesdropdown']; ?>

		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="phonenumber"><?php echo $this->_tpl_vars['LANG']['clientareaphonenumber']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="phonenumber" id="phonenumber" value="<?php echo $this->_tpl_vars['clientphonenumber']; ?>
"<?php if (in_array ( 'phonenumber' , $this->_tpl_vars['uneditablefields'] )): ?> disabled="" class="disabled"<?php endif; ?> />
		</div>
	</div>
    <?php if ($this->_tpl_vars['emailoptoutenabled']): ?>
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="emailoptout"><?php echo $this->_tpl_vars['LANG']['emailoptout']; ?>
</label>
		<div class="col-sm-9">
			<div class="tcb">
				<label>
					<input type="checkbox" class="tc tc-green" value="1" name="emailoptout" id="emailoptout" <?php if ($this->_tpl_vars['emailoptout']): ?> checked<?php endif; ?> />
					<span class="labels"> <?php echo $this->_tpl_vars['LANG']['emailoptoutdesc']; ?>
</span>
				</label>
			</div>
		</div>
	</div>
    <?php endif; ?>

<?php if ($this->_tpl_vars['customfields']): ?>
<?php $_from = $this->_tpl_vars['customfields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['customfield']):
?>
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="customfield<?php echo $this->_tpl_vars['customfield']['id']; ?>
"><?php echo $this->_tpl_vars['customfield']['name']; ?>
</label>
		<div class="col-sm-9">
		    <?php echo $this->_tpl_vars['customfield']['input']; ?>
 <?php echo $this->_tpl_vars['customfield']['description']; ?>

		</div>
	</div>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

</fieldset>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="save" value="<?php echo $this->_tpl_vars['LANG']['clientareasavechanges']; ?>
" />
		<input class="btn" type="reset" value="<?php echo $this->_tpl_vars['LANG']['cancel']; ?>
" />
	</div>
</div>

</form>