<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:29
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareacontacts.tpl */ ?>


<?php if ($this->_tpl_vars['contactid']): ?>

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['clientareanavcontacts'])));
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
    <p class="bold"><?php echo $this->_tpl_vars['LANG']['clientareaerrors']; ?>
</p>
    <ul>
        <?php echo $this->_tpl_vars['errormessage']; ?>

    </ul>
</div>
<?php endif; ?>

<script type="text/javascript">
<?php echo '
jQuery(document).ready(function(){
    jQuery("#subaccount").click(function () {
        if (jQuery("#subaccount:checked").val()!=null) {
            jQuery("#subaccountfields").slideDown();
        } else {
            jQuery("#subaccountfields").slideUp();
        }
    });
});
'; ?>

function deleteContact() {
if (confirm("<?php echo $this->_tpl_vars['LANG']['clientareadeletecontactareyousure']; ?>
")) {
window.location='clientarea.php?action=contacts&delete=true&id=<?php echo $this->_tpl_vars['contactid']; ?>
&token=<?php echo $this->_tpl_vars['token']; ?>
';
}}
</script>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=contacts" class="form-search">
<div class="note">
<h4><?php echo $this->_tpl_vars['LANG']['clientareachoosecontact']; ?>
</h4>
<select class="col-sm-2" name="contactid" onchange="submit()">
    <?php $_from = $this->_tpl_vars['contacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['contact']):
?>
        <option value="<?php echo $this->_tpl_vars['contact']['id']; ?>
"<?php if ($this->_tpl_vars['contact']['id'] == $this->_tpl_vars['contactid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['contact']['name']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
    <option value="new"><?php echo $this->_tpl_vars['LANG']['clientareanavaddcontact']; ?>
</option>
    </select> <input class="btn btn-success btn-sm" type="submit" value="<?php echo $this->_tpl_vars['LANG']['go']; ?>
" />
	</div>
</form>

<form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=contacts&id=<?php echo $this->_tpl_vars['contactid']; ?>
">

<fieldset>


    <div class="form-group">
	    <label class="col-sm-3 control-label" for="firstname"><?php echo $this->_tpl_vars['LANG']['clientareafirstname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="firstname" id="firstname" value="<?php echo $this->_tpl_vars['contactfirstname']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="lastname"><?php echo $this->_tpl_vars['LANG']['clientarealastname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="lastname" id="lastname" value="<?php echo $this->_tpl_vars['contactlastname']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="companyname"><?php echo $this->_tpl_vars['LANG']['clientareacompanyname']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="companyname" id="companyname" value="<?php echo $this->_tpl_vars['contactcompanyname']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="email"><?php echo $this->_tpl_vars['LANG']['clientareaemail']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="email" id="email" value="<?php echo $this->_tpl_vars['contactemail']; ?>
" />
		</div>
	</div>
	
	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address1"><?php echo $this->_tpl_vars['LANG']['clientareaaddress1']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-8" name="address1" id="address1" value="<?php echo $this->_tpl_vars['contactaddress1']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address2"><?php echo $this->_tpl_vars['LANG']['clientareaaddress2']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-8" name="address2" id="address2" value="<?php echo $this->_tpl_vars['contactaddress2']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="city"><?php echo $this->_tpl_vars['LANG']['clientareacity']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="city" id="city" value="<?php echo $this->_tpl_vars['contactcity']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="state"><?php echo $this->_tpl_vars['LANG']['clientareastate']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="state" id="state" value="<?php echo $this->_tpl_vars['contactstate']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="postcode"><?php echo $this->_tpl_vars['LANG']['clientareapostcode']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="postcode" id="postcode" value="<?php echo $this->_tpl_vars['contactpostcode']; ?>
" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="country"><?php echo $this->_tpl_vars['LANG']['clientareacountry']; ?>
</label>
		<div class="col-sm-9">
		    <?php echo $this->_tpl_vars['countriesdropdown']; ?>

		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="phonenumber"><?php echo $this->_tpl_vars['LANG']['clientareaphonenumber']; ?>
</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="phonenumber" id="phonenumber" value="<?php echo $this->_tpl_vars['contactphonenumber']; ?>
" />
		</div>
	</div>

	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="billingcontact"><?php echo $this->_tpl_vars['LANG']['subaccountactivate']; ?>
</label>
		<div class="col-sm-9">
			<div class="tcb">
			<label>
				<input type="checkbox" class="tc" name="subaccount" id="subaccount"<?php if ($this->_tpl_vars['subaccount']): ?> checked<?php endif; ?> />
				<span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountactivatedesc']; ?>
</span>
			</label>
			</div>
		</div>
	</div>

</fieldset>

<div id="subaccountfields" class="<?php if (! $this->_tpl_vars['subaccount']): ?> hide<?php endif; ?>">

<fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password"><?php echo $this->_tpl_vars['LANG']['clientareapassword']; ?>
</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password" id="password" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password2"><?php echo $this->_tpl_vars['LANG']['clientareaconfirmpassword']; ?>
</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password2" id="password2" />
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
	
	<div class="hr hr-dotted"></div>

    <div class="form-group">
	    <label class="full col-sm-3 control-label"><?php echo $this->_tpl_vars['LANG']['subaccountpermissions']; ?>
</label>
		<div class="col-sm-9">
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" value="profile"<?php if (in_array ( 'profile' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsprofile']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permcontacts" value="contacts"<?php if (in_array ( 'contacts' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermscontacts']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permproducts" value="products"<?php if (in_array ( 'products' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsproducts']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permmanageproducts" value="manageproducts"<?php if (in_array ( 'manageproducts' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsmanageproducts']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permdomains" value="domains"<?php if (in_array ( 'domains' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsdomains']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permmanagedomains" value="managedomains"<?php if (in_array ( 'managedomains' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsmanagedomains']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="perminvoices" value="invoices"<?php if (in_array ( 'invoices' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsinvoices']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permtickets" value="tickets"<?php if (in_array ( 'tickets' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermstickets']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permaffiliates" value="affiliates"<?php if (in_array ( 'affiliates' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsaffiliates']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permemails" value="emails"<?php if (in_array ( 'emails' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsemails']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permorders" value="orders"<?php if (in_array ( 'orders' , $this->_tpl_vars['permissions'] )): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['subaccountpermsorders']; ?>
</span>
                    </label>
				</div>
		</div>
	</div>

</fieldset>

</div>

<fieldset>

	<div class="hr hr-dotted"></div>

    <div class="form-group">
	    <label class="col-sm-3 control-label"><?php echo $this->_tpl_vars['LANG']['clientareacontactsemails']; ?>
</label>
		<div class="col-sm-9">
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="generalemails" id="generalemails" value="1"<?php if ($this->_tpl_vars['generalemails']): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['clientareacontactsemailsgeneral']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="productemails" id="productemails" value="1"<?php if ($this->_tpl_vars['productemails']): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['clientareacontactsemailsproduct']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="domainemails" id="domainemails" value="1"<?php if ($this->_tpl_vars['domainemails']): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['clientareacontactsemailsdomain']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="invoiceemails" id="invoiceemails" value="1"<?php if ($this->_tpl_vars['invoiceemails']): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['clientareacontactsemailsinvoice']; ?>
</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="supportemails" id="supportemails" value="1"<?php if ($this->_tpl_vars['supportemails']): ?> checked<?php endif; ?> />
                        <span class="labels"> <?php echo $this->_tpl_vars['LANG']['clientareacontactsemailssupport']; ?>
</span>
                    </label>
				</div>
		</div>
	</div>

</fieldset>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="submit" value="<?php echo $this->_tpl_vars['LANG']['clientareasavechanges']; ?>
" />
		<input class="btn" type="reset" value="<?php echo $this->_tpl_vars['LANG']['cancel']; ?>
" />
    <input class="btn" type="button" value="<?php echo $this->_tpl_vars['LANG']['clientareadeletecontact']; ?>
" onclick="deleteContact()" />
	</div>
</div>

</form>
<?php else: ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/clientareaaddcontact.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>