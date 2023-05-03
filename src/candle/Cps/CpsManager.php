<?php

namespace candle\Cps;

use candle\Main;
use candle\Messages;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;

class CpsManager implements Listener
{
    private static $clicks = [];

    public function addClick(Player $player)
    {
        if (!isset(self::$clicks[$player->getName()]) || empty(self::$clicks[$player->getName()])) {
            self::$clicks[$player->getName()][] = microtime(true);
        } else {
            array_unshift(self::$clicks[$player->getName()], microtime(true));
            if (count(self::$clicks[$player->getName()]) >= 100) {
                array_pop(self::$clicks[$player->getName()]);
            }
            $player->sendTip(Main::getInstance()->getCpsPopUp($player));
        }
        if (self::getCps($player) > Main::getInstance()->getMaxCps()) {
            if ($player->hasPermission(Main::getInstance()->getCpsAlertPermission())) {
                $player->sendMessage(Main::getInstance()->getAlerts($player));
                $webhook = new Webhook(Main::getInstance()->getWebhook($player));
                $msg = new Message();
                $embed = new Embed();
                $embed->setTitle(Main::getInstance()->getWebhookTitle());
                $embed->addField(Main::getInstance()->getWebhookField1($player), Main::getInstance()->getWebhookField1_1($player));
                $embed->addField(Main::getInstance()->getWebhookField2($player), Main::getInstance()->getWebhookField2_2($player));
                $embed->setColor(0x63CAB2);
                $embed->addEmbed($embed);
                $msg->addEmbed($embed);
                $webhook->send($msg);
            } else {
                return;
            }
        }
    }






    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->addClick($event->getOrigin()->getPlayer());
            }
        }
        if ($packet instanceof LevelSoundEventPacket and $packet->sound == 42) {
            $this->addClick($player);
        }
        if ($event->getPacket()->pid() === AnimatePacket::NETWORK_ID) {
            $event->getOrigin()->getPlayer()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [$event->getPacket()]);
            $event->cancel();
        }
    }

    public static function getCps(Player $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (empty(self::$clicks[$player->getName()])) {
            return 0.0;
        }
        $mt = microtime(true);
        return round(count(array_filter(self::$clicks[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {
                return ($mt - $t) <= $deltaTime;
            })) / $deltaTime, $roundPrecision);
    }

}