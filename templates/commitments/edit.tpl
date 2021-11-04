<b>{$_lang["Commitment"]}</b>
{if $data.counterparty.id}
    {include file="$dir/templates/parts/Counterparty.tpl"}
{/if}
<div class="input_div" id="input_div">
    <form action="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id={$data.commitments['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=list">{$_lang['List of commitments']}</a>
        <a class="button red" onclick="return confirm('{$_lang['Are you sure you want to remove the commitment?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&delete={$data.commitments['id']}">{$_lang['Delete']}</a>

        <br>
        <div class="input_data">
            <label for="Name"><b>{$_lang['Commitment name']}</b></label>
            <input type="text" name="Name" placeholder="{$_lang['Commitment name']}" value="{$data.commitments['Name']}" required />
            <label for="Description"><b>{$_lang['Description']}</b></label>
            <textarea name="Description" cols="40" rows="10" placeholder="{$_lang['Description']}">{$data.commitments['Description']}</textarea></p>


            {if $data.commitments.AgreementId > 0}
                <label for="StartDate"><b>{$_lang['Start date']}</b></label><br>
                <input type="date" name="StartDate" value="{$data.commitments['StartDate']}" readonly/>
                <label for="EndDate"><b>{$_lang['End date']}</b></label><br>
                <input type="date" name="EndDate" value="{$data.commitments['EndDate']}" readonly/>
                {if $data.commitments['IndefinitePeriod'] == "1"}
                    <input type="checkbox" name="IndefinitePeriod" checked disabled/>
                {else}
                    <input type="checkbox" name="IndefinitePeriod" readonly disabled/>
                {/if}
                <label for="IndefinitePeriod"><b>{$_lang['Indefinite period']}</b></label><br>
                <i>{$_lang['The fields are inactive due to a signed contract - the values are taken from the contract']}</i>
                <br>
            {else}
                <label for="StartDate"><b>{$_lang['Start date']}</b></label><br>
                <input type="date" name="StartDate" value="{$data.commitments['StartDate']}" />
                <label for="EndDate"><b>{$_lang['End date']}</b></label><br>
                <input type="date" name="EndDate" value="{$data.commitments['EndDate']}"/>
                {if $data.commitments['IndefinitePeriod'] == "1"}
                    <input type="checkbox" name="IndefinitePeriod" checked>
                {else}
                    <input type="checkbox" name="IndefinitePeriod">
                {/if}
                <label for="IndefinitePeriod"><b>{$_lang['Indefinite period']}</b></label><br>

            {/if}
            <label for="AgreementId"><b>{$_lang['Agreement']}</b></label><br>

            {if $data.commitments['AgreementId'] != 0}
                <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id={$data.commitments['AgreementId']}">{$_lang['Go to agreement']}</a>
            {/if}

            <select name="AgreementId" >
                <option value="0" selected>{$_lang['No agreement']}</option>
                {foreach from=$data.agreements key=k item=agreement}
                    {foreach from=$data.agreements key=k2 item=agreement2}
                        {if $agreement.CompanyName == $agreement2.CompanyName}
                            {if $k>$k2 or $k<$k2}
                                {break}
                            {/if}
                            <optgroup label="{$agreement2.CompanyName}">

                                {foreach from=$data.agreements key=k3 item=agreement key=k3 item=agreement3}

                                    {if $agreement2.CompanyName == $agreement3.CompanyName}
                                        {if $data.commitments.AgreementId == $agreement3.id}
                                            <option value="{$agreement3.id}" selected>{$agreement3.Number} | {$agreement3.Description}</option>
                                        {else}
                                            <option value="{$agreement3.id}">{$agreement3.Number} | {$agreement3.Description}</option>
                                        {/if}
                                    {/if}
                                {/foreach}
                            </optgroup>
                        {/if}
                    {/foreach}
                {/foreach}
            </select>
        </div>
        {$PtabHeight='800px'}
        {$tabs['General information'] = '/templates/parts/GeneralInformation.tpl'}
        {$tabs['Invoices'] = '/templates/commitments/Invoices.tpl'}
        {include file="$dir/templates/parts/Tabs.tpl"}
        <input type="text" name="id" placeholder="ID" value="{$data.commitments['id']}" required readonly hidden/><br>
</div>

