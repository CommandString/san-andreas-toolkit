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
class Station extends Template {
    public function handler(Interaction $interaction): void
    {
        $station = new RadioStation($interaction->data->options->get("name", "station")->value);
        $song = $station->songs->getSongByTimestamp($interaction->data->options->get("name", "song")?->value ?? 0);
        $playback = Config::get()->playbacks->getPlaybackByGuildId($interaction->guild_id);
        $evc = ($playback !== null) ? $playback->vc : null;

        $voiceChannel = $interaction->member->getVoiceChannel();

        if ($voiceChannel === null) {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("You must be in a voice channel!"), true);
            return;
        }

        if ($evc === null) {
            self::play($voiceChannel, $station, $song);

            $response = ($song === null) ? "Now playing to **{$station->getName()}**" : "Now playing to **{$song->name}** by **{$song->artist}** from **{$station->getName()}**";
            $interaction->respondWithMessage(MessageBuilder::new()->setContent($response), true);
        } else {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("The bot is already being used in <#{$evc->getChannel()->id}>"), true);
            return;
        }
    }

    public static function play(Channel $voiceChannel, RadioStation $station, ?Song $song = null) {
        Config::get()->discord->joinVoiceChannel($voiceChannel)->then(function (VoiceClient $vc) use ($station, $song) {
            Config::get()->playbacks->addPlayback(new Playback($vc->getChannel()->guild_id, $vc, $station, $song));

            $options = ($song !== null) ? ['-ss', $song->timestamp, '-t', $song->getDuration()] : [];

            $p = $vc->ffmpegEncode($station->getStreamUrl(), $options);
            $p->start();
            $vc->playOggStream($p->stdout);
        });
    }

    public function buildChoice(string $name, mixed $value): Choice
    {
        return (new Choice(Config::get()->discord))
            ->setName($name)
            ->setValue($value)
        ;
    }

    public function autocomplete(Interaction $interaction): void
    {
        $station = new RadioStation($interaction->data->options->get("name", "station")->value);
        $currentInput = $interaction->data->options->get("name", "song")->value;
        
        $songs = [];

        foreach ($station->songs->getSongs() as $song) {
            $percent = 0;

            similar_text("$currentInput", $song->name, $percent);
            
            $songs[] = [
                "song" => $song,
                "percent" => $percent
            ];
        }

        usort($songs, function ($a, $b) {
            return $a["percent"] >= $b["percent"];
        });

        foreach ($songs as $song) {
            $song = $song["song"];
            $choices[] = $this->buildChoice($song->name." by ".$song->artist, $song->timestamp);
        }

        $interaction->autoCompleteResult($choices);
    }

    public function getName(): string
    {
        return "station";
    }

    public function getConfig(): CommandBuilder|array
    {
        ###################################
        #   BUILD RADIO STATIONS OPTION   #
        ###################################
        $radioStations = (new Option(Config::get()->discord))
            ->setName("station")
            ->setDescription("Name of the station the song is located in")
            ->setType(Option::INTEGER)
            ->setRequired(true)
        ;

        foreach (RadioStation::getAllStations() as $station) {
            $radioStations->addChoice((new Choice(Config::get()->discord))
                ->setName($station->getName())
                ->setValue($station->stationId)
            );
        }
        
        return (new CommandBuilder)
            ->setName($this->getName())
            ->setDescription("Play a song from the GTA SA Official Sound Track")
            ->addOption($radioStations)
            ->addOption((new Option(Config::get()->discord))
                ->setName("song")
                ->setDescription("Name of song you want to play")
                ->setType(Option::INTEGER)
                ->setAutoComplete(true)
            )
        ;
    }

    public function getGuild(): string
    {
        return "";
    }
}
