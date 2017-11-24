<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="content-type" content="text/html; charset={$charset}" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		{if $filename eq index && ($smarty.get.search eq "") && ($smarty.get.action eq "")}
		
		<title>{if $kbarticle.title}{$kbarticle.title} - {/if}{$pagetitle} - {$companyname}</title>
		<meta name="keywords" content="{$LANG.meta_keywords_homepage}" />
		<meta name="description" content="{$LANG.meta_description_homepage}" />
		
		{else}
		
		<title>{if $kbarticle.title}{$kbarticle.title} - {/if}{$pagetitle} - {$companyname}</title>
		{/if}
		
		{if $systemurl}<base href="{$systemurl}" />{/if}
	
		<!-- basic styles -->
		<link href="templates/{$template}/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		
		<link rel="stylesheet" href="templates/{$template}/assets/css/plugins/owl-carousel/owl.carousel.css">
		
		
		<link id="whstyle" rel="stylesheet" href="templates/{$template}/assets/css/themes/style.css">
		<link rel="stylesheet" href="templates/{$template}/assets/css/whmcs.css" />
		
		<script src="templates/{$template}/assets/js/jquery.min.js"></script>
		<script src="templates/{$template}/assets/js/bootstrap.min.js"></script>
		
		{if $livehelpjs}{$livehelpjs}{/if}
		
		<!--[if lt IE 9]>
		<script src="templates/{$template}/assets/js/html5shiv.js"></script>
		<script src="templates/{$template}/assets/js/respond.min.js"></script>
		<![endif]-->
		
		<script src="templates/{$template}/assets/js/whmcs.js"></script>
		<link rel="shortcut icon" href="templates/{$template}/favicon.ico">

		{$headoutput}
	</head>
	
	<body class="webhoster">
	{$headeroutput}		
    <nav class="navbar top-navbar dark-menu navbar-fixed-top" role="navigation">
	
		<div class="pre-header">
			<div class="container">
				<div class="row">
					<!-- BEGIN TOP BAR LEFT PART -->
					<div class="col-xs-5">
						<ul class="list-unstyled list-inline hidden-xs hidden-sm">


						
							<!--- Chnage your Phone number and Email here Here --->
							<li><i class="fa fa-phone"></i><span>400-100-5392</span></li>
							<li><i class="fa fa-envelope-o"></i><span>service@ddweb.com.cn</span></li>
							
						</ul>
						<ul class="list-unstyled list-inline visible-xs visible-sm">
						
							<!--- Chnage your Phone number and Email here Here --->
							<li>
								<span class="tooltip-primary" data-placement="right" data-rel="tooltip" title="400-100-5392">
									<i class="fa fa-phone"></i>
								</span>
							</li>
							<li>
								<span class="tooltip-primary" data-placement="right" data-rel="tooltip" title="service@ddweb.com.cn">
									<i class="fa fa-envelope-o"></i>	
								</span>
							</li>
						</ul>
					</div>
					<!-- END TOP BAR LEFT PART -->
					
					
					
					
					<!-- BEGIN TOP BAR MENU -->
					<div class="col-xs-7 additional-nav">
						{php}foreach ($_SESSION['cart']['products'] as $prodkey => $prodval){if ($prodval['noconfig'] != '1'){if ($prodval['qty'] > 1){$cartcount = $cartcount + $prodval['qty'];}else{$cartcount = $cartcount + count($prodkey);}}}foreach ($_SESSION['cart']['domains'] as $domkey => $domval){if (array_key_exists('dnsmanagement', $domval)){$cartcount = $cartcount + count($domkey);}}foreach ($_SESSION['cart']['addons'] as $addkey => $addval){$cartcount = $cartcount + count($addkey);}foreach ($_SESSION['cart']['renewals'] as $addkey => $addval){$cartcount = $cartcount + count($addkey);}if ($cartcount){$this->_tpl_vars['cartcount'] = $cartcount;}{/php}
						<ul class="list-unstyled list-inline pull-right">
							
							{if $cartcount>0}
							<li class="dropdown">
								<a href="cart.php?a=view">
									<i class="fa fa-shopping-cart"></i> <span class="badge up badge-success">{$cartcount}</span></a>
								</a>
							</li>
							{/if}
							{if $clientsstats.numactivetickets>0}
							<li class="dropdown">
								<a href="support-tickets">
									<i class="fa fa-comments-o"></i> <span class="badge up badge-primary">{$clientsstats.numoverdueinvoices}</span>
								</a>
							</li>
							{/if}
						
							{if $clientsstats.numoverdueinvoices>0}
							<li class="dropdown">
								<a href="clientarea.php?action=invoices">
									<i class="fa fa-warning"></i> <span class="badge up badge-danger">{$clientsstats.numoverdueinvoices}</span>
								</a>
							</li>
							{/if}
							
							{if $livehelp}
							<li><a href="http://www.ddweb.com.cn/" id="Menu-Live_Chat" class="LiveHelpButton"><i class="fa fa-comments text-warning"></i> <span class="hidden-xs">Live Chat</span></a></li>
							{else}
							<li><a href="http://www.ddweb.com.cn/"><i class="fa fa-comments text-warning"></i> <span class="hidden-xs">Live Chat</span></a></li>
							{/if}
						</ul>
					</div>
					<!-- END TOP BAR MENU -->
				</div>
			</div>        
		</div>

        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="index.php">
                    <img src="templates/{$template}/assets/images/logo.png" alt="{$companyname}" class="img-responsive" />
                </a>
				
				 <!-- Top Menu Right-->
				<ul class="nav navbar-right">
				
					<!--Search Box-->
					<li>
						<div class="nav-search">
							<form method="post" class="" action="knowledgebase.php?action=search">
								<div class="form-group">
									<input type="text" placeholder="{$LANG.knowledgebasesearch} ..." name="search" class="form-control" autocomplete="off" />
									<span class="glyphicon glyphicon-search text-primary"></span>
								</div>							
							</form>
						</div>
					</li>
					<!--Search Box-->
					
					<li class="dropdown user-box-no-images">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-user"></i> <span class="user-info hidden-xs">My {$LANG.account}</span> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu dropdown-user">
							{if $loggedin}
							<li><a href="clientarea.php">{$LANG.clientareatitle}</a></li>
							<li><a href="clientarea.php?action=details">{$LANG.editaccountdetails}</a></li>
							<li><a href="clientarea.php?action=contacts">{$LANG.clientareanavcontacts}</a></li>
							<li><a href="clientarea.php?action=emails">{$LANG.navemailssent}</a></li>
							<li><a href="clientarea.php?action=changepw">{$LANG.clientareanavchangepw}</a></li>
							<li><a href="logout.php">{$LANG.logouttitle}</a></li>
							{else}
							<li><a href="clientarea.php">{$LANG.login}</a></li>
							<li><a href="register.php">{$LANG.register}</a></li>
							<li><a href="pwreset.php">{$LANG.forgotpw}</a></li>
							{/if}
						</ul>
					</li>

				</ul>
				
            </div>		
            <!-- End Top Menu Right-->
			<div class="nav-top">
			
				<!-- Top Menu Left-->
				<div class="top-menu collapse navbar-collapse  navbar-main-collapse">
					<ul class="nav navbar-nav navbar-left">

						<li class="dropdown"><a href="index.php">首页</a></li>
						<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">{$LANG.navservices} <b class="caret"></b> </a>
						  <ul class="dropdown-menu">
						    {if $loggedin}
						    <li><a id="Menu-Services-My_Services" href="clientarea.php?action=products">{$LANG.clientareanavservices}</a></li>
						      <li><a id="Menu-Services-Order_New_Services" href="cart.php">{$LANG.navservicesorder}</a></li>
						      <li><a id="Menu-Services-View_Available_Addons" href="cart.php?gid=addons">{$LANG.clientareaviewaddons}</a></li>												
							  {/if}		
					        </ul>
					  </li>
				  
					  			{if $condlinks.domainreg || $condlinks.domaintrans}
						<li class="dropdown"><a id="Menu-Domains" class="dropdown-toggle" data-toggle="dropdown" href="#">{$LANG.navdomains}&nbsp;<b class="caret"></b></a>
							<ul class="dropdown-menu">
								{if $loggedin}
								<li><a id="Menu-Domains-My_Domains" href="clientarea.php?action=domains">{$LANG.clientareanavdomains}</a></li>
								<li><a id="Menu-Domains-Renew_Domains" href="cart.php?gid=renewals">{$LANG.navrenewdomains}</a></li>
								{/if}
								{if $condlinks.domainreg}<li><a id="Menu-Domains-Register_a_New_Domain" href="cart.php?a=add&domain=register">{$LANG.navregisterdomain}</a></li>{/if}
								{if $condlinks.domaintrans}<li><a id="Menu-Domains-Transfer_Domains_to_Us" href="cart.php?a=add&domain=transfer">{$LANG.navtransferdomain}</a></li>{/if}
								<li><a id="Menu-Domains-Whois_Lookup" href="domainchecker.php">{$LANG.navwhoislookup}</a></li>
							</ul>
						</li>		
						{/if}
	  
					  
						{if $loggedin}
						<li class="dropdown">
							<a id="Menu-Billing" class="dropdown-toggle" data-toggle="dropdown" href="#">
								{$LANG.navbilling}&nbsp;<b class="caret"></b>							</a>
							<ul class="dropdown-menu">
								<li><a id="Menu-Billing-My_Invoices" href="clientarea.php?action=invoices">{$LANG.invoices}</a></li>
								<li><a id="Menu-Billing-My_Quotes" href="clientarea.php?action=quotes">{$LANG.quotestitle}</a></li>
								{if $condlinks.addfunds}<li><a id="Menu-Billing-Add_Funds" href="clientarea.php?action=addfunds">{$LANG.addfunds}</a></li>{/if}
								{if $condlinks.masspay}<li><a id="Menu-Billing-Mass_Payment" href="clientarea.php?action=masspay&all=true">{$LANG.masspaytitle}</a></li>{/if}
								{if $condlinks.updatecc}<li><a id="Menu-Billing-Manage_Credit_Card" href="clientarea.php?action=creditcard">{$LANG.navmanagecc}</a></li>{/if}
								{if $condlinks.affiliates}<li><a href="affiliates.php">{$LANG.affiliatestitle}</a></li>{/if}
							</ul>
						</li>							
						{/if}
								
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								{$LANG.navsupport} <b class="caret"></b>							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a id="Menu-Support-Tickets" href="supporttickets.php">{$LANG.navtickets}</a></li>
								<li><a id="Menu-Support-Knowledgebase" href="knowledgebase.php">{$LANG.knowledgebasetitle}</a></li>
								<li><a id="Menu-Support-Network_Status" href="serverstatus.php">{$LANG.networkstatustitle}</a></li>
								<li><a id="Menu-Annoucements" href="announcements.php">{$LANG.announcementstitle}</a></li>
								<li><a id="Menu-Support-Downloads" href="downloads.php">{$LANG.downloadstitle}</a></li>
								<!--{if $loggedin}<li><a id="Menu-Contact_Us" href="submitticket.php?step=2&deptid=1">{$LANG.contactus}</a></li>{/if}-->
							</ul>								
						</li>

						<!--About us Page links-->
						<li><a href="announcements.php?id=1">Tos条款</a></li>
						<li><a href="submitticket.php?step=2&deptid=1">联系我们</a></li>
						
						<!--{if $loggedin}{else}<li><a id="Menu-Contact_Us" href="contact.php">{$LANG.contactus}</a></li>{/if}-->
					</ul>
			  </div>
				<!-- Top Menu Left-->
				
			</div>
            <!-- /.Top Menu -->
		</div>
        <!-- /.container -->
    </nav>


	<div class="page-container"><!-- /page container -->
	
	<!-- /#layout-button -->	
		<div class="qs-layout-menu front">
			<div class="btn btn-gray qs-setting-btn" id="qs-setting-btn">
				<i class="fa fa-cog bigger-150 icon-only"></i>
			</div>
			<div class="qs-setting-box" id="qs-setting-box">
				<div style="margin-bottom:10px;">{if $langchange}{$setlanguage}{/if}</div>
				<span class="bigger-120">Color Options</span>										
				<div class="hr hr-dotted hr-8"></div>										
				<ul>									
					<li><button class="btn" style="background-color:#3498db;" onClick="swapStyle('templates/{$template}/assets/css/themes/style.css')"></button></li>
					<li><button class="btn" style="background-color:#86618f;" onClick="swapStyle('templates/{$template}/assets/css/themes/style-1.css')"></button></li> 
					<li><button class="btn" style="background-color:#ba5d32;" onClick="swapStyle('templates/{$template}/assets/css/themes/style-2.css')"></button></li>
					<li><button class="btn" style="background-color:#488075;" onClick="swapStyle('templates/{$template}/assets/css/themes/style-3.css')"></button></li>
					<li><button class="btn" style="background-color:#4e72c2;" onClick="swapStyle('templates/{$template}/assets/css/themes/style-4.css')"></button></li>
				</ul>											
			</div>
		</div>
	<!-- /#layout-button -->
		
		{if $filename eq index && ($smarty.get.search eq "") && ($smarty.get.action eq "") || $filename == "web_hosting" || $filename == "web_hosting_windows" || $filename == "reseller_hosting"  || $filename == "alpha_reseller"  || $filename == "master_reseller"  || $filename == "ssl_certificates"  || $filename == "vps_hosting" || $filename == "dedicated_servers"}{else}
		<div class="mass-head hero-1">
			<div class="container">
				<div class="hero-inner text-center">
					<h1>
						{if $pagetitle eq $LANG.supportticketspagetitle || $pagetitle eq $LANG.supportticketsviewticket || $pagetitle eq $LANG.supportticketssubmitticket}
							<i class="fa fa-ticket"></i>
						{elseif $pagetitle eq $LANG.knowledgebasetitle}
							<i class="fa fa-question-circle"></i>
						{elseif $pagetitle eq $LANG.announcementstitle}
							<i class="fa fa-bullhorn"></i>
						{/if}
						{$pagetitle}
					</h1>
				</div>
			</div>
		</div>
		{/if}
		
		{if $filename eq index && ($smarty.get.search eq "") && ($smarty.get.action eq "") || $filename == "web_hosting" || $filename == "web_hosting_windows" || $filename == "reseller_hosting"  || $filename == "alpha_reseller"  || $filename == "master_reseller"  || $filename == "ssl_certificates"  || $filename == "vps_hosting" || $filename == "dedicated_servers"}{else}
		<div class="container">
			<div class="block-s3">
			
		{/if}