<?php /* Smarty version 2.6.28, created on 2016-12-13 17:29:21
         compiled from blend/clientssummary.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'urlencode', 'blend/clientssummary.tpl', 58, false),array('function', 'cycle', 'blend/clientssummary.tpl', 66, false),)), $this); ?>
<div id="clientsummarycontainer">

<div class="clientsummaryactions">
<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['settingtaxexempt']; ?>
: <span id="taxstatus" class="csajaxtoggle" style="text-decoration:underline;cursor:pointer"><strong class="<?php if ($this->_tpl_vars['clientsdetails']['taxstatus'] == 'Yes'): ?>textgreen<?php else: ?>textred<?php endif; ?>"><?php echo $this->_tpl_vars['clientsdetails']['taxstatus']; ?>
</strong></span>
&nbsp;&nbsp;
<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['settingautocc']; ?>
: <span id="autocc" class="csajaxtoggle" style="text-decoration:underline;cursor:pointer"><strong class="<?php if ($this->_tpl_vars['clientsdetails']['autocc'] == 'Yes'): ?>textgreen<?php else: ?>textred<?php endif; ?>"><?php echo $this->_tpl_vars['clientsdetails']['autocc']; ?>
</strong></span>
&nbsp;&nbsp;
<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['settingreminders']; ?>
: <span id="overduenotices" class="csajaxtoggle" style="text-decoration:underline;cursor:pointer"><strong class="<?php if ($this->_tpl_vars['clientsdetails']['overduenotices'] == 'Yes'): ?>textgreen<?php else: ?>textred<?php endif; ?>"><?php echo $this->_tpl_vars['clientsdetails']['overduenotices']; ?>
</strong></span>
&nbsp;&nbsp;
<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['settinglatefees']; ?>
: <span id="latefees" class="csajaxtoggle" style="text-decoration:underline;cursor:pointer"><strong class="<?php if ($this->_tpl_vars['clientsdetails']['latefees'] == 'Yes'): ?>textgreen<?php else: ?>textred<?php endif; ?>"><?php echo $this->_tpl_vars['clientsdetails']['latefees']; ?>
</strong></span>
</div>
<div id="userdetails" style="font-size:18px;">#<span id="userId"><?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
</span> - <?php echo $this->_tpl_vars['clientsdetails']['firstname']; ?>
 <?php echo $this->_tpl_vars['clientsdetails']['lastname']; ?>
</div>

<?php if ($this->_tpl_vars['notes']): ?>
<div id="clientsimportantnotes">
<?php $_from = $this->_tpl_vars['notes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['note']):
?>
<div class="ticketstaffnotes">
    <table class="ticketstaffnotestable">
        <tr>
            <td><?php echo $this->_tpl_vars['note']['adminuser']; ?>
</td>
            <td align="right"><?php echo $this->_tpl_vars['note']['modified']; ?>
</td>
        </tr>
    </table>
    <div>
        <?php echo $this->_tpl_vars['note']['note']; ?>

        <div style="float:right;"><a href="clientsnotes.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=edit&id=<?php echo $this->_tpl_vars['note']['id']; ?>
"><img src="images/edit.gif" width="16" height="16" align="absmiddle" /></a></div>
    </div>
</div>
<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php $_from = $this->_tpl_vars['addons_html']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['addon_html']):
?>
<div style="margin-top:10px;"><?php echo $this->_tpl_vars['addon_html']; ?>
</div>
<?php endforeach; endif; unset($_from); ?>

<table width="100%">
<tr><td width="25%" valign="top">

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['infoheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<tr><td width="110"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['firstname']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['firstname']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['lastname']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['lastname']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['companyname']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['companyname']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['email']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['email']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['address1']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['address1']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['address2']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['address2']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['city']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['city']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['state']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['state']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['postcode']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['postcode']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['country']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['country']; ?>
 - <?php echo $this->_tpl_vars['clientsdetails']['countrylong']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['phonenumber']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['phonenumber']; ?>
</td></tr>
</table>
<ul>
<li><a id="summary-reset-password" href="clientssummary.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&resetpw=true&token=<?php echo $this->_tpl_vars['csrfToken']; ?>
"><img src="images/icons/resetpw.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clients']['resetsendpassword']; ?>
</a>
<li><a id="summary-cccard-details" href="#" onClick="openCCDetails();return false"><img src="images/icons/offlinecc.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['ccinfo']; ?>
</a>
<li><a id="summary-login-as-client" href="../dologin.php?username=<?php echo ((is_array($_tmp=$this->_tpl_vars['clientsdetails']['email'])) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
"><img src="images/icons/clientlogin.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['loginasclient']; ?>
</a>
</ul>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['contactsheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<?php $_from = $this->_tpl_vars['contacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['contact']):
?>
<tr class="<?php echo smarty_function_cycle(array('values' => ",altrow"), $this);?>
"><td align="center"><a href="clientscontacts.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&contactid=<?php echo $this->_tpl_vars['contact']['id']; ?>
"><?php echo $this->_tpl_vars['contact']['firstname']; ?>
 <?php echo $this->_tpl_vars['contact']['lastname']; ?>
</a> - <?php echo $this->_tpl_vars['contact']['email']; ?>
</td></tr>
<?php endforeach; else: ?>
<tr><td align="center"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['nocontacts']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
<ul>
<li><a href="clientscontacts.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&contactid=addnew"><img src="images/icons/clientsadd.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clients']['addcontact']; ?>
</a>
</ul>
</div>

</td><td width="25%" valign="top">

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['billingheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<tr><td width="110"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['paid']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numpaidinvoices']; ?>
 (<?php echo $this->_tpl_vars['stats']['paidinvoicesamount']; ?>
)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['status']['unpaid']; ?>
/<?php echo $this->_tpl_vars['_ADMINLANG']['status']['due']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numdueinvoices']; ?>
 (<?php echo $this->_tpl_vars['stats']['dueinvoicesbalance']; ?>
)</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['status']['cancelled']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numcancelledinvoices']; ?>
 (<?php echo $this->_tpl_vars['stats']['cancelledinvoicesamount']; ?>
)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['status']['refunded']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numrefundedinvoices']; ?>
 (<?php echo $this->_tpl_vars['stats']['refundedinvoicesamount']; ?>
)</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['status']['collections']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numcollectionsinvoices']; ?>
 (<?php echo $this->_tpl_vars['stats']['collectionsinvoicesamount']; ?>
)</td></tr>
<tr class="altrow"><td><strong><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['income']; ?>
</strong></td><td><strong><?php echo $this->_tpl_vars['stats']['income']; ?>
</strong></td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['creditbalance']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['creditbalance']; ?>
</td></tr>
</table>
<ul>
<li><a href="invoices.php?action=createinvoice&userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&token=<?php echo $this->_tpl_vars['csrfToken']; ?>
"><img src="images/icons/invoicesedit.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['create']; ?>
</a>
 <li><a href="#" onClick="showDialog('addfunds');return false"><img src="images/icons/addfunds.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['createaddfunds']; ?>
</a>
<li><a href="#" onClick="showDialog('geninvoices');return false"><img src="images/icons/ticketspredefined.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['geninvoices']; ?>
</a>
<li><a href="clientsbillableitems.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=manage"><img src="images/icons/billableitems.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['billableitems']['additem']; ?>
</a>
<li><a href="#" onClick="window.open('clientscredits.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
','','width=750,height=350,scrollbars=yes');return false"><img src="images/icons/income.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['managecredits']; ?>
</a>
<li><a href="quotes.php?action=manage&userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/quotes.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['quotes']['createnew']; ?>
</a>
</ul>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['otherinfoheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<tr><td width="110"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</td><td><?php echo $this->_tpl_vars['clientsdetails']['status']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['clientgroup']; ?>
</td><td><?php echo $this->_tpl_vars['clientgroup']['name']; ?>
</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['signupdate']; ?>
</td><td><?php echo $this->_tpl_vars['signupdate']; ?>
</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['clientfor']; ?>
</td><td><?php echo $this->_tpl_vars['clientfor']; ?>
</td></tr>
<tr><td width="110"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['lastlogin']; ?>
</td><td><?php echo $this->_tpl_vars['lastlogin']; ?>
</td></tr>
</table>
</div>

</td><td width="25%" valign="top">

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['title']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<tr><td width="140"><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['sharedhosting']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['productsnumactivehosting']; ?>
 (<?php echo $this->_tpl_vars['stats']['productsnumhosting']; ?>
 Total)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['resellerhosting']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['productsnumactivereseller']; ?>
 (<?php echo $this->_tpl_vars['stats']['productsnumreseller']; ?>
 Total)</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['server']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['productsnumactiveservers']; ?>
 (<?php echo $this->_tpl_vars['stats']['productsnumservers']; ?>
 Total)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['other']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['productsnumactiveother']; ?>
 (<?php echo $this->_tpl_vars['stats']['productsnumother']; ?>
 Total)</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['domains']['title']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numactivedomains']; ?>
 (<?php echo $this->_tpl_vars['stats']['numdomains']; ?>
 Total)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['stats']['acceptedquotes']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numacceptedquotes']; ?>
 (<?php echo $this->_tpl_vars['stats']['numquotes']; ?>
 Total)</td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['support']['supporttickets']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numactivetickets']; ?>
 (<?php echo $this->_tpl_vars['stats']['numtickets']; ?>
 Total)</td></tr>
<tr class="altrow"><td><?php echo $this->_tpl_vars['_ADMINLANG']['stats']['affiliatesignups']; ?>
</td><td><?php echo $this->_tpl_vars['stats']['numaffiliatesignups']; ?>
</td></tr>
</table>
<ul>
<li><a href="orders.php?clientid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/orders.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['vieworders']; ?>
</a>
<li><a href="ordersadd.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/ordersadd.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['orders']['addnew']; ?>
</a>
</ul>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['filesheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['file']):
?>
<tr class="<?php echo smarty_function_cycle(array('values' => ",altrow"), $this);?>
"><td align="center"><a href="../dl.php?type=f&id=<?php echo $this->_tpl_vars['file']['id']; ?>
"><img src="../images/file.png" align="absmiddle" vspace="1" border="0" /> <?php echo $this->_tpl_vars['file']['title']; ?>
</a> <?php if ($this->_tpl_vars['file']['adminonly']): ?>(<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['fileadminonly']; ?>
)<?php endif; ?> <img src="images/icons/delete.png" align="absmiddle" border="0" onClick="deleteFile('<?php echo $this->_tpl_vars['file']['id']; ?>
')" /></td></tr>
<?php endforeach; else: ?>
<tr><td align="center"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['nofiles']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
<ul>
<li><a href="#" id="addfile"><img src="images/icons/add.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['fileadd']; ?>
</a>
</ul>
<div id="addfileform" style="display:none;">
<img src="images/spacer.gif" width="1" height="4" /><br />
<form method="post" action="clientssummary.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=uploadfile" enctype="multipart/form-data">
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<tr><td width="40"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['filetitle']; ?>
</td><td class="fieldarea"><input type="text" name="title" style="width:90%" /></td></tr>
<tr><td><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['filename']; ?>
</td><td class="fieldarea"><input type="file" name="uploadfile" style="width:90%" /></td></tr>
<tr><td></td><td class="fieldarea"><input type="checkbox" name="adminonly" value="1" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['fileadminonly']; ?>
 &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" value="<?php echo $this->_tpl_vars['_ADMINLANG']['global']['submit']; ?>
" /></td></tr>
</table>
</form>
</div>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['emailsheading']; ?>
</div>
<table class="clientssummarystats" cellspacing="0" cellpadding="2">
<?php $_from = $this->_tpl_vars['lastfivemail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['email']):
?>
<tr class="<?php echo smarty_function_cycle(array('values' => ",altrow"), $this);?>
"><td align="center"><?php echo $this->_tpl_vars['email']['date']; ?>
 - <a href="#" onClick="window.open('clientsemails.php?&displaymessage=true&id=<?php echo $this->_tpl_vars['email']['id']; ?>
','','width=650,height=400,scrollbars=yes');return false"><?php echo $this->_tpl_vars['email']['subject']; ?>
</a></td></tr>
<?php endforeach; else: ?>
<tr><td align="center"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['noemails']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
</div>

</td><td width="25%" valign="top">

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['actionsheading']; ?>
</div>
<ul>
<?php $_from = $this->_tpl_vars['customactionlinks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['customactionlink']):
?>
<li><?php echo $this->_tpl_vars['customactionlink']; ?>
</li>
<?php endforeach; endif; unset($_from); ?>
<li><a href="reports.php?report=client_statement&userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/reports.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['accountstatement']; ?>
</a>
<li><a href="supporttickets.php?action=open&userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/ticketsopen.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['newticket']; ?>
</a>
<li><a href="supporttickets.php?view=any&client=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
"><img src="images/icons/ticketsother.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['viewtickets']; ?>
</a>
<li><a href="<?php if ($this->_tpl_vars['affiliateid']): ?>affiliates.php?action=edit&id=<?php echo $this->_tpl_vars['affiliateid']; ?>
<?php else: ?>clientssummary.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&activateaffiliate=true&token=<?php echo $this->_tpl_vars['csrfToken']; ?>
<?php endif; ?>"><img src="images/icons/affiliates.png" border="0" align="absmiddle" /> <?php if ($this->_tpl_vars['affiliateid']): ?><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['viewaffiliate']; ?>
<?php else: ?><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['activateaffiliate']; ?>
<?php endif; ?></a>
<li><a href="#" onClick="window.open('clientsmerge.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
','movewindow','width=500,height=280,top=100,left=100,scrollbars=1');return false"><img src="images/icons/clients.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['mergeclients']; ?>
</a>
<li><a href="#" onClick="closeClient();return false" style="color:#000000;"><img src="images/icons/delete.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['closeclient']; ?>
</a>
<li><a href="#" onClick="deleteClient();return false" style="color:#CC0000;"><img src="images/icons/delete.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['deleteclient']; ?>
</a>
</ul>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['sendemailheading']; ?>
</div>
<form action="clientsemails.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=send&type=general" method="post">
<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
">
<div align="center"><?php echo $this->_tpl_vars['messages']; ?>
 <input type="submit" value="<?php echo $this->_tpl_vars['_ADMINLANG']['global']['go']; ?>
" class="button"></div>
</form>
</div>

<div class="clientssummarybox">
<div class="title"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['adminnotes']; ?>
</div>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=savenotes">
<div align="center">
<textarea name="adminnotes" rows="6" style="width:90%;" /><?php echo $this->_tpl_vars['clientsdetails']['notes']; ?>
</textarea><br />
<input type="submit" value="<?php echo $this->_tpl_vars['_ADMINLANG']['global']['submit']; ?>
" class="button" />
</div>
</form>
</div>

</td></tr>
<tr><td colspan="4">

<p align="right"><input type="button" value="<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['statusfilter']; ?>
: <?php if ($this->_tpl_vars['statusfilterenabled']): ?><?php echo $this->_tpl_vars['_ADMINLANG']['global']['on']; ?>
<?php else: ?><?php echo $this->_tpl_vars['_ADMINLANG']['global']['off']; ?>
<?php endif; ?>" class="btn-small<?php if ($this->_tpl_vars['statusfilterenabled']): ?> btn-success<?php endif; ?>" onclick="toggleStatusFilter()" /></p>
<div id="statusfilter">
    <form>
        <div class="checkall">
            <label><input type="checkbox" id="statusfiltercheckall" onclick="checkAllStatusFilter()"<?php if (! $this->_tpl_vars['statusfilterenabled']): ?> checked<?php endif; ?> /> <?php echo $this->_tpl_vars['_ADMINLANG']['global']['checkall']; ?>
</label>
        </div>
        <table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
            <tr>
                <th></th>
            </tr>
<?php $_from = $this->_tpl_vars['itemstatuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['itemstatus'] => $this->_tpl_vars['statuslang']):
?>
            <tr>
                <td><label style="display:block;"><input type="checkbox" name="statusfilter[]" value="<?php echo $this->_tpl_vars['itemstatus']; ?>
" onclick="uncheckCheckAllStatusFilter()"<?php if (! in_array ( $this->_tpl_vars['itemstatus'] , $this->_tpl_vars['disabledstatuses'] )): ?> checked<?php endif; ?> /> <?php echo $this->_tpl_vars['statuslang']; ?>
</label></td>
            </tr>
<?php endforeach; endif; unset($_from); ?>
            <tr>
                <th></th>
            </tr>
        </table>
        <div class="applybtn">
            <input type="button" value="<?php echo $this->_tpl_vars['_ADMINLANG']['global']['apply']; ?>
" class="btn-small btn-primary" onclick="applyStatusFilter()" />
        </div>
    </form>
</div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&action=massaction">

<?php echo '<script language="javascript">
$(document).ready(function(){
    $("#prodsall").click(function () {
        $(".checkprods").attr("checked",this.checked);
    });
    $("#addonsall").click(function () {
        $(".checkaddons").attr("checked",this.checked);
    });
    $("#domainsall").click(function () {
        $(".checkdomains").attr("checked",this.checked);
    });
});
</script>'; ?>


<table width="100%" class="form">
<tr><td colspan="2" class="fieldarea" style="text-align:center;"><strong><?php echo $this->_tpl_vars['_ADMINLANG']['services']['title']; ?>
</strong></td></tr>
<tr><td align="center">

<div class="tablebg">
<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
<tr><th width="20"><input type="checkbox" id="prodsall" /></th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['id']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['product']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['amount']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['billingcycle']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['signupdate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['nextduedate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</th><th width="20"></th></tr>
<?php $_from = $this->_tpl_vars['productsummary']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['product']):
?>
<tr><td><input type="checkbox" name="selproducts[]" value="<?php echo $this->_tpl_vars['product']['id']; ?>
" class="checkprods" /></td><td><a href="clientsservices.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&id=<?php echo $this->_tpl_vars['product']['id']; ?>
"><?php echo $this->_tpl_vars['product']['idshort']; ?>
</a></td><td style="padding-left:5px;padding-right:5px"><?php echo $this->_tpl_vars['product']['dpackage']; ?>
 - <a href="http://<?php echo $this->_tpl_vars['product']['domain']; ?>
" target="_blank"><?php echo $this->_tpl_vars['product']['domain']; ?>
</a></td><td><?php echo $this->_tpl_vars['product']['amount']; ?>
</td><td><?php echo $this->_tpl_vars['product']['dbillingcycle']; ?>
</td><td><?php echo $this->_tpl_vars['product']['regdate']; ?>
</td><td><?php echo $this->_tpl_vars['product']['nextduedate']; ?>
</td><td><?php echo $this->_tpl_vars['product']['domainstatus']; ?>
</td><td><a href="clientsservices.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&id=<?php echo $this->_tpl_vars['product']['id']; ?>
"><img src="images/edit.gif" width="16" height="16" border="0" alt="Edit"></a></td></tr>
<?php endforeach; else: ?>
<tr><td colspan="9"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['norecordsfound']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
</div>

</td></tr></table>

<img src="images/spacer.gif" width="1" height="4" /><br />

<table width="100%" class="form">
<tr><td colspan="2" class="fieldarea" style="text-align:center;"><strong><?php echo $this->_tpl_vars['_ADMINLANG']['addons']['title']; ?>
</strong></td></tr>
<tr><td align="center">

<div class="tablebg">
<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
<tr><th width="20"><input type="checkbox" id="addonsall" /></th><th>ID</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['addons']['name']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['amount']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['billingcycle']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['signupdate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['nextduedate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</th><th width="20"></th></tr>
<?php $_from = $this->_tpl_vars['addonsummary']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['addon']):
?>
<tr><td><input type="checkbox" name="seladdons[]" value="<?php echo $this->_tpl_vars['addon']['id']; ?>
" class="checkaddons" /></td><td><a href="clientsservices.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&id=<?php echo $this->_tpl_vars['addon']['serviceid']; ?>
&aid=<?php echo $this->_tpl_vars['addon']['id']; ?>
"><?php echo $this->_tpl_vars['addon']['idshort']; ?>
</a></td><td style="padding-left:5px;padding-right:5px"><?php echo $this->_tpl_vars['addon']['addonname']; ?>
<br><?php echo $this->_tpl_vars['addon']['dpackage']; ?>
 - <a href="http://<?php echo $this->_tpl_vars['addon']['domain']; ?>
" target="_blank"><?php echo $this->_tpl_vars['addon']['domain']; ?>
</a></td><td><?php echo $this->_tpl_vars['addon']['amount']; ?>
</td><td><?php echo $this->_tpl_vars['addon']['dbillingcycle']; ?>
</td><td><?php echo $this->_tpl_vars['addon']['regdate']; ?>
</td><td><?php echo $this->_tpl_vars['addon']['nextduedate']; ?>
</td><td><?php echo $this->_tpl_vars['addon']['status']; ?>
</td><td><a href="clientsservices.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&id=<?php echo $this->_tpl_vars['addon']['serviceid']; ?>
&aid=<?php echo $this->_tpl_vars['addon']['id']; ?>
"><img src="images/edit.gif" width="16" height="16" border="0" alt="Edit"></a></td></tr>
<?php endforeach; else: ?>
<tr><td colspan="9"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['norecordsfound']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
</div>

</td></tr></table>

<img src="images/spacer.gif" width="1" height="4" /><br />

<table width="100%" class="form">
<tr><td colspan="2" class="fieldarea" style="text-align:center;"><strong><?php echo $this->_tpl_vars['_ADMINLANG']['domains']['title']; ?>
</strong></td></tr>
<tr><td align="center">

<div class="tablebg">
<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
<tr><th width="20"><input type="checkbox" id="domainsall" /></th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['id']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['domain']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['registrar']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['regdate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['nextduedate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['expirydate']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</th><th width="20"></th></tr>
<?php $_from = $this->_tpl_vars['domainsummary']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['domain']):
?>
<tr><td><input type="checkbox" name="seldomains[]" value="<?php echo $this->_tpl_vars['domain']['id']; ?>
" class="checkdomains" /></td><td><a href="clientsdomains.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&domainid=<?php echo $this->_tpl_vars['domain']['id']; ?>
"><?php echo $this->_tpl_vars['domain']['idshort']; ?>
</a></td><td style="padding-left:5px;padding-right:5px"><a href="http://<?php echo $this->_tpl_vars['domain']['domain']; ?>
" target="_blank"><?php echo $this->_tpl_vars['domain']['domain']; ?>
</a></td><td><?php echo $this->_tpl_vars['domain']['registrar']; ?>
</td><td><?php echo $this->_tpl_vars['domain']['registrationdate']; ?>
</td><td><?php echo $this->_tpl_vars['domain']['nextduedate']; ?>
</td><td><?php echo $this->_tpl_vars['domain']['expirydate']; ?>
</td><td><?php echo $this->_tpl_vars['domain']['status']; ?>
</td><td><a href="clientsdomains.php?userid=<?php echo $this->_tpl_vars['clientsdetails']['userid']; ?>
&domainid=<?php echo $this->_tpl_vars['domain']['id']; ?>
"><img src="images/edit.gif" width="16" height="16" border="0" alt="Edit"></a></td></tr>
<?php endforeach; else: ?>
<tr><td colspan="9"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['norecordsfound']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
</div>

</td></tr></table>

<img src="images/spacer.gif" width="1" height="4" /><br />

<table width="100%" class="form">
<tr><td colspan="2" class="fieldarea" style="text-align:center;"><strong><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['currentquotes']; ?>
</strong></td></tr>
<tr><td align="center">

<div class="tablebg">
<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
<tr><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['id']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['subject']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['date']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['total']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['validuntil']; ?>
</th><th><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</th><th width="20"></th></tr>
<?php $_from = $this->_tpl_vars['quotes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['quote']):
?>
<tr><td><?php echo $this->_tpl_vars['quote']['id']; ?>
</td><td style="padding-left:5px;padding-right:5px"><?php echo $this->_tpl_vars['quote']['subject']; ?>
</td><td><?php echo $this->_tpl_vars['quote']['datecreated']; ?>
</td><td><?php echo $this->_tpl_vars['quote']['total']; ?>
</td><td><?php echo $this->_tpl_vars['quote']['validuntil']; ?>
</td><td><?php echo $this->_tpl_vars['quote']['stage']; ?>
</td><td><a href="quotes.php?action=manage&id=<?php echo $this->_tpl_vars['quote']['id']; ?>
"><img src="images/edit.gif" width="16" height="16" border="0" alt="Edit"></a></td></tr>
<?php endforeach; else: ?>
<tr><td colspan="7"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['norecordsfound']; ?>
</td></tr>
<?php endif; unset($_from); ?>
</table>
</div>

</td></tr></table>

<p align="center"><input type="button" value="<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['massupdateitems']; ?>
" class="button" onclick="$('#massupdatebox').slideToggle()" /> <input type="submit" name="inv" value="<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['invoiceselected']; ?>
" class="button" /> <input type="submit" name="del" value="<?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['deleteselected']; ?>
" class="button" /></p>

<div id="massupdatebox" style="width:75%;background-color:#f7f7f7;border:1px dashed #cccccc;padding:10px;margin-left:auto;margin-right:auto;display:none;">
<h2 style="text-align:center;margin:0 0 10px 0"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['massupdateitems']; ?>
</h2>
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
<tr><td width="15%" class="fieldlabel" nowrap><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['firstpaymentamount']; ?>
</td><td class="fieldarea"><input type="text" size="20" name="firstpaymentamount" /></td><td width="15%" class="fieldlabel" nowrap><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['recurringamount']; ?>
</td><td class="fieldarea"><input type="text" size="20" name="recurringamount" /></td></tr>
<tr><td class="fieldlabel" width="15%"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['nextduedate']; ?>
</td><td class="fieldarea"><input type="text" size="20" name="nextduedate" class="datepick" /> &nbsp;&nbsp; <input type="checkbox" name="proratabill" id="proratabill" /> <label for="proratabill"><?php echo $this->_tpl_vars['_ADMINLANG']['clientsummary']['createproratainvoice']; ?>
</label></td><td width="15%" class="fieldlabel"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['billingcycle']; ?>
</td><td class="fieldarea"><select name="billingcycle"><option value="">- <?php echo $this->_tpl_vars['_ADMINLANG']['global']['nochange']; ?>
 -</option><option value="Free Account"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['free']; ?>
</option><option value="One Time"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['onetime']; ?>
</option><option value="Monthly"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['monthly']; ?>
</option><option value="Quarterly"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['quarterly']; ?>
</option><option value="Semi-Annually"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['semiannually']; ?>
</option><option value="Annually"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['annually']; ?>
</option><option value="Biennially"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['biennially']; ?>
</option><option value="Triennially"><?php echo $this->_tpl_vars['_ADMINLANG']['billingcycles']['triennially']; ?>
</option></select></td></tr>
<tr><td class="fieldlabel" width="15%"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['paymentmethod']; ?>
</td><td class="fieldarea"><?php echo $this->_tpl_vars['paymentmethoddropdown']; ?>
</td><td class="fieldlabel" width="15%"><?php echo $this->_tpl_vars['_ADMINLANG']['fields']['status']; ?>
</td><td class="fieldarea"><select name="status"><option value="">- <?php echo $this->_tpl_vars['_ADMINLANG']['global']['nochange']; ?>
 -</option><option value="Pending"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['pending']; ?>
</option><option value="Active"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['active']; ?>
</option><option value="Suspended"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['suspended']; ?>
</option><option value="Terminated"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['terminated']; ?>
</option><option value="Cancelled"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['cancelled']; ?>
</option><option value="Fraud"><?php echo $this->_tpl_vars['_ADMINLANG']['status']['fraud']; ?>
</option></select></td></tr>
<tr><td class="fieldlabel" width="15%"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['modulecommands']; ?>
</td><td class="fieldarea" colspan="3"><input type="submit" name="masscreate" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['create']; ?>
" class="button" /> <input type="submit" name="masssuspend" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['suspend']; ?>
" class="button" /> <input type="submit" name="massunsuspend" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['unsuspend']; ?>
" class="button" /> <input type="submit" name="massterminate" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['terminate']; ?>
" class="button" /> <input type="submit" name="masschangepackage" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['changepackage']; ?>
" class="button" /> <input type="submit" name="masschangepw" value="<?php echo $this->_tpl_vars['_ADMINLANG']['modulebuttons']['changepassword']; ?>
" class="button" /></td></tr>
<tr><td class="fieldlabel" width="15%"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['overrideautosusp']; ?>
</td><td class="fieldarea" colspan="3"><input type="checkbox" name="overideautosuspend" id="overridesuspend" /> <label for="overridesuspend"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['nosuspenduntil']; ?>
</label> <input type="text" name="overidesuspenduntil" class="datepick" /></td></tr>
</table>
<br />
<div align="center"><input type="submit" name="massupdate" value="<?php echo $this->_tpl_vars['_ADMINLANG']['global']['submit']; ?>
" /></div>
</div>

</form>

</td></tr></table>

</div>