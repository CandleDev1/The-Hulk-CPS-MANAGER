<?php

namespace candle;

use pocketmine\utils\TextFormat;

class Messages
{

    const Enable_Message = " 
 _____ _            _   _       _ _    
|_   _| |          | | | |     | | |   
  | | | |__   ___  | |_| |_   _| | | __
  | | | '_ \ / _ \ |  _  | | | | | |/ /
  | | | | | |  __/ | | | | |_| | |   < 
  \_/ |_| |_|\___| \_| |_/\__,_|_|_|\_\ ";
    const Disable_Message = "Disabled The Hulk anti-cheat";

    const PREFIX = TextFormat::GREEN . '[The Hulk]' . TextFormat::GRAY . ">> §r";

    const NoPermission = TextFormat::DARK_RED  . '[The Hulk]' . TextFormat::GRAY . ">> §r";
}