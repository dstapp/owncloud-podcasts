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
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class FeedMapperTest
 *
 * @package OCA\Podcasts\Tests\Unit\Db
 *
 * @group DB
 */
class FeedMapperTest extends TestCase
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
     * @var Feed
     */
    protected $firstFeed;

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
        $this->feedMapper = $this->container->query("FeedMapper");

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

        $invalidFeed = new Feed();
        $invalidFeed->setUid("phpunit");
        $invalidFeed->setUrl("https://www.davd.eu/some-invalid-feed.xml");

        $this->invalidFeed = $this->feedMapper->insert($invalidFeed);
    }

    /**
     * Test getFeeds()
     */
    public function testGetFeeds()
    {
        $feeds = $this->feedMapper->getFeeds("phpunit");
        $this->assertEquals(2, count($feeds));
    }

    /**
     * Test getFeed()
     */
    public function testGetFeed()
    {
        $feeds = $this->feedMapper->getAllFeeds();

        $feed = $this->feedMapper->getFeed($feeds[0]->getId(), "phpunit");
    }

    /**
     * Test feedExists()
     */
    public function testFeedExists()
    {
        $this->assertTrue($this->feedMapper->feedExists("phpunit", "https://daringfireball.net/thetalkshow/rss"));
        $this->assertFalse($this->feedMapper->feedExists("phpunit", "https://www.foo.bar"));
    }
}