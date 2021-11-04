<b>{$_lang["Invoices"]}</b>
{$url = 'addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoices_list'}
{include file="$dir/templates/parts/ListOfDates.tpl"}


<table class="table_list" id="list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="number_input" onkeyup="number_find()" placeholder="{$_lang["Find by number..."]}">
        </th>
        <th>{$_lang['Netto']}</th>
        <th>{$_lang['Brutto']}</th>
        <th>{$_lang['Company Name']}</th>
        <th>{$_lang['Commitment']}</th>
        <th></th>
    </tr>
    {foreach from=$data.invoices key=k item=invoice}
        <tr>
            <td><a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$invoice.id}">
                    <b>{$invoice.number}</b></a><i>({$invoice.DocumentDate})</i><br>
                {foreach from=$invoice.tags key=tk item=tag}
                    <tag class="tags-list"><b>#</b>{$tag}</tag>
                {/foreach}
            </td>
            <td>{$invoice.netto|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$invoice.brutto|string_format:"%.2f"} {$currency.suffix}</td>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$invoice.counterparty_id}"><b>{$invoice.CompanyName}</b></a>
            </td>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$invoice.CommitmentID}"><b>{$invoice.CommitmentName}</b></a>
                <br><i>{$invoice.CommitmentDescription}</i>
                </td>
            <td>
                <a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$invoice.id}">{$_lang['Edit']}</a>
            </td>
        </tr>
    {/foreach}

</table>

<script>
    function number_find() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("number_input");
        filter = input.value.toUpperCase();
        table = document.getElementById("list");
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