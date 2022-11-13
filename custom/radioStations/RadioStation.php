<?php

namespace Discord\Bot\radioStations;

class RadioStation {
    public const RADIO_LOS_SANTOS = 1;
    public const PLAYBACK_FM = 2;
    public const K_DST = 3;
    public const K_ROSE = 4;
    public const BOUNCE_FM = 5;
    public const SF_UR = 6;
    public const RADIO_X = 7;
    public const CSR_103_9 = 8;
    public const K_JAH_WEST = 9;
    public const MASTER_SOUNDS_98_3 = 10;
    public const WCTR = 11;

    private array $stationNames = [
        self::RADIO_LOS_SANTOS =>    "Radio Los Santos",
        self::PLAYBACK_FM =>         "Playback FM",
        self::K_DST =>               "K-DST",
        self::K_ROSE =>              "K-ROSE",
        self::BOUNCE_FM =>           "Bounce FM",
        self::SF_UR =>               "SF-UR",
        self::RADIO_X =>             "Radio X",
        self::CSR_103_9 =>           "CSR 103.9",
        self::K_JAH_WEST =>          "K-JAH West",
        self::MASTER_SOUNDS_93_3 =>  "Master Sounds 93.3",
        self::WCTR =>                "WCTR",
    ];

    public readonly Songs $songs;
    
    public function __construct(public readonly int $stationId) {
        if ($this->stationId > 11 || $this->stationId < 1) {
            throw new \InvalidArgumentException("Station ID must be between 1 and 11");
        }

        $this->songs = new Songs($this);
    }

    public function getName(): string
    {
        return $this->stationNames[$this->stationId];
    }

    public function getConstantName(): string
    {
        return strtoupper(preg_replace("/[^a-z0-9]/i", "_", $this->getName()));
    }

    public function getStreamUrl(): string
    {
        return realpath(__DIR__."/".$this->getConstantName().".mp3");
    }

    public static function getAllStations(): array
    {
        $stations = [];

        for ($i = 1; $i <= 11; $i++) {
            $stations[] = new self($i);
        }

        return $stations;
    }
}