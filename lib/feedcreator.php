<?php
/**
 * @package de.bitfolge.feedcreator
 */

/*** TEST SCRIPT *********************************************************

//include("feedcreator.class.php");

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = "PHP news";
$rss->description = "daily news from the PHP scripting world";

//optional
//$rss->descriptionTruncSize = 500;
//$rss->descriptionHtmlSyndicated = true;
//$rss->xslStyleSheet = "http://feedster.com/rss20.xsl";

$rss->link = "http://www.dailyphp.net/news";
$rss->feedURL = "http://www.dailyphp.net/".$PHP_SELF;

$image = new FeedImage();
$image->title = "dailyphp.net logo";
$image->url = "http://www.dailyphp.net/images/logo.gif";
$image->link = "http://www.dailyphp.net";
$image->description = "Feed provided by dailyphp.net. Click to visit.";

//optional
$image->descriptionTruncSize = 500;
$image->descriptionHtmlSyndicated = true;

$rss->image = $image;

// get your news items from somewhere, e.g. your database:
//mysql_select_db($dbHost, $dbUser, $dbPass);
//$res = mysql_query("SELECT * FROM news ORDER BY newsdate DESC");
//while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = "This is a the test title of an item";
    $item->link = "http://localhost/item/";
    $item->description = "<b>description in </b><br/>HTML";

    //optional
    //item->descriptionTruncSize = 500;
    $item->descriptionHtmlSyndicated = true;

    $item->date = time();
    $item->source = "http://www.dailyphp.net";
    $item->author = "John Doe";

    $rss->addItem($item);
//}

// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS
echo $rss->saveFeed("RSS0.91", "feed.xml");



***************************************************************************/

?>
