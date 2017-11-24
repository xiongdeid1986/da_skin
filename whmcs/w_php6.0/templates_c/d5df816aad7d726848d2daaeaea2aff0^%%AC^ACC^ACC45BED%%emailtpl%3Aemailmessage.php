<?php /* Smarty version 2.6.28, created on 2016-12-17 18:01:11
         compiled from emailtpl:emailmessage */ ?>
<p>An order has received its first payment but the automatic provisioning has failed and requires you to manually check & resolve.</p>
<p>Client ID: <?php echo $this->_tpl_vars['client_id']; ?>
<br /><?php if ($this->_tpl_vars['service_id']): ?>Service ID: <?php echo $this->_tpl_vars['service_id']; ?>
<br />Product/Service: <?php echo $this->_tpl_vars['service_product']; ?>
<br />Domain: <?php echo $this->_tpl_vars['service_domain']; ?>
<?php else: ?>Domain ID: <?php echo $this->_tpl_vars['domain_id']; ?>
<br />Registration Type: <?php echo $this->_tpl_vars['domain_type']; ?>
<br />Domain: <?php echo $this->_tpl_vars['domain_name']; ?>
<?php endif; ?><br />Error: <?php echo $this->_tpl_vars['error_msg']; ?>
</p>
<p><?php echo $this->_tpl_vars['whmcs_admin_link']; ?>
</p>