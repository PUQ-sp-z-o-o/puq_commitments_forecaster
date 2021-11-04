<table class="table_list" id="list">
    <tr class="header">
        <th>ID</th>
        <th>{$_lang['Name/Description']}</th>
        <th>{$_lang['Type']}</th>
        <th>{$_lang['Before synchronization']}</th>
        <th>{$_lang['Added']}</th>
        <th>{$_lang['After synchronization']}</th>
    </tr>
    {foreach from=$data.systems key=k item=system}
        <tr>
            <td>{$system.id}</td>
            <td><b>{$system.Name}</b><br>
                <i>{$system.Description}</i>
            </td>
            <td>{$system.Type}</td>
            <td>{$system.status.count}</td>
            <td>{$system.SynInvoices}</td>
            <td>{$system.SynInvoices+$system.status.count}</td>

        </tr>
    {/foreach}
</table>