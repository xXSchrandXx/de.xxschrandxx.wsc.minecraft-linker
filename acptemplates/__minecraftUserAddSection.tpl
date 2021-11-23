{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftLinker.canManage') && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY}

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.acp.page.userAddSection.minecraft.sectionTitle{/lang}</h2>

		<a href="{if !$userID|empty}{link controller='MinecraftIDAdd' id=$userID}{/link}{/if}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.page.userAddSection.minecraft.add{/lang}</span></a><br><br>

		{if $minecrafts|count > 0}
			<div class="tabularBox">
				<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\minecraft\MinecraftAction">
					<thead>
						<tr>
							<th></th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.id{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.title{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.uuid{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.minecraft.connectedSince{/lang}</th>
						</tr>
					</thead>
					<tbody>
							{foreach from=$minecrafts item=minecraft}
								<tr class="jsObjectActionObject" data-object-id="{@$minecraft->minecraftID}">
									<td>
										{objectAction action="delete" objectTitle=$minecraft->title}
									</td>
									<td>{@$minecraft->minecraftID}</td>
									<td>{$minecraft->title}</td>
									<td>{@$minecraft->minecraftUUID}</td>
									<td>{@$minecraft->createdDate|time}</td>
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
