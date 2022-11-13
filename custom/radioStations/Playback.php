<?php

namespace Discord\Bot\radioStations;

use Discord\Bot\Config;
use Discord\Bot\radioStations\RadioStation;
use Discord\Bot\radioStations\Song;
use Discord\Voice\VoiceClient;

class Playback {
    public function __construct(
        public readonly string $guild_id,
        public readonly VoiceClient $vc,
        public readonly RadioStation $station,
        public readonly ?Song $currentSong = null,
        public readonly ?Song $nextSong = null
    )
    {}

    public function pause(): self
    {
        if (!$this->vc->isPaused())
        {
            return $this;
        }

        $this->vc->pause();
        return $this;
    }

    public function play(): self
    {
        if ($this->vc->isPaused())
        {
            return $this;
        }

        $this->vc->unpause();
        return $this;
    }

    public function stop(): self
    {
        $this->vc->close();
        Config::get()->playbacks->destroyPlayback($this);
        return $this;
    }
}