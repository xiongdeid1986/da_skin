<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:16
         compiled from webhoster/supportticketsubmit-customFields.tpl */ ?>

<?php $_from = $this->_tpl_vars['customfields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['customfield']):
?>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="customfield<?php echo $this->_tpl_vars['customfield']['id']; ?>
"><?php echo $this->_tpl_vars['customfield']['name']; ?>
</label>
        <div class="col-sm-9">
            <?php echo $this->_tpl_vars['customfield']['input']; ?>
 <?php echo $this->_tpl_vars['customfield']['description']; ?>

        </div>
    </div>
<?php endforeach; endif; unset($_from); ?>