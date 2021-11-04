<table class="table_list" id="list">
    <tr class="header">
        <th>ID</th>
        <th>{$_lang['Annex']}</th>
        <th>{$_lang['Agreement']}</th>
        <th>{$_lang['Expiry date']}</th>
        <th>{$_lang['Notice period']}NoticePeriod</th>
        <th>{$_lang['Indefinite period']}</th>
    </tr>
    {foreach from=$data.annexes key=k item=annex}
        <tr>
            <td>{$annex.id}</td>
            <td>{$annex.Number} {$annex.Description}</td>
            <td>{$annex.AgreementNumber} {$annex.AgreementDescription}</td>
            <td>{$annex.NewExpiryDate}</td>
            <td>{$annex.NewNoticePeriod}</td>
            <td>{$annex.NewIndefinitePeriod}</td>
        </tr>
    {/foreach}
</table>