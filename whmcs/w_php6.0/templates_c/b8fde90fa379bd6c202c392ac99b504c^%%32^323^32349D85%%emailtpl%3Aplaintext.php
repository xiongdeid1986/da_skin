<?php /* Smarty version 2.6.28, created on 2016-12-17 18:01:10
         compiled from emailtpl:plaintext */ ?>
<p>
Dear <?php echo $this->_tpl_vars['client_name']; ?>
, 
</p>
<p>
We have received your order and will be processing it shortly. The details of the order are below: 
</p>
<p>
Order Number: <b><?php echo $this->_tpl_vars['order_number']; ?>
</b></p>
<p>
<?php echo $this->_tpl_vars['order_details']; ?>
 
</p>
<p>
You will receive an email from us shortly once your account has been setup. Please quote your order reference number if you wish to contact us about this order. 
</p>
<p>
<?php echo $this->_tpl_vars['signature']; ?>

</p>