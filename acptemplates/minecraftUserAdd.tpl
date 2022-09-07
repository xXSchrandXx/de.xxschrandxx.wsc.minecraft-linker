{if $success|isset && $success && $objectEditLink|isset && $objectEditLink}
	<meta http-equiv="refresh" content="3;url={$objectEditLink}" />
{/if}

{include file='header' pageTitle='wcf.acp.page.minecraftUserAdd.pageTitle.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.page.minecraftUserAdd.pageTitle.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
				<a href="{link controller='UserEdit' id=$userID}{/link}" class="button">
                	{lang}wcf.global.button.back{/lang}
				</a>
			</li>
            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}