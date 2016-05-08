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

use OC\URLGenerator;
use OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Controller\EpisodesController;
use OCA\Podcasts\Controller\FeedsController;
use OCA\Podcasts\Controller\WebViewController;
use OCA\Podcasts\Db\Episode;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class WebViewControllerTest
 *
 * @package OCA\Podcasts\Tests\Unit\Controller
 *
 * @group DB
 */
class WebViewControllerTest extends TestCase
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
     * @var URLGenerator
     */
    protected $urlGenerator;

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

        $config = $this->getMock('\OCP\IConfig');
        $cacheFactory = $this->getMock('\OCP\ICacheFactory');
        $this->urlGenerator = new \OC\URLGenerator($config, $cacheFactory);

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
     * Test index()
     */
    public function testIndex()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new WebViewController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->urlGenerator,
            $this->container->query("EpisodeMapper"),
            $this->container->query("FeedMapper")
        );

        $response = $controller->index();

        $this->assertEquals('OCP\AppFramework\Http\TemplateResponse', get_class($response));
    }

    /**
     * Test player()
     */
    public function testPlayer()
    {
        $request = $this->getMockBuilder('\OCP\IRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new WebViewController(
            $this->container->query("AppName"),
            $request,
            "phpunit",
            $this->urlGenerator,
            $this->container->query("EpisodeMapper"),
            $this->container->query("FeedMapper")
        );

        $episodes = $this->episodeMapper->getEpisodes("phpunit");

        $response = $controller->player($episodes[0]["id"]);

        $this->assertEquals('OCP\AppFramework\Http\TemplateResponse', get_class($response));
    }
}