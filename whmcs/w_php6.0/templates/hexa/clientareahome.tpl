    <div class="row">
    	<div class="col-md-12">
    		<h3 class="page-header"><span aria-hidden="true" class="icon icon-home"></span> {$LANG.dashboard} <i class="fa fa-info-circle animated bounce show-info"></i>
          {if $showqsl} 
          <span class="pull-right qsl"><a href="#" data-original-title="Quick Server Logins"><span aria-hidden="true" class="icon icon-settings settings-toggle"></span></a></span>
          {/if}
        </h3>        
        <blockquote class="page-information hidden">
         <p>{$LANG.dashboardintro}</p>
       </blockquote>        
     </div>
   </div>
   <div class="row">
     <div class="col-md-4">
      <a title="{$LANG.navservices}" href="clientarea.php?action=products">
       <div class="info-box  bg-info  text-white" id="initial-tour">
        <div class="info-icon bg-info-dark">
         <span aria-hidden="true" class="icon icon-layers"></span>
       </div>
       <div class="info-details">
         <h4>{$LANG.navservices}<span class="pull-right">{$clientsstats.productsnumtotal}</span></h4>
         <p>{$LANG.clientareaactive}<span class="badge pull-right bg-white text-success"> {$clientsstats.productsnumactive}</span> </p>
       </div>
     </div>
   </a>
 </div>
 <div class="col-md-4">
  <a title="{$LANG.cartproductdomain}" href="clientarea.php?action=domains">
   <div class="info-box  bg-info  text-white">
    <div class="info-icon bg-info-dark">
     <span aria-hidden="true" class="icon icon-globe"></span>
   </div>
   <div class="info-details">
     <h4>{$LANG.cartproductdomain}<span class="pull-right">{$clientsstats.numdomains}</span></h4>
     <p>{$LANG.clientareaactive}<span class="badge pull-right bg-white text-success"> {$clientsstats.numactivedomains} </span> </p>
   </div>
 </div>
</a>
</div>
<div class="col-md-4">
  <div class="info-box  bg-info  text-white">
   <div class="info-icon bg-info-dark">
    <span aria-hidden="true" class="icon icon-drawer"></span>
  </div>
  <div class="info-details">
    <h4>{$LANG.invoicesdue}<span class="pull-right">{$clientsstats.numdueinvoices}</span></h4>
    <p>{$LANG.invoicesdue}<span class="badge pull-right bg-white text-success">{$clientsstats.dueinvoicesbalance}</span> </p>
  </div>
</div>
</div>
</div>
<div class="row">
 <div class="col-md-4">
  <div class="info-box  bg-warn  text-white">
   <div class="info-icon bg-warn-dark">
    <span aria-hidden="true" class="icon icon-wallet"></span>
  </div>
  <div class="info-details">
    <h4>{$LANG.statscreditbalance}<span class="pull-right"></span></h4>
    <p><span class="badge"> {$clientsstats.creditbalance}</span> </p>
  </div>
</div>
</div>

<div class="col-md-4">
 <a title="{$LANG.ordernowbutton}" href="cart.php">
  <div class="info-box  bg-inactive  text-white">
   <div class="info-icon bg-inactive-dark">
    <span aria-hidden="true" class="icon icon-plus"></span>
  </div>
  <div class="info-details">
    <h4>{$LANG.ordernowbutton}</h4> 
    <p>{$LANG.statsnumproducts}</p>
  </div>
</div>
</a>
</div>
</div>
{if $announcements}
<div class="panel panel-default">
  <div class="panel-heading"><span aria-hidden="true" class="icon icon-list"></span> {$LANG.ourlatestnews} 
    <div class="pull-right">  <a class="prev"><span class="glyphicon glyphicon-chevron-left"></span></a> <a class="next"><span class="glyphicon glyphicon-chevron-right"></span></a></div>
  </div>
  <div class="panel-body">
   <div id="owl-news" class="owl-carousel">
    <div><span aria-hidden="true" class="icon icon-clock"></span> <a href="announcements.php?id={$announcements.0.id}">{$announcements.0.date}</a> {$announcements.0.text|strip_tags|truncate:500:'...'}</div>
    {if $announcements.1.text}<div><span aria-hidden="true" class="icon icon-clock"></span> <a href="announcements.php?id={$announcements.1.id}">{$announcements.1.date}</a> {$announcements.1.text|strip_tags|truncate:500:'...'}</div>{/if}
    {if $announcements.2.text}<div><span aria-hidden="true" class="icon icon-clock"></span> <a href="announcements.php?id={$announcements.2.id}">{$announcements.2.date}</a> {$announcements.2.text|strip_tags|truncate:500:'...'}</div>{/if}
  </div>
</div>
</div>
{literal}<script>$(document).ready(function() {
  var owl = $("#owl-news");owl.owlCarousel({autoHeight : true, singleItem:true, transitionStyle: "fade" });
  $(".next").click(function(){owl.trigger('owl.next');})
  $(".prev").click(function(){owl.trigger('owl.prev');})
});</script>{/literal}
{/if}
{if $ccexpiringsoon}
<div class="alert alert-danger">
 <p><strong>{$LANG.ccexpiringsoon}:</strong> {$LANG.ccexpiringsoondesc|sprintf2:'
  <a href="clientarea.php?action=creditcard" class="btn btn-danger btn-xs pull-right">':'</a>'}</p>
</div>
{/if}
{foreach from=$addons_html item=addon_html}
<div style="margin: 15px 0;">{$addon_html}</div>{/foreach}
<div class="row">
  <div class="col-lg-12">
    {if in_array('tickets',$contactpermissions)}
    <ul class="nav nav-tabs">
     <li class="active"><a href="#home1" data-toggle="tab"><span class="badge badge-circle badge-success">{$clientsstats.numactivetickets}</span> {$LANG.supportticketsopentickets}</a></li>
     <li class="pull-right"><a href="submitticket.php"><span class="badge badge-circle badge-important"><span aria-hidden="true" class="icon icon-settings"></span> {$LANG.opennewticket}</span></a></li>
   </ul>
   <table class="table table-data table-hover">
    <thead>
      <tr>
        <th>{$LANG.supportticketssubject}</th>
        <th class="hidden-sm hidden-xs">{$LANG.supportticketsdepartment}</th>
        <th class="hidden-sm hidden-xs">{$LANG.supportticketsticketurgency}</th>
        <th class="hidden-sm hidden-xs">{$LANG.supportticketsticketlastupdated}</th>                
        <th></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$tickets item=ticket}
      <tr>
        <td>
          <button type="button" class="btn btn-default btn-xs disabled" style="margin-right:5px;">{$ticket.status}</button><a href="viewticket.php?tid={$ticket.tid}&amp;c={$ticket.c}"> {$ticket.subject}</a>
          <ul class="cell-inner-list">
            <li class="visible-sm visible-xs"><span class="item-title">{$LANG.supportticketsticketlastupdated} : </span>{$ticket.lastreply}</li>
            <li class="visible-sm visible-xs"><span class="item-title">{$LANG.supportticketsdepartment}: </span>{$ticket.department}</li>
            <li class="visible-sm visible-xs"><span class="item-title">{$LANG.supportticketsticketurgency}: </span>{$ticket.urgency}</li>
          </ul>
        </td>
        <td class="hidden-sm hidden-xs">{$ticket.department}</td>
        <td class="hidden-sm hidden-xs">{$ticket.urgency}</td>
        <td class="hidden-sm hidden-xs">{$ticket.lastreply}</td>
        <td><a href="viewticket.php?tid={$ticket.tid}&amp;c={$ticket.c}"><span class="glyphicon glyphicon-chevron-right"></span></a>
        </td>
      </tr>
      {foreachelse}
      <tr>
        <td colspan="5" class="norecords">{$LANG.norecordsfound}</td>
      </tr>{/foreach}
    </tbody>
  </table>
  {/if}
  {if in_array('invoices',$contactpermissions)}
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home2" data-toggle="tab"><span class="badge badge-circle badge-important">{$clientsstats.numdueinvoices}</span> {$LANG.invoicesdue}</a></li>
    <li class="pull-right"><a href="clientarea.php?action=masspay&amp;all=true"><span class="badge badge-circle badge-important"><span aria-hidden="true" class="icon icon-arrow-right"></span> {$LANG.masspayall}</span></a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="home2">

     <form method="post" action="clientarea.php?action=masspay">
      <table class="table table-data table-hover">
       <thead>
        <tr>{if $masspay}
         <th class="cell-checkbox">
          <input type="checkbox" onclick="toggleCheckboxes('invids')" />
        </th>{/if}
        <th>{$LANG.invoicestitle}</th>
        <th class="text-center hidden-sm hidden-xs" style="white-space: nowrap;">{$LANG.invoicesdatecreated}</th>
        <th class="text-center hidden-sm hidden-xs">{$LANG.invoicesdatedue}</th>
        <th class="text-center hidden-sm hidden-xs">{$LANG.invoicesstatus}</th>
        <th class="text-right hidden-sm hidden-xs">{$LANG.invoicestotal}</th>
        <th class="cell-view"></th>
      </tr>
    </thead>
    <tbody>{foreach from=$invoices item=invoice}
      <tr>{if $masspay}
       <td class="cell-checkbox">
        <input type="checkbox" name="invoiceids[]" value="{$invoice.id}" class="invids" />
      </td>{/if}
      <td><a href="viewinvoice.php?id={$invoice.id}" target="_blank" class="item-title">{$invoice.invoicenum}</a>
        <ul class="cell-inner-list visible-sm visible-xs">
         <li><span class="label label-{$invoice.rawstatus} label-danger">{$invoice.statustext}</span></li>
         <li><span class="item-title">{$LANG.invoicestotal} : </span>{$invoice.total}</li>
         <li><span class="item-title">{$LANG.invoicesdatecreated} : </span>{$invoice.datecreated}</li>
         <li><span class="item-title">{$LANG.invoicesdatedue} : </span>{$invoice.datedue}</li>
       </ul>
     </td>
     <td class="text-center hidden-sm hidden-xs">{$invoice.datecreated}</td>
     <td class="text-center hidden-sm hidden-xs">{$invoice.datedue}</td>
     <td class="text-center hidden-sm hidden-xs"><span class="label label-{$invoice.rawstatus} label-danger">{$invoice.statustext}</span>
     </td>
     <td class="text-right hidden-sm hidden-xs">{$invoice.total}</td>
     <td class="cell-view"><a href="viewinvoice.php?id={$invoice.id}" target="_blank"><span class="glyphicon glyphicon-chevron-right pull-right"></span></a>
     </td>
   </tr>{foreachelse}
   <tr>
     <td colspan="{if $masspay}7{else}6{/if}" class="norecords">{$LANG.norecordsfound}</td>
   </tr>{/foreach}</tbody>{if $masspay}
   <tfoot>
     <tr>
      <td class="cell-checkbox"><input type="checkbox" onclick="toggleCheckboxes('invids')" class="invids" /></td>
      <td colspan="5" class=""><input type="submit" name="masspayselected" value="{$LANG.masspayselected}" class="btn btn-default btn-sm" /></td><td class="hidden-sm"></td>
    </tr>
  </tfoot>{/if}
</table>
</form>	
</div>
</div>
{/if}
</div></div>
{if $files}
<h3>{$LANG.clientareafiles}</h3>
<div class="row">
  <div class="form-group">{foreach from=$files item=file}
   <div class="col-lg-6"><div class="well well-sm">
    <a href="dl.php?type=f&amp;id={$file.id}"><h4><span class="glyphicon glyphicon-floppy-disk"></span> {$file.title}</h4></a>
    <p>{$LANG.clientareafilesdate}: {$file.date}</p></div></div>{/foreach}</div>
  </div>
  {/if}


  <div class="right-sidebar right-sidebar-hidden">
   <div class="right-sidebar-holder">
     <h4 class="page-header">{$LANG.quickserverlogins}<a href="javascript:;"  class="theme-panel-close text-primary pull-right"><span aria-hidden="true" class="icon icon-close"></span></a></h4> 
     {if $showqsl}               
     {if in_array('manageproducts',$contactpermissions)}
     {php}
     // Show an error if the login failed
     if(($_REQUEST['failed'] == "1") or ($error == 1)) {
     echo '<div class="alert alert-danger">'.$this->_tpl_vars['LANG']['loginattemptfailed'].'</div>';
   }
   {/php}
   {foreach from=$quickserverlogin item=qsl}
   <ul class="list-group">
   {if $showqslusage eq "Yes" and $qsl.servertype neq "licensing"}
   <li class="list-group-item"><a href="clientarea.php?action=productdetails&id={$qsl.id}">{$qsl.domain}</a><div class="pull-right">{$qsl.code|replace:'class="button"':' class="btn btn-info btn-xs"'|replace:'class="modulebutton"':' class="btn btn-info btn-xs"'}</div></li>
   {/if}
   {if $qsl.servertype eq "licensing"}
   <li class="list-group-item"><a href="clientarea.php?action=productdetails&id={$qsl.id}">{$qsl.productname}</a><div class="pull-right">{$qsl.code|replace:'class="button"':' class="btn btn-info btn-xs"'|replace:'class="modulebutton"':' class="btn btn-info btn-xs"'}</div></li>
   {/if}
    {if $showqslusage eq "Yes" and $qsl.servertype neq "licensing"}
    {literal}
    <script type="text/javascript">
      jQuery(function($) {
        $('.easy-pie-chart.percentage').each(function(){
          $(this).easyPieChart({
            scaleColor: false,
            lineCap: 'butt',
            barColor: '#495b79',
            trackColor: '#dddddd',
            lineWidth: '3',
            animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
            size: '39',
          });
        })
      })
      $(function(){
        $('#right-sidebar-holder').slimScroll({
          height: '250px'
        });
      });
    </script>   
    {/literal}
    <li class="list-group-item">
      <div class="row">
        <div class="infobox-small col-md-6">
          <div class="infobox-progress">
            <div class="easy-pie-chart percentage" data-percent="{$qsl.diskpercent}" data-size="39" style="height: 39px; width: 39px; line-height: 38px;">
              <span class="percent">{$qsl.diskpercent}</span>
              <canvas height="39" width="39"></canvas></div>
              </div>
            <div class="infobox-data">
              <span class="infobox-text">{$LANG.clientareadiskusage}</span>
              <div class="infobox-content">
                {$qsl.diskusage}MB {$LANG.clientareaused}
              </div>
            </div>
          </div>
          <div class="infobox-small col-md-6">
            <div class="infobox-progress">
              <div class="easy-pie-chart percentage" data-percent="{$qsl.bwpercent}" data-size="39" style="height: 39px; width: 39px; line-height: 38px;">
                <span class="percent">{$qsl.bwpercent}</span>
                <canvas height="39" width="39"></canvas></div>
              </div>
              <div class="infobox-data">
                <span class="infobox-text">{$LANG.clientareabwusage}</span>
                <div class="infobox-content">
                  {$qsl.bwusage}MB {$LANG.clientareaused}
                </div>
              </div>
            </div>
          </div>
        </li>
        {/if}   
      </ul>
      {foreachelse}
      <ul class="list-group">
        <li class="list-group-item">{$LANG.norecordsfound}</li>  
      </ul>
      {/foreach}
      {/if}
      {/if}
    </div>
  </div>