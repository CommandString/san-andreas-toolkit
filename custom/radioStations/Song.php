<?php

namespace Discord\Bot\radioStations;

class Song {
    public function __construct(
        public readonly string $artist,
        public readonly string $name,
        public readonly int $timestamp
    ) {}

    public function getNextSong(): ?self
    {
        $next = false;

        foreach (Songs::getAllSongs() as $station) {
            foreach ($station as $song) {
                if ($song == $this) {
                    $next = true;
                    continue;
                }

                if ($next) {
                    return $song;
                }
            }
        }

        return null;
    }

    public function getDuration(): ?int
    {
        $start = $this->timestamp;
        $end = $this->getNextSong()->timestamp;
        
        return $end - $start;
    }
}