<?php /* Smarty version 2.6.28, created on 2016-12-13 17:28:27
         compiled from blend/header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'blend/header.tpl', 29, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_tpl_vars['charset']; ?>
" />
<title>WHMCS - <?php echo $this->_tpl_vars['pagetitle']; ?>
</title>
<link href="templates/<?php echo $this->_tpl_vars['template']; ?>
/style.css" rel="stylesheet" type="text/css" />
<link href="../includes/jscript/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/jscript/jquery.js"></script>
<script type="text/javascript" src="../includes/jscript/jqueryui.js"></script>
<script type="text/javascript" src="../includes/jscript/textext.js"></script>
<script type="text/javascript">
var datepickerformat = "<?php echo $this->_tpl_vars['datepickerformat']; ?>
";
<?php if ($this->_tpl_vars['jquerycode']): ?>$(document).ready(function(){
    <?php echo $this->_tpl_vars['jquerycode']; ?>

});
<?php endif; ?>
<?php if ($this->_tpl_vars['jscode']): ?><?php echo $this->_tpl_vars['jscode']; ?>

<?php endif; ?>
</script>
<script type="text/javascript" src="templates/<?php echo $this->_tpl_vars['template']; ?>
/head.js"></script>
<script type="text/javascript" src="../includes/jscript/adminsearchbox.js"></script>
<?php echo $this->_tpl_vars['headoutput']; ?>

</head>
<body>
<?php echo $this->_tpl_vars['headeroutput']; ?>

<div class="topbar">
<div class="left"><a href="index.php"><?php echo $this->_tpl_vars['_ADMINLANG']['home']['title']; ?>
</a> | <a href="../"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['clientarea']; ?>
</a> | <a href="#" id="shownotes"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['mynotes']; ?>
</a> | <a href="myaccount.php"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['myaccount']; ?>
</a> | <a id="logout" href="logout.php"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['logout']; ?>
</a><?php echo $this->_tpl_vars['topBarNotification']; ?>
</div>
<div class="right date">
<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%A, %d %B %Y, %H:%M") : smarty_modifier_date_format($_tmp, "%A, %d %B %Y, %H:%M")); ?>

</div>
</div>

<div class="header">
<div class="logo"><a href="index.php"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/images/logo.gif" border="0" /></a></div>
<div class="stats"><a href="orders.php?status=Pending"><span class="stat"><?php echo $this->_tpl_vars['sidebarstats']['orders']['pending']; ?>
</span> <?php echo $this->_tpl_vars['_ADMINLANG']['stats']['pendingorders']; ?>
</a> | <a href="invoices.php?status=Overdue"><span class="stat"><?php echo $this->_tpl_vars['sidebarstats']['invoices']['overdue']; ?>
</span> <?php echo $this->_tpl_vars['_ADMINLANG']['stats']['overdueinvoices']; ?>
</a> | <a href="supporttickets.php"><span class="stat"><?php echo $this->_tpl_vars['sidebarstats']['tickets']['awaitingreply']; ?>
</span> <?php echo $this->_tpl_vars['_ADMINLANG']['stats']['ticketsawaitingreply']; ?>
</a></div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="sidebaropen"<?php if (! $this->_tpl_vars['minsidebar']): ?> style="display:none;"<?php endif; ?>>
<a href="#" onclick="sidebarOpen();return false"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/images/opensidebar.png" border="0" /></a>
</div>

<div id="sidebar"<?php if ($this->_tpl_vars['minsidebar']): ?> style="display:none;"<?php endif; ?>>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/sidebar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</div>

<div class="contentarea" id="contentarea"<?php if (! $this->_tpl_vars['minsidebar']): ?> style="margin-left:209px;"<?php endif; ?>>

<div style="float:left;width:100%;">

<?php if ($this->_tpl_vars['helplink']): ?><div class="contexthelp"><a href="http://nullrefer.com/?http://docs.whmcs.com/<?php echo $this->_tpl_vars['helplink']; ?>
" target="_blank"><img src="images/icons/help.png" border="0" align="absmiddle" /> <?php echo $this->_tpl_vars['_ADMINLANG']['help']['contextlink']; ?>
</a></div><?php endif; ?>

<h1><?php echo $this->_tpl_vars['pagetitle']; ?>
</h1>