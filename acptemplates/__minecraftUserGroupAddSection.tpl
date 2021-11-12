{if $__wcf->getSession()->getPermission('admin.minecraftSynchronisation.canManage') && MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY}
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.page.user_group_add_section.minecraft.title{/lang}</h2>
		
		{if $minecrafts|count > 1}
			<div class="section tabMenuContainer" data-active="minecraft-sync" data-store="activeTabMenuItem">
				<div id="minecraft-sync" class="tabMenuContainer tabMenuContent">
					<nav class="menu">
						<ul>
							{foreach from=$minecrafts item=minecraft}
								<li>
									<a href="#minecraft-sync-{$minecraft->minecraftID}">{$minecraft->connectionName}</a>
								</li>
							{/foreach}
						</ul>
					</nav>
					{foreach from=$minecrafts item=minecraft}
						<div id="minecraft-sync-{$minecraft->minecraftID}" class="tabMenuContent hidden" data-name="minecraft-sync-{$minecraft->minecraftID}">
							<div class="section">
								{include file='minecraftUserGroupTabSection' minecraftID=$minecraft->minecraftID}
							</div>
						</div>
					{/foreach}
				</div>
			</div>
		{else}
			{foreach from=$minecrafts item=minecraft}
				{include file='minecraftUserGroupTabSection' minecraftID=$minecraft->minecraftID}
			{/foreach}
		{/if}
	</section>
{/if}
