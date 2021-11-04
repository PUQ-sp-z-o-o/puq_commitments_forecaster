<div class="ListOfDates">
{if $all}
    <a class="button blue" href="{$url}&year=all">{$_lang['ALL']}</a>
{/if}
    {foreach from=$data.ListOfDates key=y item=date}
        <a class="button blue" href="{$url}&year={$y}">
            {if $smarty.get.year == $y}
                <b>>{$y}<</b>
            {else}
                {$y}
            {/if}
        </a>
    {/foreach}
    <br>
    {foreach from=$data.ListOfDates[$smarty.get.year] key=k item=m}
        <a class="button green" href="{$url}&year={$smarty.get.year}&month={$m}">
            {if $smarty.get.month == $m}
                <b>>{$m}<</b>
            {else}
                {$m}
            {/if}
        </a>
    {/foreach}
</div>