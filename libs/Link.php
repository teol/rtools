<?php
/**
 * Created by PhpStorm.
 * User: njamet
 * Date: 23/01/2015
 * Time: 17:05
 */

namespace Eperflex\Email\Message;

require_once __DIR__ . '/Utils.php';

class Link
{

    /**
     * @var array
     */
    private $_types = array(
        'hp' => 'home',
        'pdt' => 'product',
        'cart' => 'cart'
    );

    /**
     * @var array|void
     */
    private $_links;

    /**
     * @var array
     */
    private $_trackings;

    /**
     * @var string
     */
    private $_anchor;


    /**
     * @param null $links
     * @param null $trackings
     */
    public function __construct($links = null, $trackings = null)
    {
        $this->setLinks($links);
        $this->setTrackings($trackings);
        $this->_anchor = null;

        return $this;
    }

    /**
     * Gets the value of trackings.
     *
     * @return array
     */
    public function getTrackings()
    {
        return $this->_trackings;
    }

    /**
     * Gets the value of links.
     *
     * @return array|void
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Sets the value of trackings.
     *
     * @param   array|json    $trackings
     *
     * @return  $this
     */
    public function setTrackings($trackings)
    {
        if (is_array($trackings)) {
            $this->_trackings = $trackings;
        } else if (\Utils::isJson($trackings)) {
            $this->_trackings = json_decode($trackings, true);
        } else {
            $this->_trackings = null;
        }

        return $this;
    }

    /**
     * Sets the value of links.
     *
     * @param   array|string  $links
     *
     * @return  $this
     */
    public function setLinks($links)
    {
        $this->_links = is_array($links) ? $links : (is_string($links) ? array($links) : null);

        return $this;
    }

    /**
     * Sets the value of anchor.
     *
     * @param $link
     *
     * @return $this
     */
    public function setAnchor($link)
    {
        $anchors = explode("#", $link);
        if (isset($anchors[1])) {
            $this->_anchor = $anchors[1];
        } else {
            $this->_anchor = null;
        }

        return $this;
    }

    /**
     * Gets the correct tracking for the current url from
     * his key found in Eperflex\Email\Message\Message::getLinks();
     *
     * @param $key
     *
     * @return null|string
     */
    public function getTrackingFromKey($key)
    {
        $offset = strpos($key, "_");
        if (isset($offset)) {
            $type = substr($key, 0, $offset);
            if (isset($this->_types[$type]) && isset($this->_trackings[$this->_types[$type]])) {
                $tracking = $this->_trackings[$this->_types[$type]];
                if (\Utils::isEncoded($tracking)) {
                    $tracking = urldecode($tracking);
                }
                if (strpos($tracking, "%url%") === false) {
                    return null;
                }

                return $tracking;
            }
        }

        return null;
    }

    /**
     * fix the query url. Replacing duplicated '?'
     *
     * @param string $query
     *
     * @return string
     */
    private function fixQuery($query)
    {
        return str_replace('?', '&', $query);
    }

    /**
     * merge queries from an array of url. Basicaly url + tracking
     *
     * @param array $urls
     *
     * @return array
     */
    private function mergeQueriesFromUrls($urls)
    {
        $query = array();
        foreach ($urls as $url) {
            if ($url[0] === '&') {
                $url[0] = '?';
            }
            $metas = parse_url($url);
            if (isset($metas['fragment'])) {
                $this->setAnchor($url);
            }
            if (!isset($metas['query'])) {
                continue;
            }

            parse_str($metas['query'], $tmpQuery);
            $query = array_merge($query, $tmpQuery);
        }

        return $this->fixQuery(urldecode(http_build_query($query)));
    }

    /**
     * join link and tracking variables.
     *
     * @param string $link
     * @param string $tracking
     *
     * @return string
     */
    private function trackVariables($link, $tracking)
    {
        $tracking = str_replace("%url%", '', $tracking);
        if (\Utils::isEncoded($link)) {
            $link = urldecode($link);
        }
        $pos = strpos($link, '?');
        if (!$pos) {
            if ($tracking[0] === '&') {
                $tracking[0] = '?';
            } else if ($tracking[0] !== '?') {
                $tracking = '?' . $tracking;
            }

            return $link . $tracking;
        }

        $query  = $this->mergeQueriesFromUrls(array($link, $tracking));
        $anchor = isset($this->_anchor) ? '#' . $this->_anchor : null;

        return substr($link, 0, $pos) . '?' . $query . $anchor;
    }

    /**
     * track url + tracking
     *
     * @param string $link
     * @param string $tracking
     *
     * @return string
     */
    public function trackURL($link, $tracking)
    {
        $this->setAnchor($link);
        $merged_link = urldecode(str_replace("%url%", $link, $tracking));
        $pos         = strrpos($merged_link, 'http');
        if ($pos === false || strpos($link, 'http') === false) {
            throw new \InvalidArgumentException("link must be an url.");
        } else if ($pos === 0) {
            return $this->trackVariables($link, $tracking);
        } else {
            $redirection = substr($merged_link, 0, $pos);
            $sublink     = substr($merged_link, $pos);
            $pos2        = strpos($sublink, '?');
            if (!$pos2) {
                return $redirection . urlencode($sublink);
            }

            $variables_query = substr($sublink, $pos2 + 1);
            $variables_query = $this->fixQuery($variables_query);

            return $redirection . urlencode($this->trackVariables($sublink, $variables_query));
        }
    }

    /**
     * track all links from $_links attributes with
     * the correct tracking from $_trackings;
     *
     * @return array|void
     */
    public function getTrackedLinks()
    {
        foreach ($this->_links as $key => $link) {
            $tracking = $this->getTrackingFromKey($key);
            if (!isset($tracking)) {
                continue;
            }
            $this->_links[$key] = $this->trackURL($link, $tracking);
        }

        return $this->getLinks();
    }
}