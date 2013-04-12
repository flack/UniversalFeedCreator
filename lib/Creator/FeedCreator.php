<?php
/**
 * FeedCreator is the abstract base implementation for concrete
 * implementations that implement a specific format of syndication.
 *
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.4
 * @package de.bitfolge.feedcreator
 */
abstract class FeedCreator extends HtmlDescribable {

    /**
     * Mandatory attributes of a feed.
     */
    var $title, $description, $link;
    var $format = 'BASE';

    /**
     * Optional attributes of a feed.
     */
    var $syndicationURL, $image, $language, $copyright, $pubDate, $lastBuildDate, $editor, $editorEmail, $webmaster, $category, $docs, $ttl, $rating, $skipHours, $skipDays;

    /**
     * The url of the external xsl stylesheet used to format the naked rss feed.
     * Ignored in the output when empty.
     */
    var $xslStyleSheet = "";


    /**
     * @access private
     */
    var $items = Array();

    /**
     * Generator string
     *
     */

    var $generator = "info@mypapit.net";

    /**
     * This feed's MIME content type.
     * @since 1.4
     * @access private
     */
    var $contentType = "application/xml";


    /**
     * This feed's character encoding.
     * @since 1.6.1
     */
    var $encoding = "UTF-8"; //"ISO-8859-1";


    /**
     * Any additional elements to include as an associated array. All $key => $value pairs
     * will be included unencoded in the feed in the form
     *     <$key>$value</$key>
     * Again: No encoding will be used! This means you can invalidate or enhance the feed
     * if $value contains markup. This may be abused to embed tags not implemented by
     * the FeedCreator class used.
     */
    var $additionalElements = Array();


    /**
     * Adds a FeedItem to the feed.
     *
     * @param object FeedItem $item The FeedItem to add to the feed.
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    function version()
    {
        return FEEDCREATOR_VERSION." (".$this->generator.")";
    }

    /**
     * Truncates a string to a certain length at the most sensible point.
     * First, if there's a '.' character near the end of the string, the string is truncated after this character.
     * If there is no '.', the string is truncated after the last ' ' character.
     * If the string is truncated, " ..." is appended.
     * If the string is already shorter than $length, it is returned unchanged.
     *
     * @static
     * @param string    string A string to be truncated.
     * @param int        length the maximum length the string should be truncated to
     * @return string    the truncated string
     */
    function iTrunc($string, $length) {
        if (strlen($string)<=$length) {
            return $string;
        }

        $pos = strrpos($string,".");
        if ($pos>=$length-4) {
            $string = substr($string,0,$length-4);
            $pos = strrpos($string,".");
        }
        if ($pos>=$length*0.4) {
            return substr($string,0,$pos+1)." ...";
        }

        $pos = strrpos($string," ");
        if ($pos>=$length-4) {
            $string = substr($string,0,$length-4);
            $pos = strrpos($string," ");
        }
        if ($pos>=$length*0.4) {
            return substr($string,0,$pos)." ...";
        }

        return substr($string,0,$length-4)." ...";

    }


    /**
     * Creates a comment indicating the generator of this feed.
     * The format of this comment seems to be recognized by
     * Syndic8.com.
     */
    function _createGeneratorComment() {
        return "<!-- generator=\"".FEEDCREATOR_VERSION."\" -->\n";
    }


    /**
     * Creates a string containing all additional elements specified in
     * $additionalElements.
     * @param    elements    array    an associative array containing key => value pairs
     * @param indentString    string    a string that will be inserted before every generated line
     * @return    string    the XML tags corresponding to $additionalElements
     */
    function _createAdditionalElements($elements, $indentString="") {
        $ae = "";
        if (is_array($elements)) {
            foreach($elements AS $key => $value) {
                $ae.= $indentString."<$key>$value</$key>\n";
            }
        }
        return $ae;
    }

    function _createStylesheetReferences() {
        $xml = "";
        if (!empty($this->cssStyleSheet)) $xml .= "<?xml-stylesheet href=\"".$this->cssStyleSheet."\" type=\"text/css\"?>\n";
        if (!empty($this->xslStyleSheet)) $xml .= "<?xml-stylesheet href=\"".$this->xslStyleSheet."\" type=\"text/xsl\"?>\n";
        return $xml;
    }


    /**
     * Builds the feed's text.
     * 
     * @return    string    the feed's complete text
     */
    abstract function createFeed();

    /**
     * Generate a filename for the feed cache file. The result will be $_SERVER["PHP_SELF"] with the extension changed to .xml.
     * For example:
     *
     * echo $_SERVER["PHP_SELF"]."\n";
     * echo FeedCreator::_generateFilename();
     *
     * would produce:
     *
     * /rss/latestnews.php
     * latestnews.xml
     *
     * @return string the feed cache filename
     * @since 1.4
     * @access private
     */
    function _generateFilename() {
        $fileInfo = pathinfo($_SERVER["PHP_SELF"]);
        return substr($fileInfo["basename"],0,-(strlen($fileInfo["extension"])+1)).".xml";
    }


    /**
     * @since 1.4
     */
    private function _redirect($filename) {
        // attention, heavily-commented-out-area

        // maybe use this in addition to file time checking
        //header("Expires: ".date("r",time()+$this->_timeout));

        /* no caching at all, doesn't seem to work as good:
         header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        */

        // HTTP redirect, some feed readers' simple HTTP implementations don't follow it
        //header("Location: ".$filename);

        header("Content-Type: ".$this->contentType."; charset=".$this->encoding."; filename=".basename($filename));
        if (preg_match("/\.(kml|gpx)$/",$filename)) {
            header("Content-Disposition: attachment; filename=".basename($filename));
        } else {
            header("Content-Disposition: inline; filename=".basename($filename));
        }
        readfile($filename, "r");
        exit();
    }

    /**
     * Turns on caching and checks if there is a recent version of this feed in the cache.
     * If there is, an HTTP redirect header is sent.
     * To effectively use caching, you should create the FeedCreator object and call this method
     * before anything else, especially before you do the time consuming task to build the feed
     * (web fetching, for example).
     * @since 1.4
     * @param filename    string    optional    the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
     * @param timeout    int        optional    the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
     */
    function useCached($filename="", $timeout=3600) {
        $this->_timeout = $timeout;
        if ($filename=="") {
            $filename = $this->_generateFilename();
        }
        if (file_exists($filename) AND (time()-filemtime($filename) < $timeout)) {
            $this->_redirect($filename);
        }
    }


    /**
     * Saves this feed as a file on the local disk. After the file is saved, a redirect
     * header may be sent to redirect the user to the newly created file.
     * @since 1.4
     *
     * @param filename    string    optional    the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
     * @param redirect    boolean    optional    send an HTTP redirect header or not. If true, the user will be automatically redirected to the created file.
     */
    function saveFeed($filename="", $displayContents=true) {
        if ($filename=="") {
            $filename = $this->_generateFilename();
        }
        $feedFile = fopen($filename, "w+");
        if ($feedFile) {
            fputs($feedFile,$this->createFeed());
            fclose($feedFile);
            if ($displayContents) {
                $this->_redirect($filename);
            }
        } else {
            echo "<br /><b>Error creating feed file, please check write permissions.</b><br />";
        }
    }
}
?>
