<?php
/**
 * RSSCreator091 is a FeedCreator that implements RSS 0.91 Spec, revision 3.
 *
 * @see http://my.netscape.com/publish/formats/rss-spec-0.91.html
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @package de.bitfolge.feedcreator
 */
class RSSCreator091 extends FeedCreator {

    /**
     * Stores this RSS feed's version number.
     *
     * @access private
     */
    var $RSSVersion;

    function __construct() {
        $this->_setRSSVersion("0.91");
        $this->contentType = "application/rss+xml";
    }

    /**
     * Sets this RSS feed's version number.
     *
     * @access private
     */
    function _setRSSVersion($version) {
        $this->RSSVersion = $version;
    }

    /**
     * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
     * The feed will contain all items previously added in the same order.
     *
     * @return    string    the feed's complete text
     */
    function createFeed() {
        $feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
        $feed.= $this->_createGeneratorComment();
        $feed.= $this->_createStylesheetReferences();
        $feed.= "<rss version=\"".$this->RSSVersion."\"";

        if (   count($this->items) > 0
            && !empty($this->items[0]->lat))
        {
            $feed.= "    xmlns:georss=\"http://www.georss.org/georss/\"\n";
        }
        if (   count($this->items) > 0
            && isset($this->items[0]->additionalElements['xcal:dtstart']))
        {
            $feed.= "    xmlns:xcal=\"urn:ietf:params:xml:ns:xcal\"\n";
        }
        $feed.= ">\n";
        if ($this->format == 'BASE') {
            $feed.= "    <channel xmlns:g=\"http://base.google.com/ns/1.0\">\n";
        } else {
            $feed.= "    <channel>\n";
        }
        $feed.= "        <title>".FeedCreator::iTrunc(htmlspecialchars($this->title),100)."</title>\n";
        $this->descriptionTruncSize = 500;
        $feed.= "        <description>".$this->getDescription()."</description>\n";
        $feed.= "        <link>".$this->link."</link>\n";
        $now = new FeedDate();
        $feed.= "        <lastBuildDate>".htmlspecialchars($this->lastBuildDate ?: $now->rfc822())."</lastBuildDate>\n";
        $feed.= "        <generator>".FEEDCREATOR_VERSION."</generator>\n";

        if ($this->image!=null) {
            $feed.= "        <image>\n";
            $feed.= "            <url>".$this->image->url."</url>\n";
            $feed.= "            <title>".FeedCreator::iTrunc(htmlspecialchars($this->image->title),100)."</title>\n";
            $feed.= "            <link>".$this->image->link."</link>\n";
            if ($this->image->width!="") {
                $feed.= "            <width>".$this->image->width."</width>\n";
            }
            if ($this->image->height!="") {
                $feed.= "            <height>".$this->image->height."</height>\n";
            }
            if ($this->image->description!="") {
                $feed.= "            <description>".$this->image->getDescription()."</description>\n";
            }
            $feed.= "        </image>\n";
        }
        if ($this->language!="") {
            $feed.= "        <language>".$this->language."</language>\n";
        }
        if ($this->copyright!="") {
            $feed.= "        <copyright>".FeedCreator::iTrunc(htmlspecialchars($this->copyright),100)."</copyright>\n";
        }
        if ($this->editor!="") {
            $feed.= "        <managingEditor>".FeedCreator::iTrunc(htmlspecialchars($this->editor),100)."</managingEditor>\n";
        }
        if ($this->webmaster!="") {
            $feed.= "        <webMaster>".FeedCreator::iTrunc(htmlspecialchars($this->webmaster),100)."</webMaster>\n";
        }
        if ($this->pubDate!="") {
            $pubDate = new FeedDate($this->pubDate);
            $feed.= "        <pubDate>".htmlspecialchars($pubDate->rfc822())."</pubDate>\n";
        }
        if ($this->category!="") {
            $feed.= "        <category>".htmlspecialchars($this->category)."</category>\n";
        }
        if ($this->docs!="") {
            $feed.= "        <docs>".FeedCreator::iTrunc(htmlspecialchars($this->docs),500)."</docs>\n";
        }
        if ($this->ttl!="") {
            $feed.= "        <ttl>".htmlspecialchars($this->ttl)."</ttl>\n";
        }
        if ($this->rating!="") {
            $feed.= "        <rating>".FeedCreator::iTrunc(htmlspecialchars($this->rating),500)."</rating>\n";
        }
        if ($this->skipHours!="") {
            $feed.= "        <skipHours>".htmlspecialchars($this->skipHours)."</skipHours>\n";
        }
        if ($this->skipDays!="") {
            $feed.= "        <skipDays>".htmlspecialchars($this->skipDays)."</skipDays>\n";
        }
        $feed.= $this->_createAdditionalElements($this->additionalElements, "    ");

        for ($i=0;$i<count($this->items);$i++)
        {
            $feed.= "        <item>\n";
            $feed.= "            <title>".FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100)."</title>\n";
            $feed.= "            <link>".htmlspecialchars($this->items[$i]->link)."</link>\n";
            $feed.= "            <description>".$this->items[$i]->getDescription()."</description>\n";

            if ($this->items[$i]->author!="") {
                $feed.= "            <author>".htmlspecialchars($this->items[$i]->author)."</author>\n";
            }
            /*
             // on hold
            if ($this->items[$i]->source!="") {
            $feed.= "            <source>".htmlspecialchars($this->items[$i]->source)."</source>\n";
            }
            */
            if ($this->items[$i]->lat!="") {
                $feed.= "            <georss:point>".$this->items[$i]->lat." ".$this->items[$i]->long."</georss:point>\n";
            }
            if ($this->items[$i]->category!="") {
                $feed.= "            <category>".htmlspecialchars($this->items[$i]->category)."</category>\n";
            }
            if ($this->items[$i]->comments!="") {
                $feed.= "            <comments>".htmlspecialchars($this->items[$i]->comments)."</comments>\n";
            }
            if ($this->items[$i]->date!="") {
                $itemDate = new FeedDate($this->items[$i]->date);
                $feed.= "            <pubDate>".htmlspecialchars($itemDate->rfc822())."</pubDate>\n";
            }
            if ($this->items[$i]->guid!="") {
                $feed.= "            <guid>".htmlspecialchars($this->items[$i]->guid)."</guid>\n";
            }
            if ($this->items[$i]->thumb!="") {
                $feed.= "            <g:image_link>".htmlspecialchars($this->items[$i]->thumb)."</g:image_link>\n";
            }
            $feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "            ");
            $feed.= "        </item>\n";
        }
        $feed.= "    </channel>\n";
        $feed.= "</rss>\n";
        return $feed;
    }
}
?>
