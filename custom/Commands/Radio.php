<?php

namespace Discord\Bot\Commands;

use Discord\Bot\Config;
use Discord\Bot\radioStations\Playback;
use Discord\Bot\radioStations\RadioStation;
use Discord\Bot\radioStations\Song;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Channel;
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
        /**
         * @var Playback
         */
        $existingPlayback = Config::get()->playbacks->getPlaybackByGuildId($interaction->guild_id);

        if ($interaction->data->options->get("name", "play") !== null) {
            $this->play($interaction);
        }

        if ($existingPlayback === null) {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("No one is playing anything in this guild."), true);
            return;
        }

        if ($interaction->data->options->get("name", "unpause") !== null) {
            if (!$existingPlayback->unpause()) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Playback isn't paused currently."), true);
            } else {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Unpaused playback."), true);
            }

            return;
        }

        if ($interaction->data->options->get("name", "pause") !== null) {
            if (!$existingPlayback->pause()) {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Playback is already paused."), true);
            } else {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Paused playback."), true);
            }

            return;
        }

        if ($interaction->data->options->get("name", "stop") !== null) {
            $existingPlayback->stop();
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Stopped playback."), true);
            
            return;
        }
    }

    public function play(Interaction $interaction): void
    {
        /**
         * @var Playback
         */
        $existingPlayback = Config::get()->playbacks->getPlaybackByGuildId($interaction->guild_id);

        /**
         * @var Channel
         */
        $voiceChannel = $interaction->member->getVoiceChannel();
        
        if ($voiceChannel === null) {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("You must be in a voice channel!"), true);
            return;
        }

        $options = $interaction->data->options->get("name", "play")->options;

        /**
         * @var RadioStation
         */
        $station = new RadioStation($options->get("name", "station")->value);

        /**
         * @var Song
         */
        $song = $station->songs->getByTimestamp($options->get("name", "song")?->value ?? 0) ?? null;

        if ($existingPlayback === null) {
            self::startPlaying($voiceChannel, $station, $song);
            
            $response = ($song === null) ? "Now playing to **{$station->getName()}**" : "Now playing to **{$song->name}** by **{$song->artist}** from **{$station->getName()}**";
            $interaction->respondWithMessage(MessageBuilder::new()->setContent($response), true);
        } else {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("The bot is already being used in <#{$existingPlayback->vc->getChannel()->id}>"), true);
            return;
        }
    }

    public static function startPlaying(Channel $voiceChannel, RadioStation $station, ?Song $song = null) {
        Config::get()->discord->joinVoiceChannel($voiceChannel)->then(function (VoiceClient $vc) use ($station, $song) {
            Config::get()->playbacks->addPlayback(new Playback($vc->getChannel()->guild_id, $vc, $station, $song));

            $options = ($song !== null) ? ['-ss', $song->timestamp, ] : [];

            if ($song !== null && $song->getDuration() !== null) {
                array_push($options, '-t', $song->getDuration());
            }

            $p = $vc->ffmpegEncode($station->getStreamUrl(), $options);
            $p->start();
            $vc->playOggStream($p->stdout);
        });
    }

    public function autocomplete(Interaction $interaction): void
    {
        $options = $interaction->data->options->get("name", "play")->options;
        
        /**
         * @var RadioStation
         */
        $station = new RadioStation($options->get("name", "station")->value);

        $currentInput = $options->get("name", "song")->value;

        $songs = [];

        foreach ($station->songs->getSongs() as $song) {
            if (str_starts_with(strtolower($song->name), strtolower($currentInput))) {
                $songs[] = $song;
            } else if (str_starts_with(strtolower($song->artist), strtolower($currentInput))) {
                $songs[] = $song;
            }

            if (count($songs) === 25) {
                break;
            }
        }

        if (empty($songs) || count($songs) < 25) {
            foreach ($station->songs->getSongs() as $song) {
                if (!in_array($song, $songs)) {
                    $songs[] = $song;
                }

                if (count($songs) === 25) {
                    break;
                }
            }
        }

        $buildAutocompleteChoice = function (Song $song): Choice
        {
            return (new Choice(Config::get()->discord))
                ->setName($song->name." by ".$song->artist)
                ->setValue($song->timestamp)
            ;
        };

        $choices = [];

        foreach ($songs as $song) {
            $choices[] = $buildAutocompleteChoice($song);
        }

        $interaction->autoCompleteResult($choices);
    }

    public function getName(): array
    {
        return ["radio", ["play"], ["pause"], ["unpause"]];
    }

    public function getConfig(): CommandBuilder|array
    {
        #########################################
        #   BUILD RADIO STATIONS SUB COMMANDS   #
        #########################################
        $stationOption = (new Option(Config::get()->discord))
            ->setName("station")
            ->setDescription("Play a specific station")
            ->setRequired(true)
            ->setType(Option::INTEGER)
        ;

        foreach (RadioStation::getAllStations() as $station) {
            $stationOption->addChoice(Choice::new(Config::get()->discord, $station->getName(), $station->id));
        }
        
        return (new CommandBuilder)
            ->setName($this->getName()[0])
            ->setDescription("Control the radio from Grand Theft Auto San Andreas")
            ->addOption((new Option(Config::get()->discord))
                ->setName("play")
                ->setDescription("Start playing any song or radio station")
                ->setType(Option::SUB_COMMAND)
                ->addOption($stationOption)
                ->addOption((new Option(Config::get()->discord))
                    ->setName("song")
                    ->setDescription("Play a specific song")
                    ->setType(Option::INTEGER)
                    ->setAutoComplete(true)
                )
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("unpause")
                ->setDescription("Unpause playback")
                ->setType(Option::SUB_COMMAND)
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("pause")
                ->setDescription("Pause playback")
                ->setType(Option::SUB_COMMAND)
            )
            ->addOption((new Option(Config::get()->discord))
                ->setName("stop")
                ->setDescription("Stop playback")
                ->setType(Option::SUB_COMMAND)
            )
        ;
    }

    public function getGuild(): string
    {
        return "";
    }
}
