{include file="$template/pageheader.tpl" title=$LANG.clientareanavsecurity}

{include file="$template/clientareadetailslinks.tpl"}

{if $successful}
<div class="alert alert-success">
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $errormessage}
<div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

{if $twofaavailable}

{if $twofaactivation}

<script>{literal}
    function dialogSubmit() {
        $('div#twofaactivation form').attr('method', 'post');
        $('div#twofaactivation form').attr('action', 'clientarea.php');
        $('div#twofaactivation form').attr('onsubmit', '');
        $('div#twofaactivation form').submit();
        return true;
    }
    {/literal}</script>

    <div id="twofaactivation">
        {$twofaactivation}
    </div>

    <script type="text/javascript">
        $("#twofaactivation input:text:visible:first,#twofaactivation input:password:visible:first").focus();
    </script>

    {else}

    <h2>{$LANG.twofactorauth}</h2>

    <p>{$LANG.twofaactivationintro}</p>

    <form method="post" action="clientarea.php?action=security">
        <input type="hidden" name="2fasetup" value="1" />
        <p>
            {if $twofastatus}
            <input type="submit" value="{$LANG.twofadisableclickhere}" class="btn btn-danger" />
            {else}
            <input type="submit" value="{$LANG.twofaenableclickhere}" class="btn btn-success" />
            {/if}
        </p>
    </form>

    {/if}

    {/if}

    {if $securityquestionsenabled && !$twofaactivation}

    <h4>{$LANG.clientareanavsecurityquestions}</h4>
    <form method="post" action="{$smarty.server.PHP_SELF}?action=changesq">


        {if !$nocurrent}
        <div class="row">
                <div class="col-lg-12">
        <label for="currentans">{$currentquestion}</label>
        <div class="form-group">
              <input type="password" class="form-control" name="currentsecurityqans" id="currentans" />
          </div>    
      </div>
      </div>
      {/if}

      <div class="row">
          <div class="col-lg-12">
             <label for="securityqid">{$LANG.clientareasecurityquestion}</label>       
             <div class="form-group">
               <select name="securityqid" class="form-control" id="securityqid">
                {foreach key=num item=question from=$securityquestions}
                <option value={$question.id}>{$question.question}</option>
                {/foreach}  
            </select>
        </div>
        <label for="securityqans">{$LANG.clientareasecurityanswer}</label>
        <div class="form-group">
          <input type="password" class="form-control" name="securityqans" id="securityqans" />
      </div>
      <label for="securityqans2">{$LANG.clientareasecurityconfanswer}</label>
      <div class="form-group">
          <input type="password" class="form-control" name="securityqans2" id="securityqans2" />
      </div>
    <div class="btn-toolbar pull-right" role="toolbar">
      <input class="btn btn-primary btn-sm pull-right" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
      <input class="btn btn-link btn-sm pull-right" type="reset" value="{$LANG.cancel}" />
  </div>
</div>
</form>
{/if}