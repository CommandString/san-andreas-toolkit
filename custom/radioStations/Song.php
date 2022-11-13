<?php

namespace Discord\Bot\radioStations;

class Song {
    public function __construct(
        public readonly string $artist,
        public readonly string $name,
        public readonly int $timestamp
    ) {}

    public function getDuration(): ?int
    {
        $start = $this->timestamp;

        $next = false;

        foreach (Songs::getAllSongs() as $station) {
            foreach ($station as $song) {
                if ($song == $this) {
                    $next = true;
                    continue;
                }

                if ($next) {
                    $end = $song->timestamp;
                    break;
                }
            }

            if ($next) {
                break;
            }
        }

        if (!isset($end)) {
            return null;
        }

        return $end - $start;
    }
}