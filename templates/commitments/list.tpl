<b>{$_lang["Commitments"]}</b>
{$url = 'addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=list'}
{include file="$dir/templates/parts/ListOfDates.tpl"}


<table class="table_list" id="list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="Name_input" onkeyup="Name_find()" placeholder="{$_lang["Find by Name..."]}">
        </th>
        <th>{$_lang['Netto']}</th>
        <th>{$_lang['Brutto']}</th>
        <th>{$_lang['Invoicess count']}</th>

        <th>{$_lang['Company Name']}</th>
        <th>{$_lang['Start date']}</th>
        <th>{$_lang['End date']}</th>
    </tr>
    {foreach from=$data.commitments key=k item=commitment}
        {if $commitment.MismatchDates == 1}
            <tr style="background: red">
                {else}
            <tr>
        {/if}
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$commitment.id}"><b>{$commitment.Name}</b></a>
                <br><i>{$commitment.Description}</i></td>

            <td>{$commitment.Netto|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$commitment.Brutto|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$commitment.InvoicessCount}</td>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$commitment.counterparty_id}"><b>{$commitment.CompanyName}</b></a>
            </td>
            <td>{$commitment.StartDate}</td>
            <td>
                {$commitment.EndDate}
                {if $commitment.IndefinitePeriod == '1'}
                    <i style="font-size: 20px">&#8734;</i>
                {/if}</td>
        </tr>
    {/foreach}

</table>

<script>
    function Name_find() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Name_input");
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