{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftUserList.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftUserList.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
				<a href="{link controller='MinecraftUserList'}{/link}" class="button">
					<span class="icon icon16 fa-list"></span>
                	<span>{lang}wcf.acp.menu.link.configuration.minecraft.minecraftUserList{/lang}</span>
				</a>
			</li>
            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}