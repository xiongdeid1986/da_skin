{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

	<link rel="stylesheet" href="templates/{$template}/assets/css/plugins/footable/footable.min.css">

<section class="bg-gray-darker text-white">
			<div class="container">
				<h1 class="margin-top-none">Dedicated <span class="text-green">Servers</span></h1>
				<p class="lead margin-bottom-none">High performance servers with dedicated resources!</p>
			</div>
		</section>
		<section>
			<div class="container">
				<ul class="nav nav-tabs" id="nav-servers">
					<li><a href="#atom-servers" data-toggle="tab">ATOM Servers</a></li>
					<li><a href="#e3-servers" data-toggle="tab">E3 Xeon Servers</a></li>
					<li><a href="#e5-servers" data-toggle="tab">E5 Xeon Servers</a></li>
				</ul>
				<script>
					$(function() { if(window.location.hash != "") { $('#nav-servers > li > a[href="'+window.location.hash+'"]').tab('show'); } else { $('#nav-servers > li a:first').tab('show'); } });
				</script>
				<div class="tab-content margin-top">
					<div class="tab-pane active" id="atom-servers">
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Atom&trade; D510</div></div>
									<ul class="list-group">
										<li class="list-group-item">Dual Core 1.66 GHz CPU</li>
										<li class="list-group-item">4 GB RAM</li>
										<li class="list-group-item">Single Hard Drive</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-blue">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">89</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=atom-d510" title="Build ATOM 510" class="btn btn-blue"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Atom&trade; D525</div></div>
									<ul class="list-group">
										<li class="list-group-item">Dual Core 1.8 GHz CPU</li>
										<li class="list-group-item">4 GB RAM</li>
										<li class="list-group-item">Single Hard Drive</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-green">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">99</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=atom-d525" title="Order ATOM D525" class="btn btn-green"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Atom&trade; C2550</div></div>
									<ul class="list-group">
										<li class="list-group-item">Quad Core 2.4 GHz CPU</li>
										<li class="list-group-item">up to 32 GB RAM</li>
										<li class="list-group-item">up to 4 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-purple">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">129</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=atom-c2550" title="Order ATOM C2550" class="btn btn-purple"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="e3-servers">
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E3-1230v2</div></div>
									<ul class="list-group">
										<li class="list-group-item">Quad Core 3.7 GHz CPU</li>
										<li class="list-group-item">Up to 32 GB RAM</li>
										<li class="list-group-item">Up to 2 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-blue">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">189</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e3&amp;processor=e3-1230v2" title="Build E3-1230v2" class="btn btn-blue"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E3-1230v3</div></div>
									<ul class="list-group">
										<li class="list-group-item">Quad Core 3.7 GHz CPU</li>
										<li class="list-group-item">Up to 32 GB RAM</li>
										<li class="list-group-item">Up to 2 Hard Drive</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-green">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">199</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e3&amp;processor=e3-1230v3" title="Build E3-1230v3" class="btn btn-green"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E3-1270v3</div></div>
									<ul class="list-group">
										<li class="list-group-item">Quad Core 3.9 GHz CPU</li>
										<li class="list-group-item">up to 32 GB RAM</li>
										<li class="list-group-item">up to 2 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-purple">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">229</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e3&amp;processor=e3-1270v3" title="Build E3-1270v3" class="btn btn-purple"><span class="fa fa-cogs"></span> Customize &amp; Deploy</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="e5-servers">
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E5-2620</div></div>
									<ul class="list-group">
										<li class="list-group-item">Hex Core 2.5 GHz CPU</li>
										<li class="list-group-item">up to 256 GB RAM</li>
										<li class="list-group-item">up to 4 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-blue">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">289</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e5" title="Build E5-2620" class="btn btn-blue"><span class="fa fa-cogs"></span> Start Customizing</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E5-2620 v2</div></div>
									<ul class="list-group">
										<li class="list-group-item">Hex Core 2.6 GHz CPU</li>
										<li class="list-group-item">up to 256 GB RAM</li>
										<li class="list-group-item">up to 4 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-green">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">299</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e5&amp;processor=e5-2620-v2" title="Order E5-2620v2" class="btn btn-green"><span class="fa fa-cogs"></span> Start Customizing</a>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading"><div class="h4">Intel&reg; Xeon&reg; E5-2620 v2</div></div>
									<ul class="list-group">
										<li class="list-group-item">Dual Hex Core 2.6 GHz CPU</li>
										<li class="list-group-item">up to 256 GB RAM</li>
										<li class="list-group-item">up to 4 Hard Drives</li>
										<li class="list-group-item">10 TB Bandwidth</li>
									</ul>
									<div class="panel-body">
										<p style="margin-top:10px;" class="text-purple">
											<span class="text-muted">Starting at</span>
											<br>
											<span class="h4">$</span><span class="h1">369</span>
											<br>
											<span class="text-muted">Per Month</span>
										</p>
										<a href="/order/dedicated-servers/?server=e5&amp;processor=2x-e5-2620-v2" title="Order Dual E5-2620v2" class="btn btn-purple"><span class="fa fa-cogs"></span> Start Customizing</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-body text-orange text-center">
						<span class="lead">Don't see what you need?</span> <a href="/contact/?subject=Custom%20Server%20Quote" title="Contact Via E-Mail" class="btn btn-orange" style="margin-left:20px;">Custom Server Quote</a> <a href="javascript: void(0);" onclick="javascript: window.open('https://helpdesk.ndchost.com/visitor/index.php?/Default/LiveChat/Chat/Request/_sessionID=/_promptType=chat/_proactive=0/_filterDepartmentID=/_randomNumber=64wdbz0y74ug6zsq1xalo0ki7kn3o1c9/_fullName=/_email=/', 'livechatwin', 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,resizable=1,width=600,height=680');" class="livechatlink btn btn-orange" style="margin-left:20px;" title="Live Chat">Live Chat</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h3>Available Add-ons</h3>
						<hr>
						<p>Below are just a few of the following addons that can be added to your dedicated server order!</p>
						<table class="table table-striped">
							<tr>
								<td>cPanel/WHM License</td>
								<td class="text-right">$20/mo</td>
							</tr>
							<tr>
								<td>Microsoft Windows License (per CPU)</td>
								<td class="text-right">$30/Mo</td>
							</tr>
							<tr>
								<td>Remote Storage Space</td>
								<td class="text-right">$0.25/GB</td>
						</table>
					</div>
					<div class="col-md-6">
						<h3>Additional IPv4 Addresses</h3>
						<hr>
						<p>Each server includes a /29 IPv4 subnet (8 IPs).  Additional IP's are billed at $1 per IP and are given out in valid CIDR subnets.  Below is a list of just a few valid CIDR subnet sizes.  Three IPs are required from each subnet to configure networking.</p>
						<table class="table table-striped">
							<tr>
								<td>/28</td>
								<td>16 IPs</td>
							</tr>
							<tr>
								<td>/27</td>
								<td>32 IPs</td>
							</tr>
							<tr>
								<td>/26</td>
								<td>64 IPs</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</section>



		<div class="block-s1 bg-primary arrow-block">
			<div class="container">
				<div class="text-center">
					<h2>The Best Features At The Best Price!</h2>
				</div>
			</div>
		</div>


		<div class="block-s2">
			<div class="container">
				
				<h2>Get Started Quickly & Easily</h2>
				<p>Lorem ipsum dolor sit amet, dolore eiusmod quis tempor incididunt ut et dolore Ut veniam unde nostrudlaboris. Sed unde omnis iste natus error sit voluptatem.</p>
				<p>Lorem ipsum dolor sit amet, dolore eiusmod quis tempor incididunt ut et dolore Ut veniam unde nostrudlaboris. Sed unde omnis iste natus error sit voluptatem. Lorem ipsum dolor sit amet, dolore eiusmod quis tempor incididunt ut et dolore Ut veniam unde nostrudlaboris. Sed unde omnis iste natus error sit voluptatem.</p>
			
				<div class="row block-s3">
					<div class="col-sm-4 tc-box s3">
						<i class="fa fa-rocket"></i>
						<h3>Powerful Tools</h3>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
					</div>
					<div class="col-sm-4 tc-box s3">
						<i class="fa fa-sliders"></i>
						<h3>Flexibility</h3>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
					</div>
					<div class="col-sm-4 tc-box s3">
						<i class="fa fa-server"></i>
						<h3>Scalability</h3>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
					</div>
				</div>

				<h2>FAQs</h2>
				<div class="panel-group tc-accordion no-border" id="sub-faq-list-1"><!-- Services General Questions-->
					<div class="panel panel-default">
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#sub-faq-list-1" href="#s-question1">
								<h5 class="panel-title">
									<span><i class="fa fa-angle-right bigger-110"></i></span> Services Question One, Lorem ipsum dolor sit amet?
								</h5>
							</a>
						</div>
						<div id="s-question1" class="panel-collapse collapse">
							<div class="panel-body">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading ">
							<a data-toggle="collapse" data-parent="#sub-faq-list-1" href="#s-question2">
								<h5 class="panel-title">
									<span><i class="fa fa-angle-right bigger-110"></i></span> Services Question Two Lorem ipsum dolor sit amet?
								</h5>
							</a>
						</div>
						<div id="s-question2" class="panel-collapse collapse">
							<div class="panel-body">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#sub-faq-list-1" href="#s-question3">
								<h5 class="panel-title">
									<span><i class="fa fa-angle-right bigger-110"></i></span> Services Question Three Lorem ipsum dolor sit amet?
								</h5>
							</a>
						</div>
						<div id="s-question3" class="panel-collapse collapse">
							<div class="panel-body">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
				</div><!-- Services General Questions-->
						
			</div>
		</div>	
		
		
		
		
		
		
		
		
		
		
		
		
	<script src="templates/{$template}/assets/js/plugins/footable/footable.min.js"></script>
	<script src="templates/{$template}/assets/js/plugins/footable/footable.init.js"></script>