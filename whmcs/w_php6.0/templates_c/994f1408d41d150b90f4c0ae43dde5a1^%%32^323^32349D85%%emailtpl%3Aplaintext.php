<?php /* Smarty version 2.6.28, created on 2017-04-27 02:18:39
         compiled from emailtpl:plaintext */ ?>
<p>
Dear <?php echo $this->_tpl_vars['client_name']; ?>
, 
</p>
<p>
Thank you for signing up with us. Your new account has been setup and you can now login to our client area using the details below. 
</p>
<p>
Email Address: <?php echo $this->_tpl_vars['client_email']; ?>
<br />
Password: <?php echo $this->_tpl_vars['client_password']; ?>
 
</p>
<p>
To login, visit <?php echo $this->_tpl_vars['whmcs_url']; ?>
 
</p>
<p>
<?php echo $this->_tpl_vars['signature']; ?>
 
</p>