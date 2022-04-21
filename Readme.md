Quicklinks: [General](#general) | [API](#api) | [Links](#links) | [License]https://github.com/xXSchrandXx/de.xxschrarndxx.wsc.minecraft-api/blob/main/LICENSE)

"Minecraft"™ is a trademark of Mojang Synergies AB. This Resource ist not affiliate with Mojang.

# General
## Description
This plugin is an interface between other plugins and the Minecraft-API.
Minecraft Linker lets users link their Minecraft UUID (s) to their WSC account.
It does not matter whether `online-mode` is activated or deactivated. The user will be sent a confirmation code on the Minecraft server with which they can unlock themselves in the WSC.
## Requirements
1. [Minecraft-API](#links) installed on WoltLab.
2. [WSC-Minecraft-Bridge](#links) installed on your Bukkit- / Spigot- / BungeeCord-Server.
# API
## Eventlistener
```XML
<eventlistener name="MyEventExample">
    <eventclassname>wcf\data\user\minecraft\MinecraftAction</eventclassname>
    <eventname>finalizeAction</eventname>
    <listenerclassname>wcf\system\event\listener\MyEventListenerExample</listenerclassname>
</eventlistener>
```
```PHP
namespace wcf\system\event\listener;

class MyEventListenerExample implements IParameterizedEventListener {
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $action = $eventObj->action;
        if ($action = 'create') {
            $minecraft == $eventObj->parameters['data'];
            // Do stuff
        } else if ($action == 'delete') {
            foreach ($eventObj->getObjects() as $minecraft) {
                // Do stuff
            }
        }
    }
}
```
# Links
## GitHub
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-api](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile)
* [xXSchrandXx/WSC-Minecraft-Bridge](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge)
* [xXSchrandXx/WSC-Minecraft-Authenticator](https://github.com/xXSchrandXx/WSC-Minecraft-Authenticator)

## WoltLab
* [Plugin-Store/Minecraft-API](https://www.woltlab.com/pluginstore/file/7077-minecraft-api/)
* [Plugin-Store/Minecraft-Linker](https://www.woltlab.com/pluginstore/file/7093-minecraft-linker/)
## Spigot
* [Resources/WSC-Minecraft-Bridge](https://www.spigotmc.org/resources/wsc-minecraft-bridge.100716/)
* [Resources/WSC-Minecraft-Authenticator](https://www.spigotmc.org/resources/wsc-minecraft-authenticator.101169/)
## JavaDocs
* [Docs/wscbridge](https://maven.gamestrike.de/docs/wscbridge/)
* [Docs/wscauthenticator](https://maven.gamestrike.de/docs/wscauthenticator/)
## Maven
```XML
<repository>
	<id>schrand-repo</id>
	<url>https://maven.gamestrike.de/mvn/</url>
</repository>
```