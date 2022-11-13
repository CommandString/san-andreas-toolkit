<?php

namespace Discord\Bot\Events;

use Discord\Bot\Commands\Song;
use Discord\Bot\Commands\Station;
use Discord\Bot\Commands\Voice;
use Discord\Discord;

/**
 * @inheritDoc Template
 */
class ready extends Template {
    public function handler(Discord $discord = null): void
    {
        echo "\n\n{$discord->application->name} ready!\n\n";

        (new Station)->listen();
        (new Voice)->listen();
    }
  
    public function getEvent(): string
    {
        return "ready";
    }

    public function runOnce(): bool
    {
        return false;
    }
}