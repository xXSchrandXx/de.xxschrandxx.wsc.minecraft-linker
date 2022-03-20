{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftLinker.canManage') && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY}

    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.page.userAddSection.minecraft.sectionTitle{/lang}</h2>

        <a href="{if !$userID|empty}{link controller='MinecraftUserAddList' id=$userID}{/link}{/if}" class="button"><span
                class="icon icon16 fa-plus"></span>
            <span>{lang}wcf.page.userAddSection.minecraft.add{/lang}</span></a><br><br>
        <a href="{if !$userID|empty}{link controller='MinecraftUserAddText' id=$userID}{/link}{/if}" class="button"><span
                class="icon icon16 fa-plus"></span>
            <span>{lang}wcf.page.userAddSection.minecraft.addText{/lang}</span></a><br><br>

        {if $minecraftUsers|count > 0}
            <div class="tabularBox">
                <table class="table jsObjectActionContainer"
                    data-object-action-class-name="wcf\data\user\minecraft\MinecraftUserAction">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{lang}wcf.page.userAddSection.minecraft.id{/lang}</th>
                            <th>{lang}wcf.page.userAddSection.minecraft.title{/lang}</th>
                            <th>{lang}wcf.page.userAddSection.minecraft.uuid{/lang}</th>
                            {if MINECRAFT_NAME_ENABLED}
                                <th>{lang}wcf.page.userAddSection.minecraft.name{/lang}</th>
                            {/if}
                            <th>{lang}wcf.page.userAddSection.minecraft.connectedSince{/lang}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$minecraftUsers item=minecraftUser}
                            <tr class="jsObjectActionObject" data-object-id="{@$minecraftUser->minecraftUserID}">
                                <td>
                                    {objectAction action="delete" objectTitle=$minecraftUser->title}
									{event name='addMinecraftButtons'}
									{event name='rowButtons'}
                                </td>
                                <td>{@$minecraftUser->minecraftUserID}</td>
                                <td>{$minecraftUser->title}</td>
                                <td>{@$minecraftUser->minecraftUUID}</td>
                                {if MINECRAFT_NAME_ENABLED}
                                    <td>{@$minecraftUser->minecraftName}</td>
                                {/if}
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