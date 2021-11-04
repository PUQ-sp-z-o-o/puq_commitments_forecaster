<b>{$_lang['List of income systems']}</b><br>
<a class="button green" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=systems_list&create=apiWHMCS">{$_lang['Create']} apiWHMCS</a>
<table class="table_list" id="list">
    <tr class="header">
        <th>ID</th>
        <th>{$_lang['Name']}</th>
        <th>{$_lang['Type']}</th>
        <th>{$_lang['Automatic sync']}</th>
        <th>{$_lang['Count of invoices']}</th>
        <th>{$_lang['Exchange rate']}</th>
        <th>{$_lang['Netto']}</th>
        <th>{$_lang['Brutto']}</th>

        <th></th>
    </tr>
    {foreach from=$data.systems key=k item=system}
        <tr>
            <td><b>{$system['id']}</b>
            <td><b>{$system['Name']}</b><br>
                <i>{$system['Description']}</i>
            </td>
            <td>{$system['Type']}</td>
            <td>
                {if $system['AutomaticSync'] == 1}
                    {$_lang['YES']}
                {else}
                    {$_lang['No']}
                {/if}
            </td>

            <td>{$system.status.count}</td>
            <td>{$system['ExchangeRate']}</td>
            <td>{($system.status.Netto*$system['ExchangeRate'])|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{($system.status.Brutto*$system['ExchangeRate'])|string_format:"%.2f"} {$currency.suffix}</td>

            <td>
                <a class="button green" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action={$system['Type']}_edit&id={$system['id']}">{$_lang['Edit']}</a>
            </td>
        </tr>
    {/foreach}
</table>


