<div class="alert alert-block alert-warning textcenter">
    <p>{$message}</p>
</div>

<p><img src="images/loading.gif" alt="Loading" border="0" /></p>

<div id="submitfrm">
    <div align="center" class="textcenter">{$code}</div>
    <form method="post" action="{if $invoiceid}viewinvoice.php?id={$invoiceid}{else}clientarea.php{/if}">
    </form>
</div>

{literal}
<script language="javascript">
setTimeout("autoForward()", 5000);
function autoForward() {
    var submitForm = $("#submitfrm").find("form:first");
    submitForm.submit();
}
</script>
{/literal}