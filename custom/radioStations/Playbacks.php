<?php

namespace Discord\Bot\radioStations;

use Discord\Bot\Config;

class Playbacks {
    private array $playbacks = [];

    public function addPlayback(Playback $playback): self
    {
        $this->playbacks[] = $playback;
        return $this;
    }

    public function getPlaybackByGuildId(string $guild_id): ?Playback
    {
        foreach ($this->playbacks as $playback) {
            if ($playback->guild_id === $guild_id) {
                return $playback;
            }
        }

        return null;
    }

    public function destroyPlayback(Playback $playbackToDestroy): self
    {
        foreach ($this->playbacks as $key => $playback) {
            if ($playbackToDestroy->guild_id == $playback->guild_id) {
                unset($this->playbacks[$key]);
                break;
            }
        }

        return $this;
    }
}