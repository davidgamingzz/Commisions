<?php

declare(strict_types = 1);

namespace david\mr\provider;

use david\mr\MiningRewards;
use pocketmine\Player;
use SQLite3;

class SQLiteProvider {

    /** @var MiningRewards */
    private $plugin;

    /** @var SQLite3 */
    private $database;

    /**
     * SQLiteProvider constructor.
     *
     * @param MiningRewards $plugin
     */
    public function __construct(MiningRewards $plugin) {
        $this->plugin = $plugin;
        $this->database = new SQLite3($plugin->getDataFolder() . "Players.db");
        $query = "CREATE TABLE IF NOT EXISTS players(uuid VARCHAR(36), username VARCHAR(16), blocks INT DEFAULT 0);";
        $this->database->exec($query);
    }

    /**
     * @return SQLite3
     */
    public function getDatabase(): SQLite3 {
        return $this->database;
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function getBlocksMined(Player $player): int {
        $uuid = $player->getRawUniqueId();
        $query = "SELECT blocks FROM players WHERE uuid = :uuid";
        $stmt = $this->database->prepare($query);
        $stmt->bindValue(":uuid", $uuid);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC)["blocks"];
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function isRegistered(Player $player): bool {
        $uuid = $player->getRawUniqueId();
        $query = "SELECT blocks FROM players WHERE uuid = :uuid";
        $stmt = $this->database->prepare($query);
        $stmt->bindValue(":uuid", $uuid);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC)["blocks"] !== null ? true : false;
    }

    /**
     * @param Player $player
     */
    public function register(Player $player) {
        $uuid = $player->getRawUniqueId();
        $username = $player->getName();
        $query = "INSERT INTO players(uuid, username) VALUES(:uuid, :username);";
        $stmt = $this->database->prepare($query);
        $stmt->bindValue(":uuid", $uuid);
        $stmt->bindValue(":username", $username);
        $stmt->execute();
        $this->plugin->getLogger()->notice("Registering {$player->getName()} into the mining rewards database!");
    }

    /**
     * @param Player $player
     * @param int $amount
     */
    public function setBlocksMined(Player $player, int $amount) {
        $uuid = $player->getRawUniqueId();
        $query = "UPDATE players SET blocks = :blocks WHERE uuid = :uuid";
        $stmt = $this->database->prepare($query);
        $stmt->bindValue(":blocks", $amount);
        $stmt->bindValue(":uuid", $uuid);
        $stmt->execute();
        return;
    }
}