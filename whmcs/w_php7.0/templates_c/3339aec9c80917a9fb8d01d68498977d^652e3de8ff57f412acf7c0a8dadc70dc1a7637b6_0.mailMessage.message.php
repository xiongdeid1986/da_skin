<?php
/* Smarty version 3.1.29, created on 2017-11-28 03:15:40
  from "mailMessage:message" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cd4dc69bb16_31872618',
  'file_dependency' => 
  array (
    '652e3de8ff57f412acf7c0a8dadc70dc1a7637b6' => 
    array (
      0 => 'mailMessage:message',
      1 => 1511838940,
      2 => 'mailMessage',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cd4dc69bb16_31872618 ($_smarty_tpl) {
$template = $_smarty_tpl;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['charset']->value;?>
" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <style type="text/css">
            [EmailCSS]
        </style>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <table border="0" cellpadding="0" cellspacing="0" id="templateContainer">
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                                        <tr>
                                            <td valign="top" class="headerContent">
                                                <a href="<?php echo $_smarty_tpl->tpl_vars['company_domain']->value;?>
">
                                                    <img src="<?php echo $_smarty_tpl->tpl_vars['company_logo_url']->value;?>
" style="max-width:600px;padding:20px" id="headerImage" alt="<?php echo $_smarty_tpl->tpl_vars['company_name']->value;?>
" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                        <tr>
                                            <td valign="top" class="bodyContent">
<!-- message header end --><p>Dear <?php echo $_smarty_tpl->tpl_vars['client_first_name']->value;?>
,</p>
<p>Thank you for creating a <?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
 account. Please review this email in its entirety as it contains important information.</p>
<p><strong>Logging In</strong></p>
<p>You can access our client area at <?php echo $_smarty_tpl->tpl_vars['whmcs_link']->value;?>
</p>
<p>You will need your email address and the password you chose during signup to login.</p>
<p>If you created an account as part of placing a new order with us, you will shortly receive an order confirmation email.</p>
<p><strong>Getting Support</strong></p>
<p>If you need any help or assistance, you can access our support resources below.</p>
<ul>
<li><a href="<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
/knowledgebase.php">Knowledgebase</a></li>
<li><a href="<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
/submitticket.php">Submit a Ticket</a></li>
</ul>
<p><?php echo $_smarty_tpl->tpl_vars['signature']->value;?>
</p>
<p><small>You are receiving this email because you recently created an account. If you did not do this, please contact us.</small></p><!-- message footer start -->
</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter">
                                        <tr>
                                            <td valign="top" class="footerContent">
                                                 <a href="<?php echo $_smarty_tpl->tpl_vars['company_domain']->value;?>
">visit our website</a>
                                                <span class="hide-mobile"> | </span>
                                                <a href="<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
">log in to your account</a>
                                                <span class="hide-mobile"> | </span>
                                                <a href="<?php echo $_smarty_tpl->tpl_vars['whmcs_url']->value;?>
submitticket.php">get support</a> <br />
                                                Copyright © <?php echo $_smarty_tpl->tpl_vars['company_name']->value;?>
, All rights reserved.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html><?php }
}
