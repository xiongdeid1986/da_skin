{foreach $navbar as $item}
    <div class="menu-entry">
        <a {if $item->hasChildren()}class="dropdown-toggle" data-toggle="dropdown" href="#"{else}href="{$item->getUri()}"{/if}{if $item->getAttribute('target')} target="{$item->getAttribute('target')}"{/if}>
            {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
            {$item->getLabel()}
            {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
            {if $item->hasChildren()}&nbsp;<span class="submenu-icon"><span class="glyphicon glyphicon-chevron-down"></span></span>{/if}
        </a>
        {if $item->hasChildren()}
            <div class="submenu">
                <div>
                    {foreach $item->getChildren() as $childItem}
                        {if $childItem->getLabel() != '-----'}
                        <a href="{$childItem->getUri()}"{if $childItem->getAttribute('target')} target="{$childItem->getAttribute('target')}"{/if}>
                            {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                            {$childItem->getLabel()}
                            {if $childItem->hasBadge()}&nbsp;<span class="badge">{$childItem->getBadge()}</span>{/if}
                        </a>
                        {/if}
                    {/foreach}
                </div>
            </div>
        {/if}
    </div>
{/foreach}
