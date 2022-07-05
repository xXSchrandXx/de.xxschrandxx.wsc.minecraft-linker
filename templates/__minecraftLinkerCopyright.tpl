{event name='copyright'}

{if !'MINECRAFT_LINKER_BRANDING'|defined || MINECRAFT_LINKER_BRANDING}
	{if $showMinecraftLinkerBranding|isset && $showMinecraftLinkerBranding}
		<div class="copyright">
			<a href="https://www.woltlab.com/pluginstore/file/7093-minecraft-linker/" rel="nofollow" {if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}>{lang}wcf.copyright.minecraft-linker{/lang}</a>
		</div>
	{/if}
{/if}
