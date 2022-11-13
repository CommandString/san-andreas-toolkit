<?php

$string = "00:20|Masta Ace|Me and the Biz
05:07|Eric B. & Rakim|I Know You Got Soul
09:44|Spoonie Gee|The Godfather
13:29|Big Daddy Kane|Warm It Up, Kane
16:35|Kool G Rap & DJ Polo|Road To The Riches
21:12|Public Enemy|Rebel Without a Pause
25:26|Rob Base and DJ E-Z Rock|It Takes Two
29:11|Gang Starr|B.Y.S.
32:13|Ultramagnetic MCs|Critical Beatdown
35:41|Biz Markie|The Vapors
40:34|Brand Nubian|Brand Nubian
44:07|Slick Rick|Children's Story";

$lines = explode(PHP_EOL, $string);

$songTemplate = "                new Song(\"%s\", \"%s\", %s),".PHP_EOL;

function convert(string $timestamp): int
{
    $parts = explode(":", $timestamp);

    if (count($parts) === 3) {
        $hours = intval($parts[0]);
        $minutes = intval($parts[1]);
        $seconds = intval($parts[2]);
    } else {
        $hours = 0;
        $minutes = intval($parts[0]);
        $seconds = intval($parts[1]);
    }

    return ($hours*3600) + ($minutes*60) + $seconds;
}

$code = "";

foreach ($lines as $line) {
	$line = explode("|", $line);

    $timestamp = convert($line[0]);
    $artist = $line[1];
    $name = $line[2];

    $code .= sprintf($songTemplate, $artist, $name, $timestamp);
}

echo substr($code, 0, -3); // removes last comma