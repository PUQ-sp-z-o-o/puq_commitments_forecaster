{if $message[0] != '0'}
    {if $_lang[$message[2]]}
            {$message[2] = $_lang[$message[2]]}
    {/if}
    <div class="notice-{$message[0]}">
        <span class="notice-x" onclick="this.parentElement.style.display='none';">&times;</span>
        <b style='font-size:80px;'>{$message[1]}</b><br>{$message[2]}
    </div>
{/if}
