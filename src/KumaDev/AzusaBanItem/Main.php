<?php

namespace KumaDev\AzusaBanItem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\event\EventPriority;

class Main extends PluginBase implements Listener {

    private Config $config;
    private array $bannedItems;
    private string $banMode;
    private array $worlds;
    private bool $allWorlds;
    private array $lastMessageTime = [];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        $this->bannedItems = $this->config->get("banned_items", []);
        $this->banMode = $this->config->get("ban-mode", "whitelist");
        $this->worlds = $this->config->get("world", []);
        $this->allWorlds = $this->config->get("allworld", false);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function isBannedItem(Item $item, Player $player): bool {
        $world = $player->getWorld()->getFolderName();
        $itemName = strtolower($item->getName());

        if ($this->allWorlds) {
            return in_array($itemName, $this->bannedItems);
        }

        if ($this->banMode === "whitelist" && !in_array($world, $this->worlds)) {
            return false;
        }

        if ($this->banMode === "blacklist" && in_array($world, $this->worlds)) {
            return false;
        }

        return in_array($itemName, $this->bannedItems);
    }

    private function sendBanMessage(Player $player): void {
        $playerName = $player->getName();
        $currentTime = microtime(true);

        if (!isset($this->lastMessageTime[$playerName]) || $currentTime - $this->lastMessageTime[$playerName] > 1) {
            $player->sendMessage($this->config->get("ban_message", "§cThis item/block is banned from the world"));
            $this->lastMessageTime[$playerName] = $currentTime;
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($this->isBannedItem($item, $player)) {
            $event->cancel();
            $this->sendBanMessage($player);
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();

        if ($damager instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            
            if ($this->isBannedItem($item, $damager)) {
                $event->cancel();
                $this->sendBanMessage($damager);
            }
        }
    }

    public function onShootBow(EntityShootBowEvent $event): void {
        $player = $event->getEntity();

        if ($player instanceof Player) {
            $item = $event->getBow();

            if ($this->isBannedItem($item, $player)) {
                $event->cancel();
                $this->sendBanMessage($player);
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($this->isBannedItem($item, $player) && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $event->cancel();
            $this->sendBanMessage($player);
        }
    }

    public function onConsume(PlayerItemConsumeEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($this->isBannedItem($item, $player)) {
            $event->cancel();
            $this->sendBanMessage($player);
        }
    }
}