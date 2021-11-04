<div class="input_div">
    <form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=add&c_id={$data.counterparty['id']}" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <a class="button gray" href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">{$_lang['List counterparties']}</a>
        <br>
        <div class="input_data">
            <label for="id"><b>ID</b></label><br>
            <input type="text" name="id" placeholder="ID" value="{$data.counterparty['id']}" required readonly/><br>
            <label for="CompanyName"><b>{$_lang['Company Name']}</b></label><br>
            <input type="text" name="CompanyName" placeholder="{$_lang['Company Name']}" value="{$data.counterparty['CompanyName']}" readonly/><br>
            <input type="text" name="VATNumber" placeholder="{$_lang['VAT Number']}" value="{$data.counterparty['VATNumber']}" readonly/><br>
            <input type="text" name="Address" placeholder="{$_lang['Address']}" value="{$data.counterparty['Address']}" readonly/><br>
            <input type="text" name="City" placeholder="{$_lang['City']}" value="{$data.counterparty['City']}" readonly/><br>
            <input type="text" name="Postcode" placeholder="{$_lang['Postcode']}" value="{$data.counterparty['Postcode']}"  readonly/><br>
        </div>
        <div class="input_data">
            <label for="file"><b>{$_lang['Select document file']}</b></label><br>
            <input type="file" name="file" accept="application/pdf" required><br>
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
    </form>
</div>

