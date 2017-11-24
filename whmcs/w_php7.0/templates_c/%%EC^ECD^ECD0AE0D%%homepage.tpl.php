<?php /* Smarty version 2.6.28, created on 2016-12-13 17:29:01
         compiled from blend/homepage.tpl */ ?>
<?php if ($this->_tpl_vars['viewincometotals']): ?><div id="incometotals" style="float:right;position:relative;top:-35px;font-size:18px;"><a href="transactions.php"><img src="images/icons/transactions.png" align="absmiddle" border="0"> <b><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['income']; ?>
</b></a> <img src="images/loading.gif" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['global']['loading']; ?>
</div><?php endif; ?>

<?php if ($this->_tpl_vars['maintenancemode']): ?>
<div class="errorbox" style="font-size:14px;">
<?php echo $this->_tpl_vars['_ADMINLANG']['home']['maintenancemode']; ?>

</div>
<br />
<?php endif; ?>

<?php echo $this->_tpl_vars['infobox']; ?>


<p><?php echo $this->_tpl_vars['_ADMINLANG']['global']['welcomeback']; ?>
 <?php echo $this->_tpl_vars['admin_username']; ?>
!</p>

<?php $_from = $this->_tpl_vars['addons_html']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['addon_html']):
?>
<div style="margin-bottom:15px;"><?php echo $this->_tpl_vars['addon_html']; ?>
</div>
<?php endforeach; endif; unset($_from); ?>

<div class="homecolumn" id="homecol1">

    <div class="homewidget" id="sysinfo">
        <div class="widget-header"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['systeminfo']; ?>
</div>
        <div class="widget-content">
<table width="100%">
<tr><td width="20%" style="text-align:right;padding-right:5px;"><?php echo $this->_tpl_vars['_ADMINLANG']['license']['regto']; ?>
</td><td width="35%"><?php echo $this->_tpl_vars['licenseinfo']['registeredname']; ?>
</td><td width="10%" style="text-align:right;padding-right:5px;"><?php echo $this->_tpl_vars['_ADMINLANG']['license']['expires']; ?>
</td><td width="35%"><?php echo $this->_tpl_vars['licenseinfo']['expires']; ?>
</td></tr>
<tr><td style="text-align:right;padding-right:5px;"><?php echo $this->_tpl_vars['_ADMINLANG']['license']['type']; ?>
</td><td><?php echo $this->_tpl_vars['licenseinfo']['productname']; ?>
</td><td style="text-align:right;padding-right:5px;"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['version']; ?>
</td><td><?php echo $this->_tpl_vars['licenseinfo']['currentversion']; ?>
<?php if ($this->_tpl_vars['licenseinfo']['updateavailable']): ?> <span class="textred"><b><?php echo $this->_tpl_vars['_ADMINLANG']['license']['updateavailable']; ?>
</b></span><?php endif; ?></td></tr>
<tr><td style="text-align:right;padding-right:5px;"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['staffonline']; ?>
</td><td colspan="3"><?php echo $this->_tpl_vars['adminsonline']; ?>
</td></tr>
</table>
        </div>
    </div>

<?php $_from = $this->_tpl_vars['widgets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['widget']):
?>
    <div class="homewidget" id="<?php echo $this->_tpl_vars['widget']['name']; ?>
">
        <div class="widget-header"><?php echo $this->_tpl_vars['widget']['title']; ?>
</div>
        <div class="widget-content">
            <?php echo $this->_tpl_vars['widget']['content']; ?>

        </div>
    </div>
<?php endforeach; endif; unset($_from); ?>

</div>

<div class="homecolumn" id="homecol2">

</div>

<div style="clear:both;"></div>

<div id="geninvoices" title="<?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['geninvoices']; ?>
">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span><?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['geninvoicessendemails']; ?>
</p>
</div>
<div id="cccapture" title="<?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['attemptcccaptures']; ?>
">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span><?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['attemptcccapturessure']; ?>
</p>
</div>