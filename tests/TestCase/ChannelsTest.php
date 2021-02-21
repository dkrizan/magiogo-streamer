<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <danyelkrizan@gmail.com>
 * Date: 02.02.21 14:45
 */

namespace Test\TestCase;


use App\Channels;
use Test\TestCase;

class ChannelsTest extends TestCase {

    /** @var Channels */
    private $channels;

    protected function setUp(): void {
        parent::setUp();
        $this->channels = $this->getContainer()->get('channels');
    }

    /**
     * Get channels test
     */
    public function testGetChannels() {
        $channels = $this->channels->getChannels();
        self::assertNotEmpty($channels);
        self::assertGreaterThan(20, count($channels));
        self::assertTrue(in_array("Jednotka HD", $channels));
    }
}