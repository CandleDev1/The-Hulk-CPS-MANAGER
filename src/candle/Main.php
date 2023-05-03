<?php

namespace candle;

use candle\Cps\CpsManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    /** @var Config */
    private $alerts;



    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }

    public function onEnable(): void
    {
        $this->getLogger()->info(Messages::Enable_Message);
        $this->registerListener();
        $this->saveResource("Alerts.yml");
        $this->alerts = new Config($this->getDataFolder() . "Alerts.yml", Config::YAML);
    }

    public function onDisable(): void
    {
        $this->getLogger()->info(Messages::Disable_Message);
    }

    public function registerListener(){
        $this->getServer()->getPluginManager()->registerEvents(new CpsManager(), $this);
    }

    public function getAlerts(Player $player){
        $alert = $this->alerts->get('Alert', []);
        $alert = str_replace('{$player}', $player->getName(), $alert);
        $alert = str_replace('{cps}',  CpsManager::getCps($player), $alert);
        return $alert;
    }

    public function getCpsAlertPermission() {
        $maxcps = $this->alerts->get('permission', '');
        return $maxcps;
    }

    public function getWebhook(Player $player) {
        $webhook = $this->alerts->get('webhook', []);
        return $webhook;
    }

    public function getWebhookTitle() {
        $webhookname = $this->alerts->get('webhookname', []);
        $webhookname = str_replace("&", '§', $webhookname);
        return $webhookname;
    }

    public function getWebhookField1(Player $player) {
        $webhookfield1 = $this->alerts->get('webhookfield1', []);
        $webhookfield1 = str_replace('{$player}', $player->getName(), $webhookfield1);
        return $webhookfield1;
    }
    public function getWebhookField1_1(Player $player) {
        $webhookfield1_1 = $this->alerts->get('webhookfield1_1', '' );
        $webhookfield1_1 = str_replace('{$player}', $player->getName(), $webhookfield1_1);
        return $webhookfield1_1;
    }

    public function getWebhookField2(Player $player) {
        $webhookfield2 = $this->alerts->get('webhookfield2', []);
        $webhookfield2 =  str_replace('{cps}', CpsManager::getCps($player), $webhookfield2);
        return $webhookfield2;
    }

    public function getWebhookField2_2(Player $player) {
        $webhookfield2_2 = $this->alerts->get('webhookfield2_2', '');
        $webhookfield2_2 =  str_replace('{cps}', CpsManager::getCps($player), $webhookfield2_2);
        return $webhookfield2_2;
    }

    public function getMaxCps() {
        $maxcps = $this->alerts->get('max_cps', '');
        return $maxcps;
    }

    public function getCpsPopUp(Player $player) {
        $cpspopup = $this->alerts->get('cpspopup', '');
        $cpspopup = str_replace('{cps}',  CpsManager::getCps($player), $cpspopup);
        return $cpspopup;
    }


}