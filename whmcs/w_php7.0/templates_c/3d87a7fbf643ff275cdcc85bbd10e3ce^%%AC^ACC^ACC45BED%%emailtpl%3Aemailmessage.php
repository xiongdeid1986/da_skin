<?php /* Smarty version 2.6.28, created on 2016-12-19 20:05:04
         compiled from emailtpl:emailmessage */ ?>
<p>An order has received its first payment and the product/service has been automatically provisioned successfully.</p>
<p>Client ID: <?php echo $this->_tpl_vars['client_id']; ?>
<br /><?php if ($this->_tpl_vars['service_id']): ?>Service ID: <?php echo $this->_tpl_vars['service_id']; ?>
<br />Product/Service: <?php echo $this->_tpl_vars['service_product']; ?>
<br />Domain: <?php echo $this->_tpl_vars['service_domain']; ?>
<?php else: ?>Domain ID: <?php echo $this->_tpl_vars['domain_id']; ?>
<br />Registration Type: <?php echo $this->_tpl_vars['domain_type']; ?>
<br />Domain: <?php echo $this->_tpl_vars['domain_name']; ?>
<?php endif; ?></p>
<p><?php echo $this->_tpl_vars['whmcs_admin_link']; ?>
</p>