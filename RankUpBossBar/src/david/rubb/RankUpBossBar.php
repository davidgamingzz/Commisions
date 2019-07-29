<?php

declare(strict_types = 1);

namespace david\rubb;

use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use rankup\RankUp;

class RankUpBossBar extends PluginBase {

    /** @var RankUp */
    private $rankUpManager;

    /** @var string */
    private $rankUpMessage;

    /** @var string */
    private $maxRankMessage;

    public function onEnable() {
        @@mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->rankUpMessage = $this->getConfig()->get("rankUpMessage");
        $this->maxRankMessage = $this->getConfig()->get("maxRankMessage");
        $this->rankUpManager = $this->getServer()->getPluginManager()->getPlugin("RankUp");
    }

    /**
     * @param Player $player
     *
     * @return string
     */
    public function getMessageFor(Player $player): string {
        $currentRank = $this->rankUpManager->getPermManager()->getGroup($player);
        $nextRank = $this->rankUpManager->getRankStore()->getNextRank($player);
        if($nextRank === false) {
            return TextFormat::colorize($this->maxRankMessage);
        }
        $price = $nextRank->getPrice();
        $balance = EconomyAPI::getInstance()->myMoney($player);
        if($balance === 0) {
            $progress = 0;
        }
        elseif($price === 0) {
            $progress = 100;
        }
        else {
            $progress = $balance / $price;
            $progress *= 100;
        }
        $message = $this->rankUpMessage;
        $message = str_replace("{money}", $balance, $message);
        $message = str_replace("{rankUpPrice}", $price, $message);
        $message = str_replace("{progressPercentage}", $progress, $message);
        $message = str_replace("{currentRank}", $currentRank, $message);
        $message = str_replace("{nextRank}", $nextRank->getName(), $message);
        return TextFormat::colorize($message);
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function getProgressFor(Player $player): int {
        $nextRank = $this->rankUpManager->getRankStore()->getNextRank($player);
        if($nextRank === false) {
            return 100;
        }
        $price = $nextRank->getPrice();
        $balance = EconomyAPI::getInstance()->myMoney($player);
        if($balance === 0) {
            $progress = 0;
        }
        elseif($price === 0) {
            $progress = 100;
        }
        else {
            $progress = $balance / $price;
            $progress *= 100;
        }
        return (int)round($progress);
    }
}