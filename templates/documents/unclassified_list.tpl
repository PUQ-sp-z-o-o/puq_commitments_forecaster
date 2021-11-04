<b>{$_lang['List of unclassified documents']}</b>

<form enctype="multipart/form-data" action="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list" method="post">
    <input class="button blue" type="file" name="file[]" id="file" accept="application/pdf" multiple>
    <input class="button green" type='submit' name='submit' value='{$_lang['Upload']}'>
</form>

{if $data.uploads}
    {foreach from=$data.uploads key=k item=upload}
        {$_lang['File']}
        {if $upload == 0}
            <b>{$k}</b>  <font color="red">ERROR {$_lang['The file is already on the system.']}</font><br>
        {else}
            <b>{$k}</b>  <font color="green">OK!</font><br>
        {/if}
    {/foreach}
{/if}

<table class="table_list" id="list">
    <tr class="header">
        <th>ID</th>
        <th>{$_lang['name']}</th>
        <th>md5</th>
        <th></th>
    </tr>
    {foreach from=$data.documents key=k item=document}
        <tr>
            <td><b>{$document['id']}</b>
            <td>{$document['file']}</td>
            <td>{$document['md5']}</td>
             <td>
                 <a class="button green" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_edit&id={$document['id']}">{$_lang['Classify']}</a>
                 <a class="button red" onclick="return confirm('{$_lang['Are you sure you want to delete the document?']}')" href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list&delete={$document['id']}">{$_lang['Delete']}</a>
            </td>
        </tr>
    {/foreach}

</table>
<hr>
