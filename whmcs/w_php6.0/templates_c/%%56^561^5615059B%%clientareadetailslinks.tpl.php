<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:25
         compiled from webhoster/clientareadetailslinks.tpl */ ?>

<div class="tc-tabsbar arrow">
    <ul class="nav nav-tabs">
        <li <?php if ($this->_tpl_vars['clientareaaction'] == 'details'): ?>class="active"<?php endif; ?>><a href="clientarea.php?action=details"><?php echo $this->_tpl_vars['LANG']['clientareanavdetails']; ?>
</a></li>
        <?php if ($this->_tpl_vars['condlinks']['updatecc']): ?><li <?php if ($this->_tpl_vars['clientareaaction'] == 'creditcard'): ?>class="active"<?php endif; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>
?action=creditcard"><?php echo $this->_tpl_vars['LANG']['clientareanavccdetails']; ?>
</a></li><?php endif; ?>
        <li <?php if ($this->_tpl_vars['clientareaaction'] == 'contacts' || $this->_tpl_vars['clientareaaction'] == 'addcontact'): ?>class="active"<?php endif; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>
?action=contacts"><?php echo $this->_tpl_vars['LANG']['clientareanavcontacts']; ?>
</a></li>
        <li <?php if ($this->_tpl_vars['clientareaaction'] == 'changepw'): ?>class="active"<?php endif; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>
?action=changepw"><?php echo $this->_tpl_vars['LANG']['clientareanavchangepw']; ?>
</a></li>
        <?php if ($this->_tpl_vars['condlinks']['security']): ?><li <?php if ($this->_tpl_vars['clientareaaction'] == 'security'): ?>class="active"<?php endif; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>
?action=security"><?php echo $this->_tpl_vars['LANG']['clientareanavsecurity']; ?>
</a></li><?php endif; ?>
    </ul>
</div>

<div class="space-16"></div>


