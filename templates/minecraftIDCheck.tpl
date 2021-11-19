{include file='userMenuSidebar'}

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