{include file="$template/pageheader.tpl" title=$LANG.sslconfsslcertificate}
{if !$status}
<p>{$LANG.sslinvalidlink}</p>
<p><input type="button" value="{$LANG.clientareabacklink}" class="btn btn-default" onclick="history.go(-1)" /></p>
{else}

{if $errormessage}
<div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

{include file="$template/subheader.tpl" title=$LANG.sslcertinfo}

<div class="row">
    <div class="col-md-6">
        <h4>{$LANG.sslcerttype}:</h4><p>{$certtype}</p>
    </div>
    <div class="col-md-6">
        <h4>{$LANG.sslorderdate}:</h4><p>{$date}</p>
    </div>
    {if $domain}<div class="col-md-6">
    <h4>{$LANG.domainname}:</h4><p>{$domain}</p>
</div>{/if}
<div class="col-md-6">
    <h4>{$LANG.orderprice}:</h4><p>{$price}</p>
</div>
<div class="col-md-6">
    <h4>{$LANG.sslstatus}:</h4><p>{$status}</p>
</div>
{foreach from=$displaydata key=displaydataname item=displaydatavalue}
<div class="col-md-6">
    <h4>{$displaydataname}:</h4><p>{$displaydatavalue}</p>
</div>
{/foreach}
</div>

{if $status eq "Awaiting Configuration"}
<form method="post" action="{$smarty.server.PHP_SELF}?cert={$cert}&step=2">
    {include file="$template/subheader.tpl" title=$LANG.sslserverinfo}
    <p>{$LANG.sslserverinfodetails}</p>
    <fieldset>
        <div class="form-group">
            <label for="servertype">{$LANG.sslservertype}</label>
            <select name="servertype" id="servertype" class="form-control">
                <option value="" selected>{$LANG.pleasechooseone}</option>
                {foreach from=$webservertypes key=webservertypeid item=webservertype}<option value="{$webservertypeid}"{if $servertype eq $webservertypeid} selected{/if}>{$webservertype}</option>{/foreach}
            </select>
        </div>
        <div class="form-group">
            <label for="csr">{$LANG.sslcsr}</label>     
            <textarea name="csr" id="csr" rows="7" class="form-control" >-----BEGIN CERTIFICATE REQUEST-----

                -----END CERTIFICATE REQUEST-----</textarea>
            </div>
        </fieldset>

        {foreach from=$additionalfields key=heading item=fields}
        <p>{$heading}</p>
        <table class="table"><tr><td>
            <table class="table">
                {foreach from=$fields item=vals}
                <tr><td>{$vals.name}</td><td>{$vals.input} {$vals.description}</td></tr>
                {/foreach}
            </table>
        </td></tr></table>
        {/foreach}

        {include file="$template/subheader.tpl" title=$LANG.ssladmininfo}

        <p>{$LANG.ssladmininfodetails}</p>

            <div class="row">
            <div class="col-md-6">
                 <div class="form-group">
                <label for="firstname">{$LANG.clientareafirstname}</label>
                <input type="text" name="firstname" id="firstname" value="{$firstname}" class="form-control" />
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="lastname">{$LANG.clientarealastname}</label>
                <input type="text" name="lastname" id="lastname" value="{$lastname}" class="form-control" />
            </div>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                <label for="orgname">{$LANG.organizationname}</label>       
                <input type="text" name="orgname" id="orgname" value="{$orgname}" class="form-control" />
            </div>
            </div>
            <div class="col-md-6">            
            <div class="form-group">
                <label for="jobtitle">{$LANG.jobtitle}</label>
                <input type="text" name="jobtitle" id="jobtitle" value="{$jobtitle}" class="form-control" />
                <span class="help-block">{$LANG.jobtitlereqforcompany}</span>
            </div>
            </div>
            </div>

            <div class="form-group">
                <label for="email">{$LANG.clientareaemail}</label>
                <input type="text" name="email" id="email" value="{$email}" class="form-control" />
            </div>

            <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                <label for="address1">{$LANG.clientareaaddress1}</label>        
                <input type="text" name="address1" id="address1" value="{$address1}" class="form-control" />
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="address2">{$LANG.clientareaaddress2}</label>        
                <input type="text" name="address2" id="address2" value="{$address2}" class="form-control" />
            </div>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">            
            <div class="form-group">
                <label for="city">{$LANG.clientareacity}</label>        
                <input type="text" name="city" id="city" value="{$city}" class="form-control" />
            </div>
            </div>
            <div class="col-md-6"> 
            <div class="form-group">
                <label for="state">{$LANG.clientareastate}</label>
                <input type="text" name="state" id="state" value="{$state}" class="form-control" />
            </div>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">  
            <div class="form-group">
                <label for="postcode">{$LANG.clientareapostcode}</label>
                <input type="text" name="postcode" id="postcode" value="{$postcode}" class="form-control" />
            </div>
            </div>
            <div class="col-md-6"> 
            <div class="form-group">
                <label for="country">{$LANG.clientareacountry}</label>
                {$countriesdropdown|replace:'name="country"':'name="country" style="width:100%; height: 34px; padding: 6px 12px; font-size: 14px; border-radius: 4px; vertical-align: middle; border: 1px solid #ccc; color: #555; line-height: 1.428571429;"'}
            </div>
            </div>
            </div>

            <div class="form-group">
                <label for="phonenumber">{$LANG.clientareaphonenumber}</label>
                <input type="text" name="phonenumber" id="phonenumber" value="{$phonenumber}" class="form-control" />
            </div>
        <input type="submit" value="{$LANG.ordercontinuebutton}" class="btn btn-primary" />
    </form>

    {else}

    <form method="post" action="clientarea.php?action=productdetails">
        <input type="hidden" name="id" value="{$serviceid}" />
        <p><input type="submit" value="{$LANG.invoicesbacktoclientarea}" class="btn btn-default" /></p>
    </form>

    {/if}{/if}
