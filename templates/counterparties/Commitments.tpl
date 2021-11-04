<table class="table_list" id="commitments_list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="CommitmentNameInput" onkeyup="CommitmentNameFind()" placeholder="{$_lang["Find by Name..."]}">
        </th>
        <th>{$_lang['Agreement']}</th>
        <th>{$_lang['Start date']}</th>
        <th>{$_lang['End date']}</th>

        <th></th>
    </tr>
    {foreach from=$data.commitments key=k item=commitment}
        <tr>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$commitment.id}">
                    <b>{$commitment.Name}</b>
                </a>
                <br><i>{$commitment.Description}</i></td>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$commitment.AgreementId}">
                    <b>{$commitment.AgreementNumber}</b><br><i>{$commitment.AgreementDescription}</i>
                </a>
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
    function CommitmentNameFind() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CommitmentNameInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("commitments_list");
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