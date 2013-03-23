<?php
class UniverslFeedCreatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_createFeed
     */
    public function test_createFeed_rss($format, $expected)
    {
        $creator = new UniversalFeedCreator;
        $creator->addItem(new FeedItem());
        $feed = $creator->createFeed($format);

        $expected = simplexml_load_file(__DIR__ . DIRECTORY_SEPARATOR . '__files'  . DIRECTORY_SEPARATOR . $expected);
        $actual = simplexml_load_string($feed);
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->attributes(), $actual->attributes());
        $this->assertEquals($expected->count(), $actual->count());
        $this->assertEquals($expected->channel->title, $actual->channel->title);
        $this->assertEquals($expected->channel->description, $actual->channel->description);
        $this->assertEquals($expected->channel->link, $actual->channel->link);
        $this->assertEquals($expected->channel->generator, $actual->channel->generator);
    }

    public function provider_createFeed()
    {
        return array
        (
            array(null, 'rss091empty.xml'),
            array('RSS9.91', 'rss091empty.xml'),
            array('0.91', 'rss091empty.xml'),
            array('RSS2.0', 'rss20empty.xml'),
            array('2.0', 'rss20empty.xml'),
            array('RSS1.0', 'rss10empty.xml'),
            array('1.0', 'rss10empty.xml'),
        );
    }
}
?>