<b>{{$_lang['Invoice']}}</b>
{if $data.counterparty.id}
    {include file="$dir/templates/parts/Counterparty.tpl"}
{/if}
<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id={$data.invoice['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoices_list">{$_lang['List invoices']}</a>
        <a class="button red" onclick="return confirm('{$_lang['You definitely want to unmap the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&unmapping={$data.invoice['id']}">{$_lang['Unmapping']}</a>
        <br>
        <div class="input_data">
            <label for="CommitmentId"><b>{$_lang['Commitment']}</b></label><br>
            {if $data.invoice['CommitmentId'] != 0}
            <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$data.invoice['CommitmentId']}">{$_lang['Go to commitment']}</a>
            {/if}

            <select name="CommitmentId" >
                <optgroup label="{$data.counterparty.CompanyName}">
                {foreach from=$data.commitments key=k item=commitment}
                            {if $data.counterparty['id'] == $commitment['counterparty_id']}
                                {if $data.invoice['CommitmentId'] == $commitment['id']}
                                    <option value="{$commitment.id}" selected >{$commitment.Name} | {$commitment.Description}</option>
                                    {$selected = '1'}
                                {else}
                                    <option value="{$commitment.id}">{$commitment.Name} | {$commitment.Description}</option>
                                {/if}
                            {/if}
                {/foreach}
                </optgroup>
                <optgroup label="{$_lang['Other']}">
                    {foreach from=$data.commitments key=k item=commitment}
                        {if $commitment['counterparty_id'] == ""}
                            <option value="{$commitment.id}">{$commitment.Name} | {$commitment.Description}</option>
                        {/if}
                    {/foreach}
                </optgroup>
                {if $data.invoice['CommitmentId'] == '0'}
                    <option value="0" selected>{$_lang['No commitments']}</option>
                {else}
                    <option value="0">{$_lang['No commitments']}</option>
                {/if}
            </select>

            {if $selected == '1'}
                <input type="checkbox" name="CreateNewCommitment">
            {else}
                <input type="checkbox" name="CreateNewCommitment" />
            {/if}

            <label for="CreateNewCommitment">{$_lang['Create new commitment']}</label>
            <HR>
            <label for="id"><b>ID</b></label><br>
            <input type="text" name="id" placeholder="ID" value="{$data['invoice']['id']}" required readonly/><br>

            <label for="number"><b>{$_lang['Number']}</b></label><br>
            <input type="text" name="number" placeholder="{$_lang['Number']}" value="{$data['invoice']['number']}" required/><br>

            <label for="netto"><b>{$_lang['Netto']}</b></label><br>
            <input type=number step=0.01 name="netto" placeholder="{$_lang['Netto']}" value="{$data['invoice']['netto']}" required/><br>

            <label for="vat"><b>{$_lang['VAT']}</b></label><br>
            <input type=number step=0.01 name="vat" placeholder="{$_lang['VAT']}" value="{$data['invoice']['vat']}" required/><br>

            <label for="brutto"><b>{$_lang['Brutto']}</b></label><br>
            <input type=number step=0.01 name="brutto" placeholder="{$_lang['Brutto']}" value="{$data['invoice']['brutto']}" required/><br>

            <label for="DocumentDate"><b>{$_lang['Document Date']}</b></label><br>
            <input type="date" name="DocumentDate" value="{$data['invoice']['DocumentDate']}" required/><br>

            <label for="ReceiptDate"><b>{$_lang['Receipt Date']}</b></label><br>
            <input type="date" name="ReceiptDate" value="{$data['invoice']['ReceiptDate']}" required/>
            {include file="$dir/templates/parts/TagsInput.tpl"}
        </div>
        <div class="input_data_pdf">
            <embed src= "addonmodules.php?module=puq_commitments_forecaster&m=get_file&id={$data['invoice']['file_id']}" width= "100%" height= "1024">
        </div>
    </form>

</div>




