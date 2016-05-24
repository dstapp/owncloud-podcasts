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

style("podcasts", "default");
vendor_script("podcasts", "angular/angular.min");
vendor_script("podcasts", "angular-sanitize/angular-sanitize.min");
vendor_script("podcasts", "videogular/videogular");
vendor_script("podcasts", "videogular-controls/vg-controls");
vendor_script("podcasts", "videogular-buffering/vg-buffering");
vendor_script("podcasts", "videogular-poster/vg-poster");
script("podcasts", "podcasts");

?>
<div ng-app="Podcasts">
    <div class="player" ng-controller="PlayerController as controller" data-id="<?php echo $_["id"]; ?>">
        <videogular vg-theme="controller.config.theme.url"
                    vg-start-time="startTime"
                    vg-player-ready="controller.onPlayerReady($API)"
                    vg-update-time="controller.onUpdateTime($currentTime, $duration)"
                    class="videogular-container">
            <vg-media vg-src="controller.config.sources" vg-type="audio"></vg-media>

            <vg-controls>
                <vg-play-pause-button></vg-play-pause-button>
                <vg-time-display>{{ currentTime | date:'HH:mm:ss':'+0000' }}</vg-time-display>
                <vg-scrub-bar>
                    <vg-scrub-bar-current-time></vg-scrub-bar-current-time>
                </vg-scrub-bar>
                <vg-time-display>{{ timeLeft | date:'HH:mm:ss':'+0000' }}</vg-time-display>
                <vg-volume>
                    <vg-mute-button></vg-mute-button>
                </vg-volume>
            </vg-controls>

            <vg-buffering></vg-buffering>
            <vg-poster vg-url='controller.config.plugins.poster'></vg-poster>
        </videogular>
    </div>
</div>
