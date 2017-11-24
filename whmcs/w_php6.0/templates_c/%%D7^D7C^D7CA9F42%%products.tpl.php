<?php /* Smarty version 2.6.28, created on 2016-12-13 18:05:51
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/products.tpl */ ?>
<script type="text/javascript" src="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/js/main.js"></script>
<link rel="stylesheet" type="text/css" href="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/style.css" />

<div id="order-modern">

<h1><?php echo $this->_tpl_vars['groupname']; ?>
</h1>
<div align="center"><a href="#" onclick="showcats();return false;">(<?php echo $this->_tpl_vars['LANG']['cartchooseanothercategory']; ?>
)</a></div>

<div id="categories">
<?php $_from = $this->_tpl_vars['productgroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['productgroup']):
?>
<?php if ($this->_tpl_vars['productgroup']['gid'] != $this->_tpl_vars['gid']): ?><a href="cart.php?gid=<?php echo $this->_tpl_vars['productgroup']['gid']; ?>
"><?php echo $this->_tpl_vars['productgroup']['name']; ?>
</a><?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['loggedin']): ?>
<?php if ($this->_tpl_vars['gid'] != 'addons'): ?><a href="cart.php?gid=addons"><?php echo $this->_tpl_vars['LANG']['cartproductaddons']; ?>
</a><?php endif; ?>
<?php if ($this->_tpl_vars['renewalsenabled'] && $this->_tpl_vars['gid'] != 'renewals'): ?><a href="cart.php?gid=renewals"><?php echo $this->_tpl_vars['LANG']['domainrenewals']; ?>
</a><?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['registerdomainenabled'] && $this->_tpl_vars['domain'] != 'register'): ?><a href="cart.php?a=add&domain=register"><?php echo $this->_tpl_vars['LANG']['registerdomain']; ?>
</a><?php endif; ?>
<?php if ($this->_tpl_vars['transferdomainenabled'] && $this->_tpl_vars['domain'] != 'transfer'): ?><a href="cart.php?a=add&domain=transfer"><?php echo $this->_tpl_vars['LANG']['transferdomain']; ?>
</a><?php endif; ?>
<a href="cart.php?a=view"><?php echo $this->_tpl_vars['LANG']['viewcart']; ?>
</a>
</div>
<div class="clear"></div>

<?php if (! $this->_tpl_vars['loggedin'] && $this->_tpl_vars['currencies']): ?>
<div id="currencychooser">
<?php $_from = $this->_tpl_vars['currencies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr']):
?>
<a href="cart.php?gid=<?php echo $this->_tpl_vars['gid']; ?>
&currency=<?php echo $this->_tpl_vars['curr']['id']; ?>
"><img src="images/flags/<?php if ($this->_tpl_vars['curr']['code'] == 'AUD'): ?>au<?php elseif ($this->_tpl_vars['curr']['code'] == 'CAD'): ?>ca<?php elseif ($this->_tpl_vars['curr']['code'] == 'EUR'): ?>eu<?php elseif ($this->_tpl_vars['curr']['code'] == 'GBP'): ?>gb<?php elseif ($this->_tpl_vars['curr']['code'] == 'INR'): ?>in<?php elseif ($this->_tpl_vars['curr']['code'] == 'JPY'): ?>jp<?php elseif ($this->_tpl_vars['curr']['code'] == 'USD'): ?>us<?php elseif ($this->_tpl_vars['curr']['code'] == 'ZAR'): ?>za<?php else: ?>na<?php endif; ?>.png" border="0" alt="" /> <?php echo $this->_tpl_vars['curr']['code']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
</div>
<div class="clear"></div>
<?php else: ?>
<br />
<?php endif; ?>

<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['product']):
?>
<div class="products">
<div class="product" id="product<?php echo $this->_tpl_vars['num']; ?>
" onclick="window.location='cart.php?a=add&<?php if ($this->_tpl_vars['product']['bid']): ?>bid=<?php echo $this->_tpl_vars['product']['bid']; ?>
<?php else: ?>pid=<?php echo $this->_tpl_vars['product']['pid']; ?>
<?php endif; ?>'">

<div class="pricing">
    <?php if ($this->_tpl_vars['product']['bid']): ?>
    <?php echo $this->_tpl_vars['LANG']['bundledeal']; ?>
<br />
    <?php if ($this->_tpl_vars['product']['displayprice']): ?><span class="pricing"><?php echo $this->_tpl_vars['product']['displayprice']; ?>
</span><?php endif; ?>
    <?php else: ?>
    <?php if ($this->_tpl_vars['product']['pricing']['hasconfigoptions']): ?><?php echo $this->_tpl_vars['LANG']['startingfrom']; ?>
<br /><?php endif; ?>
    <span class="pricing"><?php echo $this->_tpl_vars['product']['pricing']['minprice']['price']; ?>
</span><br />
    <?php if ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'monthly'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermmonthly']; ?>

    <?php elseif ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'quarterly'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermquarterly']; ?>

    <?php elseif ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'semiannually'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermsemiannually']; ?>

    <?php elseif ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'annually'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermannually']; ?>

    <?php elseif ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'biennially'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermbiennially']; ?>

    <?php elseif ($this->_tpl_vars['product']['pricing']['minprice']['cycle'] == 'triennially'): ?>
    <?php echo $this->_tpl_vars['LANG']['orderpaymenttermtriennially']; ?>

    <?php endif; ?>
    <?php endif; ?>
</div>

<div class="name">
    <?php echo $this->_tpl_vars['product']['name']; ?>
<?php if ($this->_tpl_vars['product']['qty'] != ""): ?> <span class="qty">(<?php echo $this->_tpl_vars['product']['qty']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderavailable']; ?>
)</span><?php endif; ?>
</div>

<?php $_from = $this->_tpl_vars['product']['features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feature'] => $this->_tpl_vars['value']):
?>
<span class="prodfeature"><span class="feature"><?php echo $this->_tpl_vars['feature']; ?>
</span><br /><?php echo $this->_tpl_vars['value']; ?>
</span>
<?php endforeach; endif; unset($_from); ?>

<div class="clear"></div>

<div class="description"><?php echo $this->_tpl_vars['product']['featuresdesc']; ?>
</div>

<form method="post" action="cart.php?a=add&<?php if ($this->_tpl_vars['product']['bid']): ?>bid=<?php echo $this->_tpl_vars['product']['bid']; ?>
<?php else: ?>pid=<?php echo $this->_tpl_vars['product']['pid']; ?>
<?php endif; ?>">
<div class="ordernowbox"><input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordernowbutton']; ?>
 &raquo;" class="ordernow" /></div>
</form>

</div>
</div>
<?php if ($this->_tpl_vars['num'] % 2): ?><div class="clear"></div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<div class="clear"></div>

<?php if (! $this->_tpl_vars['loggedin'] && $this->_tpl_vars['currencies']): ?>
<div id="currencychooser">
<?php $_from = $this->_tpl_vars['currencies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr']):
?>
<a href="cart.php?gid=<?php echo $this->_tpl_vars['gid']; ?>
&currency=<?php echo $this->_tpl_vars['curr']['id']; ?>
"><img src="images/flags/<?php if ($this->_tpl_vars['curr']['code'] == 'AUD'): ?>au<?php elseif ($this->_tpl_vars['curr']['code'] == 'CAD'): ?>ca<?php elseif ($this->_tpl_vars['curr']['code'] == 'EUR'): ?>eu<?php elseif ($this->_tpl_vars['curr']['code'] == 'GBP'): ?>gb<?php elseif ($this->_tpl_vars['curr']['code'] == 'INR'): ?>in<?php elseif ($this->_tpl_vars['curr']['code'] == 'JPY'): ?>jp<?php elseif ($this->_tpl_vars['curr']['code'] == 'USD'): ?>us<?php elseif ($this->_tpl_vars['curr']['code'] == 'ZAR'): ?>za<?php else: ?>na<?php endif; ?>.png" border="0" alt="" /> <?php echo $this->_tpl_vars['curr']['code']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
</div>
<div class="clear"></div>
<?php endif; ?>

<br />
<br />

</div>