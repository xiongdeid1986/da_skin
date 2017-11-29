<?php
/* Smarty version 3.1.29, created on 2017-11-28 00:54:19
  from "3252bfe29a2f326a8861f625d7b9649c2da7af27" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cb3bbd94f21_90703914',
  'file_dependency' => 
  array (
    '3252bfe29a2f326a8861f625d7b9649c2da7af27' => 
    array (
      0 => '3252bfe29a2f326a8861f625d7b9649c2da7af27',
      1 => 0,
      2 => 'string',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cb3bbd94f21_90703914 ($_smarty_tpl) {
$template = $_smarty_tpl;
?>
<div class="alert alert-warning info-alert"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.gatewaysIntro"),$_smarty_tpl);?>
</div>

<div class="clearfix">
    <div style="float:left;"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/wizard/paypal.png" alt="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.paypal"),$_smarty_tpl);?>
"></div>
    <div style="float:left;padding:20px;width:390px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.paypalDescription"),$_smarty_tpl);?>
</div>
</div>

<div class="row">
    <div class="col-sm-3 text-right">
        <label>
            <input id="checkboxPayPalEnable" type="checkbox" name="PayPalEnable" checked>
            <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.enable"),$_smarty_tpl);?>

        </label>
    </div>
    <div class="col-sm-9">
        <input id="fieldPayPalEmailAddress" type="email" name="PayPalEmailAddress" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.paypalEnterEmail"),$_smarty_tpl);?>
">
    </div>
</div>
<div style="padding:15px 0 20px;font-size:0.9em;font-style:italic;">
    <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.paypalDontHaveAccount"),$_smarty_tpl);?>

</div>

<div class="credit-card clearfix<?php if (!in_array($_smarty_tpl->tpl_vars['DefaultCountry']->value,$_smarty_tpl->tpl_vars['allowedCcSignupCountries']->value)) {?> hidden<?php }?>">
    <div style="float:left;"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/wizard/creditcard.png" alt="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCard"),$_smarty_tpl);?>
"></div>
    <div style="float:left;padding:20px;width:390px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardDescription"),$_smarty_tpl);?>
</div>
</div>

<div class="row bottom-margin-5 credit-card<?php if (!in_array($_smarty_tpl->tpl_vars['DefaultCountry']->value,$_smarty_tpl->tpl_vars['allowedCcSignupCountries']->value)) {?> hidden<?php }?>">
    <div class="col-sm-3 text-right">
        <label>
            <input id="checkboxCreditCardEnable" type="checkbox" name="CreditCardEnable" <?php if (in_array($_smarty_tpl->tpl_vars['DefaultCountry']->value,$_smarty_tpl->tpl_vars['allowedCcSignupCountries']->value)) {?> checked<?php }?>>
            <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.enable"),$_smarty_tpl);?>

        </label>
    </div>
    <div class="col-sm-9">
        <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardApply"),$_smarty_tpl);?>

    </div>
</div>

<div class="clearfix" style="margin-top:22px;">
    <div style="float:left;"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/wizard/mailin.png" alt="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.mailIn"),$_smarty_tpl);?>
"></div>
    <div style="float:left;padding:10px 20px;width:390px;">
        <label>
            <input id="checkboxMailInEnable" type="checkbox" name="MailInEnable" checked>
            <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.enable"),$_smarty_tpl);?>

        </label>
        <div style="display:inline-block;padding-left:25px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.mailInDescription"),$_smarty_tpl);?>
</div>
    </div>
</div><?php }
}
