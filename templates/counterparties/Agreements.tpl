<table class="table_list" id="agreements_list">
    <tr class="header">
        <th>
            <input type="text" class="input" id="AgreementNumberInput" onkeyup="AgreementNumberFind()" placeholder="{$_lang["Find by number/Description..."]}">
        </th>
        <th>{$_lang['Commitment name']}</th>
        <th>{$_lang['Commitment date']}</th>
        <th>{$_lang['Expiry date']}</th>
        <th>
        </th>
    </tr>
    {foreach from=$data.agreements key=k item=agreement}
        <tr>
            <td>
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$agreement.id}">
                    <b>{$agreement.Number}</b><br><i>{$agreement.Description}</i>
                </a>
            </td>
            <td>

                {foreach from=$data.commitments key=k item=commitment}
                    {if $commitment['AgreementId'] == $agreement['id']}
                        <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$commitment['id']}">
                            <b>{$commitment['Name']}</b>
                        </a>
                        (<i>{$commitment['Description']}</i>)<br>
                    {/if}
                {/foreach}

            </td>
            <td>{$agreement.CommitmentDate}</td>
            <td>{$agreement.ExpiryDate}
                {if $agreement.IndefinitePeriod == '1'}
                    <i style="font-size: 25px">&#8734;</i>
                {/if}
            </td>
            <td>
                <a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$agreement.id}">{$_lang['Edit']}</a>
            </td>

        </tr>
    {/foreach}

</table>

<script>
    function AgreementNumberFind() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("AgreementNumberInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("agreements_list");
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