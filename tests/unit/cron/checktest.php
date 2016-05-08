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

namespace OCA\Podcasts\Tests\Unit\Cron;

use OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Cron\Check;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\IAppContainer;
use OCP\IDBConnection;
use Test\TestCase;

/**
 * Class CheckTest
 *
 * @package OCA\Podcasts\Tests\Unit\Cron
 *
 * @group DB
 */
class CheckTest extends TestCase
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
     * @var Check
     */
    protected $cronClass;

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
        $this->cronClass = new Check();
        $this->feedMapper = $this->container->query("FeedMapper");
        $this->feedUpdater = $this->container->query("FeedUpdater");

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

    public function testCheck()
    {
        $this->cronClass->run();
    }


}