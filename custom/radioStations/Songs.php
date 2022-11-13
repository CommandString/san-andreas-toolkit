<?php

namespace Discord\Bot\radioStations;

class Songs {
    private array $songs;

    public function __construct(RadioStation $station) {
        $this->songs = self::getAllSongs($station->stationId);
    }

    public function getSongs() {
        return $this->songs;
    }

    public function getSongByTimestamp(int $timestamp): ?Song
    {
        foreach ($this->songs as $song) {
            if ($timestamp === $song->timestamp) {
                return $song;
            }
        }

        return null;
    }

    public static function getAllSongs(int $stationId = 0): array
    {
        $songs = [
            RadioStation::RADIO_LOS_SANTOS => [
                //       ARTIST                     NAME                            TIMESTAMP
                new Song("Dr Dre",                  "Fuck Wit Dre Day",             25),
                new Song("Ice Cube",                "Check Yo Self",                275),
                new Song("Too \$hort",              "The Ghetto",                   493),
                new Song("Above The Law",           "Murder Rap",                   721),
                new Song("Compton's Most Wanted",   "Hood Took Me Under",           985),
                new Song("N.W.A.",                  "Express Yourself",             1192),
                new Song("The D.O.C",               "It's Funky Enough",            1446),
                new Song("Da Lench Mob",            "Guerillas In Tha Mist",        1686),
                new Song("Cypress Hill",            "How I Could Just Kill a Man",  1878),
                new Song("Dr. Dre & Snoop Dogg",    "Deep Cover",                   2135),
                new Song("Ice Cube",                "It Was A Good Day",            2379),
                new Song("N.W.A.",                  "Alwayz Into Something",        2580),
                new Song("Dr. Dre & Snoop Dog",     "Nothin' But a \"G\" Thang",    2807),
                new Song("2pac & Pogo",             "I Don't Give a Fuck",          3034),
                new Song("Easy-E",                  "Eazy-Er Said Than Dunn",       3279),
                new Song("Kid Frost",               "La Raza",                      3512)
            ],
            RadioStation::PLAYBACK_FM =>         [],
            RadioStation::K_DST =>               [],
            RadioStation::K_ROSE =>              [],
            RadioStation::BOUNCE_FM =>           [],
            RadioStation::SF_UR =>               [],
            RadioStation::RADIO_X =>             [],
            RadioStation::CSR_103_9 =>           [],
            RadioStation::K_JAH_WEST =>          [],
            RadioStation::MASTER_SOUNDS_93_3 =>  [],
            RadioStation::WCTR =>                [],
        ];

        return ($stationId !== 0) ? $songs[$stationId] : $songs;
    }
}