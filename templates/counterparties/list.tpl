<b>{$_lang['List counterparties']}</b><br>

<a class="button green" href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=add">{$_lang['Add counterparty']}</a>

<table class="table_list" id="list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="CompanyName_input" onkeyup="CompanyName_find()" placeholder="{$_lang["Find by Company Name..."]}">
        </th>
        <th>
            <input type="text" class="input" id="VATNumber_input" onkeyup="VATNumber_find()" placeholder="{$_lang['Find by VAT Number...']}">
        </th>
        <th>{$_lang['Address']}</th>
        <th></th>
    </tr>
    {foreach from=$data['counterparties'] key=k item=record}
        <tr>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$record.id}">
                    <b>{$record.CompanyName}</b>
                </a>
            </td>
            <td>{$record.VATNumber}</td>
            <td>{$record.Address}<br><i>{$record.City}</i></td>
            <td>
                <a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=add&c_id={$record.id}">{$_lang['Add document']}</a>
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

    function VATNumber_find() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("VATNumber_input");
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

</script>