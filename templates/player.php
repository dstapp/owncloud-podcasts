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

script("podcasts", "app");
script("podcasts", "controller/player");

script("podcasts", "player");
?>
<div id="player" class="player" data-url="<?php echo $_["episode"]->getUrl() ?>"
     data-name="<?php echo $_["episode"]->getName() ?>"
     data-current-time="<?php echo $_["episode"]->getCurrentSecond() ?>"
     data-update-endpoint="<?php echo $_["update_endpoint"] ?>"
     data-id="<?php echo $_["episode"]->getId() ?>">

    <span class="player--loading">
        <img src="<?php print_unescaped(\OCP\Template::image_path("podcasts", "loading.gif")); ?>"/>
    </span>

    <audio class="audio" name="" src="<?php echo $_["episode"]->getUrl() ?>"></audio>

    <div class="player--cover">
        <img src="<?php echo $_["feed"]->getCover() ?>" class="cover--image"/>
    </div>
    <div class="player--content">
        <div class="player--title"><?php echo $_["episode"]->getName() ?></div>

        <div class="player--seekbar">
            <input type="range" step="any" class="seekbar"/>
            <span class="player--remaining">00:00:00</span>
        </div>
        <div class="player--controls">
            <i class="icon-pause-big player--play-btn"></i>
        </div>
    </div>
    <div class="clearfix"></div>
</div>