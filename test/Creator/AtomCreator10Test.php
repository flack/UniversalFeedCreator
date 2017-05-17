<?php

class AtomCreator10Test extends PHPUnit_Framework_TestCase
{
    public function test_create_empty_feed()
    {
        $creator = new UniversalFeedCreator;
        $creator->description = 'Feed Description';
        $feed = $creator->createFeed('ATOM1.0');

        $parsed = simplexml_load_string($feed);
        $this->assertEquals('Feed Description', $parsed->subtitle);
    }
}
