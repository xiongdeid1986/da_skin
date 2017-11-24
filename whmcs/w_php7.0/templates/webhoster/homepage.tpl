{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

		<div class="mass-head hero-1">
			<div class="container">
				{if $condlinks.domainreg || $condlinks.domaintrans}
				<div class="hero-inner text-center home-hero">				
					<h1>拥有一个域名</h1>
					<p>赠送免费云防护, DNS云解析</p>
					
					<div class="space-12"></div>
					
					<!--Domain Box For Tablet and Desktop-->
					<div class="hidden-xs">
						<form action="{$systemsslurl}domainchecker.php" method="post">
							<input type="hidden" name="direct" value="true" />
							<input type="text" name="domain" value="{$LANG.domaincheckerdomainexample}" onfocus="if(this.value=='{$LANG.domaincheckerdomainexample}')this.value=''" onblur="if(this.value=='')this.value='{$LANG.domaincheckerdomainexample}'" class="domains-input" maxlength="65">
							<button type="submit" class="btn btn-inverse domain-btn" onclick="$('#modalpleasewait').modal();">获得域名</button>                        	
						</form>
					</div>
					<!--End Domain Box For Tablet and Desktop-->
					
					<!--Domain Box for Mobile-->
					<div class="visible-xs">
						<form action="{$systemsslurl}domainchecker.php" method="post">
							<div class="input-group">
								<input type="hidden" name="direct" value="true" />
								<input type="text" name="domain" value="{$LANG.domaincheckerdomainexample}" onfocus="if(this.value=='{$LANG.domaincheckerdomainexample}')this.value=''" onblur="if(this.value=='')this.value='{$LANG.domaincheckerdomainexample}'" class="domains-input form-control input-lg"  maxlength="65">
								<span class="input-group-btn btn-group-lg">
									<button type="submit" class="btn btn-inverse" onclick="$('#modalpleasewait').modal();">
										<i class="fa fa-search icon-only"></i>
									</button>
								</span>
							</div>                        	
						</form>
					</div>
					<!--End Domain Box for Mobile-->
					
					<!-- Domain Offers-->
					<div class="domain-pricing">
						<div class="row">
							<div class="col-sm-3 col-xs-6">
								<span class="tld">.com</span> <span class="price">$9.45</span>
							</div>
							<div class="col-sm-3 col-xs-6 position-relative">
								<span class="tld">.net</span> <span class="price">$9.45</span>
								<div class="promo"></div><!-- On sale promo-->
							</div>
							<div class="col-sm-3 col-xs-6 position-relative">
								<span class="tld">.biz</span> <span class="price">$9.45</span>
								<div class="promo"></div><!-- On sale promo-->
							</div>
							<div class="col-sm-3 col-xs-6">
								<span class="tld">.org</span> <span class="price">$9.45</span>
							</div>
						</div>
					</div>
					<!-- Domain Offers-->
				</div>
				
				{else}
				
				<div class="hero-inner home-hero position-relative">
					<div class="row">
						<div class="col-md-9">
							<h1 class="extra-large">完全免费的虚拟主机</h1>
							<h2>没有强制广告以及附加条件</h2>
							
							<div class="row hidden-xs">
								<div class="col-sm-6">
									<ul>
										<li>DirectAdmin中文面板</li>
										<li>拥有多节点可选</li>
										<li>自由升降级选择</li>
										<li>支持多种常用php程序</li>
									</ul>
								</div>
								<div class="col-sm-6">
									<ul>
										<li>100-500M 免费磁盘容量</li>
										<li>2-8G 数据流量</li>
										<li>99％的在线时间</li>
										<li>24/7在线工单支持</li>
									</ul>
								</div>
							</div>
							
							<div class="space-8 hidden-xs"></div>
							
							<div class="row">
								<div class="col-lg-5 col-sm-6">
									<div class="padding-all no-padding-bottom">
										<a href="cart.php" class="btn btn-lg btn-inverse" here="cart.php">立即申请</a>									</div>
								</div>
								<div class="col-lg-7 col-sm-6">
									<div class="padding-all no-padding-bottom no-padding-left">
										<h4 class="no-margin">完全免费</h4>
										<h2 class="bigger-275 bolder no-margin">审核开通<small class="text-white"></small></h2>
									</div>
								</div>
							</div>
						</div>
						<div clas="col-md-3 hidden-sm">
							<div class="robo hidden-sm"></div>
						</div>
					</div>
				</div>
				
				{/if}

			</div>
		</div>

		<div class="block-s1 white arrow-block">
			<div class="container">
				<div class="text-center">
					<h2>提供多节点 <span class="text-warning">免费</span> in <span class="text-warning">可靠的</span> 虚拟主机</h2>
				</div>
			</div>
		</div>


		<div class="block-s2 block-holder-1">
			<div class="container">
			<!--CONTENT-->
				
				<!--Pricing Tables 1 stye 2-->
				<div class="row block-s3">
					<div class="col-sm-4">
					  <div class="pricing-table-4">
							<h3 class="plan-title">美国内华达</h3>
							<div class="plan-pricing arrow-block bg-danger">
								<p class="plan-price">
									<span class="plan-text"></span>
									Free <span class="plan-unit"></span>
								</p>
							</div>							
							<ul class="plan-features">
								<li>美国优质线路</li>
								<li><span class="text-warning bolder">允许免费域名申请</span></li>
								<li><strong>自由升降</strong>产品套餐</li>
								<li>DirectAdmin中文面板</li>
								<li>无附加要求</li>
							</ul>
						  <a href="cart.php?gid=2" class="btn btn-danger btn-lg btn-block plan-button">
							  立即订购<i class="fa fa-angle-double-right icon-on-right"></i>						  </a>						</div>
					</div>
					<div class="col-sm-4">
					  <div class="pricing-table-4">
							<h3 class="plan-title">香港免备案</h3>							
							<div class="plan-pricing arrow-block bg-success">
								<p class="plan-price">
									<span class="plan-text"></span>
									Free <span class="plan-unit"></span>
								</p>
							</div>
										
							<ul class="plan-features">
								<li>香港极速免备案</li>
								<li><span class="text-warning bolder">禁止免费域名申请</span></li>
								<li><strong>自由升降</strong>产品套餐</li>
								<li>DirectAdmin中文面板</li>
								<li>无附加要求</li>
							</ul>
						  <a href="cart.php?gid=6" class="btn btn-success btn-lg btn-block plan-button">
							  立即订购<i class="fa fa-angle-double-right icon-on-right"></i>						  </a>						</div>
					</div>
					<div class="col-sm-4">
					  <div class="pricing-table-4">
							<h3 class="plan-title">美国佛里蒙特</h3>
							<div class="plan-pricing arrow-block bg-warning">
								<p class="plan-price">
									<span class="plan-text"></span>
									Free <span class="plan-unit"></span>
								</p>
							</div>
							<ul class="plan-features">
								<li>美国优质线路</li>
								<li><span class="text-warning bolder">允许免费域名申请</span></li>
								<li><strong>自由升降</strong>产品套餐</li>
								<li>DirectAdmin中文面板</li>
								<li>无附加要求</li>
							</ul>
						  <a href="cart.php?gid=3" class="btn btn-warning btn-lg btn-block plan-button">
							  立即订购<i class="fa fa-angle-double-right icon-on-right"></i>						  </a>						</div>
					</div>
				</div>
				
				<div class="offer">
					<div class="row no-gutter block-s3">
						<div class="col-sm-4 col-lg-3 bg-primary">
							<div class="offer-content">
								<h3>关于免费计划声明</h3>
								<p>完全免费<span class="bigger-170">可靠的</span>托管计划</p>
							</div>
						</div>
						<div class="col-sm-8 col-lg-9 white">
						  <div class="offer-content">
								<h3>香港节点禁止免费以及二级域名申请，美国节点相对允许。</h3>
								<p>为方便管理，所有空间均使用<span class="text-warning">优惠码</span>免费开通<span class="text-warning">续费</span></p>
								
							    <a href="http://www.51mf.top/post-41.html" class="btn btn-primary btn-sm">查看优惠码</a>
							  <i class="fa fa-server"></i>							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>



		<div class="block-s1 white arrow-block">
			<div class="container">
				<div class="text-center">
					<h2>我们的产片<span class="text-warning">特性</span></h2>
				</div>
			</div>
		</div>


		<div class="block-s2 light">
			<div class="container">
				<div class="space-12"></div>
				<div class="owl-carousel features-slider">
					<!-- Wrapper for slides -->  
					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-server"></i>
							<h3>快速加载</h3>
							<p> </p>
						</div>
					</div>
					
					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-support"></i>
							<h3>常用程序</h3>
							<p> </p>
						</div>
					</div>
					
					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-sliders"></i>
							<h3>完美兼容</h3>
							<p></p>
						</div>
					</div>

					<div class="slide-item">
						<div class="tc-box s3 active">
							<i class="fa fa-envelope-o"></i>
							<h3>邮件服务</h3>
							<p></p>
						</div>
					</div>

					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-comments-o"></i>
							<h3>工单支持</h3>
							<p></p>
						</div>
					</div>
					
					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-cloud"></i>
							<h3>来自云端</h3>
							<p> </p>
						</div>
					</div>
					
					<div class="slide-item">
						<div class="tc-box s3">
							<i class="fa fa-briefcase"></i>
							<h3>自由设置</h3>
							<p></p>
						</div>
					</div>
					<!-- /Wrapper for slides -->
				</div>
			</div>
		</div>	


		
		<div class="block-s1 white arrow-block">
			<div class="container">
				<div class="text-center">
					<h2><span class="text-warning">来自客户的评价</span></h2> 
				</div>
			</div>
		</div>
		
		<div class="block-s3 bg-primary">
			<div class="container block-s3">
				<div class=" carousel tc-carousel slide text-center" data-ride="carousel" id="testimonials-block">
					<!-- Wrapper for slides -->
					<div class="carousel-inner">
						<!-- Carousel items -->
						<div class="item active">									
							<div class="padding-2x bigger-190 lighter">
								<i class="fa fa-quote-left pull-left"></i><i> 在用过的所有虚拟主机里面，这可能是最令我满意的，多节点可选，完美兼容所有常用程序。</i>
							</div>
									
							<span class="bigger-190">姚红</span>
							<div class="space-14"></div>
						</div>
						<!-- Carousel items -->
						<div class="item">
							<div class="padding-2x bigger-190 lighter">
								<i class="fa fa-quote-left pull-left"></i><i> 在使用好主机之前我的网站一直处于不太稳定的状态，现在好了，来自香港的托管计划，让我更显奢华。</i>
							</div>
								
							<span class="bigger-190">杨鹏</span>
							<div class="space-14"></div>
						</div>
						<!-- Carousel items -->
						<div class="item">
							<div class="padding-2x bigger-190 lighter">
								<i class="fa fa-quote-left pull-left"></i></i>好的空间以及服务，不需要华丽的言语表达，我支持，所以，我推荐。</i>
							</div>
								
							<span class="bigger-190">李军</span>
							<div class="space-14"></div>
						</div>
					</div>
					<!-- Indicators -->
					<ol class="carousel-indicators">
						<li data-target="#testimonials-block" data-slide-to="0" class="active"></li>
						<li data-target="#testimonials-block" data-slide-to="1" class=""></li>
						<li data-target="#testimonials-block" data-slide-to="2" ></li>
					</ol>
				</div>
			</div>			
		</div>

















<!--div class="block-s2">
	<div class="row">	
		<div class="col-sm-12">	
			{if $twitterusername}	
				<h3>{$LANG.twitterlatesttweets}</h3>
				<div id="twitterfeed">
					<p><img src="images/loading.gif"></p>
				</div>
				{literal}<script language="javascript">jQuery(document).ready(function(){jQuery.post("announcements.php", { action: "twitterfeed", numtweets: 3 },function(data){jQuery("#twitterfeed").html(data);});});</script>{/literal}
			
			{elseif $announcements}
		
			<h3>{$LANG.latestannouncements}</h3>
				{foreach from=$announcements item=announcement}
					<h5><a href="{if $seofriendlyurls}announcements/{$announcement.id}/{$announcement.urlfriendlytitle}.html{else}announcements.php?id={$announcement.id}{/if}">{$announcement.title}</a></h5><p>{$announcement.text|strip_tags|truncate:100:"..."}</p><p><small><i class="fa fa-calendar"></i> {$announcement.date}</small></p>
				{/foreach}
				<p><div class="action-buttons"><a href="announcements.php">View All <i class="fa fa-angle-double-right icon-on-right"></i></a></div></p>
		{/if}	
		</div>
	</div>
</div-->




<div class="modal fade in" id="modalpleasewait">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header text-center">
                 <h4><i class="fa fa-spinner fa-pulse text-warning"></i> {$LANG.pleasewait}</h4>
            </div>
        </div>
    </div>
</div>
