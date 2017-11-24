<section id="content-wrapper">

     <!-- BLOCK "TYPE 10" -->
            <div class="block type-10 block-system-slider">
                <div class="main-banner-height">
                    <div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <div class="container container-slide">
                                    <div class="slide-container">
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content">
                                                    <img src="images/slider-image-1.png" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content text-entry">
                                                    <h3 class="title">Data Proccesing</h3>
                                                    <div class="text">Integer faucibus, dui quis pellentesque vestibulum, nulla ante aliquet turpis, in consectetur ex dui vitae erat in eleifend eros</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="container container-slide">
                                    <div class="slide-container">
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content">
                                                    <img src="images/slider-image-2.png" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content text-entry">
                                                    <h3 class="title">Cloud Services</h3>
                                                    <div class="text">Integer faucibus, dui quis pellentesque vestibulum, nulla ante aliquet turpis, in consectetur ex dui vitae erat in eleifend eros, dui quis pellentesque vestibulum,</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="container container-slide">
                                    <div class="slide-container">
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content">
                                                    <img src="images/slider-image-3.png" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slide-block nopadding col-sm-6">
                                            <div class="vertical-align">
                                                <div class="content text-entry">
                                                    <h3 class="title">Virus Protection</h3>
                                                    <div class="text">Integer faucibus, dui quis pellentesque vestibulum, nulla ante aliquet turpis, in consectetur ex dui vitae erat in eleifend eros, dui quis pellentesque vestibulum,</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="container container-slide">
                                    <div class="slide-container">
                                        <div class="slide-block nopadding col-sm-6 col-sm-push-6">
                                            <div class="vertical-align">
                                                <div class="content">
                                                    <img src="images/slider-image-4.png" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slide-block nopadding col-sm-6 col-sm-pull-6">
                                            <div class="vertical-align">
                                                <div class="content text-entry">
                                                    <h3 class="title">Technical Assistance</h3>
                                                    <div class="text">Integer faucibus, dui quis pellentesque vestibulum, nulla ante aliquet turpis, in consectetur ex dui vitae erat in eleifend eros, dui quis pellentesque vestibulum,</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="pagination"></div>
                    </div>
                </div>
                <div class="banner-tabs">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="tab-entry active col-md-3">
                                <div class="title">Data Proccesing</div>
                                <div class="text">mattis gravida risus sed gravida</div>
                            </div>
                            <div class="tab-entry col-md-3">
                                <div class="title">Cloud Services</div>
                                <div class="text">ultrices nulla semper quis</div>
                            </div>
                            <div class="tab-entry col-md-3">
                                <div class="title">Virus Protection</div>
                                <div class="text">bibendum eget nunc fermentum</div>
                            </div>
                            <div class="tab-entry col-md-3">
                                <div class="title">Technical Assistance</div>
                                <div class="text">posuere blandit orci sed porttitor</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="block type-8">
                <div class="container container-form">
                    <div class="row">
                        <div class="form-description col-md-12">
                            <h1 class="title tille_mod">{$LANG.domaincheckerchoosedomain}</h1>
                            <p class="text text-center">{$LANG.domaincheckerenterdomain}</p>
                        </div>
                        {if $condlinks.domainreg || $condlinks.domaintrans || $condlinks.domainown}
                        <div class="well well-form text-center margin-top">
                            <form method="post" action="domainchecker.php">
                                <div class="row form-group">
                                    <div class="col-md-10 col-md-offset-1">
                                        <input class="form-control input-lg" name="domain" type="text" value="" placeholder="{$LANG.domaincheckerdomainexample}">
                                    </div>
                                </div>
                            {if $capatacha}
                                <div class="row">
                                    <div class="col-md-6 col-md-offset-3">
                                        <div class="panel panel-default main-panel">
                                            <div class="panel-body">
                                                <p><small>{$LANG.captchaverify}</small></p>
                                        {if $capatacha eq "recaptcha"}
                                                <p>{$recapatchahtml}</p>
                                        {else}
                                                <div class="col-sm-3 col-sm-offset-3  col-xs-6 text-right ">
                                                    <input id="cap-input" type="text" name="code" class="form-control input-sm" maxlength="5">
                                                </div>
                                                <div class="col-sm-6  col-xs-6  text-left">
                                                    <img src="includes/verifyimage.php" alt="captcha" style="margin-bottom: 20px;">
                                                </div>
                                        {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                                <div>
                                    {if $condlinks.domainreg}<input type="submit" value="{$LANG.checkavailability}" class="btn main-btn btn-primary btn-lg">{/if}
                                    {if $condlinks.domaintrans}<input type="submit" name="transfer" value="{$LANG.domainstransfer}" class="btn main-btn  btn-success btn-lg">{/if}
                                    {if $condlinks.domainown}<input type="submit" name="hosting" value="{$LANG.domaincheckerhostingonly}" class="btn  main-btn btn-default btn-lg">{/if}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <!-- BLOCK "TYPE 1" -->
        <div class="block type-1 block-sercices">
            <div class="container container-sercices">

                <div class="row wow fadeInDown">
                    <div class="block-header col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">
                        <h2 class="title">Our Services</h2>
                        <div class="text">Integer faucibus, dui quis pellentesque vestibulum, nulla ante aliquet turpis, in consectetur ex dui vitae erat in eleifend eros</div>
                    </div>
                </div>

                <div class="row wow fadeInUp">
                    <div class="icon-entry col-xs-12 col-sm-6 col-md-4">
                        <img src="images/icon-1.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Firewall</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-2.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Data Enctyption</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-3.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Data Analysis</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-4.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Data Protection</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-5.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Support Center</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-6.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Technical Service</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-7.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Monitoring</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-8.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Website Optimization</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                    <div class="icon-entry col-xs-12 col-sm-6  col-md-4">
                        <img src="images/icon-9.png" alt=""/>
                        <div class="content">
                            <h3 class="title">Bug Fixing</h3>
                            <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="container container-serv">
            <div class="row">
                <div class="col-md-6 col-info">
                    <img src="images/icon-10.png" alt="img">
                    <h2 class="main-title">{$LANG.navservicesorder}</h2>
                    <p class="main-text">{$LANG.clientareahomeorder}</p>
                    <div class="text-center">
                        <a href="cart.php" title="{$LANG.clientareahomeorderbtn}" class="btn btn-primary">{$LANG.clientareahomeorderbtn}</a>
                    </div>
                </div>
                <div class="col-md-6 col-info">
                    <img src="images/icon-11.png" alt="img">
                    <h2 class="main-title">{$LANG.manageyouraccount}</h2>
                    <p class="main-text">{$LANG.clientareahomelogin}</p>
                    <div class="text-center">
                        <a href="clientarea.php" title="{$LANG.clientareahomeloginbtn}" class="btn btn-primary">{$LANG.clientareahomeloginbtn}</a>
                    </div>
                </div>
            </div>

            {if $twitterusername}
            <div class="page-header">
                <h2>{$LANG.twitterlatesttweets}</h2>
            </div>
            <div id="twitterfeed">
                <p><img src="images/loading.gif" alt="loading..."></p>
            </div>
            {literal}
            <script type="text/javascript">
                jQuery(document).ready(function(){jQuery.post("announcements.php",{action:"twitterfeed",numtweets:3},function(data){jQuery("#twitterfeed").html(data);});});
            </script>
            {/literal}
            {elseif $announcements}
            <h2>{$LANG.latestannouncements}</h2>
                {foreach from=$announcements item=announcement}
            <p>{$announcement.date} - <a href="{if $seofriendlyurls}announcements/{$announcement.id}/{$announcement.urlfriendlytitle}.html{else}announcements.php?id={$announcement.id}{/if}"><b>{$announcement.title}</b></a><br>{$announcement.text|strip_tags|truncate:100:"..."}</p>
                {/foreach}
            {/if}
       </div>
</section>
