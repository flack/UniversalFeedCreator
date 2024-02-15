<?php

namespace Creator;
use FeedItem;
use PHPUnit_Framework_TestCase;
use UniversalFeedCreator;

class JSONCreatorTest extends PHPUnit_Framework_TestCase
{
    public function test_create_empty_feed()
    {
        $creator = new UniversalFeedCreator;
        $creator->description = 'Feed Description';
        $item = new FeedItem();
        $item->date = time();
        $item->category = array('1', '2');
        $creator->addItem($item);

        $feed = $creator->createFeed('JSON');

        $parsed = json_decode($feed, true);
        $this->assertEquals('Feed Description', $parsed['description']);
    }
}
