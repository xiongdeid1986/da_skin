<?php /* Smarty version 2.6.28, created on 2016-12-13 23:53:07
         compiled from webhoster/clientarearecordslimit.tpl */ ?>

<div class="space-6"></div>
<div class="pull-right">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=<?php echo $this->_tpl_vars['clientareaaction']; ?>
" /><?php echo $this->_tpl_vars['LANG']['resultsperpage']; ?>
: 
    <select name="itemlimit" onchange="submit()" style="width: 59px;">
        <option value="10"<?php if ($this->_tpl_vars['itemlimit'] == 10): ?> selected<?php endif; ?>>10</option>
        <option value="25"<?php if ($this->_tpl_vars['itemlimit'] == 25): ?> selected<?php endif; ?>>25</option>
        <option value="50"<?php if ($this->_tpl_vars['itemlimit'] == 50): ?> selected<?php endif; ?>>50</option>
        <option value="100"<?php if ($this->_tpl_vars['itemlimit'] == 100): ?> selected<?php endif; ?>>100</option>
    </select>
    </form>
</div>