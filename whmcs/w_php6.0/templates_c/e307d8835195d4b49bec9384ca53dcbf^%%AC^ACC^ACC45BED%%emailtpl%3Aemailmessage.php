<?php /* Smarty version 2.6.28, created on 2016-12-17 18:01:10
         compiled from emailtpl:emailmessage */ ?>
<p><strong>Order Information</strong></p>
<p>Order ID: <?php echo $this->_tpl_vars['order_id']; ?>
<br />
Order Number: <?php echo $this->_tpl_vars['order_number']; ?>
<br />
Date/Time: <?php echo $this->_tpl_vars['order_date']; ?>
<br />
Invoice Number: <?php echo $this->_tpl_vars['invoice_id']; ?>
<br />
Payment Method: <?php echo $this->_tpl_vars['order_payment_method']; ?>
</p>
<p><strong>Customer Information</strong></p>
<p>Customer ID: <?php echo $this->_tpl_vars['client_id']; ?>
<br />
Name: <?php echo $this->_tpl_vars['client_first_name']; ?>
 <?php echo $this->_tpl_vars['client_last_name']; ?>
<br />
Email: <?php echo $this->_tpl_vars['client_email']; ?>
<br />
Company: <?php echo $this->_tpl_vars['client_company_name']; ?>
<br />
Address 1: <?php echo $this->_tpl_vars['client_address1']; ?>
<br />
Address 2: <?php echo $this->_tpl_vars['client_address2']; ?>
<br />
City: <?php echo $this->_tpl_vars['client_city']; ?>
<br />
State: <?php echo $this->_tpl_vars['client_state']; ?>
<br />
Postcode: <?php echo $this->_tpl_vars['client_postcode']; ?>
<br />
Country: <?php echo $this->_tpl_vars['client_country']; ?>
<br />
Phone Number: <?php echo $this->_tpl_vars['client_phonenumber']; ?>
</p>
<p><strong>Order Items</strong></p>
<p><?php echo $this->_tpl_vars['order_items']; ?>
</p>
<?php if ($this->_tpl_vars['order_notes']): ?><p><strong>Order Notes</strong></p>
<p><?php echo $this->_tpl_vars['order_notes']; ?>
</p><?php endif; ?>
<p><strong>ISP Information</strong></p>
<p>IP: <?php echo $this->_tpl_vars['client_ip']; ?>
<br />
Host: <?php echo $this->_tpl_vars['client_hostname']; ?>
</p><p><a href="<?php echo $this->_tpl_vars['whmcs_admin_url']; ?>
orders.php?action=view&id=<?php echo $this->_tpl_vars['order_id']; ?>
"><?php echo $this->_tpl_vars['whmcs_admin_url']; ?>
orders.php?action=view&id=<?php echo $this->_tpl_vars['order_id']; ?>
</a></p>