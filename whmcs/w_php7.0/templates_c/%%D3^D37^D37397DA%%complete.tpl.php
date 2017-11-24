<?php /* Smarty version 2.6.28, created on 2016-12-17 18:01:11
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/complete.tpl */ ?>
<link rel="stylesheet" type="text/css" href="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/style.css" />

<div id="order-modern">

<h1><?php echo $this->_tpl_vars['LANG']['orderconfirmation']; ?>
</h1>

<br />

<div class="signupfields padded">

<p><?php echo $this->_tpl_vars['LANG']['orderreceived']; ?>
</p>

<div class="cartbox">
<p align="center"><strong><?php echo $this->_tpl_vars['LANG']['ordernumberis']; ?>
 <?php echo $this->_tpl_vars['ordernumber']; ?>
</strong></p>
</div>

<p><?php echo $this->_tpl_vars['LANG']['orderfinalinstructions']; ?>
</p>

<?php if ($this->_tpl_vars['invoiceid'] && ! $this->_tpl_vars['ispaid']): ?>
<br />
<div class="errorbox" style="display:block;"><?php echo $this->_tpl_vars['LANG']['ordercompletebutnotpaid']; ?>
</div>
<p align="center"><a href="viewinvoice.php?id=<?php echo $this->_tpl_vars['invoiceid']; ?>
" target="_blank"><?php echo $this->_tpl_vars['LANG']['invoicenumber']; ?>
<?php echo $this->_tpl_vars['invoiceid']; ?>
</a></p>
<?php endif; ?>

<?php $_from = $this->_tpl_vars['addons_html']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['addon_html']):
?>
<div style="margin:15px 0 15px 0;"><?php echo $this->_tpl_vars['addon_html']; ?>
</div>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['ispaid']): ?>
<!-- Enter any HTML code which needs to be displayed once a user has completed the checkout of their order here - for example conversion tracking and affiliate tracking scripts -->
<?php endif; ?>

</div>

<p align="center"><a href="clientarea.php"><?php echo $this->_tpl_vars['LANG']['ordergotoclientarea']; ?>
</a></p>

<br /><br />

</div>