{include file="$template/pageheader.tpl" title=$LANG.domainregisterns}
<p>
<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
    <input type="hidden" name="id" value="{$domainid}" />
    <input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-default" />
</form>
</p>
<blockquote>
    {$LANG.domainname}: {$domain}
</blockquote>
<p>{$LANG.domainregisternsexplanation}</p>
{if $result}
<div class="alert alert-danger">
    <p>{$result}</p>
</div>
{/if}
<hr>
<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns">
    <input type="hidden" name="sub" value="register" />
    <input type="hidden" name="domainid" value="{$domainid}" />
    <h3>{$LANG.domainregisternsreg}</h3>
    <fieldset>
        <div class="row">
            <div class="col-sm-5">
                <label for="ns1">{$LANG.domainregisternsns}</label>
                <div class="input-group">
                    <input type="text" name="ns" id="ns1" class="form-control" />
                    <span class="input-group-addon"> . {$domain}</span>
                </div>
            </div>
            <div class="col-sm-5">
                <label for="ip1">{$LANG.domainregisternsip}</label>
                <input type="text" class="form-control" name="ipaddress" id="ip1" />
            </div>
            <div class="col-sm-2">
                <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary pull-right" style="margin-top:24px;" />
            </div>
        </div>
    </fieldset>
</form>
<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns">
    <input type="hidden" name="sub" value="modify" />
    <input type="hidden" name="domainid" value="{$domainid}" />
    <hr>
    <h3>{$LANG.domainregisternsmod}</h3>
    <fieldset>
        <div class="row">
            <div class="col-sm-4">
                <label for="ns2">{$LANG.domainregisternsns}</label>
                <div class="input-group">
                    <input type="text" name="ns" id="ns2" class="form-control" />
                    <span class="input-group-addon"> . {$domain}</span>
                </div>
            </div>
            <div class="col-sm-3">
                <label for="ip2">{$LANG.domainregisternscurrentip}</label>
                <input type="text" name="currentipaddress" class="form-control" id="ip2" />
            </div>
            <div class="col-sm-3">
                <label for="ip3">{$LANG.domainregisternsnewip}</label>
                <input type="text" name="newipaddress" class="form-control" id="ip3" />
            </div>
            <div class="col-sm-2">
                <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary pull-right" style="margin-top:24px;" />
            </div>
        </div>
    </fieldset>
</form>
<form method="post" action="{$smarty.server.PHP_SELF}?action=domainregisterns">
    <input type="hidden" name="sub" value="delete" />
    <input type="hidden" name="domainid" value="{$domainid}" />
    <hr>
    <h3>{$LANG.domainregisternsdel}</h3>
    <fieldset>
        <div class="row">
            <div class="col-sm-10">
                <label for="ns3">{$LANG.domainregisternsns}</label>
                <div class="input-group">
                    <input type="text" name="ns" id="ns3" class="form-control" />
                    <span class="input-group-addon"> . {$domain}</span>
                </div>
            </div>
            <div class="col-sm-2">
            <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary pull-right" style="margin-top:24px;" />
        </div>
        </div>
    </fieldset>
</form>