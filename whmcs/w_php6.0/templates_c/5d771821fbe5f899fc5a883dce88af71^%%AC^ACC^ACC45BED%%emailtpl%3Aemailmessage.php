<?php /* Smarty version 2.6.28, created on 2016-12-17 18:01:09
         compiled from emailtpl:emailmessage */ ?>
<p><a href="<?php echo $this->_tpl_vars['company_domain']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['company_logo_url']; ?>
" alt="<?php echo $this->_tpl_vars['company_name']; ?>
" border="0" /></a></p>
<p>
Dear <?php echo $this->_tpl_vars['client_name']; ?>
, 
</p>
<p>
This is a notice that an invoice has been generated on <?php echo $this->_tpl_vars['invoice_date_created']; ?>
. 
</p>
<p>
Your payment method is: <?php echo $this->_tpl_vars['invoice_payment_method']; ?>
 
</p>
<p>
Invoice #<?php echo $this->_tpl_vars['invoice_num']; ?>
<br />
Amount Due: <?php echo $this->_tpl_vars['invoice_total']; ?>
<br />
Due Date: <?php echo $this->_tpl_vars['invoice_date_due']; ?>
 
</p>
<p>
<strong>Invoice Items</strong> 
</p>
<p>
<?php echo $this->_tpl_vars['invoice_html_contents']; ?>
 <br />
------------------------------------------------------ 
</p>
<p>
You can login to your client area to view and pay the invoice at <?php echo $this->_tpl_vars['invoice_link']; ?>
 
</p>
<p>
<?php echo $this->_tpl_vars['signature']; ?>
 
</p>