<b>{$_lang["Add commitment"]}</b>
<div class="input_div">
    <form action="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=add" method="post">
        <button class="button green" type="submit" >{$_lang['Save']}</button>
        <br>
        <div class="input_data">
            <label for="Name"><b>{$_lang['Commitment name']}</b></label>
            <input type="text" name="Name" placeholder="{$_lang['Commitment name']}" value="" required />
            <label for="Description"><b>{$_lang['Description']}</b></label>
            <textarea name="Description" cols="40" rows="10" placeholder="{$_lang['Description']}"></textarea></p>
        </div>
    </form>
</div>
