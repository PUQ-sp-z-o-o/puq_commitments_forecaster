<table class="table_list" id="invoices_list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="InvoiceNumberInput" onkeyup="InvoiceNumberFind()" placeholder="{$_lang["Find by number..."]}">
        </th>
        <th>{$_lang['Netto']}</th>
        <th>{$_lang['Brutto']}</th>
        <th></th>
    </tr>
    {foreach from=$data.invoices key=k item=invoice}

        {if $invoice.MismatchDates == 1}
            <tr style="background: red">
                {else}
            <tr>
        {/if}
        <td><a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$invoice.id}">
                <b>{$invoice.number}</b></a><i>({$invoice.DocumentDate})</i><br>
            {foreach from=$invoice.tags key=tk item=tag}
                <tag class="tags-list"><b>#</b>{$tag}</tag>
            {/foreach}
        </td>
        <td>{$invoice.netto|string_format:"%.2f"} {$currency.suffix}</td>
        <td>{$invoice.brutto|string_format:"%.2f"} {$currency.suffix}</td>
        <td>
            <a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$invoice.id}">{$_lang['Edit']}</a>
        </td>
        </tr>
    {/foreach}
</table>


<script>
    function InvoiceNumberFind() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("InvoiceNumberInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("invoices_list");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>