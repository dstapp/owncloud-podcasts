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

namespace OCA\Podcasts\Tests\Unit\Feed;

use OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class FeedUpdaterTest
 *
 * @package OCA\Podcasts\Tests\Unit\Feed
 *
 * @group DB
 */
class FeedUpdaterTest extends TestCase
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
     * @var FeedUpdater
     */
    protected $feedUpdater;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * @var EpisodeMapper
     */
    protected $episodeMapper;

    /**
     * @var Feed
     */
    protected $firstFeed;

    /**
     * @var Feed
     */
    protected $secondFeed;

    /**
     * @var Feed
     */
    protected $invalidFeed;

    /**
     * Set up required properties
     */
    protected function setUp()
    {
        parent::setUp();

        $app = new Application();
        $this->container = $app->getContainer();
        $this->feedUpdater = $this->container->query("FeedUpdater");
        $this->feedMapper = $this->container->query("FeedMapper");
        $this->episodeMapper = $this->container->query("EpisodeMapper");

        $this->db = \OC::$server->getDatabaseConnection();

        $this->setUpFeeds();
    }

    /**
     * Clean up test data
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

        $secondFeed = new Feed();
        $secondFeed->setUid("phpunit");
        $secondFeed->setUrl("http://freakshow.fm/feed/m4a");

        $this->secondFeed = $this->feedMapper->insert($secondFeed);

        $invalidFeed = new Feed();
        $invalidFeed->setUid("phpunit");
        $invalidFeed->setUrl("https://www.davd.eu/some-invalid-feed.xml");

        $this->invalidFeed = $this->feedMapper->insert($invalidFeed);
    }

    /**
     * Test checkFeed()
     */
    public function testCheckFeed()
    {
        $checkResult = $this->feedUpdater->checkFeed($this->firstFeed);
        $this->assertTrue($checkResult);

        $checkResult = $this->feedUpdater->checkFeed($this->secondFeed);
        $this->assertTrue($checkResult);

        $checkResult = $this->feedUpdater->checkFeed($this->invalidFeed);
        $this->assertFalse($checkResult);
    }

    /**
     * Test processFeed()
     */
    public function testProcessFeedValid()
    {
        $this->feedUpdater->processFeed($this->firstFeed);

        $this->firstFeed = $this->feedMapper->getFeed($this->firstFeed->getId(), "phpunit");
        $this->assertContains("The Talk Show", $this->firstFeed->getName());
        $this->assertEquals("", $this->firstFeed->getCover());

        $episodes = $this->episodeMapper->getEpisodes("phpunit", $this->firstFeed->getId());
        $this->assertTrue(count($episodes) > 0);

        $this->feedUpdater->processFeed($this->secondFeed);

        $this->secondFeed = $this->feedMapper->getFeed($this->secondFeed->getId(), "phpunit");
        $this->assertContains("Freak Show", $this->secondFeed->getName());
        $this->assertContains(".jpg", $this->secondFeed->getCover());

        $episodes = $this->episodeMapper->getEpisodes("phpunit", $this->secondFeed->getId());
        $this->assertTrue(count($episodes) > 0);
    }

    /**
     * Test processFeed() with an invalid feed
     *
     * @expectedException \PicoFeed\Client\InvalidUrlException
     */
    public function testProcessFeedInvalid()
    {
        $this->feedUpdater->processFeed($this->invalidFeed);
    }

    /**
     * Test processFeeds()
     */
    public function testProcessFeeds()
    {
        $this->feedUpdater->processFeeds();
    }

}