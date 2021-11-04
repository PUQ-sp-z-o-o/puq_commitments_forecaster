<a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">{$_lang['Add document']}</a>
<a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=list">{$_lang['List of commitments']}</a>
<br>

{$summaNetto = 0}
{$summaBrutto = 0}
{$count = 0}
{$CompanyName = array()}
{foreach from=$data['invoices'] key=k item=invoice}
    {$summaNetto = $summaNetto + $invoice['netto']}
    {$summaBrutto = $summaBrutto + $invoice['brutto']}
    {$CompanyName[$invoice['CompanyName']] = $CompanyName[$invoice['CompanyName']] + $invoice['netto']}
    {$count = $count +1 }
{/foreach}
{$avgNetto = $summaNetto/$count}
{$avgBrutto = $summaBrutto/$count}

{$IncomesSummaNetto = 0}
{$IncomesSummaBrutto = 0}
{$IncomesCount = 0}
{foreach from=$data['IncomesInvoices'] key=k item=invoice}
    {$IncomesSummaNetto = $IncomesSummaNetto + $invoice['Netto']}
    {$IncomesSummaBrutto = $IncomesSummaBrutto + $invoice['Brutto']}
    {$IncomesCount = $IncomesCount +1 }
{/foreach}
{$IncomesAvgNetto = $IncomesSummaNetto/$IncomesCount}
{$IncomesAvgBrutto = $IncomesSummaBrutto/$IncomesCount}

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://www.google.com/jsapi"></script>

<div class="input_div">
    <div class="input_data"  style="height: 220px; width: 450px; ">
        <h2>{$_lang['Statistics for the current year']}</h2>
        <table class="table_list" >
            <tr class="header">
                <th>{$_lang['Costs']}: ({$_lang['Count of invoices']}: {$count})</th>
                <th><b>{$_lang['Netto']}</b></th>
                <th><b>{$_lang['Brutto']}</b></th>
            </tr>
            <tr>
                <td><b>{$_lang['Sum']}</b></td>
                <td>{$summaNetto|string_format:"%.2f"} {$currency.suffix}</td>
                <td>{$summaBrutto|string_format:"%.2f"} {$currency.suffix}</td>
            </tr>
            <tr>
                <td><b>{$_lang['Avg per invoice']}</b></td>
                <td>{$avgNetto|string_format:"%.2f"} {$currency.suffix}</td>
                <td>{$avgBrutto|string_format:"%.2f"} {$currency.suffix}</td>
            </tr>
            <tr class="header">
                <th>{$_lang['Revenue']}: ({$_lang['Count of invoices']}: {$IncomesCount})</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td><b>{$_lang['Sum']}</b></td>
                <td>{$IncomesSummaNetto|string_format:"%.2f"} {$currency.suffix}</td>
                <td>{$IncomesSummaBrutto|string_format:"%.2f"} {$currency.suffix}</td>
            </tr>
            <tr>
                <td><b>{$_lang['Avg per invoice']}</b></td>
                <td>{$IncomesAvgNetto|string_format:"%.2f"} {$currency.suffix}</td>
                <td>{$IncomesAvgBrutto|string_format:"%.2f"} {$currency.suffix}</td>
            </tr>
            <tr class="header">
                <th>{$_lang['Incomes']}</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td><b>{$_lang['Sum']}</b></td>
                <td>{($IncomesSummaNetto-$summaNetto)|string_format:"%.2f"} {$currency.suffix}</td>
                <td>{($IncomesSummaBrutto-$summaBrutto)|string_format:"%.2f"} {$currency.suffix}</td>
            </tr>
        </table>
    </div>
    <div class="input_data" style="height: 220px; width:150px);">
        <h2>{$_lang['System information']}</h2>
        <table class="table_list" >
            <tr>
                <td><b>{$_lang['Version']}</b></td>
                <td>{$data.version}</td>
            </tr>
            <tr>
                <td><b>{$_lang['Available version']}</b></td>
                <td>{$data.conf.available_version}</td>
            </tr>
        </table>
        <br>
        <table class="table_list" >
            <tr class="header">
                <td colspan="2" style="text-align: center"><b>{$data.status.MinStartDate} - {$data.status.MaxEndDate}</b></td>
            </tr>
            <tr>
                <td><b>{$_lang['Count of commitments']}</b></td>
                <td>{$data.status.CountOfCommitments}</td>
            </tr>
            <tr>
                <td><b>{$_lang['Active commitments']}</b></td>
                <td>{$data.status.ActiveCommitments}</td>
            </tr>

            <tr>
                <td><b>{$_lang['Count of conterpartners']}</b></td>
                <td>{$data.status.CountOfConterpartners}</td>
            </tr>
            <tr>
                <td><b>{$_lang['Count of invoices']}</b></td>
                <td>{$data.status.CountOfInvoices}</td>
            </tr>

        </table>
    </div>
    <div class="input_data" style="overflow-y: scroll; height: 220px; width: 300px;">
        {$data.conf.info|htmlspecialchars_decode}
    </div>
</div>
<HR>
<div id="oil"></div>

<script>
    google.load("visualization", "1", {literal}{packages:["corechart"]}{/literal});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['', '{$_lang['Costs']}', '{$_lang['Revenue']}', '{$_lang['Incomes']}'],

            {foreach from=$data.ListOfDates key=k item=monthA}
            {foreach from=$monthA key=k2 item=month}
            {$tmpCosts = 0}
            {$tmpRevenue = 0}
            {$tmpIncomes = 0}
            {foreach from=$data.invoices key=k3 item=invoice}
            {if $k|cat:$month == $invoice['DocumentDate']|date_format:"%Y%m"}
            {$tmpCosts = $tmpCosts + $invoice['netto']}
            {/if}
            {/foreach}

            {foreach from=$data.IncomesInvoices key=k4 item=IncomesInvoice}
            {if $k|cat:$month == $IncomesInvoice['InvoiceDate']|date_format:"%Y%m"}
            {$tmpIncomes = $tmpIncomes + $IncomesInvoice['Netto']}
            {/if}
            {/foreach}

            {if $tmpCosts !=0 or $tmpIncomes != 0}
            ['{$k}-{$month}', {$tmpCosts}, {$tmpIncomes},{$tmpIncomes-$tmpCosts} ],
            {/if}
            {/foreach}
            {/foreach}
        ]);
        var options =
                    {literal}{
            chartArea:{left:80},
            {/literal}

            title: '{$_lang['Actual statistics from invoices']}',
            hAxis: {literal}{{/literal}title: ''{literal}}{/literal},
            vAxis: {literal}{{/literal}title: ''{literal}}{/literal}
            {literal}}{/literal};

        var chart = new google.visualization.ColumnChart(document.getElementById('oil'));
        chart.draw(data, options);
    }
</script>