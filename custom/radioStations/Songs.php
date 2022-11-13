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
            RadioStation::PLAYBACK_FM =>         [
                //       ARTIST                         NAME                        TIMESTAMP
                new Song("Kool G Rap & DJ Polo",        "Road to the Riches",       26),
                new Song("Big Daddy Kane",              "Warm It Up, Kane",         268),
                new Song("Spoonie Gee",                 "The Godfather",            479),
                new Song("Masta Ace",                   "Me and the Biz",           702),
                new Song("Slick Rick",                  "Children's Story",         936),
                new Song("Public Enemy",                "Rebel Without a Pause",    1191),
                new Song("Eric B. & Rakim",             "I Know You Got Soul",      1483),
                new Song("Rob Base and DJ E-Z Rock",    "It Takes Two ",            1705),
                new Song("Gang Starr",                  "B.Y.S.",                   1936),
                new Song("Biz Markie",                  "The Vapors",               2126),
                new Song("Brand Nubian",                "Brand Nubian",             2373),
                new Song("Ultramagnetic MCs",           "Critical Beatdown",        2608),
            ],
            RadioStation::K_DST =>               [
                new Song("Foghat", "Slow Ride", 13),
                new Song("Creedence Clearwater Revival", "Green River", 210),
                new Song("Heart", "Barracuda", 364),
                new Song("Kiss", "Strutter", 566),
                new Song("Toto", "Hold The Line ", 782),
                new Song("Rod Stewart", "Young Turks", 1010),
                new Song("Tom Petty", "Ruunin' Down A Dream", 1279),
                new Song("Joe Cocker", "Woman to Woman ", 1543),
                new Song("Humble Pie", "Get Down To It", 1780),
                new Song("Grand Funk Railroad", "Some Kinf Of Wonderful", 1999),
                new Song("Lynyrd Skynyrd", "Free Bird", 2215),
                new Song("America", "A Horse With No name ", 2585),
                new Song("The Who", "Eminence Front", 2814),
                new Song("Boston", "Smokin'", 3078),
                new Song("David Bowie", "Somebody Up There Likes Me ", 3301),
                new Song("Eddie Money", "2 Tickets To Paradise ", 3548),
                new Song("Billy Idol", "White Wedding", 3772)
            ],
            RadioStation::K_ROSE =>              [],
            RadioStation::BOUNCE_FM =>           [],
            RadioStation::SF_UR =>               [],
            RadioStation::RADIO_X =>             [],
            RadioStation::CSR_103_9 =>           [
                //       ARTIST             NAME                                        TIMESTAMP
                new Song("Bobby Brown",     "Don't Be Cruel",                           13),
                new Song("Wreckx-n-Effect", "New Jack Swing",                           260),
                new Song("Today",           "I Got the Feeling",                        467),
                new Song("SWV",             "I'm So Into You",                          702),
                new Song("Boyz II Men",     "Motownphilly",                             909),
                new Song("Soul II Soul",    "Keep On Movin'",                           1131),
                new Song("Bell Biv DeVoe",  "Poison",                                   1357),
                new Song("Guy",             "Groove Me",                                1554),
                new Song("Johnny Gill",     "Rub You the Right Way (remix)",            1782),
                new Song("Samuelle",        "So You Like What You See",                 1986),
                new Song("Aaron Hall",      "Don't Be Afraid",                          2162),
                new Song("En Vogue",        "My Lovin' (You're Never Gonna Get It)",    2370),
                new Song("Ralph Tresvant",  "Sensitivity",                              2634)
            ],
            RadioStation::K_JAH_WEST =>          [],
            RadioStation::MASTER_SOUNDS_93_3 =>  [],
            RadioStation::WCTR =>                [],
        ];

        return ($stationId !== 0) ? $songs[$stationId] : $songs;
    }
}