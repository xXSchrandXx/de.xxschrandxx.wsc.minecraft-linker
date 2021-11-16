{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	{if MINECRAFT_MAX_UUIDS == 0 || $objects|count < MINECRAFT_MAX_UUIDS}
		<li><a href="{link controller='MinecraftIDAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.page.minecraftList.add{/lang}</span></a></li>
	{/if}		
{/capture}

{include file='header' __sidebarLeftHasMenu=true}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\user\\minecraft\\MinecraftAction', $('.jsRow'));
	});
</script>

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign='pagesLinks' controller='MinecraftIDList' link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count > 0}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.page.minecraftList.table.title{/lang}</th>
					<th>{lang}wcf.page.minecraftList.table.connectedSince{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=object}
					<tr class="jsMinecraftRow">
						<td class="columnIcon">
							<span class="icon icon16 fa-remove pointer jsDeleteButton jsTooltip" title="{lang}wcf.page.minecraftList.minecraft3_identity_delete_title{/lang}" data-object-id="{@$object->minecraftID}" data-confirm-message="{lang}wcf.page.minecraftList.minecraft3_identity_delete_question{/lang}"></span>
						</td>
						<td class="columnText">{$object->title}</td>
						<td class="columnDate">{@$object->createdDate|time}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}

	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
