{include file="$template/pageheader.tpl" title=$LANG.domainemailforwarding}
<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
    <input type="hidden" name="id" value="{$domainid}" />
    <p><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-default" /></p>
</form>
<blockquote>
{$LANG.domainname}: {$domain}
</blockquote>
<p>{$LANG.domainemailforwardingdesc}</p>
{if $error}
<div class="alert alert-danger">
    {$error}
</div>
{/if}
{if $external}
<div class="textcenter">
    {$code}
</div>
{else}
<form method="post" action="{$smarty.server.PHP_SELF}?action=domainemailforwarding">
    <input type="hidden" name="sub" value="save" />
    <input type="hidden" name="domainid" value="{$domainid}" />
    <div class="row">
    <div class="col-lg-12">
        <table class="table">
                <tr>
                    <td width="55%">{$LANG.domainemailforwardingprefix}</td>
                    <td width="45%">{$LANG.domainemailforwardingforwardto}</td>
                </tr>
                {foreach key=num item=emailforwarder from=$emailforwarders}
                <tr>
                    <td>
                    <div class="input-group">
                    <input type="text" class="form-control" name="emailforwarderprefix[{$num}]" value="{$emailforwarder.prefix}" />
                    <span class="input-group-addon"> @{$domain}</span>
                    </div>
                    </td>
                    <td><input type="text" class="form-control" name="emailforwarderforwardto[{$num}]" value="{$emailforwarder.forwardto}" /></td>
                </tr>
                {/foreach}
                <tr>
                    <td>
                    <div class="input-group">
                    <input type="text" class="form-control" name="emailforwarderprefixnew" />
                    <span class="input-group-addon"> @{$domain}</span>
                    </div>
                    </td>
                    <td><input type="text" class="form-control" name="emailforwarderforwardtonew" /></td>
                </tr>
        </table>
        </div>
        </div>
    <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />
</form>
{/if}
