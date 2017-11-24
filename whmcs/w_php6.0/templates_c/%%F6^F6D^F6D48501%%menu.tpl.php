<?php /* Smarty version 2.6.28, created on 2016-12-13 17:28:28
         compiled from blend/menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'blend/menu.tpl', 77, false),)), $this); ?>
<div class="navigation">
<ul id="menu">
<li><a id="Menu-Clients" <?php if (in_array ( 'List Clients' , $this->_tpl_vars['admin_perms'] )): ?>href="clients.php"<?php endif; ?> title="Clients"><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'List Clients' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-View_Search_Clients" href="clients.php"><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['viewsearch']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Add New Client' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Add_New_Client" href="clientsadd.php"><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['addnew']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'List Services' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Clients-Products_Services" href="clientshostinglist.php"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['title']; ?>
</a>
        <ul>
        <li><a id="Menu-Clients-Products_Services-Shared_Hosting" href="clientshostinglist.php?listtype=hostingaccount">- <?php echo $this->_tpl_vars['_ADMINLANG']['services']['listhosting']; ?>
</a></li>
        <li><a id="Menu-Clients-Products_Services-Reseller_Accounts" href="clientshostinglist.php?listtype=reselleraccount">- <?php echo $this->_tpl_vars['_ADMINLANG']['services']['listreseller']; ?>
</a></li>
        <li><a id="Menu-Clients-Products_Services-VPS_Servers" href="clientshostinglist.php?listtype=server">- <?php echo $this->_tpl_vars['_ADMINLANG']['services']['listservers']; ?>
</a></li>
        <li><a id="Menu-Clients-Products_Services-Other_Services" href="clientshostinglist.php?listtype=other">- <?php echo $this->_tpl_vars['_ADMINLANG']['services']['listother']; ?>
</a></li>
        </ul>
    </li>
    <?php endif; ?>
    <?php if (in_array ( 'List Addons' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Service_Addons" href="clientsaddonslist.php"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['listaddons']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'List Domains' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Domain_Registration" href="clientsdomainlist.php"><?php echo $this->_tpl_vars['_ADMINLANG']['services']['listdomains']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'View Cancellation Requests' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Cancelation_Requests" href="cancelrequests.php"><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['cancelrequests']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Affiliates' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Manage_Affiliates" href="affiliates.php"><?php echo $this->_tpl_vars['_ADMINLANG']['affiliates']['manage']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Mass Mail' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Clients-Mass_Mail_Tool" href="massmail.php"><?php echo $this->_tpl_vars['_ADMINLANG']['clients']['massmail']; ?>
</a></li><?php endif; ?>
  </ul>
</li>
<li><a id="Menu-Orders" <?php if (in_array ( 'View Orders' , $this->_tpl_vars['admin_perms'] )): ?>href="orders.php"<?php endif; ?> title="Orders"><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'View Orders' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Orders-List_All_Orders" href="orders.php"><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['listall']; ?>
</a></li>
    <li><a id="Menu-Orders-Pending_Orders" href="orders.php?status=Pending">- <?php echo $this->_tpl_vars['_ADMINLANG']['orders']['listpending']; ?>
</a></li>
    <li><a id="Menu-Orders-Active_Orders" href="orders.php?status=Active">- <?php echo $this->_tpl_vars['_ADMINLANG']['orders']['listactive']; ?>
</a></li>
    <li><a id="Menu-Orders-Fraud_Orders" href="orders.php?status=Fraud">- <?php echo $this->_tpl_vars['_ADMINLANG']['orders']['listfraud']; ?>
</a></li>
    <li><a id="Menu-Orders-Cancelled_Orders" href="orders.php?status=Cancelled">- <?php echo $this->_tpl_vars['_ADMINLANG']['orders']['listcancelled']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Add New Order' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Orders-Add_New_Order" href="ordersadd.php"><?php echo $this->_tpl_vars['_ADMINLANG']['orders']['addnew']; ?>
</a></li><?php endif; ?>
  </ul>
</li>
<li><a id="Menu-Billing" <?php if (in_array ( 'List Transactions' , $this->_tpl_vars['admin_perms'] )): ?>href="transactions.php"<?php endif; ?> title="Billing"><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'List Transactions' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Billing-Transactions_List" href="transactions.php"><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['transactionslist']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'List Invoices' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Billing-Invoices" href="invoices.php"><?php echo $this->_tpl_vars['_ADMINLANG']['invoices']['title']; ?>
</a>
        <ul>
        <li><a id="Menu-Billing-Invoices-Paid" href="invoices.php?status=Paid">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['paid']; ?>
</a></li>
        <li><a id="Menu-Billing-Invoices-Unpaid" href="invoices.php?status=Unpaid">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['unpaid']; ?>
</a></li>
        <li><a id="Menu-Billing-Invoices-Overdue" href="invoices.php?status=Overdue">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['overdue']; ?>
</a></li>
        <li><a id="Menu-Billing-Invoices-Cancelled" href="invoices.php?status=Cancelled">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['cancelled']; ?>
</a></li>
        <li><a id="Menu-Billing-Invoices-Refunded" href="invoices.php?status=Refunded">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['refunded']; ?>
</a></li>
        <li><a id="Menu-Billing-Invoices-Collections" href="invoices.php?status=Collections">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['collections']; ?>
</a></li>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'View Billable Items' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Billing-Billable_Items" href="billableitems.php"><?php echo $this->_tpl_vars['_ADMINLANG']['billableitems']['title']; ?>
</a>
        <ul>
        <li><a id="Menu-Billing-Billable_Items-Uninvoiced_Items" href="billableitems.php?status=Uninvoiced">- <?php echo $this->_tpl_vars['_ADMINLANG']['billableitems']['uninvoiced']; ?>
</a></li>
        <li><a id="Menu-Billing-Billable_Items-Recurring_Items" href="billableitems.php?status=Recurring">- <?php echo $this->_tpl_vars['_ADMINLANG']['billableitems']['recurring']; ?>
</a></li>
        <?php if (in_array ( 'Manage Billable Items' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Billing-Billable_Items-Add_New" href="billableitems.php?action=manage">- <?php echo $this->_tpl_vars['_ADMINLANG']['billableitems']['addnew']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'Manage Quotes' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Billing-Quotes" href="quotes.php"><?php echo $this->_tpl_vars['_ADMINLANG']['quotes']['title']; ?>
</a>
        <ul>
        <li><a id="Menu-Billing-Quotes-Valid" href="quotes.php?validity=Valid">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['valid']; ?>
</a></li>
        <li><a id="Menu-Billing-Quotes-Expired" href="quotes.php?validity=Expired">- <?php echo $this->_tpl_vars['_ADMINLANG']['status']['expired']; ?>
</a></li>
        <li><a id="Menu-Billing-Quotes-Create_New_Quote" href="quotes.php?action=manage">- <?php echo $this->_tpl_vars['_ADMINLANG']['quotes']['createnew']; ?>
</a></li>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'Offline Credit Card Processing' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Billing-Offline_CC_Processing" href="offlineccprocessing.php"><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['offlinecc']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'View Gateway Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Billing-Gateway_Log" href="gatewaylog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['billing']['gatewaylog']; ?>
</a></li><?php endif; ?>
  </ul>
</li>
<li><a id="Menu-Support" <?php if (in_array ( 'Support Center Overview' , $this->_tpl_vars['admin_perms'] )): ?>href="supportcenter.php"<?php endif; ?> title="Support"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'Support Center Overview' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Support_Overview" href="supportcenter.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['supportoverview']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Announcements' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Annoucements" href="supportannouncements.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['announcements']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Downloads' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Downloads" href="supportdownloads.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['downloads']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Knowledgebase' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Knowledgebase" href="supportkb.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['knowledgebase']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'List Support Tickets' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Support-Support_Tickets" href="supporttickets.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['supporttickets']; ?>
</a>
        <ul>
        <li><a id="Menu-Support-Support_Tickets-Flagged_Tickets" href="supporttickets.php?view=flagged">- <?php echo $this->_tpl_vars['_ADMINLANG']['support']['flagged']; ?>
</a></li>
        <li><a id="Menu-Support-Support_Tickets-All_Active_Tickets" href="supporttickets.php?view=active">- <?php echo $this->_tpl_vars['_ADMINLANG']['support']['allactive']; ?>
</a></li>
        <?php $_from = $this->_tpl_vars['menuticketstatuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['status']):
?>
            <li><a id="Menu-Support-Support_Tickets-<?php echo ((is_array($_tmp=$this->_tpl_vars['status']['title'])) ? $this->_run_mod_handler('replace', true, $_tmp, ' ', '_') : smarty_modifier_replace($_tmp, ' ', '_')); ?>
" href="supporttickets.php?view=<?php echo $this->_tpl_vars['status']['title']; ?>
">- <?php echo $this->_tpl_vars['status']['title']; ?>
</a></li>
        <?php endforeach; endif; unset($_from); ?>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'Open New Ticket' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Open_New_Ticket" href="supporttickets.php?action=open"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['opennewticket']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Predefined Replies' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Support-Predefined_Replies" href="supportticketpredefinedreplies.php"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['predefreplies']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Manage Network Issues' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Support-Network_Issues" href="networkissues.php"><?php echo $this->_tpl_vars['_ADMINLANG']['networkissues']['title']; ?>
</a>
        <ul>
        <li><a id="Menu-Support-Network_Issues-Open" href="networkissues.php">- <?php echo $this->_tpl_vars['_ADMINLANG']['networkissues']['open']; ?>
</a></li>
        <li><a id="Menu-Support-Network_Issues-Scheduled" href="networkissues.php?view=scheduled">- <?php echo $this->_tpl_vars['_ADMINLANG']['networkissues']['scheduled']; ?>
</a></li>
        <li><a id="Menu-Support-Network_Issues-Resolved" href="networkissues.php?view=resolved">- <?php echo $this->_tpl_vars['_ADMINLANG']['networkissues']['resolved']; ?>
</a></li>
        <li><a id="Menu-Support-Network_Issues-Create_New" href="networkissues.php?action=manage">- <?php echo $this->_tpl_vars['_ADMINLANG']['networkissues']['addnew']; ?>
</a></li>
        </ul>
    </li><?php endif; ?>
  </ul>
</li>
<?php if (in_array ( 'View Reports' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Reports" title="Reports" href="reports.php"><?php echo $this->_tpl_vars['_ADMINLANG']['reports']['title']; ?>
</a>
  <ul>
    <li><a id="Menu-Reports-Daily_Performance" href="reports.php?report=daily_performance">Daily Performance</a></li>
    <li><a id="Menu-Reports-Income_Forecast" href="reports.php?report=income_forecast">Income Forecast</a></li>
    <li><a id="Menu-Reports-Annual_Income_Report" href="reports.php?report=annual_income_report">Annual Income Report</a></li>
    <li><a id="Menu-Reports-New_Customers" href="reports.php?report=new_customers">New Customers</a></li>
    <li><a id="Menu-Reports-Ticket_Feedback_Scores" href="reports.php?report=ticket_feedback_scores">Ticket Feedback Scores</a></li>
    <li><a id="Menu-Reports-Batch_Invoice_PDF_Export" href="reports.php?report=pdf_batch">Batch Invoice PDF Export</a></li>
    <li><a id="Menu-Reports-More..." href="reports.php">More...</a></li>
  </ul>
</li><?php endif; ?>
<li><a id="Menu-Utilities" title="Utilities" href=""><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'Email Marketer' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Email_Marketer" href="utilitiesemailmarketer.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['emailmarketer']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Link Tracking' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Link_Tracking" href="utilitieslinktracking.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['linktracking']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Browser' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Browser" href="browser.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['browser']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Calendar' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Calendar" href="calendar.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['calendar']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( "To-Do List" , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-To-Do_List" href="todolist.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['todolist']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'WHOIS Lookups' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-WHOIS_Lookups" href="whois.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['whois']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Domain Resolver Checker' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Domain_Resolver" href="utilitiesresolvercheck.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['domainresolver']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'View Integration Code' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Integration_Code" href="systemintegrationcode.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['integrationcode']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'WHM Import Script' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-cPanel_WHM_Import" href="whmimport.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['cpanelimport']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Database Status' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'System Cleanup Operations' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View PHP Info' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Utilities-System" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['system']; ?>
</a>
        <ul>
        <?php if (in_array ( 'Database Status' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-System-Database_Status" href="systemdatabase.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['dbstatus']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'System Cleanup Operations' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-System-System_Cleanup" href="systemcleanup.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['syscleanup']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View PHP Info' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-System-PHP_Info" href="systemphpinfo.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['phpinfo']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'View Activity Log' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Admin Log' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Module Debug Log' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Email Message Log' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Ticket Mail Import Log' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View WHOIS Lookup Log' , $this->_tpl_vars['admin_perms'] )): ?><li class="expand"><a id="Menu-Utilities-Logs" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['logs']; ?>
</a>
        <ul>
        <?php if (in_array ( 'View Activity Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-Activity_Log" href="systemactivitylog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['activitylog']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Admin Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-Admin_Log" href="systemadminlog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['adminlog']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Module Debug Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-Module_Log" href="systemmodulelog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['modulelog']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Email Message Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-Email_Message_Log" href="systememaillog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['emaillog']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Ticket Mail Import Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-Ticket_Email_Import_Log" href="systemmailimportlog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['ticketmaillog']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View WHOIS Lookup Log' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Utilities-Logs-WHOIS_Lookup_Log" href="systemwhoislog.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['whoislog']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
  </ul>
</li>
<li><a id="Menu-Addons" title="Addons" href="addonmodules.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['addonmodules']; ?>
</a>
  <ul>
    <?php $_from = $this->_tpl_vars['addon_modules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['module'] => $this->_tpl_vars['displayname']):
?>
    <li><a id="Menu-Addons-<?php echo $this->_tpl_vars['displayname']; ?>
" href="addonmodules.php?module=<?php echo $this->_tpl_vars['module']; ?>
"><?php echo $this->_tpl_vars['displayname']; ?>
</a></li>
    <?php endforeach; else: ?>
    <li><a id="Menu-Addons-Addons_Directory" href="addonmodules.php"><?php echo $this->_tpl_vars['_ADMINLANG']['utilities']['addonsdirectory']; ?>
</a></li>
    <?php endif; unset($_from); ?>
  </ul>
</li>
<li><a id="Menu-Setup" title="Setup" href=""><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['title']; ?>
</a>
  <ul>
    <?php if (in_array ( 'Configure General Settings' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-General_Settings" href="configgeneral.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['general']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Automation Settings' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Automation_Settings" href="configauto.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['automation']; ?>
</a></li><?php endif; ?>
<?php if (in_array ( 'Configure Administrators' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Admin Roles' , $this->_tpl_vars['admin_perms'] ) || in_array ( "Configure Two-Factor Authentication" , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Setup-Staff_Management" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['staff']; ?>
</a>
        <ul>
        <?php if (in_array ( 'Configure Administrators' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Staff_Management-Administrator_Users" href="configadmins.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['admins']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Admin Roles' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Staff_Management-Administrator_Roles" href="configadminroles.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['adminroles']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( "Configure Two-Factor Authentication" , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Staff_Management-Two-Factor_Authentication" href="configtwofa.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['twofa']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php else: ?>
    <li><a id="Menu-Setup-Staff_Management-My_Account" href="myaccount.php"><?php echo $this->_tpl_vars['_ADMINLANG']['global']['myaccount']; ?>
</a></li><?php endif; ?>
<?php if (in_array ( 'Configure Currencies' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Payment Gateways' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Tax Setup' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Promotions' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Setup-Payments" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['payments']; ?>
</a>
        <ul>
        <?php if (in_array ( 'Configure Currencies' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Payments-Currencies" href="configcurrencies.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['currencies']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Payment Gateways' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Payments-Payment_Gateways" href="configgateways.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['gateways']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Tax Setup' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Payments-Tax_Rules" href="configtax.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['tax']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Promotions' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Payments-Promotions" href="configpromotions.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['promos']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
<?php if (in_array ( "View Products/Services" , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Product Addons' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Product Bundles' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Domain Pricing' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Domain Registrars' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Servers' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Setup-Products_Services" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['products']; ?>
</a>
        <ul>
        <?php if (in_array ( "View Products/Services" , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Products_Services" href="configproducts.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['products']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( "View Products/Services" , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Configurable_Options" href="configproductoptions.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['configoptions']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Product Addons' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Product_Addons" href="configaddons.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['addons']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Product Bundles' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Product_Bundles" href="configbundles.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['bundles']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Domain Pricing' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Domain_Pricing" href="configdomains.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['domainpricing']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Domain Registrars' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Domain_Registrars" href="configregistrars.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['registrars']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Servers' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Products_Services-Servers" href="configservers.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['servers']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
<?php if (in_array ( 'Configure Support Departments' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Ticket Statuses' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Support Departments' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Spam Control' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Setup-Support" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['support']['title']; ?>
</a>
        <ul>
        <?php if (in_array ( 'Configure Support Departments' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Support-Support_Departments" href="configticketdepartments.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['supportdepartments']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Ticket Statuses' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Support-Ticket_Statuses" href="configticketstatuses.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['ticketstatuses']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Support Departments' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Support-Escalation_Rules" href="configticketescalations.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['escalationrules']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Spam Control' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Support-Spam_Control" href="configticketspamcontrol.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['spam']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
    <?php if (in_array ( 'View Email Templates' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Email_Templates" href="configemailtemplates.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['emailtpls']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Addon Modules' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Addons_Modules" href="configaddonmods.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['addonmodules']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Client Groups' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Client_Groups" href="configclientgroups.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['clientgroups']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Custom Client Fields' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Custom_Client_Fields" href="configcustomfields.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['customclientfields']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Fraud Protection' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Fraud_Protection" href="configfraud.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['fraud']; ?>
</a></li><?php endif; ?>
<?php if (in_array ( 'Configure Order Statuses' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Security Questions' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'View Banned IPs' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Banned Emails' , $this->_tpl_vars['admin_perms'] ) || in_array ( 'Configure Database Backups' , $this->_tpl_vars['admin_perms'] )): ?>
    <li class="expand"><a id="Menu-Setup-Other" href="#"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['other']; ?>
</a>
        <ul>
        <?php if (in_array ( 'Configure Order Statuses' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Other-Order_Statuses" href="configorderstatuses.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['orderstatuses']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Security Questions' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Other-Security_Questions" href="configsecurityqs.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['securityqs']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'View Banned IPs' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Other-Banned_IPs" href="configbannedips.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['bannedips']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Banned Emails' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Other-Banned_Emails" href="configbannedemails.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['bannedemails']; ?>
</a></li><?php endif; ?>
        <?php if (in_array ( 'Configure Database Backups' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Setup-Other-Database_Backups" href="configbackups.php"><?php echo $this->_tpl_vars['_ADMINLANG']['setup']['backups']; ?>
</a></li><?php endif; ?>
        </ul>
    </li><?php endif; ?>
  </ul>
</li>
<li><a id="Menu-Help" title="Help" href=""><?php echo $this->_tpl_vars['_ADMINLANG']['help']['title']; ?>
</a>
  <ul>
    <li><a id="Menu-Help-Documentation" href="http://nullrefer.com/?http://docs.whmcs.com/" target="_blank"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['docs']; ?>
</a></li>
    <?php if (in_array ( 'Main Homepage' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Help-License_Information" href="systemlicense.php"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['licenseinfo']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure Administrators' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Help-Change_License_Key" href="licenseerror.php?licenseerror=change"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['changelicense']; ?>
</a></li><?php endif; ?>
    <?php if (in_array ( 'Configure General Settings' , $this->_tpl_vars['admin_perms'] )): ?><li><a id="Menu-Help-Check_For_Updates" href="systemupdates.php"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['updates']; ?>
</a></li>
    <li><a id="Menu-Help-Get_Help" href="systemsupportrequest.php"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['support']; ?>
</a></li><?php endif; ?>
    <li><a id="Menu-Help-Community_Forums" href="http://nullrefer.com/?http://forum.whmcs.com/" target="_blank"><?php echo $this->_tpl_vars['_ADMINLANG']['help']['forums']; ?>
</a></li>
  </ul>
</li>
</ul>
</div>