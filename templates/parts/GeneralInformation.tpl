<div class="input_data">
    <table class="table_list" >
        <tr>
            <td colspan="2"><b>{$data.status.MinStartDate} - {$data.status.MaxEndDate}</b></td>
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


<div class="input_data" style="width: 350px;">
    <table class="table_list" >
        <tr class="header">
            <th></th>
            <th><b>{$_lang['Netto']}</b></th>
            <th><b>{$_lang['Brutto']}</b></th>
        </tr>
        <tr>
            <td><b>{$_lang['Costs incurred']}</b></td>
            <td>{$data.status.Netto|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$data.status.Brutto|string_format:"%.2f"} {$currency.suffix}</td>
        </tr>
        <tr>
            <td><b>{$_lang['Avg per invoice']}</b></td>
            <td>{$data.status.NettoAVGinvoice|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$data.status.BruttoAVGinvoice|string_format:"%.2f"} {$currency.suffix}</td>
        </tr>

        <tr>
            <td><b>{$_lang['Avg per month']}</b></td>
            <td>{$data.status.NettoAVGmonth|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$data.status.BruttoAVGmonth|string_format:"%.2f"} {$currency.suffix}</td>
        </tr>
        <tr>
            <td><b>{$_lang['To end']}</b></td>
            <td>{$data.status.NettoEnd|string_format:"%.2f"} {$currency.suffix}</td>
            <td>{$data.status.BruttoEnd|string_format:"%.2f"} {$currency.suffix}</td>
        </tr>
    </table>
</div>
<hr>
<script src="https://www.google.com/jsapi"></script>
<script>
    google.load("visualization", "1", {literal}{packages:["corechart"]}{/literal});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['', '{$currency.code}'],
            {foreach from=$data.status['MonthlysRange'] key=k item=month}
            {$tmp = 0}
            {foreach from=$data.status['Invoices'] key=k item=invoice}
            {if $month|date_format:"%Y%m" == $invoice['DocumentDate']|date_format:"%Y%m"}
            {$tmp = $tmp + $invoice['netto']}
            {/if}
            {/foreach}
            ['{$month|date_format:"%Y-%m"}', {$tmp}],
            {/foreach}
        ]);
        var options =
                    {literal}{{/literal}
            title: '{$_lang['Actual statistics from invoices']}',
            hAxis: {literal}{{/literal}title: ''{literal}}{/literal},
            vAxis: {literal}{{/literal}title: ''{literal}}{/literal}
            {literal}}{/literal};

        var chart = new google.visualization.ColumnChart(document.getElementById('oil'));
        chart.draw(data, options);
    }
</script>
<div id="oil" style="width:100%; height:400px;"></div>



