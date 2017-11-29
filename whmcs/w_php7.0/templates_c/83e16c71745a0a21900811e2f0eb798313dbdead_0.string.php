<?php
/* Smarty version 3.1.29, created on 2017-11-28 00:54:19
  from "83e16c71745a0a21900811e2f0eb798313dbdead" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cb3bbdae4f3_69904642',
  'file_dependency' => 
  array (
    '83e16c71745a0a21900811e2f0eb798313dbdead' => 
    array (
      0 => '83e16c71745a0a21900811e2f0eb798313dbdead',
      1 => 0,
      2 => 'string',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cb3bbdae4f3_69904642 ($_smarty_tpl) {
$template = $_smarty_tpl;
?>
<div class="clearfix">
    <div style="float:left;"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/wizard/creditcard.png" alt="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCard"),$_smarty_tpl);?>
"></div>
    <div style="float:left;padding:20px;width:390px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardSignup"),$_smarty_tpl);?>
</div>
</div>

<p><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardSignupIntro"),$_smarty_tpl);?>
</p>

<div class="signup-frm">
    <div class="alert alert-warning info-alert"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardSignupContact"),$_smarty_tpl);?>
</div>
    
    <div class="row bottom-margin-5">
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="name" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderYourName"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="email" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderEmail"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="address" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderAddress"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="city" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderCity"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="state" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderState"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="postcode" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderPostcode"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="country" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderCountry"),$_smarty_tpl);?>
" />
        </div>
        <div class="col-sm-6 bottom-margin-5">
            <input type="text" name="phone" class="form-control" placeholder="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.placeholderPhoneNumber"),$_smarty_tpl);?>
" />
        </div>
    </div>
    
    <p class="small"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardAgreeInfoSharing"),$_smarty_tpl);?>
</p>
</div>

<div class="signup-frm-success hidden">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="alert alert-success text-center">
                <h2><i class="fa fa-check"></i> <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardApplicationStarted"),$_smarty_tpl);?>
</h2>
                <p>
                    <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.creditCardApplicationNextSteps"),$_smarty_tpl);?>

                </p>
            </div>
        </div>
    </div>
</div>
<?php }
}
