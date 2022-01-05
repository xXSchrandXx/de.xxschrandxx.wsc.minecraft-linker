{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	<li><a href="{link controller='MinecraftUserCheck' resend=true}{/link}" class="button"><span class="icon icon16 fa-undo"></span> <span>{lang}wcf.page.minecraftUserCheck.resend{/lang}</span></a></li>
{/capture}

{include file='header' __sidebarLeftHasMenu=true}

{@$form->getHtml()}

<footer class="contentFooter">
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
