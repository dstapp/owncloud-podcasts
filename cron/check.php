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

namespace OCA\Podcasts\Cron;

use \OCA\Podcasts\AppInfo\Application;
use OCA\Podcasts\Db\Episode;
use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\FeedUpdater;
use PicoFeed\Reader\Reader;

/**
 * Class Check / Cronjob
 *
 * @package OCA\Podcasts\Cron
 */
class Check
{
    /**
     * Runs the cronjob
     */
    public static function run()
    {
        $app = new Application();
        $container = $app->getContainer();

        /** @var FeedUpdater $feedUpdater */
        $feedUpdater = $container->query("FeedUpdater");

        $feedUpdater->processFeeds();
    }

}
