<?php

namespace Discord\Bot\Commands;

use Discord\Bot\Config;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

/**
 * @inheritDoc CommandTemplate
 */
class Voice extends Template {
    private const PLAY =    1;
    private const PAUSE =   2;
    private const STOP =    3;


    public function handler(Interaction $interaction): void
    {
        $action = ($interaction->data->options->get("name", "play") !== null) ? self::PLAY : (($interaction->data->options->get("name", "pause") !== null) ? self::PAUSE : (($interaction->data->options->get("name", "stop") !== null) ? self::STOP : false));
        $evc = Config::get()->playbacks->getPlaybackByGuildId($interaction->guild_id);

        if ($evc === null) {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("No one is playing anything in this guild."), true);
            return;
        }

        if ($action === self::PLAY) {
            if (!$evc->vc->isPaused()) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("The bot is already playing"), true);
                return;
            }

            $evc->play();
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Unpaused current playback"), true);
        }

        if ($action === self::PAUSE) {
            if ($evc->vc->isPaused()) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("The current playback is already paused"), true);
                return;
            }

            $evc->pause();
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Paused current playback"), true);
        }

        if ($action === self::STOP) {
            $evc->stop();
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Stopped current playback"), true);
        }

        $interaction->respondWithMessage(MessageBuilder::new()->setContent("Was unable to detect your desired action :("), true);
    }

    public function autocomplete(Interaction $interaction): void {
        
    }

    public function getName(): string
    {
        return "voice";
    }

    public function getConfig(): CommandBuilder|array
    {
        return (new CommandBuilder)
            ->setName($this->getName())
            ->setDescription("Play/Pause/Stop music currently playing")
            ->addOption((new Option(Config::get()->discord))
                ->setName("play")
                ->setDescription("Resume playback")
                ->setType(Option::SUB_COMMAND)
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("pause")
                ->setDescription("Pause playback")
                ->setType(Option::SUB_COMMAND)
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("stop")
                ->setDescription("Disconnect bot")
                ->setType(Option::SUB_COMMAND)
            )
        ;
    }

    public function getGuild(): string
    {
        return "";
    }
}
