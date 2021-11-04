<b>{{$_lang['Counterparty']}}</b>
<div class="input_div">
    <form action="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=edit&id={$data.counterparty.id}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button blue" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=add&c_id={$data.counterparty.id}">{$_lang['Add document']}</a>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">{$_lang['List counterparties']}</a>
        <br>
        <div class="input_data">
            <label for="CompanyName"><b>{$_lang['Company Name']}</b></label>
            <input type="text" name="CompanyName" placeholder="{$_lang['Company Name']}" value="{$data.counterparty.CompanyName}" required readonly/>

            <label for="VATNumber"><b>{$_lang['VAT Number']}</b></label>
            <input type="text" name="VATNumber" placeholder="{$_lang['VAT Number']}" value="{$data.counterparty.VATNumber}" required readonly/>

            <label for="FirstName"><b>{$_lang['First Name']}</b></label><br>
            <input type="text" name="FirstName" placeholder="{$_lang['First Name']}" value="{$data.counterparty.FirstName}"  /><br>

            <label for="LastName"><b>{$_lang['Last Name']}</b></label><br>
            <input type="text" name="LastName" placeholder="{$_lang['Last Name']}" value="{$data.counterparty.LastName}"  /><br>

            <label for="Email"><b>{$_lang['E-mail']}</b></label><br>
            <input type="text" name="Email" placeholder="{$_lang['E-mail']}" value="{$data.counterparty.Email}"  /><br>

            <label for="PhoneNumber"><b>{$_lang['Phone Number']}</b></label><br>
            <input type="text" name="PhoneNumber" placeholder="{$_lang['Phone Number']}" value="{$data.counterparty.PhoneNumber}"  /><br>

            <label for="Address"><b>{$_lang['Address']}</b></label><br>
            <input type="text" name="Address" placeholder="{$_lang['Address']}" value="{$data.counterparty.Address}" required /><br>

            <label for="City"><b>{$_lang['City']}</b></label><br>
            <input type="text" name="City" placeholder="{$_lang['City']}" value="{$data.counterparty.City}" required /><br>

            <label for="Region"><b>{$_lang['Region']}</b></label><br>
            <input type="text" name="Region" placeholder="{$_lang['Region']}" value="{$data.counterparty.Region}" /><br>

            <label for="Postcode"><b>{$_lang['Postcode']}</b></label><br>
            <input type="text" name="Postcode" placeholder="{$_lang['Postcode']}" value="{$data.counterparty.Postcode}" required /><br>

            <label for="Country"><b>{$_lang['Country']}</b></label><br>
            <input type="text" name="Country" placeholder="{$_lang['Country']}" value="{$data.counterparty.Country}" required /><br>
        </div>

        {$PtabHeight='800px'}
        {$tabs['General information'] = '/templates/parts/GeneralInformation.tpl'}
        {$tabs['Invoices'] = '/templates/counterparties/Invoices.tpl'}
        {$tabs['Agreements'] = '/templates/counterparties/Agreements.tpl'}
        {$tabs['Commitments'] = '/templates/counterparties/Commitments.tpl'}
        {include file="$dir/templates/parts/Tabs.tpl"}

    </form>

</div>













