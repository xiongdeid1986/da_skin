<?php /* Smarty version 2.6.28, created on 2016-12-14 01:37:16
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/configureproductdomain.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'sprintf2', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/configureproductdomain.tpl', 27, false),array('modifier', 'substr', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/configureproductdomain.tpl', 53, false),)), $this); ?>
<script type="text/javascript" src="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/js/main.js"></script>
<link rel="stylesheet" type="text/css" href="templates/orderforms/<?php echo $this->_tpl_vars['carttpl']; ?>
/style.css" />

<div id="order-modern">

<h1><?php echo $this->_tpl_vars['LANG']['cartproductselection']; ?>
: <?php echo $this->_tpl_vars['productinfo']['groupname']; ?>
 - <?php echo $this->_tpl_vars['productinfo']['name']; ?>
</h1>

<p><?php echo $this->_tpl_vars['LANG']['cartmakedomainselection']; ?>
</p>

<form onsubmit="checkdomain();return false">

<div class="domainoptions">
<?php if ($this->_tpl_vars['incartdomains']): ?>
<div class="option">
<label><input type="radio" name="domainoption" value="incart" id="selincart" /><?php echo $this->_tpl_vars['LANG']['cartproductdomainuseincart']; ?>
</label>
<div class="domainreginput" id="domainincart">
<select id="incartsld">
<?php $_from = $this->_tpl_vars['incartdomains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['incartdomain']):
?>
<option value="<?php echo $this->_tpl_vars['incartdomain']; ?>
"><?php echo $this->_tpl_vars['incartdomain']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordercontinuebutton']; ?>
" />
</div>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['registerdomainenabled']): ?>
<div class="option">
<label><input type="radio" name="domainoption" value="register" id="selregister" /><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['cartregisterdomainchoice'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['companyname']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['companyname'])); ?>
</label>
<div class="domainreginput" id="domainregister">
www. <input type="text" id="registersld" size="30" value="<?php echo $this->_tpl_vars['sld']; ?>
" /> <select id="registertld">
<?php $_from = $this->_tpl_vars['registertlds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['listtld']):
?>
<option value="<?php echo $this->_tpl_vars['listtld']; ?>
"<?php if ($this->_tpl_vars['listtld'] == $this->_tpl_vars['tld']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['listtld']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['checkavailability']; ?>
" />
</div>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['transferdomainenabled']): ?>
<div class="option">
<label><input type="radio" name="domainoption" value="transfer" id="seltransfer" /><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['carttransferdomainchoice'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['companyname']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['companyname'])); ?>
</label>
<div class="domainreginput" id="domaintransfer">
www. <input type="text" id="transfersld" size="30" value="<?php echo $this->_tpl_vars['sld']; ?>
" /> <select id="transfertld">
<?php $_from = $this->_tpl_vars['transfertlds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['listtld']):
?>
<option value="<?php echo $this->_tpl_vars['listtld']; ?>
"<?php if ($this->_tpl_vars['listtld'] == $this->_tpl_vars['tld']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['listtld']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['checkavailability']; ?>
" />
</div>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['owndomainenabled']): ?>
<div class="option">
<label><input type="radio" name="domainoption" value="owndomain" id="selowndomain" /><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['cartexistingdomainchoice'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['companyname']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['companyname'])); ?>
</label>
<div class="domainreginput" id="domainowndomain">
www. <input type="text" id="owndomainsld" size="30" value="<?php echo $this->_tpl_vars['sld']; ?>
" /> . <input type="text" id="owndomaintld" size="5" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['tld'])) ? $this->_run_mod_handler('substr', true, $_tmp, 1) : substr($_tmp, 1)); ?>
" /> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordercontinuebutton']; ?>
" />
</div>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['subdomains']): ?>
<div class="option">
<label><input type="radio" name="domainoption" value="subdomain" id="selsubdomain" /><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['cartsubdomainchoice'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['companyname']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['companyname'])); ?>
</label>
<div class="domainreginput" id="domainsubdomain">
http:// <input type="text" id="subdomainsld" size="30" value="<?php echo $this->_tpl_vars['sld']; ?>
" /> <select id="subdomaintld"><?php $_from = $this->_tpl_vars['subdomains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subid'] => $this->_tpl_vars['subdomain']):
?><option value="<?php echo $this->_tpl_vars['subid']; ?>
"><?php echo $this->_tpl_vars['subdomain']; ?>
</option><?php endforeach; endif; unset($_from); ?></select> <input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordercontinuebutton']; ?>
" />
</div>
</div>
<?php endif; ?>
</div>

<?php if ($this->_tpl_vars['freedomaintlds']): ?><p>* <em><?php echo $this->_tpl_vars['LANG']['orderfreedomainregistration']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderfreedomainappliesto']; ?>
: <?php echo $this->_tpl_vars['freedomaintlds']; ?>
</em></p><?php endif; ?>

</form>

<div id="greyout"></div>
<div id="domainpopupcontainer">
<form id="domainfrm" onsubmit="completedomain();return false">
<div class="domainresults" id="domainresults"><img src="images/loading.gif" border="0" alt="Loading..." /></div>
</form>
</div>

<div id="prodconfigcontainer"></div>

</div>

<?php echo '
<script language="javascript">
jQuery(".domainreginput").hide();
jQuery(".domainoptions input:first").attr(\'checked\',\'checked\');
jQuery(".domainoptions input:first").parent().parent().addClass(\'optionselected\');
jQuery("#domain"+jQuery(".domainoptions input:first").val()).show();
jQuery(document).ready(function(){
    jQuery(".domainoptions input:radio").click(function(){
        jQuery(".domainoptions .option").removeClass(\'optionselected\');
        jQuery(this).parent().parent().addClass(\'optionselected\');
        jQuery("#domainresults").slideUp();
        jQuery(".domainreginput").hide();
        jQuery("#domain"+jQuery(this).val()).show();
    });
});
function checkdomain() {
    jQuery("#greyout").fadeIn();
    jQuery("#domainpopupcontainer").slideDown();
    var domainoption = jQuery(".domainoptions input:checked").val();
    var sld = jQuery("#"+domainoption+"sld").val();
    var tld = \'\';
    if (domainoption==\'incart\') var sld = jQuery("#"+domainoption+"sld option:selected").text();
    if (domainoption==\'subdomain\') var tld = jQuery("#"+domainoption+"tld option:selected").text();
    else var tld = jQuery("#"+domainoption+"tld").val();
    jQuery.post("cart.php", { ajax: 1, a: "domainoptions", sld: sld, tld: tld, checktype: domainoption },
    function(data){
        jQuery("#domainresults").html(data);
    });
}
function cancelcheck() {
    jQuery("#domainpopupcontainer").slideUp(\'slow\',function() {
        jQuery("#greyout").fadeOut();
        jQuery("#domainresults").html(\'<img src="images/loading.gif" border="0" alt="Loading..." />\');
    });
}
function completedomain() {
    jQuery("#domainresults").append(\'<img src="images/loading.gif" border="0" alt="Loading..." />\');
    jQuery.post("cart.php", \'ajax=1&a=add&pid='; ?>
<?php echo $this->_tpl_vars['pid']; ?>
<?php echo '&domainselect=1&\'+jQuery("#domainfrm").serialize(),
    function(data){
        if (data==\'\') {
            window.location=\'cart.php?a=view\';
        } else if (data==\'nodomains\') {
            jQuery("#domainpopupcontainer").slideUp(\'slow\',function() {
                jQuery("#greyout").fadeOut();
            });
        } else {
            jQuery("#prodconfigcontainer").replaceWith(data);
            jQuery("#domainpopupcontainer").slideUp(\'slow\',function() {
                jQuery("#greyout").fadeOut();
            });
            jQuery("#prodconfigcontainer").slideDown();
        }
    });
}
</script>
'; ?>
