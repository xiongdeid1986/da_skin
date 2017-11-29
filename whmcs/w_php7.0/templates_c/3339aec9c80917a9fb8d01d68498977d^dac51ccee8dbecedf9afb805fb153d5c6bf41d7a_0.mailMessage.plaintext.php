<?php
/* Smarty version 3.1.29, created on 2017-11-28 03:15:40
  from "mailMessage:plaintext" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cd4dc6a3344_23953719',
  'file_dependency' => 
  array (
    'dac51ccee8dbecedf9afb805fb153d5c6bf41d7a' => 
    array (
      0 => 'mailMessage:plaintext',
      1 => 1511838940,
      2 => 'mailMessage',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cd4dc6a3344_23953719 ($_smarty_tpl) {
$template = $_smarty_tpl;
?>Dear <?php echo $_smarty_tpl->tpl_vars['client_first_name']->value;?>
,


Thank you for creating a <?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
 account. Please review this email in its entirety as it contains important information.


Logging In


You can access our client area at <?php echo $_smarty_tpl->tpl_vars['whmcs_link']->value;?>



You will need your email address and the password you chose during signup to login.


If you created an account as part of placing a new order with us, you will shortly receive an order confirmation email.


Getting Support


If you need any help or assistance, you can access our support resources below.



<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
/knowledgebase.php
<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
/submitticket.php

<?php echo $_smarty_tpl->tpl_vars['signature']->value;?>



You are receiving this email because you recently created an account. If you did not do this, please contact us.<?php }
}
