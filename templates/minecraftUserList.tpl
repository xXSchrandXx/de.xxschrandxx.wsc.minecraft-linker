{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	{if MINECRAFT_MAX_UUIDS == 0 || $objects|count < MINECRAFT_MAX_UUIDS}
		<li>
			<a href="{link controller='MinecraftUserAdd' application='minecraft'}{/link}" class="button">
				{icon size=16 name='plus' type='solid'} {lang}wcf.page.minecraftUserList.add{/lang}
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
					<th>{lang}wcf.global.objectID{/lang}</th>
					{if MINECRAFT_MAX_UUIDS > 1}
						<th>{lang}wcf.global.title{/lang}</th>
					{/if}
					<th>{lang}wcf.page.minecraftUserList.table.minecraftUUID{/lang}</th>
					<th>{lang}wcf.page.minecraftUserList.table.minecraftName{/lang}</th>
					<th>{lang}wcf.page.minecraftUserList.table.createdDate{/lang}</th>
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{foreach from=$objects item=object}
					<tr class="jsObjectActionObject" data-object-id="{@$object->getObjectID()}">
						<td class="columnIcon">
							{if MINECRAFT_MAX_UUIDS > 1}
								<a href="{link controller='MinecraftUserEdit' application='minecraft' id=$object->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
									{icon size=16 name='pencil' type='solid'}
								</a>
							{/if}
							{objectAction action="delete" objectTitle=$object->getTitle()}
						</td>
						<td class="columnID">{#$object->getObjectID()}</td>
						{if MINECRAFT_MAX_UUIDS > 1}
							<td class="columnText">{$object->getTitle()}</td>
						{/if}
						<td class="columnText">{$object->getMinecraftUUID()}</td>
						<td class="columnText">{$object->getMinecraftName()}</td>
						<td class="columnDate">{@$object->getCreatdDate()|time}</td>
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
