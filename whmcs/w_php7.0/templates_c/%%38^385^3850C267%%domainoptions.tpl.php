<?php /* Smarty version 2.6.28, created on 2016-12-17 18:00:08
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/domainoptions.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'sprintf2', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/orderforms/modern/domainoptions.tpl', 15, false),)), $this); ?>
<?php if ($this->_tpl_vars['invalid']): ?>
    <div class="domaininvalid"><?php if ($this->_tpl_vars['reason']): ?><?php echo $this->_tpl_vars['reason']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['cartdomaininvalid']; ?>
<?php endif; ?></div>
    <p align="center"><a href="#" onclick="cancelcheck();return false"><?php echo $this->_tpl_vars['LANG']['carttryanotherdomain']; ?>
</a></p>
<?php elseif ($this->_tpl_vars['alreadyindb']): ?>
    <div class="domaininvalid"><?php echo $this->_tpl_vars['LANG']['cartdomainexists']; ?>
</div>
    <p align="center"><a href="#" onclick="cancelcheck();return false"><?php echo $this->_tpl_vars['LANG']['carttryanotherdomain']; ?>
</a></p>
<?php else: ?>

<?php if ($this->_tpl_vars['checktype'] == 'register' && $this->_tpl_vars['regenabled']): ?>

<input type="hidden" name="domainoption" value="register" />

<?php if ($this->_tpl_vars['status'] == 'available'): ?>

<div class="domainavailable"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['cartcongratsdomainavailable'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['domain']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['domain'])); ?>
</div>
<input type="hidden" name="domains[]" value="<?php echo $this->_tpl_vars['domain']; ?>
" />
<div class="domainregperiod"><?php echo $this->_tpl_vars['LANG']['cartregisterhowlong']; ?>
 <select name="domainsregperiod[<?php echo $this->_tpl_vars['domain']; ?>
]" id="regperiod"><?php $_from = $this->_tpl_vars['regoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['period'] => $this->_tpl_vars['regoption']):
?><?php if ($this->_tpl_vars['regoption']['register']): ?><option value="<?php echo $this->_tpl_vars['period']; ?>
"><?php echo $this->_tpl_vars['period']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
 @ <?php echo $this->_tpl_vars['regoption']['register']; ?>
</option><?php endif; ?><?php endforeach; endif; unset($_from); ?></select></div>

<?php $this->assign('continueok', true); ?>

<?php elseif ($this->_tpl_vars['status'] == 'unavailable'): ?>

<div class="domainunavailable"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['cartdomaintaken'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['domain']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['domain'])); ?>
</div>
<p align="center"><a href="#" onclick="cancelcheck();return false"><?php echo $this->_tpl_vars['LANG']['carttryanotherdomain']; ?>
</a></p>

<?php endif; ?>

<?php elseif ($this->_tpl_vars['checktype'] == 'transfer' && $this->_tpl_vars['transferenabled']): ?>

<input type="hidden" name="domainoption" value="transfer" />

<?php if ($this->_tpl_vars['status'] == 'available'): ?>

<div class="domainunavailable"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['carttransfernotregistered'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['domain']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['domain'])); ?>
</div>
<p align="center"><a href="#" onclick="cancelcheck();return false"><?php echo $this->_tpl_vars['LANG']['carttryanotherdomain']; ?>
</a></p>

<?php elseif ($this->_tpl_vars['status'] == 'unavailable'): ?>

<div class="domainavailable"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['carttransferpossible'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['domain'], $this->_tpl_vars['transferprice']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['domain'], $this->_tpl_vars['transferprice'])); ?>
</div>
<input type="hidden" name="domains[]" value="<?php echo $this->_tpl_vars['domain']; ?>
" />
<input type="hidden" name="domainsregperiod[<?php echo $this->_tpl_vars['domain']; ?>
]" value="<?php echo $this->_tpl_vars['transferterm']; ?>
" />

<?php $this->assign('continueok', true); ?>

<?php endif; ?>

<?php elseif ($this->_tpl_vars['checktype'] == 'owndomain' || $this->_tpl_vars['checktype'] == 'subdomain'): ?>

<input type="hidden" name="domainoption" value="<?php echo $this->_tpl_vars['checktype']; ?>
" />
<input type="hidden" name="sld" value="<?php echo $this->_tpl_vars['sld']; ?>
" />
<input type="hidden" name="tld" value="<?php echo $this->_tpl_vars['tld']; ?>
" />
<script language="javascript">
completedomain();
</script>

<?php endif; ?>

<?php if ($this->_tpl_vars['othersuggestions']): ?>

<div class="domainsuggestions"><?php echo $this->_tpl_vars['LANG']['cartotherdomainsuggestions']; ?>
</div>

<table align="center" cellspacing="1" class="domainsuggestions">
<tr><th width="50"></th><th><?php echo $this->_tpl_vars['LANG']['domainname']; ?>
</th><th><?php echo $this->_tpl_vars['LANG']['clientarearegistrationperiod']; ?>
</th></tr>
<?php $_from = $this->_tpl_vars['othersuggestions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['other']):
?>
<tr><td><input type="checkbox" name="domains[]" value="<?php echo $this->_tpl_vars['other']['domain']; ?>
" /></td><td><?php echo $this->_tpl_vars['other']['domain']; ?>
</td><td><select name="domainsregperiod[<?php echo $this->_tpl_vars['other']['domain']; ?>
]"><?php $_from = $this->_tpl_vars['other']['regoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['period'] => $this->_tpl_vars['regoption']):
?><?php if ($this->_tpl_vars['regoption']['register']): ?><option value="<?php echo $this->_tpl_vars['period']; ?>
"><?php echo $this->_tpl_vars['period']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
 @ <?php echo $this->_tpl_vars['regoption']['register']; ?>
</option><?php endif; ?><?php endforeach; endif; unset($_from); ?></select></td></tr>
<?php endforeach; endif; unset($_from); ?>
</table>

<?php $this->assign('continueok', true); ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['continueok']): ?><p align="center"><input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordercontinuebutton']; ?>
" /></p><?php endif; ?>

<?php endif; ?>