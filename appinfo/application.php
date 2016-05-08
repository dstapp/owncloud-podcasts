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

namespace OCA\Podcasts\AppInfo;

use OCA\Podcasts\Controller\EpisodesController;
use OCA\Podcasts\Controller\PodcastController;
use OCA\Podcasts\Controller\WebViewController;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use OCP\AppFramework\App;

/**
 * Main application class
 */
class Application extends App
{

    /**
     * Sets up the applications' shared services
     *
     * @param array $urlParams
     */
    public function __construct(array $urlParams = [])
    {
        parent::__construct("podcasts", $urlParams);

        $container = $this->getContainer();

        $container->registerService("EpisodeMapper", function ($c) {
            $episodeMapper = new EpisodeMapper(
                \OC::$server->getDatabaseConnection()
            );

            return $episodeMapper;
        });

        $container->registerService("FeedMapper", function ($c) {
            $feedMapper = new FeedMapper(
                \OC::$server->getDatabaseConnection()
            );

            return $feedMapper;
        });

        $container->registerService("FeedUpdater", function ($c) {
            $feedUpdater = new FeedUpdater(
                $c->query("UserId"),
                $c->query("FeedMapper"),
                $c->query("EpisodeMapper")
            );

            return $feedUpdater;
        });

        $container->registerService("EpisodesController", function ($c) {
            return new EpisodesController(
                $c->query("AppName"),
                $c->query("Request"),
                $c->query("UserId"),
                $c->query("EpisodeMapper")
            );
        });

        $container->registerService("WebViewController", function ($c) {
            return new WebViewController(
                $c->query("AppName"),
                $c->query("Request"),
                $c->query("UserId"),
                $c->query("UrlGenerator"),
                $c->query("EpisodeMapper"),
                $c->query("FeedMapper")
            );
        });
    }

}
