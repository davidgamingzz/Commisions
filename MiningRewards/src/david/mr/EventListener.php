<?php

declare(strict_types = 1);

namespace david\mr;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

    /** @var MiningRewards */
    private $plugin;

    /**
     * EventListener constructor.
     *
     * @param MiningRewards $plugin
     */
    public function __construct(MiningRewards $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @priority NORMAL
     * @param PlayerLoginEvent $event
     */
    public function onPlayerLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->plugin->getProvider()->isRegistered($player)) {
            $this->plugin->getProvider()->register($player);
        }
    }

    /**
     * @priority NORMAL
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->plugin->createSession($player);
    }

    /**
     * @priority NORMAL
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $this->plugin->updateSession($player);
        $this->plugin->deleteSession($player);
    }

    /**
     * @priority HIGHEST
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        $this->plugin->addBlock($player);
        $message = [];
        foreach($this->plugin->getReward() as $reward) {
            if($reward->qualify($player)) {
                $message[] = $reward->execute($player);
            }
        }
        if(empty($message)) {
            return;
        }
        $message = implode("\n", $message);
        $player->sendPopup(TextFormat::colorize($message));
    }
}