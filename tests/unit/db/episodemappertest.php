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

namespace OCA\Podcasts\Tests\Unit\Db;

use OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Db\Episode;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class EpisodeMapperTest
 *
 * @package OCA\Podcasts\Tests\Unit\Db
 *
 * @group DB
 */
class EpisodeMapperTest extends TestCase
{
    /**
     * @var IAppContainer
     */
    protected $container;

    /**
     * @var IDBConnection
     */
    protected $db;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * @var FeedUpdater
     */
    protected $feedUpdater;

    /**
     * @var EpisodeMapper
     */
    protected $episodeMapper;

    /**
     * @var Feed
     */
    protected $firstFeed;

    /**
     * Set up required properties
     */
    protected function setUp()
    {
        parent::setUp();

        $app = new Application();
        $this->container = $app->getContainer();
        $this->feedMapper = $this->container->query("FeedMapper");
        $this->feedUpdater = $this->container->query("FeedUpdater");
        $this->episodeMapper = $this->container->query("EpisodeMapper");

        $this->db = \OC::$server->getDatabaseConnection();

        $this->setUpFeeds();
    }

    /**
     * Delete test data
     */
    public function tearDown()
    {
        libxml_clear_errors();

        $this->db->executeUpdate(
            "DELETE FROM *PREFIX*podcasts_feeds WHERE uid = ?",
            [ "phpunit" ]
        );

        $this->db->executeUpdate(
            "DELETE FROM *PREFIX*podcasts_episodes WHERE uid = ?",
            [ "phpunit" ]
        );

        parent::tearDown();
    }

    /**
     * Set up feeds
     */
    protected function setUpFeeds()
    {
        $firstFeed = new Feed();
        $firstFeed->setUid("phpunit");
        $firstFeed->setUrl("https://daringfireball.net/thetalkshow/rss");

        $this->firstFeed = $this->feedMapper->insert($firstFeed);

        $this->feedUpdater->processFeed($this->firstFeed);
    }

    /**
     * Try to get a episode when ownership does not match
     *
     * @expectedException \OCP\AppFramework\Db\DoesNotExistException
     */
    public function testGetEpisodeWrongOwner()
    {
        $this->episodeMapper->getEpisode($this->firstFeed->getId(), "john");
    }

    /**
     * Test markAllAsPlayed()
     */
    public function testMarkAllAsPlayed()
    {
        $this->episodeMapper->markAllAsPlayed("phpunit");

        $episodes = $this->episodeMapper->getEpisodes("phpunit", $this->firstFeed->getId());
        $this->assertTrue(is_array($episodes));

        foreach ($episodes as $episode) {
            // each episode is an array
            $this->assertTrue(is_array($episode));

            $this->assertEquals(1, $episode["played"]);
        }
    }

    /**
     * Test updatePosition()
     */
    public function testUpdatePosition()
    {
        $episodes = $this->episodeMapper->getEpisodes("phpunit", $this->firstFeed->getId());
        $this->assertTrue(array_key_exists(0, $episodes));
        $this->assertTrue(is_array($episodes[0]));

        $this->episodeMapper->updatePosition("phpunit", $episodes[0]["id"], 35, 100);

        $episode = $this->episodeMapper->getEpisode($episodes[0]["id"], "phpunit");
        $this->assertTrue(is_array($episode));

        $this->assertEquals(35, $episode["current_second"]);
        $this->assertEquals(100, $episode["duration"]);
    }

    /**
     * Test deleteByFeedId()
     */
    public function testDeleteByFeedId()
    {
        $this->episodeMapper->deleteByFeedId($this->firstFeed->getId(), "phpunit");
    }


}