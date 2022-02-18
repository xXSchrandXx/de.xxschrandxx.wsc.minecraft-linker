{include file='header'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.page.minecraftUserAddACP.pageTitle{/lang}</h1>
    </div>
    <nav class="contentHeaderNavigation" role="presentation">
        <ul>
            <li><a href="{link controller='MinecraftUserAddList' id=$userID}{/link}" class="button"><span
                        class="icon icon16 fa-exchange"></span>
                    <span>{lang}wcf.page.minecraftUserAddACP.change{/lang}</span></a></li>
            <li><a href="{link controller='UserEdit' id=$userID}{/link}" class="button"><span
                        class="icon icon16 fa-arrow-left"></span>
                    <span>{lang}wcf.page.minecraftUserAddACP.back{/lang}</span></a></li>
        </ul>
</header>

{@$form->getHtml()}

{include file='footer'}