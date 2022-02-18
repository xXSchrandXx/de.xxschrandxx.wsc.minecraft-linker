<?php

define('MINECRAFT_LINKER_ENABLED', 1);
define('MINECRAFT_LINKER_IDENTITY', '');
define('MINECRAFT_MAX_UUIDS', 9);
define('MINECRAFT_NAME_ENABLED', 1);
define('MINECRAFT_ENABLE_ACTIVE_USER', 0);
define('MINECRAFT_COMMAND_LIST', 'list uuids');
define('MINECRAFT_COMMAND_SENDCODE', 'tellraw {$name} [{"text":"{lang}wcf.minecraft.message{/lang}","clickEvent":{"action":"copy_to_clipboard","value":"{$code}"},"hoverEvent":{"action":"show_text","contents":["{lang}wcf.minecraft.hoverMessage{/lang}"]}}');
