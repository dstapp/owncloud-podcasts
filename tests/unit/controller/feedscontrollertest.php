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

namespace OCA\Podcasts\Tests\Unit\Controller;

use OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Controller\FeedsController;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class FeedsControllerTest
 *
 * @package OCA\Podcasts\Tests\Unit\Controller
 *
 * @group DB
 */
class FeedsControllerTest extends TestCase
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
     * @var Feed
     */
    protected $firstFeed;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * @var FeedUpdater
     */
    protected $feedUpdater;

    /**
     * Set up required properties
     */
    protected function setUp()
    {
        parent::setUp();

        $app = new Application();
        $this->container = $app->getContainer();

        $this->db = \OC::$server->getDatabaseConnection();
        $this->feedMapper = $this->container->query("FeedMapper");
        $this->feedUpdater = $this->container->query("FeedUpdater");

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
            ["phpunit"]
        );

        $this->db->executeUpdate(
            "DELETE FROM *PREFIX*podcasts_episodes WHERE uid = ?",
            ["phpunit"]
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
     * Test getFeeds()
     */
    public function testGetFeeds()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new FeedsController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->container->query("EpisodeMapper"),
            $this->feedMapper,
            $this->feedUpdater
        );

        $response = $controller->getFeeds();
        $json = $response->render();
        $data = json_decode($json, true);

        $this->assertTrue($data["success"]);
        $this->assertTrue(count($data["data"]) > 0);
        $this->assertArrayHasKey("uid", $data["data"][0]);
        $this->assertArrayHasKey("name", $data["data"][0]);
        $this->assertArrayHasKey("url", $data["data"][0]);
        $this->assertArrayHasKey("cover", $data["data"][0]);
        $this->assertArrayHasKey("id", $data["data"][0]);
    }

    /**
     * Helper method for adding a feed
     *
     * @throws \Exception
     * @return array
     */
    protected function addFeed($url)
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method("getParam")
            ->with(
                $this->equalTo("url")
            )
            ->will($this->returnValue($url));

        $controller = new FeedsController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->container->query("EpisodeMapper"),
            $this->feedMapper,
            $this->feedUpdater
        );

        $response = $controller->addFeed();
        $json = $response->render();
        $data = json_decode($json, true);

        return $data;
    }

    /**
     * Test addFeed()
     */
    public function testAddFeed()
    {
        $data = $this->addFeed("http://freakshow.fm/feed/m4a");

        $this->assertTrue($data["success"]);

        // add the same feed again
        $data = $this->addFeed("http://freakshow.fm/feed/m4a");

        $this->assertFalse($data["success"]);
        $this->assertContains("Feed already exists", $data["message"]);
    }

    /**
     * Test addFeed() with an empty url
     */
    public function testAddFeedEmptyUrl()
    {
        $data = $this->addFeed("");

        $this->assertFalse($data["success"]);
        $this->assertContains("URL is empty", $data["message"]);
    }

    /**
     * Test addFeed() with an invalidFeed
     */
    public function testAddFeedInvalidFeed()
    {
        $data = $this->addFeed("http://www.davd.eu/index.html");

        $this->assertFalse($data["success"]);
        $this->assertContains("Feed is invalid", $data["message"]);
    }

    /**
     * Test addFeed() with an invalid url
     */
    public function testAddFeedInvalidUrl()
    {
        $data = $this->addFeed("foobarthisisnotaurl");

        $this->assertFalse($data["success"]);
        $this->assertContains("URL is invalid", $data["message"]);
    }

    /**
     * Test deleteFeed()
     */
    public function testDeleteFeed()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new FeedsController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->container->query("EpisodeMapper"),
            $this->feedMapper,
            $this->feedUpdater
        );

        $response = $controller->deleteFeed($this->firstFeed->getId());
        $json = $response->render();
        $data = json_decode($json, true);

        $this->assertTrue($data["success"]);
    }
}