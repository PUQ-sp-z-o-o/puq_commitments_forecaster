{if !$PtabHeight}
    {$PtabHeight=500}
{/if}
{$i=0}
<div class="Ptabs" style="height: {$PtabHeight}; width: {$PtabWidth};">
    {foreach from=$tabs key=k item=tab}
        <div class="Ptab">
            <input type="radio" id="Ptab{$i}" name="Ptab-group" {if $i==0} checked {/if}>
            <label for="Ptab{$i}" class="Ptab-title">{$_lang[{$k}]}</label>
            <section class="Ptab-content" style="height: {$PtabHeight-40}px;">
                {include file="$dir$tab"}
            </section>
            {$i = $i+1}
        </div>
    {/foreach}
</div>
