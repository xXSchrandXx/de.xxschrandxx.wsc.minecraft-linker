{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftLinker.canManage') && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY}
	<script data-relocate="true">
		$(function() {
			new WCF.Action.Delete('wcf\\data\\user\\minecraft\\MinecraftAction', $('.jsRow'));
		});
	</script>
	
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.page.user_add_section.minecraft.title{/lang}</h2>
		
		<a href="{if !$userID|empty}{link controller='MinecraftIDAdd' id=$userID}{/link}{/if}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.page.user_add_section.minecraft.addButton{/lang}</span></a><br><br>
		
		{if $minecraftList|count > 0}
			<div class="tabularBox">
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<th>{lang}wcf.page.user_add_section.minecraft.id{/lang}</th>
							<th>{lang}wcf.page.user_add_section.minecraft.title{/lang}</th>
							<th>{lang}wcf.page.user_add_section.minecraft.uid{/lang}</th>
							<th>{lang}wcf.page.user_add_section.minecraft.connectedSince{/lang}</th>
						</tr>
					</thead>
					<tbody>
							{foreach from=$minecratList item=minecraft}
								<tr class="jsRow">
									<td>
										<span class="icon icon16 fa-remove pointer jsDeleteButton jsTooltip" title="{lang}wcf.page.user_add_section.minecraft.delete{/lang}" data-object-id="{@$minecraft->minecraftID}" data-confirm-message="{lang}wcf.page.user_add_section.minecraft.delete.question{/lang}"></span>
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
