<?php

declare(strict_types = 1);

namespace david\rubb;

use onebone\economyapi\event\money\MoneyChangedEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

    /** @var RankUpBossBar */
    private $plugin;

    /** @var BossBar[] */
    private $bossBar = [];

    /**
     * EventListener constructor.
     *
     * @param RankUpBossBar $plugin
     */
    public function __construct(RankUpBossBar $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @priority NORMAL
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->bossBar[$player->getRawUniqueId()] = new BossBar($player);
        $this->bossBar[$player->getRawUniqueId()]->update("\n\n" . $this->plugin->getMessageFor($player), $this->plugin->getProgressFor($player));
    }

    /**
     * @priority NORMAl
     * @param MoneyChangedEvent $event
     */
    public function onMoneyChange(MoneyChangedEvent $event): void {
        if(($player = $this->plugin->getServer()->getPlayer($event->getUsername())) === null) {
            return;
        }
        $this->bossBar[$player->getRawUniqueId()]->update("\n\n" . $this->plugin->getMessageFor($player), $this->plugin->getProgressFor($player));
    }
}