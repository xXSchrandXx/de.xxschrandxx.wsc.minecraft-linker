{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	{if MINECRAFT_MAX_UUIDS == 0 || $objects|count < MINECRAFT_MAX_UUIDS}
		<li><a href="{link controller='MinecraftIDAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.page.minecraftList.add{/lang}</span></a></li>
	{/if}
{/capture}

{include file='header' __sidebarLeftHasMenu=true}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign='pagesLinks' controller='MinecraftIDList' link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count > 0}
	<div class="section tabularBox">
		<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\minecraft\MinecraftAction">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.page.minecraftList.table.minecraftID{/lang}</th>
					<th>{lang}wcf.page.minecraftList.table.title{/lang}</th>
					<th>{lang}wcf.page.minecraftList.table.minecraftUUID{/lang}</th>
					<th>{lang}wcf.page.minecraftList.table.createdDate{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=object}
					<tr class="jsObjectActionObject" data-object-id="{@$object->minecraftID}">
						<td class="columnIcon">
							{objectAction action="delete" objectTitle=$object->title}
						</td>
						<td class="columnID">{#$object->minecraftID}</td>
						<td class="columnText">{$object->title}</td>
						<td class="columnText">{$object->minecraftUUID}</td>
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
