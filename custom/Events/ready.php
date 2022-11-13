<?php

namespace Discord\Bot\Events;

use Discord\Bot\Commands\Radio;
use Discord\Discord;

/**
 * @inheritDoc Template
 */
class ready extends Template {
    public function handler(Discord $discord = null): void
    {
        echo "\n\n{$discord->application->name} ready!\n\n";

        (new Radio)->listen();
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