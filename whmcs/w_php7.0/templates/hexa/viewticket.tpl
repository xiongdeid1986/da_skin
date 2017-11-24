{if $error}
<p>{$LANG.supportticketinvalid}</p>
{else}
<h2>{$LANG.supportticketsviewticket|cat:' #'|cat:$tid} <span class="btn btn-default disabled">{$status}</span></h2>
    <script>
    {literal}
        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
        });
        
        $(document).ready( function() {
            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
                
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;
                
                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }
                
            });
        });     
        {/literal}
    </script>
{if $errormessage}
<div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}
<h4>{$subject}</h4>
<div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-calendar"></span> </span>
            <input type="text" disabled class="form-control" placeholder="{$date}">
        </div>
    </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><span aria-hidden="true" class="icon icon-pointer"></span> </span>
            <input type="text" disabled class="form-control" placeholder="{$department}">
        </div>
    </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><span class="glyphicon glyphicon-fire"></span> </span>
            <input type="text" disabled class="form-control" placeholder="{$urgency} {$LANG.supportticketspriority}">
        </div>
    </div>
</div>
</div>

{if $customfields}
<table class="table">
    {foreach from=$customfields item=customfield}
    <tr><td>{$customfield.name}:</td><td>{$customfield.value}</td></tr>
    {/foreach}
</table>
{/if}

<p><div class="btn-toolbar" role="toolbar"><button type="button" onclick="window.location='supporttickets.php'" class="btn btn-default btn-xs">{$LANG.clientareabacklink}</button><button type="button" onclick="jQuery('#replycont').slideToggle()" class="btn btn-xs btn-default"><i class="fa fa-reply"></i> {$LANG.supportticketsreply}</button> {if $showclosebutton} <button type="button" onclick="window.location='{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;closeticket=true'" class="btn btn-danger btn-xs pull-right">{$LANG.supportticketsclose}</button>{/if}</div></p>
<div id="replycont" class="{if !$smarty.get.postreply} subhide{/if}">
    <form method="post" action="{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;postreply=true" enctype="multipart/form-data" class="form-stacked">
        <div class="panel panel-default">
          <div class="panel-body">
            <fieldset class="control-group">
               <div class="row">
                <div class="col-lg-6">
                <div class="form-group">
                <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
               {if $loggedin}<input class="form-control disabled input-sm" type="text" id="name" value="{$clientname}" disabled="disabled" />{else}<input class="input-sm form-control" type="text" name="replyname" id="name" value="{$replyname}" />{/if}
              </div>
              </div>
              </div>
              <div class="col-lg-6">
              <div class="form-group">
              <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
              {if $loggedin}<input class="form-control disabled input-sm" type="text" id="email" value="{$email}" disabled="disabled" />{else}<input class="input-sm form-control" type="text" name="replyemail" id="email" value="{$replyemail}" />{/if}</div>   
              </div>
      </div>
      </div>

      <div class="form-group">
          <div class="controls">
             <textarea name="replymessage" id="message" class="form-control" placeholder="{$LANG.contactmessage}" rows="3">{$replymessage}</textarea>
         </div>
     </div>
                    <div id="fileuploads">
                            <div class="input-group input-group-sm">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-sm btn-file">
                                        <span class="glyphicon glyphicon-folder-open"></span> <input type="file" name="attachments[]" multiple="">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly="">
                            </div>
                        </div>
                        <span class="help-block"><a href="#" class="btn btn-xs btn-default" onclick="extraTicketAttachment();return false"><span class="glyphicon glyphicon-plus-sign"></span> {$LANG.addmore}</a> <small> {$LANG.supportticketsallowedextensions}: {$allowedfiletypes}</small></span>

</fieldset>
<input type="submit" value="{$LANG.supportticketsticketsubmit}" class="btn btn-primary pull-right btn-sm" />
</div>
</div>
</form>
</div>



<div class="panel panel-default">
    {foreach from=$descreplies key=num item=reply}
    <div class="{if $reply.admin}admin{else}client{/if}header panel-heading">
        <div class="pull-right"><h4 class="panel-title" style="margin-top:3px;"><i class="fa fa-clock-o"></i> {$reply.date}</h4></div>
        <h4 class="panel-title">{if $reply.admin}
            <img src="templates/{$template}/img/user.jpg" class="menu-avatar" alt="">{$reply.name} <span class="badge">{$LANG.supportticketsstaff}</span>
            {elseif $reply.contactid}
            {$reply.name} <span class="badge">{$LANG.supportticketscontact}</span>
            {elseif $reply.userid}
            <img src="https://www.gravatar.com/avatar/{php}$userid = $this->_tpl_vars['clientsdetails']['userid'];$result = mysqli_query($GLOBALS['whmcsmysql'],"SELECT email FROM tblclients WHERE id=$userid");$data = mysqli_fetch_array($result);$email = $data["email"];echo md5( strtolower( trim( $email ) ) );{/php}?s=30&d=mm" class="menu-avatar" alt="">{$reply.name}
            {else}
            {$reply.name} <span class="badge">{$reply.email}</span>
            {/if}</h4>
        </div>
        <div class="{if $reply.admin}admin{else}client{/if}msg panel-body">
            <p>{$reply.message}</p>
            {if $reply.attachments}
            <div class="well well-sm">
                <h4><span class="glyphicon glyphicon-floppy-disk" style="color:#aaa;"></span> {$LANG.supportticketsticketattachments}:</h4>
                {foreach from=$reply.attachments key=num item=attachment}
                <a href="dl.php?type={if $reply.id}ar&id={$reply.id}{else}a&id={$id}{/if}&i={$num}">{$attachment}</a>
                {/foreach}
            </div>
            {/if}

            {if $reply.id && $reply.admin && $ratingenabled}
            {if $reply.rating}
            <table class="ticketrating" align="right">
                <tr>
                    <td>{$LANG.ticketreatinggiven}&nbsp;</td>
                    {foreach from=$ratings item=rating}
                    <td background="images/rating_{if $reply.rating>=$rating}pos{else}neg{/if}.png"></td>
                    {/foreach}
                </tr>
            </table>
            {else}
            <table class="ticketrating" align="right">
                <tr onmouseout="rating_leave('rate{$reply.id}')">
                    <td>{$LANG.ticketratingquestion}&nbsp;</td>
                    <td class="point" onmouseover="rating_hover('rate{$reply.id}_1')" onclick="rating_select('{$tid}','{$c}','rate{$reply.id}_1')"><strong>{$LANG.ticketratingpoor}&nbsp;</strong></td>
                    {foreach from=$ratings item=rating}
                    <td class="star" id="rate{$reply.id}_{$rating}" onmouseover="rating_hover(this.id)" onclick="rating_select('{$tid}','{$c}',this.id)"></td>
                    {/foreach}
                    <td class="point" onmouseover="rating_hover('rate{$reply.id}_5')" onclick="rating_select('{$tid}','{$c}','rate{$reply.id}_5')"><strong>&nbsp;{$LANG.ticketratingexcellent}</strong></td>
                </tr>
            </table>
            {/if}
            {/if}

        </div>
        {/foreach}
    </div>

   <p><div class="btn-toolbar" role="toolbar"><button type="button" onclick="window.location='supporttickets.php'" class="btn btn-default btn-xs">{$LANG.clientareabacklink}</button><button type="button" onclick="jQuery('#replycont2').slideToggle()" class="btn btn-default btn-xs"><i class="fa fa-reply"></i> {$LANG.supportticketsreply}</button> {if $showclosebutton} <button type="button" onclick="window.location='{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;closeticket=true'" class="btn btn-danger btn-xs pull-right">{$LANG.supportticketsclose}</button>{/if}</div></p>
    <div id="replycont2" class="subhide">
  <form method="post" action="{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;postreply=true" enctype="multipart/form-data" class="form-stacked">
    <div class="panel panel-default">
      <div class="panel-body">
        <fieldset class="control-group">
         <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                {if $loggedin}<input class="form-control disabled input-sm" type="text" id="name" value="{$clientname}" disabled="disabled" />{else}<input class="input-sm form-control" type="text" name="replyname" id="name" value="{$replyname}" />{/if}
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {if $loggedin}<input class="form-control disabled input-sm" type="text" id="email" value="{$email}" disabled="disabled" />{else}<input class="input-sm form-control" type="text" name="replyemail" id="email" value="{$replyemail}" />{/if}</div>   
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="controls">
             <textarea name="replymessage" id="message" class="form-control" placeholder="{$LANG.contactmessage}" rows="3">{$replymessage}</textarea>
           </div>
         </div>
         <div id="fileuploads">
            <div class="input-group input-group-sm">
              <span class="input-group-btn">
                <span class="btn btn-default btn-file">
                  <span class="glyphicon glyphicon-folder-open"></span> <input type="file" name="attachments[]" multiple="">
                </span>
              </span>
              <input type="text" class="form-control" readonly="">
            </div>
          </div>
          <span class="help-block"><a href="#" class="btn btn-xs btn-default" onclick="extraTicketAttachment();return false"><span class="glyphicon glyphicon-plus-sign"></span> {$LANG.addmore}</a> <small> {$LANG.supportticketsallowedextensions}: {$allowedfiletypes}</small></span>
          </fieldset>
        <input type="submit" value="{$LANG.supportticketsticketsubmit}" class="btn btn-primary pull-right btn-sm" />
      </div>
    </div>
  </form>
</div>
{/if}