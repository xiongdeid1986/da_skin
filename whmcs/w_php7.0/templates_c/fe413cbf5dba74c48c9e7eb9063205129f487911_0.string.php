<?php
/* Smarty version 3.1.29, created on 2017-11-28 00:54:19
  from "fe413cbf5dba74c48c9e7eb9063205129f487911" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cb3bbdc5558_07575800',
  'file_dependency' => 
  array (
    'fe413cbf5dba74c48c9e7eb9063205129f487911' => 
    array (
      0 => 'fe413cbf5dba74c48c9e7eb9063205129f487911',
      1 => 0,
      2 => 'string',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cb3bbdc5558_07575800 ($_smarty_tpl) {
$template = $_smarty_tpl;
?>
<div class="alert alert-info info-alert"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.sellingDomains"),$_smarty_tpl);?>
</div>

<div class="form-horizontal">
    <div class="form-group">
        <label for="inputDomainsEnable" class="col-sm-3 control-label"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.enableDomains"),$_smarty_tpl);?>
</label>
        <div class="col-sm-9">
            <input id="inputDomainsEnable" type="checkbox" name="EnableDomains" value="1" data-size="mini" checked />
        </div>
    </div>
    <div class="form-group">
        <label for="inputExtensionCom" class="col-sm-3 control-label"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.autoSetupTlds"),$_smarty_tpl);?>
</label>
        <div class="col-sm-9">
            <div style="help-block"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.setupExtensions"),$_smarty_tpl);?>
<br /><small><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.extensionsAddMoreLater"),$_smarty_tpl);?>
</small></div>
            <div class="bottom-margin-5">
                <label class="checkbox-inline">
                    <input type="checkbox" name="Extensions[]" value=".com" id="inputExtensionCom" checked> .com
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="Extensions[]" value=".net" checked> .net
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="Extensions[]" value=".org" checked> .org
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="Extensions[]" value=".biz" checked> .biz
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="Extensions[]" value=".info" checked> .info
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputDomainPrice" class="col-sm-3 control-label"><?php echo WHMCS\Smarty::langFunction(array('key'=>"fields.price"),$_smarty_tpl);?>
</label>
        <div class="col-sm-9">
            <input type="text" name="DomainPrice" id="inputDomainPrice" placeholder="14.95" value="14.95" class="form-control input-100 input-inline" /> <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.extensionsChangeLater"),$_smarty_tpl);?>

        </div>
    </div>
</div>

<div class="alert alert-warning info-alert" style="margin:20px 0 5px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.domainRegistrarPromo"),$_smarty_tpl);?>
</div>

<div class="clearfix">
    <div style="float:left;"><img src="<?php echo $_smarty_tpl->tpl_vars['BASE_PATH_IMG']->value;?>
/wizard/enom.png" alt="<?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.registrarEnom"),$_smarty_tpl);?>
"></div>
    <div style="float:left;padding:20px;width:390px;"><?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.registrarEnomDescription"),$_smarty_tpl);?>
</div>
</div>

<div class="row bottom-margin-5">
    <div class="col-sm-3 text-right">
        <label>
            <input id="checkboxEnomEnable" type="checkbox" name="EnomEnable" checked> <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.enable"),$_smarty_tpl);?>

        </label>
    </div>
    <div class="col-sm-9">
        <?php echo WHMCS\Smarty::langFunction(array('key'=>"wizard.createFreeEnomAccount"),$_smarty_tpl);?>

    </div>
</div>
<?php }
}
