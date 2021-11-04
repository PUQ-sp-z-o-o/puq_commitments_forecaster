<b>{{$_lang['Unclassified document']}}</b>
<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$data.document['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list">{$_lang['List of unclassified documents']}</a>
        {foreach from=$data.documents key=k item=doc}
            {if $doc['id'] == $data.document['id']}
                {if $k+1!=$data.documents|count}
                    <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$data.documents[$k+1]['id']}"> << {$_lang['Previous']}</a>
                {/if}

                {if $k!=0}
                    <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$data.documents[$k-1]['id']}">{$_lang['Next']} >> </a>
                {/if}
            {/if}
        {/foreach}

        {foreach from=$data.documents key=k item=doc}
            {if $doc['id'] == $data.document['id']}
                {if $k!=0}
                    <a class="button red" onclick="return confirm('{$_lang['Are you sure you want to delete the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$data.documents[$k-1]['id']}&delete={$data.document['id']}">{$_lang['Delete']}</a>
                {else}
                    {if $k+1!=$data.documents|count}
                        <a class="button red" onclick="return confirm('{$_lang['Are you sure you want to delete the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$data.documents[$k+1]['id']}&delete={$data.document['id']}">{$_lang['Delete']}</a>
                    {/if}
                {/if}
            {/if}
        {/foreach}

 {if $data.documents|count == 1}
        <a class="button red" onclick="return confirm('{$_lang['Are you sure you want to delete the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list&delete={$data.document['id']}">{$_lang['Delete']}</a>
 {/if}


        <br>
        <div class="input_data">

            <label for="CounterpartyId"><b>{$_lang['Counterparty']}</b></label><br>

            <select name="CounterpartyId">
                 {foreach from=$data.counterparties key=k item=counterparty}
                                <option value="{$counterparty['id']}" selected >{$counterparty['CompanyName']} | {$counterparty['VATNumber']}</option>
                    {/foreach}
            </select>
            <HR>
            <p>
                <input name="DocumentType" type="radio" value="invoice" checked>
                <label>{$_lang['Invoice']}</label>
            </p>
            <p>
                <input name="DocumentType" type="radio" value="agreement">
                <label>{$_lang['Agreement']}</label>
            </p>
            <p>
                <input name="DocumentType" type="radio" value="annex">
                <label>{$_lang['Agreement annex']}</label>
            </p>
        </div>
        <div class="input_data_pdf">
            <embed src= "addonmodules.php?module=puq_commitments_forecaster&m=get_file&id={$data.document['id']}" width= "100%" height= "1024">
        </div>
    </form>

</div>