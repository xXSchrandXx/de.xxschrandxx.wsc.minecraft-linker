{if $__wcf->getSession()->getPermission('admin.minecraftLinker.canManage') && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY}
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.page.user_group_add_section.minecraft.title{/lang}</h2>
		
		{if $minecrafts|count > 1}
			<div class="section tabMenuContainer" data-active="minecraft-linker" data-store="activeTabMenuItem">
				<div id="minecraft-linker" class="tabMenuContainer tabMenuContent">
					<nav class="menu">
						<ul>
							{foreach from=$minecrafts item=minecraft}
								<li>
									<a href="#minecraft-linker-{$minecraft->minecraftID}">{$minecraft->connectionName}</a>
								</li>
							{/foreach}
						</ul>
					</nav>
					{foreach from=$minecrafts item=minecraft}
						<div id="minecraft-linker-{$minecraft->minecraftID}" class="tabMenuContent hidden" data-name="minecraft-linker-{$minecraft->minecraftID}">
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
