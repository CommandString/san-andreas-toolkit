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
        $playback = Config::get()->playbacks->getPlaybackByGuildId($voiceUpdate->guild_id);

        if ($playback === null) {
            return;
        }

        if (count($playback->vc->getChannel()->members) < 2) {
            $playback->stop();
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