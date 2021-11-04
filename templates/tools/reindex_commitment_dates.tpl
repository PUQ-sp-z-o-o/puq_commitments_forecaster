<table class="table_list" id="list">
    <tr class="header">
        <th>ID</th>
        <th>{$_lang['Commitment']}</th>
        <th>{$_lang['New start date']}</th>
        <th>{$_lang['New end date']}</th>
        <th>{$_lang['Indefinite period']}</th>
    </tr>
    {foreach from=$data.commitments key=k item=commitment}
        <tr>
            <td>{$commitment.id}</td>
            <td>{$commitment.Name}</td>
            <td>{$commitment.NewStartDate}</td>
            <td>{$commitment.NewEndDate}</td>
            <td>{$commitment.NewIndefinitePeriod}</td>
        </tr>
    {/foreach}
</table>