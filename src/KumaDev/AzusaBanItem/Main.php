<?php

namespace KumaDev\AzusaBanItem;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class Main extends PluginBase {

    private $banItemConfig;
    private $banItemData;
    public $lastMessageTime = [];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->banItemConfig = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "allow-op" => true,
            "no_item_message" => "§cNo blocks/items detected in your hand",
            "ban_success_message" => "§aItem has been successfully banned",
            "unban_success_message" => "§aItem has been successfully unbanned",
            "already_banned_message" => "§cThe item is already on the banned",
            "not_banned_message" => "§cThe item is not banned",
            "banned_usage_message" => "§cThis item/block is banned from the world"
        ]);
        $this->banItemData = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        
        $this->getServer()->getPluginManager()->registerEvents(new Event($this), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game");
            return false;
        }
        
        $item = $sender->getInventory()->getItemInHand();
        $itemName = $item->getName();
        
        switch ($command->getName()) {
            case "banitem":
                if ($item->isNull()) {
                    $sender->sendMessage($this->banItemConfig->get("no_item_message"));
                    return true;
                }
                if (in_array($itemName, $this->banItemData->getAll(true))) {
                    $sender->sendMessage($this->banItemConfig->get("already_banned_message"));
                    return true;
                }
                $this->banItemData->set($itemName, null);
                $this->banItemData->save();
                $sender->sendMessage($this->banItemConfig->get("ban_success_message"));
                break;
            case "unbanitem":
                if ($item->isNull()) {
                    $sender->sendMessage($this->banItemConfig->get("no_item_message"));
                    return true;
                }
                if (!in_array($itemName, $this->banItemData->getAll(true))) {
                    $sender->sendMessage($this->banItemConfig->get("not_banned_message"));
                    return true;
                }
                $this->banItemData->remove($itemName);
                $this->banItemData->save();
                $sender->sendMessage($this->banItemConfig->get("unban_success_message"));
                break;
            case "banitemlist":
                $bannedItems = $this->banItemData->getAll(true);
                if (empty($bannedItems)) {
                    $sender->sendMessage("§cNo items are banned.");
                } else {
                    $sender->sendMessage("§aBanned items:");
                    foreach ($bannedItems as $item) {
                        $sender->sendMessage("§e- $item");
                    }
                }
                break;
        }
        
        return true;
    }

    public function getBanItemData(): Config {
        return $this->banItemData;
    }

    public function getBanItemConfig(): Config {
        return $this->banItemConfig;
    }
}