<br /><br />
<p align="center">{$message}</p>
<p align="center"><img src="images/loading.gif" alt="Loading" border="0" /></p>
<div id="submitfrm" class="textcenter">
    {$code}
    <form method="post" action="{if $invoiceid}viewinvoice.php?id={$invoiceid}{else}clientarea.php{/if}">
    </form>
</div>
<br /><br /><br />
{literal}
<script language="javascript">
setTimeout("autoForward()", 5000);
function autoForward() {
    var submitForm = $("#submitfrm").find("form:first");
    submitForm.submit();
}
</script>
{/literal}
