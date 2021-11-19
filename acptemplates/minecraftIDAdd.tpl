{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.page.minecraftIDAdd.title{/lang}</h1>
	</div>
	<nav class="contentHeaderNavigation" role="presentation">
		<ul>
			<li><a href="{link controller='UserEdit' id=$userID}{/link}" class="button"><span class="icon icon16 fa-arrow-left"></span> <span>{lang}wcf.acp.page.minecraftIDAdd.back{/lang}</span></a></li>
		</ul>
</header>

{@$form->getHtml()}

{include file='footer'}
