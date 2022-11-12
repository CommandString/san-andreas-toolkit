<?php

namespace Discord\Bot\Commands;

use Discord\Bot\Config;
use Discord\Bot\radioStations\RadioStations;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Voice\VoiceClient;

/**
 * @inheritDoc CommandTemplate
 */
class Radio extends Template {
    public function handler(Interaction $interaction): void
    {
        $options = $interaction->data->options->get("name", "play")?->options ?? null;

        if ($interaction->data->options->get("name", "play") !== null) {
            $this->play($interaction, $options);
        }

        if ($interaction->data->options->get("name", "stop") !== null) {
            $existingVoiceClient = Config::get()->discord->getVoiceClient($interaction->guild_id);

            if ($existingVoiceClient === null) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("No one is listening to a radio station on this guild."), true);
                return;
            }

            $existingVoiceClient->close();
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Stopped playing **".RadioStations::getPrintableName(Config::get()->stationsPlaying->{$interaction->guild_id})."**"), true);
            Config::get()->stationsPlaying->{$interaction->guild_id} = null;
        }
    }

    public function play(Interaction $interaction, $options) {
        $station = RadioStations::getByInt($options->get("name", "station")->value);
        $stationName = RadioStations::getPrintableName($station);
        $stationStreamUrl = realpath(RadioStations::getStreamUrl($station));
        $existingVoiceClient = Config::get()->discord->getVoiceClient($interaction->guild_id);

        $voiceChannel = $interaction->member->getVoiceChannel();

        if ($voiceChannel === null) {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("You must be in a voice channel!"), true);
            return;
        }

        if ($existingVoiceClient === null) {
            Config::get()->discord->joinVoiceChannel($voiceChannel)->then(function (VoiceClient $voiceClient) use ($station, $stationName, $stationStreamUrl, $interaction) {
                $voiceClient->playFile($stationStreamUrl);
                Config::get()->stationsPlaying->{$voiceClient->getChannel()->guild_id} = $station;
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Now playing to $stationName"), true);
            });
        } else {
            if ($voiceChannel->id !== $existingVoiceClient->getChannel()->id) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Members are listening to **".RadioStations::getPrintableName(Config::get()->stationsPlaying->{$voiceChannel->guild_id})."** in <#{$existingVoiceClient->getChannel()->id}>."), true);
            } else {
                $existingVoiceClient->close();
                $this->play($interaction, $options);
            }
        }
    }

    public function autocomplete(Interaction $interaction): void {
        
    }

    public function getName(): string
    {
        return "radio";
    }

    public function getConfig(): CommandBuilder|array
    {
        ###################################
        #   BUILD RADIO STATIONS OPTION   #
        ###################################
        $radioStations = (new Option(Config::get()->discord))
            ->setName("station")
            ->setDescription("Name of the station you want to place")
            ->setType(Option::INTEGER)
        ;

        foreach (RadioStations::cases() as $station) {
            $radioStations->addChoice((new Choice(Config::get()->discord))
                ->setName(RadioStations::getPrintableName($station))
                ->setValue($station->value)
            );
        }

        ############################
        #   FINALIZE THE COMMAND   #
        ############################
        return (new CommandBuilder)
            ->setName($this->getName())
            ->setDescription("Listen to a radio station")
            ->addOption((new Option(Config::get()->discord))
                ->setName("play")
                ->setDescription("Start playing one of the GTA SA radio stations.")
                ->setType(Option::SUB_COMMAND)
                ->addOption($radioStations->setRequired(true))
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("stop")
                ->setDescription("Stop playing the current radio station")
                ->setType(Option::SUB_COMMAND)
            )
        ;
    }

    public function getGuild(): string
    {
        return "";
    }
}
