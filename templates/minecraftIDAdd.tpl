{include file='userMenuSidebar'}

{include file='header' __sidebarLeftHasMenu=true}

<form method="post">
	<div class="section">
		<dl{if $errorField == 'uuid' || $errorField == 'title'} class="formError"{/if}>
            {if MINECRAFT_MAX_UUIDS > 1}
                <dt><label for="uuid">{lang}wcf.page.minecraftIDAdd.title{/lang}</label></dt>
                <dd>
    				<input type="text" id="title" name="title" class="long" required>
                    {if $errorField == 'title'}
  		    			<small class="innerError">
  			    			{if $errorType == 'empty'}
  				    			{lang}wcf.global.form.error.empty{/lang}
					        {/if}
					    </small>
    				{/if}
	    			<small>{lang}wcf.page.minecraftIDAdd.title.description{/lang}</small>
                </dd>
            {/if}
			<dt><label for="uuid">{lang}wcf.page.minecraftIDAdd.uuid{/lang}</label></dt>
			<dd>
                <select id="uuid" name="uuid" required autofocus>
                    {foreach from=$mcUsers key=key item=value}
                        <option value="{$key}">{$value}</option>
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
	</div>

	{event name='sections'}

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{csrfToken}
	</div>
</form>

<footer class="contentFooter">
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
