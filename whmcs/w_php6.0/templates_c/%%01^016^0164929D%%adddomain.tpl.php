<?php /* Smarty version 2.6.28, created on 2016-12-14 01:37:07
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/adddomain.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/adddomain.tpl', 41, false),)), $this); ?>
<script type="text/javascript" src="includes/jscript/jqueryui.js"></script>
<script type="text/javascript" src="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/js/main.js"></script>
<link rel="stylesheet" type="text/css" href="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/style.css" />

<div id="order-modern">

<h1><?php if ($this->_tpl_vars['domain'] == 'register'): ?><?php echo $this->_tpl_vars['LANG']['registerdomain']; ?>
<?php elseif ($this->_tpl_vars['domain'] == 'transfer'): ?><?php echo $this->_tpl_vars['LANG']['transferdomain']; ?>
<?php endif; ?></h1>
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
<a href="cart.php?a=add&domain=<?php echo $this->_tpl_vars['domain']; ?>
&currency=<?php echo $this->_tpl_vars['curr']['id']; ?>
"><img src="images/flags/<?php if ($this->_tpl_vars['curr']['code'] == 'AUD'): ?>au<?php elseif ($this->_tpl_vars['curr']['code'] == 'CAD'): ?>ca<?php elseif ($this->_tpl_vars['curr']['code'] == 'EUR'): ?>eu<?php elseif ($this->_tpl_vars['curr']['code'] == 'GBP'): ?>gb<?php elseif ($this->_tpl_vars['curr']['code'] == 'INR'): ?>in<?php elseif ($this->_tpl_vars['curr']['code'] == 'JPY'): ?>jp<?php elseif ($this->_tpl_vars['curr']['code'] == 'USD'): ?>us<?php elseif ($this->_tpl_vars['curr']['code'] == 'ZAR'): ?>za<?php else: ?>na<?php endif; ?>.png" border="0" alt="" /> <?php echo $this->_tpl_vars['curr']['code']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
</div>
<div class="clear"></div>
<?php else: ?>
<br />
<?php endif; ?>

<br />

<div class="product">

<p><?php if ($this->_tpl_vars['domain'] == 'register'): ?><?php echo $this->_tpl_vars['LANG']['registerdomaindesc']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['transferdomaindesc']; ?>
<?php endif; ?></p>

<?php if ($this->_tpl_vars['errormessage']): ?><div class="errorbox"><?php echo ((is_array($_tmp=$this->_tpl_vars['errormessage'])) ? $this->_run_mod_handler('replace', true, $_tmp, '<li>', ' &nbsp;#&nbsp; ') : smarty_modifier_replace($_tmp, '<li>', ' &nbsp;#&nbsp; ')); ?>
 &nbsp;#&nbsp; </div><br /><?php endif; ?>

<form onsubmit="checkavailability();return false">
<div class="domainreginput">www. <input type="text" name="sld" id="sld" size="25" value="<?php echo $this->_tpl_vars['sld']; ?>
" /> <select name="tld" id="tld">
<?php $_from = $this->_tpl_vars['tlds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['listtld']):
?>
<option value="<?php echo $this->_tpl_vars['listtld']; ?>
"<?php if ($this->_tpl_vars['listtld'] == $this->_tpl_vars['tld']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['listtld']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select> <input type="submit" value=" <?php echo $this->_tpl_vars['LANG']['checkavailability']; ?>
 " /></div>
</form>

</div>

<div id="loading" class="loading"><img src="images/loading.gif" border="0" alt="<?php echo $this->_tpl_vars['LANG']['loading']; ?>
" /></div>

<form method="post" action="cart.php?a=add&domain=<?php echo $this->_tpl_vars['domain']; ?>
">

<div id="domainresults"></div>

</form>

<?php echo '
<script language="javascript">
function checkavailability() {
    jQuery("#loading").slideDown();
    jQuery.post("cart.php", { ajax: 1, a: "domainoptions", sld: jQuery("#sld").val(), tld: jQuery("#tld").val(), checktype: \''; ?>
<?php echo $this->_tpl_vars['domain']; ?>
<?php echo '\' },
    function(data){
        jQuery("#domainresults").html(data);
        jQuery("#domainresults").slideDown();
        jQuery("#loading").slideUp();
    });
}
function cancelcheck() {
    jQuery("#domainresults").slideUp();
}'; ?>

<?php if ($this->_tpl_vars['sld']): ?><?php echo '
jQuery(document).ready(function(){
    checkavailability();
});
'; ?>
<?php endif; ?>
</script>

</div>