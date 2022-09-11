{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftLinker.canManage') && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY}

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.acp.page.userAddSection.minecraft.sectionTitle{/lang}</h2>
		{if !$userID|empty}
			<a href="{link controller='MinecraftUserAdd' id=$userID}{/link}" class="button"><span
					class="icon icon16 fa-plus"></span>
				<span>{lang}wcf.acp.page.userAddSection.minecraft.add{/lang}</span>
			</a>
		{/if}

		{if $minecraftUsers|isset && !$minecraftUsers|empty}
			<div class="tabularBox">
				<table class="table jsObjectActionContainer"
					data-object-action-class-name="wcf\data\user\minecraft\MinecraftUserAction">
					<thead>
						<tr>
							<th></th>
							<th>{lang}wcf.global.objectID{/lang}</th>
							<th>{lang}wcf.global.title{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.uuid{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.name{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.connectedSince{/lang}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$minecraftUsers item=minecraftUser}
							<tr class="jsObjectActionObject" data-object-id="{@$minecraftUser->getObjectID()}">
								<td>
									<a href="{link controller='MinecraftUserEdit' id=$minecraftUser->getObjectID()}{/link}"
										title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
										<span class="icon icon16 fa-pencil"></span>
									</a>
									{objectAction action="delete" objectTitle=$minecraftUser->getTitle()}
									{event name='rowButtons'}
								</td>
								<td class="columnID">{#$minecraftUser->getObjectID()}</td>
								<td class="columnTitle">{$minecraftUser->getTitle()}</td>
								<td class="columnText">{$minecraftUser->getMinecraftUUID()}</td>
								<td class="columnText">{$minecraftUser->getMinecraftName()}</td>
								<td class="columnDate">{@$minecraftUser->getCreatdDate()|time}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{else}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
		{/if}
	</section>
{/if}