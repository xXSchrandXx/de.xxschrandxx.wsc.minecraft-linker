{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	{if MINECRAFT_MAX_UUIDS == 0 || $objects|count < MINECRAFT_MAX_UUIDS}
		<li>
			<a href="{link controller='MinecraftUserAdd'}{/link}" class="button">
				<span class="icon icon16 fa-plus"></span>
				<span>{lang}wcf.page.minecraftUserList.add{/lang}</span>
			</a>
		</li>
	{/if}
{/capture}

{include file='header' __sidebarLeftHasMenu=true}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign='pagesLinks' controller='MinecraftUserList' link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count > 0}
	<div class="section tabularBox">
		<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\minecraft\MinecraftUserAction">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.page.minecraftUserList.table.minecraftUserID{/lang}</th>
					<th>{lang}wcf.page.minecraftUserList.table.title{/lang}</th>
					<th>{lang}wcf.page.minecraftUserList.table.minecraftUUID{/lang}</th>
					{if MINECRAFT_NAME_ENABLED}
						<th>{lang}wcf.page.minecraftUserList.table.minecraftName{/lang}</th>
					{/if}
					<th>{lang}wcf.page.minecraftUserList.table.createdDate{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=object}
					<tr class="jsObjectActionObject" data-object-id="{@$object->minecraftUserID}">
						<td class="columnIcon">
							{if MINECRAFT_MAX_UUIDS > 1}
								<a href="{link controller='MinecraftUserEdit' id=$object->minecraftUserID}{/link}"
									title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
									<span class="icon icon16 fa-pencil"></span>
								</a>
							{/if}
							{objectAction action="delete" objectTitle=$object->title}
						</td>
						<td class="columnID">{#$object->minecraftUserID}</td>
						<td class="columnText">{$object->title}</td>
						<td class="columnText">{$object->minecraftUUID}</td>
						{if MINECRAFT_NAME_ENABLED}
							<td class="columnText">{$object->minecraftName}</td>
						{/if}
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
