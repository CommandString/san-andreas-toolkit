<?php

namespace Discord\Bot\Events;

use Discord\Bot\Config;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate;
use Discord\WebSockets\Event;

/**
 * @inheritDoc Template
 */
class VOICE_STATE_UPDATE extends Template {
    public function handler(VoiceStateUpdate $voiceUpdate = null, Discord $discord = null): void
    {
        $voiceClient = $discord->getVoiceClient($voiceUpdate->guild_id);

        if ($voiceClient === null) {
            return;
        }

        if (count($voiceClient->getChannel()->members) < 2) {
            Config::get()->stationsPlaying->{$voiceClient->getChannel()->id} = null;
            $voiceClient->close();
        }
    }
  
    public function getEvent(): string
    {
        return Event::VOICE_STATE_UPDATE;
    }

    public function runOnce(): bool
    {
        return false;
    }
}