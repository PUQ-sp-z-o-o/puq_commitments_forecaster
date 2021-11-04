<b>{$_lang["Annexes"]}</b>
<table class="table_list" id="list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="Number_input" onkeyup="Number_find()" placeholder="{$_lang["Find by number/Description..."]}">
        </th>
        <th><input type="text" class="input" id="CompanyName_input" onkeyup="CompanyName_find()" placeholder="{$_lang["Find by Company Name..."]}"></th>
        <th>{$_lang['Agreement']}</th>
        <th>{$_lang['Commitment date']}</th>
        <th>{$_lang['Expiry date']}</th>
        <th></th>
    </tr>
    {foreach from=$data['annexes'] key=k item=record}
        <tr>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&id={$record.id}">
                <b>{$record.Number}</b><br><i>{$record.Description}</i>
                </a>
            </td>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$record.counterparty_id}"><b>{$record.CompanyName}</b></a>
            </td>
            <td>
            <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$record.AgreementId}"><b>{$record.AgreementNumber}</b>
            <br><i>{$record.AgreementDescription}</i>
            </a>
            </td>
            <td>{$record.CommitmentDate}</td>
            <td>{$record.AgreementExpiryDate}
                {if $record.AgreementIndefinitePeriod == '1'}
                    <i style="font-size: 25px">&#8734;</i>
                {/if}
            </td>
            <td>
                <a class="button green" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&id={$record.id}">{$_lang['Edit']}</a>
            </td>
        </tr>
    {/foreach}

</table>

<script>
    function CompanyName_find() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CompanyName_input");
        filter = input.value.toUpperCase();
        table = document.getElementById("list");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
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

    function Number_find() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Number_input");
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