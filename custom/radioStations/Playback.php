<?php

namespace Discord\Bot\radioStations;

use Discord\Bot\Commands\Radio;
use Discord\Bot\Config;
use Discord\Bot\radioStations\RadioStation;
use Discord\Bot\radioStations\Song;
use Discord\Voice\VoiceClient;

class Playback {
    private RadioStation $station;
    private ?Song $currentSong;
    private ?Song $nextSong;

    public function __construct(
        public readonly string $guild_id,
        public readonly VoiceClient $vc,
        RadioStation $station,
        ?Song $currentSong = null,
        ?Song $nextSong = null
    ) {
        $this->station = $station;
        $this->currentSong = $currentSong;
        $this->nextSong = ($nextSong !== null) ? $nextSong : (($currentSong !== null) ? $currentSong->getNextSong() : null);
    }

    public function __get($name) {
        if (in_array($name, ["guild_id", "vc", "station", "currentSong", "nextSong"])) {
            return $this->name;
        }

        return null;
    }

    private function isPlaying(): bool
    {
        return (!$this->vc->isPaused() && $this->vc->isSpeaking());
    }

    public function pause(): self|bool
    {
        if (!$this->isPlaying())
        {
            return false;
        }

        $this->vc->pause();
        return $this;
    }

    public function unpause(): self|bool
    {
        if ($this->isPlaying())
        {
            return false;
        }

        $this->vc->unpause();
        return $this;
    }

    public function play(RadioStation $station, ?Song $song)
    {
        $this->vc->stop();
        Radio::startPlaying($this->vc->channel, $station, $song);
    }

    public function stop(): void
    {
        $this->vc->close();
        Config::get()->playbacks->destroyPlayback($this);
        return;
    }

    public function next(): self
    {
        $this->vc->close();
        // TODO
        return $this;
    }
}