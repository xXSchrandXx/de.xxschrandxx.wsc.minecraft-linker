{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftConsole'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.page.minecraftIDAdd.title{/lang}</h1>
	</div>
</header>

{if $errorType == 'cantConnect'}
	<div class="error">
		{lang}wcf.page.minecraftIDAdd.error.{@$errorType}{/lang}
	</div>
{else}
	<form method="post" action="{link controller="MinecraftIDCheck"}{/lang}">
		<div class="section">
			<dl {if $errorField == 'uuid'}class="formError"{/if}>
				<dt><label for="uuid">{lang}wcf.page.minecraftIDAdd.uuid{/lang}</label></dt>
				<dd>
					<select name="uuid">
						<option />
						{foreach from=$mcUsers key=uuid item=name}
							<option value="{$uuid}">{$name}</option>
						{/foreach}
					</select>
					{if $errorField == 'uuid'}
						<small class="innerError">
   							{if $errorType == 'empty'}
   								{lang}wcf.global.form.error.empty{/lang}
   							{else}
								{lang}wcf.page.minecraftIDAdd.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}wcf.page.minecraftIDAdd.uuid.description{/lang}</small>
				</dd>
			</dl>

			{event name='dataFields'}

			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				{csrfToken}
			</div>

		</div>
	</form>
{/if}

{event name='sections'}

{include file='footer'}
