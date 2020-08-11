<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 09.07.20 20:48
 */

namespace App;

class Channels {

    /**
     * Array of channels and theirs ids
     * @var int[]
     */
    public static $channels = [
        'jednotka' => 4099,
        'dvojka' => 4146,
        'markiza' => 4044,
        'joj' => 4070,
        'jojplus' => 4222,
        'doma' => 4221,
        'wau' => 4223,
        'dajto' => 4111,
        'ct1' => 4240,
        'ct2' => 4239,
        'ct24' => 4074,
        'ctsport' => 4464,
        'sport2' => 4143,
        'amc' => 4324
    ];

    /**
     * Returns channel ID
     * @param String $name
     * @return int
     * @throws UnknownChannelNameException
     */
    public static function getChannelId(String $name) {
        if (!isset(self::$channels[$name])) {
            $channels = join(', ',array_keys(self::$channels));
            throw new UnknownChannelNameException("Given channel name is not found. Available channels are: $channels");
        }
        return self::$channels[$name];
    }
}