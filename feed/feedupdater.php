<?php

/**
 * Copyright (c) 2016 David Prandzioch
 * https://github.com/dprandzioch/owncloud-podcasts
 *
 * This file is part of owncloud-podcasts.
 *
 * owncloud-podcasts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Podcasts\Feed;

use OCA\Podcasts\Db\Episode;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use PicoFeed\Reader\Reader;

/**
 * Class FeedUpdater
 *
 * @package OCA\Podcasts\Feed
 */
class FeedUpdater
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * @var EpisodeMapper
     */
    protected $episodeMapper;

    /**
     * FeedUpdater constructor.
     *
     * @param               $userId
     * @param FeedMapper    $feedMapper
     * @param EpisodeMapper $episodeMapper
     */
    public function __construct($userId, FeedMapper $feedMapper, EpisodeMapper $episodeMapper)
    {
        $this->userId = $userId;
        $this->feedMapper = $feedMapper;
        $this->episodeMapper = $episodeMapper;
    }

    /**
     * Updates all existing feeds for all users
     */
    public function processFeeds()
    {
        $feeds = $this->feedMapper->getAllFeeds();

        foreach ($feeds as $feed) {
            try {
                $this->processFeed($feed);
            } catch (\Exception $e) {
                // @todo warn
            }
        }
    }

    /**
     * Checks if a feed can be parsed properly
     *
     * @param Feed $feed
     *
     * @return bool
     */
    public function checkFeed(Feed $feed)
    {
        $success = false;

        try {
            $reader = new Reader();
            $resource = $reader->download($feed->getUrl());

            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            $parser->execute();

            $success = true;
        } catch (\Exception $e) {
            // $success = false set on initialization
        }

        return $success;
    }

    /**
     * Processes a single feed
     *
     * @param Feed $feed
     *
     * @throws \PicoFeed\Parser\MalformedXmlException
     * @throws \PicoFeed\Reader\UnsupportedFeedFormatException
     */
    public function processFeed(Feed $feed)
    {
        $reader = new Reader();
        $resource = $reader->download($feed->getUrl());

        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );

        $rss = $parser->execute();

        $feed->setName($rss->getTitle());
        $feed->setCover($rss->getLogo());
        $this->feedMapper->update($feed);


        /** @var \PicoFeed\Parser\Item $item */
        foreach ($rss->getItems() as $item) {
            try {
                $exists = $this->episodeMapper->episodeExists($feed->getUid(), $item->getEnclosureUrl());

                if (false === $exists) {
                    $episode = new Episode();
                    $episode->setFeedId($feed->getId());

                    $episode->setUid($feed->getUid());
                    $episode->setName($item->getTitle());
                    $episode->setUrl($item->getEnclosureUrl());
                    $episode->setCreatedAt($item->getDate()->format("Y-m-d H:i:s"));

                    $this->episodeMapper->insert($episode);
                }
            } catch (\Exception $e) {
                // @todo warn
            }
        }
    }
}
