<b>{{$_lang['Annex']}}</b>
{if $data.counterparty.id}
    {include file="$dir/templates/parts/Counterparty.tpl"}
{/if}
<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&id={$data.annex['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annexes_list">{$_lang['List annexes']}</a>
        <a class="button red" onclick="return confirm('{$_lang['You definitely want to unmap the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&unmapping={$data.annex['id']}">{$_lang['Unmapping']}</a>
        <br>
        <div class="input_data">

            <label for="AgreementId"><b>{$_lang['Agreement']}</b></label><br>
            <select name="AgreementId" >
                <option value="0" selected>{$_lang['No agreement']}</option>
                {foreach from=$data.agreements key=k item=agreement}
                    {if $data.annex.AgreementId == $agreement.id}
                        <option value="{$agreement.id}" selected>{$agreement.Number} | {$agreement.Description}</option>
                    {else}
                        <option value="{$agreement.id}">{$agreement.Number} | {$agreement.Description}</option>
                    {/if}
                {/foreach}
            </select>
            {if $data.annex['AgreementId'] != 0}
                <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$data.annex['AgreementId']}">{$_lang['Go to agreement']}</a>
            {/if}
            <hr>
            <label for="id"><b>ID</b></label><br>
            <input type="text" name="id" placeholder="ID" value="{$data.annex['id']}" required readonly/><br>

            <label for="Number"><b>{$_lang['Number']}</b></label><br>
            <input type="text" name="Number" placeholder="{$_lang['Number']}" value="{$data.annex['Number']}" required/><br>

            <label for="Description"><b>{$_lang['Description']}</b></label>
            <textarea name="Description" cols="40" rows="10" placeholder="{$_lang['Description']}">{$data.annex['Description']}</textarea></p>

            <label for="ConclusionDate"><b>{$_lang['Conclusion date']}</b></label><br>
            <input type="date" name="ConclusionDate" value="{$data.annex['ConclusionDate']}" required/><br>

            <label for="CommitmentDate"><b>{$_lang['Commitment date']}</b></label><br>
            <input type="date" name="CommitmentDate" value="{$data.annex['CommitmentDate']}" required/>

            <hr>
            {if $data.annex['ChangeAgreement'] == "1"}
                <input type="checkbox" name="ChangeAgreement" checked>
            {else}
                <input type="checkbox" name="ChangeAgreement">
            {/if}
            <label for="ChangeAgreement"><b>{$_lang['Change agreement']}</b></label>



            <label for="AgreementExpiryDate"><b>{$_lang['Agreement expiry date']}</b></label><br>
            <input type="date" name="AgreementExpiryDate" value="{$data.annex['AgreementExpiryDate']}" />

            <label for="AgreementNoticePeriod"><b>{$_lang['Agreement notice period (days)']}</b></label><br>
            <input type=number step=1 name="AgreementNoticePeriod" value="{$data.annex['AgreementNoticePeriod']}" />

            {if $data.annex['AgreementIndefinitePeriod'] == "1"}
                <input type="checkbox" name="AgreementIndefinitePeriod" checked>
            {else}
                <input type="checkbox" name="AgreementIndefinitePeriod">
            {/if}
            <label for="AgreementIndefinitePeriod"><b>{$_lang['Agreement indefinite period']}</b></label>


        </div>
        <div class="input_data_pdf">
            <embed src= "addonmodules.php?module=puq_commitments_forecaster&m=get_file&id={$data.annex['file_id']}" width= "100%" height= "1024">
        </div>
<hr>
    </form>

</div>