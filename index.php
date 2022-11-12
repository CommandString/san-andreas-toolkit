<?php

use Discord\Bot\Config;
use Discord\Bot\Events\ready;
use Discord\Bot\Events\VOICE_STATE_UPDATE;
use Discord\Discord;
use Discord\WebSockets\Intents;

require_once "./vendor/autoload.php";

$config = new Config();

#############################
#   ENVIRONMENT VARIABLES   #
#############################
$config->stationsPlaying = new stdClass;

$config->discord = new Discord([
    "token" => $config->token,
    "loadAllMembers" => true,
    "intents" => Intents::getAllIntents()
]);

(new ready)->listen();
(new VOICE_STATE_UPDATE)->listen();

$config->discord->run();