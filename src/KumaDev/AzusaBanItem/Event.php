<?php

namespace KumaDev\AzusaBanItem;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class Event implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function isOpAllowed(Player $player): bool {
        return $this->plugin->getBanItemConfig()->get("allow-op") && Server::getInstance()->isOp($player->getName());
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $item = $event->getItem();
        $itemName = $item->getName();
        $player = $event->getPlayer();
        if (!$this->isOpAllowed($player) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
            $event->cancel();
            $this->sendBannedMessage($player);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $item = $event->getItem();
        $itemName = $item->getName();
        $player = $event->getPlayer();
        if (!$this->isOpAllowed($player) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
            $event->cancel();
            $this->sendBannedMessage($player);
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event) {
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            $itemName = $item->getName();
            if (!$this->isOpAllowed($damager) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
                $event->cancel();
                $this->sendBannedMessage($damager);
            }
        }
    }

    public function onEntityShootBow(EntityShootBowEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $bow = $event->getBow();
            $itemName = $bow->getName();
            if (!$this->isOpAllowed($entity) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
                $event->cancel();
                $this->sendBannedMessage($entity);
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $item = $event->getItem();
        $block = $event->getBlock();
        $itemName = $item->getName();
        $blockName = $block->getName();
        $player = $event->getPlayer();
        if (!$this->isOpAllowed($player) && (in_array($itemName, $this->plugin->getBanItemData()->getAll(true)) || in_array($blockName, $this->plugin->getBanItemData()->getAll(true)))) {
            $event->cancel();
            $this->sendBannedMessage($player);
        }
    }

    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) {
        $item = $event->getItem();
        $itemName = $item->getName();
        $player = $event->getPlayer();
        if (!$this->isOpAllowed($player) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
            $event->cancel();
            $player->getHungerManager()->setFood($player->getHungerManager()->getFood());
            $player->getHungerManager()->setSaturation($player->getHungerManager()->getSaturation());
            $this->sendBannedMessage($player);
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event) {
        $item = $event->getItem();
        $itemName = $item->getName();
        $player = $event->getPlayer();
        if (!$this->isOpAllowed($player) && in_array($itemName, $this->plugin->getBanItemData()->getAll(true))) {
            $event->cancel();
            $this->sendBannedMessage($player);
        }
    }

    private function sendBannedMessage(Player $player): void {
        $message = $this->plugin->getBanItemConfig()->get("banned_usage_message");
        if (!isset($this->plugin->lastMessageTime[$player->getName()]) || (microtime(true) - $this->plugin->lastMessageTime[$player->getName()]) > 1) {
            $player->sendMessage($message);
            $this->plugin->lastMessageTime[$player->getName()] = microtime(true);
        }
    }
}