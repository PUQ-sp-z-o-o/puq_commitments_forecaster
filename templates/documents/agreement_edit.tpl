<b>{{$_lang['Agreement']}}</b>
{if $data.counterparty.id}
    {include file="$dir/templates/parts/Counterparty.tpl"}
{/if}
<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$data.agreement['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreements_list">{$_lang['List agreements']}</a>
        <a class="button red" onclick="return confirm('{$_lang['You definitely want to unmap the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&unmapping={$data.agreement['id']}">{$_lang['Unmapping']}</a>
        <br>
        <div class="input_data">
            <b>{$_lang['List of commitments']}:</b>
            <br>
            {if $data.commitments == ''}
                {$_lang['None']}<br>
            {/if}
            {foreach from=$data.commitments key=k item=commitment}
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$commitment['id']}">
                    <b>{$commitment['Name']}</b>
                </a>
                (<i>{$commitment['Description']}</i>)<br>
            {/foreach}
            <hr>
            <label for="id"><b>ID</b></label><br>
            <input type="text" name="id" placeholder="ID" value="{$data.agreement['id']}" required readonly/><br>

            <label for="Number"><b>{$_lang['Number']}</b></label><br>
            <input type="text" name="Number" placeholder="{$_lang['Number']}" value="{$data.agreement['Number']}" required/><br>

            <label for="Description"><b>{$_lang['Description']}</b></label>
            <textarea name="Description" cols="40" rows="10" placeholder="{$_lang['Description']}">{$data.agreement['Description']}</textarea></p>

            <label for="ConclusionDate"><b>{$_lang['Conclusion date']}</b></label><br>
            <input type="date" name="ConclusionDate" value="{$data.agreement['ConclusionDate']}" required/><br>

            <label for="CommitmentDate"><b>{$_lang['Commitment date']}</b></label><br>
            <input type="date" name="CommitmentDate" value="{$data.agreement['CommitmentDate']}" required/>

            <label for="ExpiryDate"><b>{$_lang['Expiry date']}</b></label><br>
            <input type="date" name="ExpiryDate" value="{$data.agreement['ExpiryDate']}" />

            <label for="NoticePeriod"><b>{$_lang['Notice period (days)']}</b></label><br>
            <input type=number step=1 name="NoticePeriod" value="{$data.agreement['NoticePeriod']}" />

            {if $data.agreement['IndefinitePeriod'] == "1"}
                <input type="checkbox" name="IndefinitePeriod" checked>
            {else}
                <input type="checkbox" name="IndefinitePeriod">
            {/if}
            <label for="IndefinitePeriod"><b>{$_lang['Indefinite period']}</b></label>
            <hr>
            <b>{$_lang['List annexes']}:</b><br>
            {foreach from=$data.annexes key=k item=annex}
                <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&id={$annex['id']}"><b>{$annex['Number']}</b>(<i>{$annex['Description']}</i>)</a>
                <br>
            {/foreach}

        </div>
        <div class="input_data_pdf">
            <embed src= "addonmodules.php?module=puq_commitments_forecaster&m=get_file&id={$data.agreement['file_id']}" width= "100%" height= "1024">
        </div>
<hr>
    </form>

</div>