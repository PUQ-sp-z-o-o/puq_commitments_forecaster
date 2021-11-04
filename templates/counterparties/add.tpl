<b>{$_lang['Add counterparty']}</b><br>

<div class="input_div">
    <form action="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=add" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">{$_lang['List counterparties']}</a>
        <br>
        <div class="input_data">
            <label for="CompanyName"><b>{$_lang['Company Name']}</b></label>
            <input type="text" name="CompanyName" placeholder="{$_lang['Company Name']}" value="" required />

            <label for="VATNumber"><b>{$_lang['VAT Number']}</b></label>
            <input type="text" name="VATNumber" placeholder="{$_lang['VAT Number']}" value="" required />

        </div>
       <div class="input_data">

            <label for="FirstName"><b>{$_lang['First Name']}</b></label><br>
            <input type="text" name="FirstName" placeholder="{$_lang['First Name']}" value=""  /><br>

            <label for="LastName"><b>{$_lang['Last Name']}</b></label><br>
            <input type="text" name="LastName" placeholder="{$_lang['Last Name']}" value=""  /><br>

            <label for="Email"><b>{$_lang['E-mail']}</b></label><br>
            <input type="text" name="Email" placeholder="{$_lang['E-mail']}" value=""  /><br>

            <label for="PhoneNumber"><b>{$_lang['Phone Number']}</b></label><br>
            <input type="text" name="PhoneNumber" placeholder="{$_lang['Phone Number']}" value=""  /><br>
        </div>

        <div class="input_data">
            <label for="Address"><b>{$_lang['Address']}</b></label><br>
            <input type="text" name="Address" placeholder="{$_lang['Address']}" value="" required /><br>

            <label for="City"><b>{$_lang['City']}</b></label><br>
            <input type="text" name="City" placeholder="{$_lang['City']}" value="" required /><br>

            <label for="Region"><b>{$_lang['Region']}</b></label><br>
            <input type="text" name="Region" placeholder="{$_lang['Region']}" value="" /><br>

            <label for="Postcode"><b>{$_lang['Postcode']}</b></label><br>
            <input type="text" name="Postcode" placeholder="{$_lang['Postcode']}" value="" required /><br>

            <label for="Country"><b>{$_lang['Country']}</b></label><br>
            <input type="text" name="Country" placeholder="{$_lang['Country']}" value="" required /><br>

        </div>

    </form>
</div>