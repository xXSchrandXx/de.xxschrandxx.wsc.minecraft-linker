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
							<th>{lang}wcf.acp.page.userAddSection.minecraft.id{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.title{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.uuid{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.name{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.connectedSince{/lang}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$minecraftUsers item=minecraftUser}
							<tr class="jsObjectActionObject" data-object-id="{@$minecraftUser->minecraftUserID}">
								<td>
									<a href="{link controller='MinecraftUserEdit' id=$minecraftUser->minecraftUserID}{/link}"
										title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
										<span class="icon icon16 fa-pencil"></span>
									</a>
									{objectAction action="delete" objectTitle=$minecraftUser->title}
									{event name='rowButtons'}
								</td>
								<td>{#$minecraftUser->minecraftUserID}</td>
								<td>{$minecraftUser->title}</td>
								<td>{$minecraftUser->minecraftUUID}</td>
								<td>{$minecraftUser->minecraftName}</td>
								<td>{@$minecraftUser->createdDate|time}</td>
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