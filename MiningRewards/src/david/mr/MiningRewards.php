<?php

declare(strict_types = 1);

namespace david\mr;

use david\mr\provider\SQLiteProvider;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class MiningRewards extends PluginBase {

    /** @var self */
    private static $instance;

    /** @var SQLiteProvider */
    private $provider;

    /** @var Entry[] */
    private $rewards = [];

    /** @var int[] */
    private $sessions = [];

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveConfig();
        $this->provider = new SQLiteProvider($this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        foreach($this->getConfig()->getAll() as $key => $value) {
            $this->rewards[] = new Entry($key, $value["command"], $value["message"]);
        }
    }

    /**
     * @return MiningRewards
     */
    public static function getInstance(): self {
        return self::$instance;
    }

    /**
     * @return SQLiteProvider
     */
    public function getProvider(): SQLiteProvider {
        return $this->provider;
    }

    /**
     * @return Entry[]
     */
    public function getReward(): array {
        return $this->rewards;
    }

    /**
     * @param Player $player
     */
    public function createSession(Player $player): void {
        $this->sessions[$player->getRawUniqueId()] = $this->provider->getBlocksMined($player);
    }

    /**
     * @param Player $player
     */
    public function deleteSession(Player $player): void {
        unset($this->sessions[$player->getRawUniqueId()]);
    }

    /**
     * @param Player $player
     */
    public function updateSession(Player $player): void {
        $this->provider->setBlocksMined($player, $this->sessions[$player->getRawUniqueId()]);
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function getSession(Player $player): int {
        return $this->sessions[$player->getRawUniqueId()];
    }

    /**
     * @param Player $player
     */
    public function addBlock(Player $player): void {
        ++$this->sessions[$player->getRawUniqueId()];
    }
}