<?php

declare(strict_types = 1);

namespace david\mr;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;

class Entry {

    /** @var int */
    private $blocks;

    /** @var string */
    private $command;

    /** @var string */
    private $message;

    /**
     * Entry constructor.
     *
     * @param int $blocks
     * @param string $command
     * @param string $message
     */
    public function __construct(int $blocks, string $command, string $message) {
        $this->blocks = $blocks;
        $this->command = $command;
        $this->message = $message;
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function qualify(Player $player): bool {
        return (MiningRewards::getInstance()->getSession($player) % $this->blocks) == 0 ? true : false;
    }

    /**
     * @param Player $player
     *
     * @return string
     */
    public function execute(Player $player): string {
        MiningRewards::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", "\"{$player->getName()}\"", $this->command));
        return $this->message;
    }
}