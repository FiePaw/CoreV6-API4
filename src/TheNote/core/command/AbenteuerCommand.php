<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗ 
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝ 
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use TheNote\core\Main;

class AbenteuerCommand extends Command
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $langsettings = new Config($this->plugin->getDataFolder() . Main::$lang . "LangConfig.yml", Config::YAML);
        $l = $langsettings->get("Lang");
        $lang = new Config($this->plugin->getDataFolder() . Main::$lang . "Lang_" . $l . ".json", Config::JSON);
        $config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
        parent::__construct("gma", $config->get("prefix") . $lang->get("adventureprefix"), "/gma", ["abenteuer", "gm2"]);
        $this->setPermission("core.command.adventure");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): int
    {
        $langsettings = new Config($this->plugin->getDataFolder() . Main::$lang . "LangConfig.yml", Config::YAML);
        $l = $langsettings->get("Lang");
        $lang = new Config($this->plugin->getDataFolder() . Main::$lang . "Lang_" . $l . ".json", Config::JSON);
        $config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings.json", Config::JSON);
        if (!$sender instanceof Player) {
            $sender->sendMessage($config->get("error") . $lang->get("commandingame"));
            return false;
        }
        if (!$this->testPermission($sender)) {
            $sender->sendMessage($config->get("error") . $lang->get("nopermission"));
            return false;
        }
        if (isset($args[0])) {
            if ($sender->hasPermission("core.command.adventure.other")) {
                $victim = $this->plugin->getServer()->getPlayerExact($args[0]);
                $target = Server::getInstance()->getPlayerExact(strtolower($args[0]));
                if ($target == null) {
                    $sender->sendMessage($config->get("error") . $lang->get("playernotonline"));
                    return false;
                } else {
                    $victim->setGamemode(GameMode::ADVENTURE());
                    $cfgmsg = str_replace("{victim}", $victim->getName(), $lang->get("adventuretarget2"));
                    $victim->sendMessage($config->get("prefix") . $lang->get("adventuretarget1"));
                    $sender->sendMessage($config->get("prefix") . $cfgmsg);
                    return false;
                }
            } else {
                $sender->sendMessage($config->get("error") . $lang->get("adventurenopermtarget"));
                return false;
            }
        }
        $sender->setGamemode(GameMode::ADVENTURE()) ;
        $sender->sendMessage($config->get("prefix") . $lang->get("adventuresender"));
        return true;
    }
}