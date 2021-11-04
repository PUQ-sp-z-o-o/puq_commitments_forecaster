{$all=1}
{$url = 'addonmodules.php?module=puq_commitments_forecaster&m=reports&action=invoices'}
{include file="$dir/templates/parts/ListOfDates.tpl"}

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

    <div class="input_data"  style="width: 815px; ">
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

        <div id="oil" style="width:100%; height:300px;"></div>

        <div id="piechart" style="width: 800px; height: 300px;"></div>
    </div>

    <div class="input_data" style="width: 370px;">

        <div id="chart_div" style="height: 250px; width: 350px;"></div>

        <table class="table_list" >
             <tr class="header">
                <th colspan="3" style="text-align: center;"><b>{$_lang['Invoices']}</b></th>
            </tr>
            {foreach from=$data['invoices'] key=k item=invoice}
                <tr>
                    <td>
                        <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$invoice['id']}"><b>{$invoice['number']}</b></a>
                        <br>
                        <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$invoice['counterparty_id']}"
                           title="{$invoice['CompanyName']}">
                            {$invoice['CompanyName']|truncate:30}
                        </a>
                    </td>
                    <td>{$invoice['netto']|string_format:"%.2f"} {$currency.suffix}</td>
                    <td>{$invoice['brutto']|string_format:"%.2f"} {$currency.suffix}</td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>



{literal}


<script type="text/javascript">

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);


    function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],

            {/literal}


            {foreach from=$CompanyName key=k item=netto}
                ['{$k}',{$netto}],
            {/foreach}

        ]);

        var options = {literal}{
            chartArea:{left:20,top:20,width:'800',height:'300'},
            legend: {position: 'left'},

            {/literal}
            title: '{$smarty.get.month}-{$smarty.get.year} ({$summaNetto|string_format:"%.2f"} {$currency.suffix})'
            {literal}};

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }


</script>
{/literal}

{literal}
<script>
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawAxisTickColors);

    function drawAxisTickColors() {
        var data = google.visualization.arrayToDataTable([
            {/literal}

            ['', '{$_lang['Costs']}', '{$_lang['Revenue']}', '{$_lang['Incomes']}'],
            ['', {$summaNetto|string_format:"%.2f"}, {$IncomesSummaNetto|string_format:"%.2f"}, {($IncomesSummaNetto-$summaNetto)|string_format:"%.2f"}],

            {literal}
        ]);

        var options = {
           chartArea:{left:0,top:0, height:200, width:250},
            {/literal}
            title: '{$smarty.get.month}-{$smarty.get.year}',
            {literal}

            hAxis: {
                {/literal}
                title: '{$currency.code}',
                {literal}
                minValue: 0,
                textStyle: {
                    bold: true,
                    fontSize: 12,
                    color: '#4d4d4d'
                },
                titleTextStyle: {
                    bold: true,
                    fontSize: 18,
                    color: '#4d4d4d'
                }
            },
            vAxis: {
                title: '',
                textStyle: {
                    fontSize: 14,
                    bold: true,
                    color: '#848484'
                },
                titleTextStyle: {
                    fontSize: 14,
                    bold: true,
                    color: '#848484'
                }
            }
        };
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
{/literal}

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
           chartArea:{left:50,top:10,width:'630',height:'200'},
                    {/literal}

            title: '{$_lang['Actual statistics from invoices']}',
            hAxis: {literal}{{/literal}title: ''{literal}}{/literal},
            vAxis: {literal}{{/literal}title: ''{literal}}{/literal}
            {literal}}{/literal};

        var chart = new google.visualization.ColumnChart(document.getElementById('oil'));
        chart.draw(data, options);
    }
</script>