Quicklinks: [General](#general) | [API](#api) | [Links](#links) | [License](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker/blob/main/LICENSE)

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
* [PayPal](https://www.paypal.com/donate/?hosted_button_id=RFYYT7QSAU7YJ)
* [GitHub](https://github.com/xXSchrandXx?tab=repositories)
* [Plugins von WoltLab](https://www.woltlab.com/pluginstore/user-file-list/1503877-xxschrandxx/)
* [Plugins von SpigotMC](https://www.spigotmc.org/resources/authors/_xxschrandxx_.228634/)