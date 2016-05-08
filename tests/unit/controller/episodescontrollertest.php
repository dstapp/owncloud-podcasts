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
use OCA\Podcasts\Controller\EpisodesController;
use OCA\Podcasts\Controller\FeedsController;
use OCA\Podcasts\Db\Episode;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class EpisodesControllerTest
 *
 * @package OCA\Podcasts\Tests\Unit\Controller
 *
 * @group DB
 */
class EpisodesControllerTest extends TestCase
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
     * @var EpisodeMapper
     */
    protected $episodeMapper;

    /**
     * @var FeedUpdater
     */
    protected $feedUpdater;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * Set up required properties
     */
    protected function setUp()
    {
        parent::setUp();

        $app = new Application();
        $this->container = $app->getContainer();

        $this->db = \OC::$server->getDatabaseConnection();
        $this->episodeMapper = $this->container->query("EpisodeMapper");
        $this->feedUpdater = $this->container->query("FeedUpdater");
        $this->feedMapper = $this->container->query("FeedMapper");

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
     * Test getEpisodes()
     */
    public function testGetEpisodes()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new EpisodesController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->episodeMapper
        );

        $response = $controller->getEpisodes();
        $json = $response->render();
        $data = json_decode($json, true);

        $this->assertTrue($data["success"]);
        $this->assertTrue(count($data["data"]) > 0);
        $this->assertArrayHasKey("id", $data["data"][0]);
        $this->assertArrayHasKey("feed_id", $data["data"][0]);
        $this->assertArrayHasKey("uid", $data["data"][0]);
        $this->assertArrayHasKey("name", $data["data"][0]);
        $this->assertArrayHasKey("created_at", $data["data"][0]);
        $this->assertArrayHasKey("url", $data["data"][0]);
        $this->assertArrayHasKey("current_second", $data["data"][0]);
        $this->assertArrayHasKey("duration", $data["data"][0]);
        $this->assertArrayHasKey("played", $data["data"][0]);
        $this->assertArrayHasKey("cover", $data["data"][0]);
    }

    /**
     * Test updatePosition()
     */
    public function testUpdatePosition()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method("getParam")
            ->will($this->onConsecutiveCalls(99, 100));

        $controller = new EpisodesController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->episodeMapper
        );

        $episodes = $this->episodeMapper->getEpisodes("phpunit");

        $episode = $episodes[0];

        $response = $controller->updatePosition($episode["id"]);
        $json = $response->render();
        $data = json_decode($json, true);

        $this->assertTrue($data["success"]);

        $episode = $this->episodeMapper->getEpisode($episode["id"], "phpunit");

        $this->assertEquals(99, $episode->getCurrentSecond());
        $this->assertEquals(100, $episode->getDuration());
    }

    /**
     * Test markPlayed()
     */
    public function testMarkPlayed()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new EpisodesController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->episodeMapper
        );

        $response = $controller->markPlayed();
        $json = $response->render();
        $data = json_decode($json, true);

        $this->assertTrue($data["success"]);

        $episodes = $this->episodeMapper->getEpisodes("phpunit");

        foreach ($episodes as $episode) {
            // each episode is an array
            $this->assertTrue(is_array($episode));

            $this->assertEquals(1, $episode["played"]);
        }
    }

}