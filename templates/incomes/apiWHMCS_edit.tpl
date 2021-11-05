<b>{$_lang['Income system']}</b>
<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=apiWHMCS_edit&id={$data.system['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=systems_list">{$_lang['List of systems']}</a>

        <a class="button red" onclick="return confirm('{$_lang['You definitely want to delete the system and all invoices?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=systems_list&delete={$data.system['id']}">{$_lang['Delete']}</a>
        <br>
        <div class="input_data">
            <label for="id"><b>ID</b></label><br>
            <input type="text" name="id" placeholder="ID" value="{$data.system['id']}" required readonly/><br>
            <label for="Name"><b>{$_lang['Name']}</b></label><br>
            <input type="text" name="Name" placeholder="{$_lang['Name']}" value="{$data.system['Name']}" required/><br>
            <label for="Description"><b>{$_lang['Description']}</b></label>
            <textarea name="Description" cols="20" rows="5" placeholder="{$_lang['Description']}">{$data.system['Description']}</textarea></p>
            <label for="URL"><b>{$_lang['URL']}</b></label><br>
            <input type="text" name="URL" placeholder="{$_lang['URL']}" value="{$data.system['URL']}" required/><br>
            <label for="Username"><b>{$_lang['Username']}</b></label><br>
            <input type="text" name="Username" placeholder="{$_lang['Username']}" value="{$data.system['Username']}" /><br>
            <label for="Password"><b>{$_lang['Password']}</b></label><br>
            <input type="text" name="Password" placeholder="{$_lang['Password']}" value="{$data.system['Password']}" /><br>
            <label for="APIkey1"><b>APIkey1</b></label><br>
            <input type="text" name="APIkey1" placeholder="APIkey1" value="{$data.system['APIkey1']}" /><br>
            <label for="APIkey2"><b>APIkey2</b></label><br>
            <input type="text" name="APIkey2" placeholder="APIkey2" value="{$data.system['APIkey2']}" /><br>
            <label for="APIkey1"><b>APIkey3</b></label><br>
            <input type="text" name="APIkey3" placeholder="APIkey3" value="{$data.system['APIkey3']}" /><br>

            <label for="Step"><b>{$_lang['Step']}</b></label><br>
            <input type=number min="1" step="1" name="Step" value="{$data.system['Step']}" />

            {if $data.system['AutomaticSync'] == "1"}
                <input type="checkbox" name="AutomaticSync" checked>
            {else}
                <input type="checkbox" name="AutomaticSync">
            {/if}
            <label for="AutomaticSync"><b>{$_lang['Automatic sync']}</b></label><br>
            <label for="ExchangeRate"><b>{$_lang['Exchange rate']}</b></label><br>
            <input type=number step=0.0001 name="ExchangeRate" value="{$data.system['ExchangeRate']}" />
        </div>

        <div class="input_data" style="width: calc(100% - 265px);">

            <div class="input_data">
            {if !$data.test}
                <table class="table_list" >
                    <tr>
                        <td colspan="2"><b><font color="red">{$_lang['API connection error!']}</font></b></td>
                    </tr>
                </table>
            {/if}

            {if $data.test['result'] == 'success'}
                <table class="table_list" >
                    <tr>
                        <td colspan="2"><b><font color="green">{$_lang['API connection successful']}</font></b></td>
                    </tr>
                    <tr>
                        <td><b>{$_lang['Invoicess count']}</b></td>
                        <td>{$data.test.totalresults}</td>
                    </tr>
                    <tr>
                        <td><b>{$_lang['Number of import steps']}</b></td>
                        <td>{(($data.test.totalresults/$data.system['Step'])|ceil)  }</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <a class="button blue" onclick="return confirm('{$_lang['You just want to start syncing?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=apiWHMCS_edit&id={$data.system['id']}&step=1">{$_lang['Full sync']}</a>
                        </td>
                    </tr>
                </table>

                {if $data.step == 'OK'}
                    {$total = 0}
                    {foreach from=$smarty.session.FullSyncWHMCS key=k item=m}
                    {$_lang['Step']} {$k} - {$m['status']} {$_lang['Sync']}: {$m['sync']}<br>
                        {$total = $total + $m['sync']}
                    {/foreach}
                <b>{$_lang['Total sync']}:</b> {$total}
                {/if}

                {if $smarty.get.step and $smarty.get.step <  (($data.test.totalresults/$data.system['Step'])|ceil) }
                    <script>
                        setTimeout('window.location = "addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=apiWHMCS_edit&id={$data.system['id']}&step={$smarty.get.step+1}";', 100);
                    </script>
                {/if}
            {else}
                <table class="table_list" >
                    <tr>
                        <td colspan="2"><b><font color="red">{$_lang['API connection error!']}</font></b></td>
                    </tr>
                </table>
            {/if}
            </div>

            <div class="input_data" style="width: 300px;">
                <table class="table_list" >
                    <tr>
                        <td><b>{$_lang['Count of invoices']}</b></td>
                        <td>{$data.system.status.count}</td>
                    </tr>
                    <tr>
                        <td><b>{$_lang['Netto']}</b></td>
                        <td>{($data.system.status.Netto*$data.system['ExchangeRate'])|string_format:"%.2f"} {$currency.suffix}</td>
                    </tr>
                    <tr>
                        <td><b>{$_lang['Brutto']}</b></td>
                        <td>{($data.system.status.Brutto*$data.system['ExchangeRate'])|string_format:"%.2f"} {$currency.suffix}</td>
                    </tr>
                    <tr>
                        <td><b>{$_lang['Last synchronization']}</b></td>
                        <td>{$data.system.LastSyncDate}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <a class="button blue" onclick="return confirm('{$_lang['You just want to start syncing?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=apiWHMCS_edit&id={$data.system['id']}&sync={$data.system['id']}">{$_lang['Sync']}</a>
                        </td>
                    </tr>
                </table>

            </div>






            <hr>
        </div>








    </form>
</div>




