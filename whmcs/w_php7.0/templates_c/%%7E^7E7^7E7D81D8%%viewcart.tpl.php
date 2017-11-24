<?php /* Smarty version 2.6.28, created on 2016-12-16 02:03:15
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/viewcart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/viewcart.tpl', 33, false),)), $this); ?>
<link rel="stylesheet" type="text/css" href="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/style.css" />
<script language="javascript">var statesTab=10;</script>
<script type="text/javascript" src="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/js/main.js"></script>
<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
<script type="text/javascript" src="includes/jscript/pwstrength.js"></script>
<script type="text/javascript" src="includes/jscript/creditcard.js"></script>

<?php echo '<script language="javascript">
function removeItem(type,num) {
    var response = confirm("'; ?>
<?php echo $this->_tpl_vars['LANG']['cartremoveitemconfirm']; ?>
<?php echo '");
    if (response) {
        window.location = \'cart.php?a=remove&r=\'+type+\'&i=\'+num;
    }
}
function emptyCart(type,num) {
    var response = confirm("'; ?>
<?php echo $this->_tpl_vars['LANG']['cartemptyconfirm']; ?>
<?php echo '");
    if (response) {
        window.location = \'cart.php?a=empty\';
    }
}
</script>'; ?>

<script>
window.langPasswordStrength = "<?php echo $this->_tpl_vars['LANG']['pwstrength']; ?>
";
window.langPasswordWeak = "<?php echo $this->_tpl_vars['LANG']['pwstrengthweak']; ?>
";
window.langPasswordModerate = "<?php echo $this->_tpl_vars['LANG']['pwstrengthmoderate']; ?>
";
window.langPasswordStrong = "<?php echo $this->_tpl_vars['LANG']['pwstrengthstrong']; ?>
";
</script>

<div id="order-modern">

<h1><?php echo $this->_tpl_vars['LANG']['cartreviewcheckout']; ?>
</h1>

<?php if ($this->_tpl_vars['errormessage']): ?><div class="errorbox" style="display:block;"><?php echo ((is_array($_tmp=$this->_tpl_vars['errormessage'])) ? $this->_run_mod_handler('replace', true, $_tmp, '<li>', ' &nbsp;#&nbsp; ') : smarty_modifier_replace($_tmp, '<li>', ' &nbsp;#&nbsp; ')); ?>
 &nbsp;#&nbsp; </div><?php elseif ($this->_tpl_vars['promotioncode'] && $this->_tpl_vars['rawdiscount'] == "0.00"): ?><div class="errorbox" style="display:block;"><?php echo $this->_tpl_vars['LANG']['promoappliedbutnodiscount']; ?>
</div><?php endif; ?>

<?php if ($this->_tpl_vars['bundlewarnings']): ?>
<div class="cartwarningbox">
<strong><?php echo $this->_tpl_vars['LANG']['bundlereqsnotmet']; ?>
</strong><br />
<?php $_from = $this->_tpl_vars['bundlewarnings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['warning']):
?>
<?php echo $this->_tpl_vars['warning']; ?>
<br />
<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if (! $this->_tpl_vars['loggedin'] && $this->_tpl_vars['currencies']): ?>
<div id="currencychooser">
<?php $_from = $this->_tpl_vars['currencies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr']):
?>
<a href="cart.php?a=view&currency=<?php echo $this->_tpl_vars['curr']['id']; ?>
"><img src="images/flags/<?php if ($this->_tpl_vars['curr']['code'] == 'AUD'): ?>au<?php elseif ($this->_tpl_vars['curr']['code'] == 'CAD'): ?>ca<?php elseif ($this->_tpl_vars['curr']['code'] == 'EUR'): ?>eu<?php elseif ($this->_tpl_vars['curr']['code'] == 'GBP'): ?>gb<?php elseif ($this->_tpl_vars['curr']['code'] == 'INR'): ?>in<?php elseif ($this->_tpl_vars['curr']['code'] == 'JPY'): ?>jp<?php elseif ($this->_tpl_vars['curr']['code'] == 'USD'): ?>us<?php elseif ($this->_tpl_vars['curr']['code'] == 'ZAR'): ?>za<?php else: ?>na<?php endif; ?>.png" border="0" alt="" /> <?php echo $this->_tpl_vars['curr']['code']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
</div>
<div class="clear"></div>
<?php else: ?>
<br />
<?php endif; ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?a=view">

<table class="cart" cellspacing="1">
<tr class="textcenter"><th width="60%"><?php echo $this->_tpl_vars['LANG']['orderdesc']; ?>
</th><th width="40%"><?php echo $this->_tpl_vars['LANG']['orderprice']; ?>
</th></tr>

<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['product']):
?>
<tr class="carttableproduct"><td>
<strong><em><?php echo $this->_tpl_vars['product']['productinfo']['groupname']; ?>
</em> - <?php echo $this->_tpl_vars['product']['productinfo']['name']; ?>
</strong><?php if ($this->_tpl_vars['product']['domain']): ?> (<?php echo $this->_tpl_vars['product']['domain']; ?>
)<?php endif; ?><br />
<?php if ($this->_tpl_vars['product']['configoptions']): ?>
<?php $_from = $this->_tpl_vars['product']['configoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['confnum'] => $this->_tpl_vars['configoption']):
?>&nbsp;&raquo; <?php echo $this->_tpl_vars['configoption']['name']; ?>
: <?php if ($this->_tpl_vars['configoption']['type'] == 1 || $this->_tpl_vars['configoption']['type'] == 2): ?><?php echo $this->_tpl_vars['configoption']['option']; ?>
<?php elseif ($this->_tpl_vars['configoption']['type'] == 3): ?><?php if ($this->_tpl_vars['configoption']['qty']): ?><?php echo $this->_tpl_vars['LANG']['yes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['no']; ?>
<?php endif; ?><?php elseif ($this->_tpl_vars['configoption']['type'] == 4): ?><?php echo $this->_tpl_vars['configoption']['qty']; ?>
 x <?php echo $this->_tpl_vars['configoption']['option']; ?>
<?php endif; ?><br /><?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>
?a=confproduct&i=<?php echo $this->_tpl_vars['num']; ?>
" class="cartedit">[<?php echo $this->_tpl_vars['LANG']['carteditproductconfig']; ?>
]</a> <a href="#" onclick="removeItem('p','<?php echo $this->_tpl_vars['num']; ?>
');return false" class="cartremove">[<?php echo $this->_tpl_vars['LANG']['cartremove']; ?>
]</a>
<?php if ($this->_tpl_vars['product']['allowqty']): ?>
<br /><br />
<div align="right"><?php echo $this->_tpl_vars['LANG']['cartqtyenterquantity']; ?>
 <input type="text" name="qty[<?php echo $this->_tpl_vars['num']; ?>
]" size="3" value="<?php echo $this->_tpl_vars['product']['qty']; ?>
" /> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['cartqtyupdate']; ?>
" /></div>
<?php endif; ?>
</td><td class="textcenter"><strong><?php echo $this->_tpl_vars['product']['pricingtext']; ?>
<?php if ($this->_tpl_vars['product']['proratadate']): ?><br />(<?php echo $this->_tpl_vars['LANG']['orderprorata']; ?>
 <?php echo $this->_tpl_vars['product']['proratadate']; ?>
)<?php endif; ?></strong></td></tr>
<?php $_from = $this->_tpl_vars['product']['addons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['addonnum'] => $this->_tpl_vars['addon']):
?>
<tr class="carttableproduct"><td><strong><?php echo $this->_tpl_vars['LANG']['orderaddon']; ?>
</strong> - <?php echo $this->_tpl_vars['addon']['name']; ?>
</td><td class="textcenter"><strong><?php echo $this->_tpl_vars['addon']['pricingtext']; ?>
</strong></td></tr>
<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['addons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['addon']):
?>
<tr class="carttableproduct"><td>
<strong><?php echo $this->_tpl_vars['addon']['name']; ?>
</strong><br />
<?php echo $this->_tpl_vars['addon']['productname']; ?>
<?php if ($this->_tpl_vars['addon']['domainname']): ?> - <?php echo $this->_tpl_vars['addon']['domainname']; ?>
<br /><?php endif; ?>
<a href="#" onclick="removeItem('a','<?php echo $this->_tpl_vars['num']; ?>
');return false" class="cartremove">[<?php echo $this->_tpl_vars['LANG']['cartremove']; ?>
]</a>
</td><td class="textcenter"><strong><?php echo $this->_tpl_vars['addon']['pricingtext']; ?>
</strong></td></tr>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['domains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['domain']):
?>
<tr class="carttableproduct"><td>
<strong><?php if ($this->_tpl_vars['domain']['type'] == 'register'): ?><?php echo $this->_tpl_vars['LANG']['orderdomainregistration']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['orderdomaintransfer']; ?>
<?php endif; ?></strong> - <?php echo $this->_tpl_vars['domain']['domain']; ?>
 - <?php echo $this->_tpl_vars['domain']['regperiod']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
<br />
<?php if ($this->_tpl_vars['domain']['dnsmanagement']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domaindnsmanagement']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['domain']['emailforwarding']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domainemailforwarding']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['domain']['idprotection']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domainidprotection']; ?>
<br /><?php endif; ?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>
?a=confdomains" class="cartedit">[<?php echo $this->_tpl_vars['LANG']['cartconfigdomainextras']; ?>
]</a> <a href="#" onclick="removeItem('d','<?php echo $this->_tpl_vars['num']; ?>
');return false" class="cartremove">[<?php echo $this->_tpl_vars['LANG']['cartremove']; ?>
]</a>
</td><td class="textcenter"><strong><?php echo $this->_tpl_vars['domain']['price']; ?>
</strong></td></tr>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['renewals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['domain']):
?>
<tr class="carttableproduct"><td>
<strong><?php echo $this->_tpl_vars['LANG']['domainrenewal']; ?>
</strong> - <?php echo $this->_tpl_vars['domain']['domain']; ?>
 - <?php echo $this->_tpl_vars['domain']['regperiod']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
<br />
<?php if ($this->_tpl_vars['domain']['dnsmanagement']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domaindnsmanagement']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['domain']['emailforwarding']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domainemailforwarding']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['domain']['idprotection']): ?>&nbsp;&raquo; <?php echo $this->_tpl_vars['LANG']['domainidprotection']; ?>
<br /><?php endif; ?>
<a href="#" onclick="removeItem('r','<?php echo $this->_tpl_vars['num']; ?>
');return false" class="cartremove">[<?php echo $this->_tpl_vars['LANG']['cartremove']; ?>
]</a>
</td><td class="textcenter"><strong><?php echo $this->_tpl_vars['domain']['price']; ?>
</strong></td></tr>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['cartitems'] == 0): ?>
<tr class="clientareatableactive"><td colspan="2" class="textcenter">
<br />
<?php echo $this->_tpl_vars['LANG']['cartempty']; ?>

<br /><br />
</td></tr>
<?php endif; ?>

<tr class="subtotal"><td class="textright"><?php echo $this->_tpl_vars['LANG']['ordersubtotal']; ?>
: &nbsp;</td><td class="textcenter"><?php echo $this->_tpl_vars['subtotal']; ?>
</td></tr>
<?php if ($this->_tpl_vars['promotioncode']): ?>
<tr class="promotion"><td class="textright"><?php echo $this->_tpl_vars['promotiondescription']; ?>
: &nbsp;</td><td class="textcenter"><?php echo $this->_tpl_vars['discount']; ?>
</td></tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['taxrate']): ?>
<tr class="subtotal"><td class="textright"><?php echo $this->_tpl_vars['taxname']; ?>
 @ <?php echo $this->_tpl_vars['taxrate']; ?>
%: &nbsp;</td><td class="textcenter"><?php echo $this->_tpl_vars['taxtotal']; ?>
</td></tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['taxrate2']): ?>
<tr class="subtotal"><td class="textright"><?php echo $this->_tpl_vars['taxname2']; ?>
 @ <?php echo $this->_tpl_vars['taxrate2']; ?>
%: &nbsp;</td><td class="textcenter"><?php echo $this->_tpl_vars['taxtotal2']; ?>
</td></tr>
<?php endif; ?>
<tr class="total"><td class="textright"><?php echo $this->_tpl_vars['LANG']['ordertotalduetoday']; ?>
: &nbsp;</td><td class="textcenter"><?php echo $this->_tpl_vars['total']; ?>
</td></tr>
<?php if ($this->_tpl_vars['totalrecurringmonthly'] || $this->_tpl_vars['totalrecurringquarterly'] || $this->_tpl_vars['totalrecurringsemiannually'] || $this->_tpl_vars['totalrecurringannually'] || $this->_tpl_vars['totalrecurringbiennially'] || $this->_tpl_vars['totalrecurringtriennially']): ?>
<tr class="recurring"><td class="textright"><?php echo $this->_tpl_vars['LANG']['ordertotalrecurring']; ?>
: &nbsp;</td><td class="textcenter">
<?php if ($this->_tpl_vars['totalrecurringmonthly']): ?><?php echo $this->_tpl_vars['totalrecurringmonthly']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermmonthly']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['totalrecurringquarterly']): ?><?php echo $this->_tpl_vars['totalrecurringquarterly']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermquarterly']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['totalrecurringsemiannually']): ?><?php echo $this->_tpl_vars['totalrecurringsemiannually']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermsemiannually']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['totalrecurringannually']): ?><?php echo $this->_tpl_vars['totalrecurringannually']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermannually']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['totalrecurringbiennially']): ?><?php echo $this->_tpl_vars['totalrecurringbiennially']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermbiennially']; ?>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['totalrecurringtriennially']): ?><?php echo $this->_tpl_vars['totalrecurringtriennially']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderpaymenttermtriennially']; ?>
<br /><?php endif; ?>
</td></tr>
<?php endif; ?>
</table>

</form>

<div class="cartbuttons"><input type="button" value="<?php echo $this->_tpl_vars['LANG']['emptycart']; ?>
" onclick="emptyCart();return false" /> <input type="button" value="<?php echo $this->_tpl_vars['LANG']['continueshopping']; ?>
" onclick="window.location='cart.php'" /></div>

<?php $_from = $this->_tpl_vars['gatewaysoutput']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['gatewayoutput']):
?>
<div class="clear"></div>
<div class="cartbuttons"><?php echo $this->_tpl_vars['gatewayoutput']; ?>
</div>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['cartitems'] != 0): ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?a=checkout" id="mainfrm">
<input type="hidden" name="submit" value="true" />
<input type="hidden" name="custtype" id="custtype" value="<?php echo $this->_tpl_vars['custtype']; ?>
" />

<br /><br />

<h2><?php echo $this->_tpl_vars['LANG']['yourdetails']; ?>
</h2>

<div style="float:left;width:20px;">&nbsp;</div><div class="signuptype<?php if (! $this->_tpl_vars['loggedin'] && $this->_tpl_vars['custtype'] != 'existing'): ?> active<?php endif; ?>"<?php if (! $this->_tpl_vars['loggedin']): ?> id="newcust"<?php endif; ?>><?php echo $this->_tpl_vars['LANG']['newcustomer']; ?>
</div><div class="signuptype<?php if ($this->_tpl_vars['custtype'] == 'existing' && ! $this->_tpl_vars['loggedin'] || $this->_tpl_vars['loggedin']): ?> active<?php endif; ?>" id="existingcust"><?php echo $this->_tpl_vars['LANG']['existingcustomer']; ?>
</div>
<div class="clear"></div>

<div class="signupfields<?php if ($this->_tpl_vars['custtype'] == 'existing' && ! $this->_tpl_vars['loggedin']): ?><?php else: ?> hidden<?php endif; ?>" id="loginfrm">
<table width="100%" cellspacing="0" cellpadding="0" class="configtable">
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaemail']; ?>
</td><td class="fieldarea"><input type="text" name="loginemail" size="40" /></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareapassword']; ?>
</td><td class="fieldarea"><input type="password" name="loginpw" size="25" /></td></tr>
</table>
</div>
<div class="signupfields<?php if ($this->_tpl_vars['custtype'] == 'existing' && ! $this->_tpl_vars['loggedin']): ?> hidden<?php endif; ?>" id="signupfrm">
<table width="100%" cellspacing="0" cellpadding="0" class="configtable">
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareafirstname']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['firstname']; ?>
<?php else: ?><input type="text" name="firstname" tabindex="1" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['firstname']; ?>
" /><?php endif; ?></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaaddress1']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['address1']; ?>
<?php else: ?><input type="text" name="address1" tabindex="7" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['address1']; ?>
" /><?php endif; ?></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientarealastname']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['lastname']; ?>
<?php else: ?><input type="text" name="lastname" tabindex="2" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['lastname']; ?>
" /><?php endif; ?></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaaddress2']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['address2']; ?>
<?php else: ?><input type="text" name="address2" tabindex="8" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['address2']; ?>
" /><?php endif; ?></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacompanyname']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['companyname']; ?>
<?php else: ?><input type="text" name="companyname" tabindex="3" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['companyname']; ?>
" /><?php endif; ?></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacity']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['city']; ?>
<?php else: ?><input type="text" name="city" tabindex="9" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['city']; ?>
" /><?php endif; ?></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaemail']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['email']; ?>
<?php else: ?><input type="text" name="email" tabindex="4" style="width:90%;" value="<?php echo $this->_tpl_vars['clientsdetails']['email']; ?>
" /><?php endif; ?></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareastate']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['state']; ?>
<?php else: ?><input type="text" name="state" tabindex="10" style="width:80%;" value="<?php echo $this->_tpl_vars['clientsdetails']['state']; ?>
" /><?php endif; ?></td></tr>
<tr><?php if (! $this->_tpl_vars['loggedin']): ?><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareapassword']; ?>
</td><td class="fieldarea"><input type="password" name="password" tabindex="5" id="newpw" size="20" value="<?php echo $this->_tpl_vars['password']; ?>
" /></td><?php else: ?><td class="fieldlabel"></td><td class="fieldarea"></td><?php endif; ?><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareapostcode']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['postcode']; ?>
<?php else: ?><input type="text" name="postcode" tabindex="11" size="15" value="<?php echo $this->_tpl_vars['clientsdetails']['postcode']; ?>
" /><?php endif; ?></td></tr>
<tr><?php if (! $this->_tpl_vars['loggedin']): ?><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaconfirmpassword']; ?>
</td><td class="fieldarea"><input type="password" name="password2" tabindex="6" size="20" value="<?php echo $this->_tpl_vars['password2']; ?>
" /></td><?php else: ?><td class="fieldlabel"></td><td class="fieldarea"></td><?php endif; ?><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacountry']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['country']; ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['clientcountrydropdown'])) ? $this->_run_mod_handler('replace', true, $_tmp, '<select', '<select tabindex="12"') : smarty_modifier_replace($_tmp, '<select', '<select tabindex="12"')); ?>
<?php endif; ?></td></tr>
<tr><td colspan="2" class="fieldarea"><?php if (! $this->_tpl_vars['loggedin']): ?><script language="javascript">showStrengthBar();</script><?php endif; ?></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaphonenumber']; ?>
</td><td class="fieldarea"><?php if ($this->_tpl_vars['loggedin']): ?><?php echo $this->_tpl_vars['clientsdetails']['phonenumber']; ?>
<?php else: ?><input type="text" name="phonenumber" tabindex="13" size="20" value="<?php echo $this->_tpl_vars['clientsdetails']['phonenumber']; ?>
" /><?php endif; ?></td></tr>
<?php if ($this->_tpl_vars['customfields'] || $this->_tpl_vars['securityquestions']): ?>
<?php if ($this->_tpl_vars['securityquestions'] && ! $this->_tpl_vars['loggedin']): ?>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareasecurityquestion']; ?>
</td><td class="fieldarea" colspan="3"><select name="securityqid" tabindex="14">
<?php $_from = $this->_tpl_vars['securityquestions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['question']):
?>
    <option value="<?php echo $this->_tpl_vars['question']['id']; ?>
"<?php if ($this->_tpl_vars['question']['id'] == $this->_tpl_vars['securityqid']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['question']['question']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareasecurityanswer']; ?>
</td><td class="fieldarea" colspan="3"><input type="password" name="securityqans" value="<?php echo $this->_tpl_vars['securityqans']; ?>
" tabindex="15" size="30"></td></tr>
<?php endif; ?>
<?php $_from = $this->_tpl_vars['customfields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['customfield']):
?>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['customfield']['name']; ?>
</td><td class="fieldarea" colspan="3"><?php echo $this->_tpl_vars['customfield']['input']; ?>
 <?php echo $this->_tpl_vars['customfield']['description']; ?>
</td></tr>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
</table>
</div>

<?php if ($this->_tpl_vars['taxenabled'] && ! $this->_tpl_vars['loggedin']): ?>
<div class="carttaxwarning"><?php echo $this->_tpl_vars['LANG']['carttaxupdateselections']; ?>
 <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['carttaxupdateselectionsupdate']; ?>
" name="updateonly" /></div>
<?php endif; ?>

<?php if ($this->_tpl_vars['domainsinorder']): ?>
<h2><?php echo $this->_tpl_vars['LANG']['domainregistrantinfo']; ?>
</h2>
<select name="contact" id="domaincontact" onchange="domaincontactchange()">
<option value=""><?php echo $this->_tpl_vars['LANG']['usedefaultcontact']; ?>
</option>
<?php $_from = $this->_tpl_vars['domaincontacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['domcontact']):
?>
<option value="<?php echo $this->_tpl_vars['domcontact']['id']; ?>
"<?php if ($this->_tpl_vars['contact'] == $this->_tpl_vars['domcontact']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['domcontact']['name']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
<option value="addingnew"<?php if ($this->_tpl_vars['contact'] == 'addingnew'): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['LANG']['clientareanavaddcontact']; ?>
...</option>
</select><br /><br />
<div class="signupfields<?php if ($this->_tpl_vars['contact'] != 'addingnew'): ?> hidden<?php endif; ?>" id="domaincontactfields">
<table width="100%" cellspacing="0" cellpadding="0" class="configtable">
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareafirstname']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactfirstname" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['firstname']; ?>
" /></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaaddress1']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactaddress1" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['address1']; ?>
" /></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientarealastname']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactlastname" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['lastname']; ?>
" /></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaaddress2']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactaddress2" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['address2']; ?>
" /></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacompanyname']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactcompanyname" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['companyname']; ?>
" /></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacity']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactcity" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['city']; ?>
" /></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaemail']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactemail" style="width:90%;" value="<?php echo $this->_tpl_vars['domaincontact']['email']; ?>
" /></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareastate']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactstate" style="width:80%;" value="<?php echo $this->_tpl_vars['domaincontact']['state']; ?>
" /></td></tr>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareaphonenumber']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactphonenumber" size="20" value="<?php echo $this->_tpl_vars['domaincontact']['phonenumber']; ?>
" /></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareapostcode']; ?>
</td><td class="fieldarea"><input type="text" name="domaincontactpostcode" size="15" value="<?php echo $this->_tpl_vars['domaincontact']['postcode']; ?>
" /></td></tr>
<tr><td class="fieldlabel"></td><td class="fieldarea"></td><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['clientareacountry']; ?>
</td><td class="fieldarea"><?php echo $this->_tpl_vars['domaincontactcountrydropdown']; ?>
</td></tr>
</table>
</div>
<?php endif; ?>

<div class="checkoutcol1">

<div class="signupfields padded">
<h2><?php echo $this->_tpl_vars['LANG']['orderpromotioncode']; ?>
</h2>
<?php if ($this->_tpl_vars['promotioncode']): ?><?php echo $this->_tpl_vars['promotioncode']; ?>
 - <?php echo $this->_tpl_vars['promotiondescription']; ?>
<br /><a href="<?php echo $_SERVER['PHP_SELF']; ?>
?a=removepromo"><?php echo $this->_tpl_vars['LANG']['orderdontusepromo']; ?>
</a><?php else: ?><input type="text" name="promocode" size="20" value="" /> <input type="submit" name="validatepromo" value="<?php echo $this->_tpl_vars['LANG']['orderpromovalidatebutton']; ?>
" /><?php endif; ?>
</div>

<?php if ($this->_tpl_vars['shownotesfield']): ?>
<div class="signupfields padded">
<h2><?php echo $this->_tpl_vars['LANG']['ordernotes']; ?>
</h2>
<textarea name="notes" rows="2" style="width:100%" onFocus="if(this.value=='<?php echo $this->_tpl_vars['LANG']['ordernotesdescription']; ?>
'){this.value='';}" onBlur="if (this.value==''){this.value='<?php echo $this->_tpl_vars['LANG']['ordernotesdescription']; ?>
';}"><?php echo $this->_tpl_vars['notes']; ?>
</textarea>
</div>
<?php endif; ?>

</div>
<div class="checkoutcol2">

<div class="signupfields padded">
<h2><?php echo $this->_tpl_vars['LANG']['orderpaymentmethod']; ?>
</h2>
<?php $_from = $this->_tpl_vars['gateways']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['gateway']):
?><label><input type="radio" name="paymentmethod" value="<?php echo $this->_tpl_vars['gateway']['sysname']; ?>
" id="pgbtn<?php echo $this->_tpl_vars['num']; ?>
" onclick="<?php if ($this->_tpl_vars['gateway']['type'] == 'CC'): ?>showCCForm()<?php else: ?>hideCCForm()<?php endif; ?>"<?php if ($this->_tpl_vars['selectedgateway'] == $this->_tpl_vars['gateway']['sysname']): ?> checked<?php endif; ?> /> <?php echo $this->_tpl_vars['gateway']['name']; ?>
</label> <?php endforeach; endif; unset($_from); ?>

<br /><br />

<div id="ccinputform" class="signupfields<?php if ($this->_tpl_vars['selectedgatewaytype'] != 'CC'): ?> hidden<?php endif; ?>">
<table width="100%" cellspacing="0" cellpadding="0" class="configtable textleft">
<?php if ($this->_tpl_vars['clientsdetails']['cclastfour']): ?><tr><td class="fieldlabel"></td><td class="fieldarea"><label><input type="radio" name="ccinfo" value="useexisting" id="useexisting" onclick="useExistingCC()"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour']): ?> checked<?php else: ?> disabled<?php endif; ?> /> <?php echo $this->_tpl_vars['LANG']['creditcarduseexisting']; ?>
<?php if ($this->_tpl_vars['clientsdetails']['cclastfour']): ?> (<?php echo $this->_tpl_vars['clientsdetails']['cclastfour']; ?>
)<?php endif; ?></label><br />
<label><input type="radio" name="ccinfo" value="new" id="new" onclick="enterNewCC()"<?php if (! $this->_tpl_vars['clientsdetails']['cclastfour'] || $this->_tpl_vars['ccinfo'] == 'new'): ?> checked<?php endif; ?> /> <?php echo $this->_tpl_vars['LANG']['creditcardenternewcard']; ?>
</label></td></tr><?php else: ?><input type="hidden" name="ccinfo" value="new" /><?php endif; ?>
<tr class="newccinfo"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour'] && $this->_tpl_vars['ccinfo'] != 'new'): ?> style="display:none;"<?php endif; ?>><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcardtype']; ?>
</td><td class="fieldarea"><select name="cctype" id="cctype">
<?php $_from = $this->_tpl_vars['acceptedcctypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['cardtype']):
?>
<option<?php if ($this->_tpl_vars['cctype'] == $this->_tpl_vars['cardtype']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['cardtype']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select></td></tr>
<tr class="newccinfo"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour'] && $this->_tpl_vars['ccinfo'] != 'new'): ?> style="display:none;"<?php endif; ?>><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcardnumber']; ?>
</td><td class="fieldarea"><input type="text" name="ccnumber" size="30" value="<?php echo $this->_tpl_vars['ccnumber']; ?>
" autocomplete="off" /></td></tr>
<tr class="newccinfo"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour'] && $this->_tpl_vars['ccinfo'] != 'new'): ?> style="display:none;"<?php endif; ?>><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcardexpires']; ?>
</td><td class="fieldarea"><select name="ccexpirymonth" id="ccexpirymonth" class="newccinfo">
<?php $_from = $this->_tpl_vars['months']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['month']):
?>
<option<?php if ($this->_tpl_vars['ccexpirymonth'] == $this->_tpl_vars['month']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['month']; ?>
</option>
<?php endforeach; endif; unset($_from); ?></select> / <select name="ccexpiryyear" class="newccinfo">
<?php $_from = $this->_tpl_vars['expiryyears']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year']):
?>
<option<?php if ($this->_tpl_vars['ccexpiryyear'] == $this->_tpl_vars['year']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['year']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select></td></tr>
<?php if ($this->_tpl_vars['showccissuestart']): ?>
<tr class="newccinfo"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour'] && $this->_tpl_vars['ccinfo'] != 'new'): ?> style="display:none;"<?php endif; ?>><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcardstart']; ?>
</td><td class="fieldarea"><select name="ccstartmonth" id="ccstartmonth" class="newccinfo">
<?php $_from = $this->_tpl_vars['months']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['month']):
?>
<option<?php if ($this->_tpl_vars['ccstartmonth'] == $this->_tpl_vars['month']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['month']; ?>
</option>
<?php endforeach; endif; unset($_from); ?></select> / <select name="ccstartyear" class="newccinfo">
<?php $_from = $this->_tpl_vars['startyears']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['year']):
?>
<option<?php if ($this->_tpl_vars['ccstartyear'] == $this->_tpl_vars['year']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['year']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select></td></tr>
<tr class="newccinfo"<?php if ($this->_tpl_vars['clientsdetails']['cclastfour'] && $this->_tpl_vars['ccinfo'] != 'new'): ?> style="display:none;"<?php endif; ?>><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcardissuenum']; ?>
</td><td class="fieldarea"><input type="text" name="ccissuenum" value="<?php echo $this->_tpl_vars['ccissuenum']; ?>
" size="5" maxlength="3" /></td></tr>
<?php endif; ?>
<tr><td class="fieldlabel"><?php echo $this->_tpl_vars['LANG']['creditcardcvvnumber']; ?>
</td><td class="fieldarea"><input type="text" name="cccvv" id="cccvv" value="<?php echo $this->_tpl_vars['cccvv']; ?>
" size="5" autocomplete="off" /> <a href="#" onclick="window.open('images/ccv.gif','','width=280,height=200,scrollbars=no,top=100,left=100');return false"><?php echo $this->_tpl_vars['LANG']['creditcardcvvwhere']; ?>
</a></td></tr>
<?php if ($this->_tpl_vars['shownostore']): ?><tr><td class="fieldlabel"><input type="checkbox" name="nostore" id="nostore" /></td><td><label for="nostore"><?php echo $this->_tpl_vars['LANG']['creditcardnostore']; ?>
</label></td></tr><?php endif; ?>
</table>
</div>

</div>

</div>
<div class="clear"></div>

<?php if ($this->_tpl_vars['accepttos']): ?>
<div align="center"><label><input type="checkbox" name="accepttos" id="accepttos" /> <?php echo $this->_tpl_vars['LANG']['ordertosagreement']; ?>
 <a href="<?php echo $this->_tpl_vars['tosurl']; ?>
" target="_blank"><?php echo $this->_tpl_vars['LANG']['ordertos']; ?>
</a></label></div>
<br />
<?php endif; ?>

<div align="center"><input type="submit" value="<?php echo $this->_tpl_vars['LANG']['completeorder']; ?>
"<?php if ($this->_tpl_vars['cartitems'] == 0): ?> disabled<?php endif; ?> onclick="this.value='<?php echo $this->_tpl_vars['LANG']['pleasewait']; ?>
'" class="ordernow" /></div>

</form>

<?php else: ?>

<br /><br />

<?php endif; ?>

<div class="cartwarningbox"><img src="images/padlock.gif" align="absmiddle" border="0" alt="Secure Transaction" /> &nbsp;<?php echo $this->_tpl_vars['LANG']['ordersecure']; ?>
 (<strong><?php echo $this->_tpl_vars['ipaddress']; ?>
</strong>) <?php echo $this->_tpl_vars['LANG']['ordersecure2']; ?>
</div>

</div>