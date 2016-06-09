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

$application = new Application();

$application->registerRoutes($this, [
    "routes" => [
        [
            "name" => "web_view#index",
            "url"  => "/",
            "verb" => "GET",
        ],
        [
            "name" => "web_view#player_template",
            "url"  => "/playerTemplate",
            "verb" => "GET",
        ],
        [
            "name" => "web_view#player",
            "url"  => "/player/{episodeId}",
            "verb" => "GET",
        ],
        [
            "name" => "episodes#get_episodes",
            "url"  => "/episodes",
            "verb" => "GET",
        ],
        [
            "name" => "episodes#get_episode",
            "url"  => "/episodes/{episodeId}",
            "verb" => "GET",
        ],
        [
            "name" => "episodes#update_position",
            "url"  => "/episodes/{episodeId}/position",
            "verb" => "POST",
        ],
        [
            "name" => "episodes#mark_played",
            "url"  => "/episodes/markplayed",
            "verb" => "POST",
        ],
        [
            "name" => "feeds#add_feed",
            "url"  => "/feeds",
            "verb" => "PUT",
        ],
        [
            "name" => "feeds#get_feeds",
            "url"  => "/feeds",
            "verb" => "GET",
        ],
        [
            "name" => "feeds#delete_feed",
            "url"  => "/feeds/{feedId}",
            "verb" => "DELETE",
        ],
    ],
]);
