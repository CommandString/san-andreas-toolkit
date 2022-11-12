<?php

namespace Discord\Bot\radioStations;

enum RadioStations: int
{
    case RADIO_LOS_SANTOS = 1;
    case PLAYBACK_FM = 2;
    case K_DST = 3;
    case K_ROSE = 4;
    case BOUNCE_FM = 5;
    case SF_UR = 6;
    case RADIO_X = 7;
    case CSR_103_9 = 8;
    case K_JAH_WEST = 9;
    case MASTER_SOUNDS_93_3 = 10;
    case WCTR = 11;

    public static function getStreamUrl(self $station): string
    {
        return __DIR__."/".self::getName($station).".mp3";
    }

    public static function getPrintableName(self $station): string
    {
        $stations = [
            self::RADIO_LOS_SANTOS->value =>    "Radio Los Santos",
            self::PLAYBACK_FM->value =>         "Playback FM",
            self::K_DST->value =>               "K-DST",
            self::K_ROSE->value =>              "K-ROSE",
            self::BOUNCE_FM->value =>           "Bounce FM",
            self::SF_UR->value =>               "SF-UR",
            self::RADIO_X->value =>             "Radio X",
            self::CSR_103_9->value =>           "CSR 103.9",
            self::K_JAH_WEST->value =>          "K-JAH West",
            self::MASTER_SOUNDS_93_3->value =>  "Master Sounds 93.3",
            self::WCTR->value =>                "WCTR",
        ];

        return $stations[$station->value];
    }


    public static function getName(self $station): ?string
    {
        foreach (self::cases() as $key => $case) {
            if ($case === $station) {
                break;
            }
        }

        return array_column(self::cases(), "name")[$key];
    }

    public static function getByInt(int $int): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $int) {
                return $case;
            }
        }

        return null;
    }

    public static function getNameByInt(int $int): ?string
    {
        return self::getName(self::getByInt($int));
    }
}