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
    <div id="app-navigation" class="app--navigation" ng-controller="SidebarController as sidebar">
        <ul id="navigation-list" class="with-icon">
            <li class="navigation--add-new">
                <div class="add-new--container">
                    <form class="add-feed" data-url="<?php echo $_["add_url"] ?>">
                        <input type="text" class="add-feed--input" ng-model="feedUrl"
                               placeholder="<?php p($l->t("Enter Feed URL")); ?>"/>
                        <button class="add-feed--button" ng-click="sidebar.subscribeFeed()"
                                title="<?php p($l->t('Add Feed')); ?>"><?php p($l->t("Subscribe")); ?></button>
                        <img class="navigation--loading-indicator" ng-show="loading"
                             src="<?php print_unescaped(\OCP\Template::image_path("podcasts", "loading.gif")); ?>"/>
                    </form>
                </div>
            </li>

            <li class="navigation--feed" ng-repeat="feed in feeds">
                <a href="#" class="feed--item" ng-click="sidebar.filter(feed)" ng-class="{'is--active' : sidebar.isSelected(feed)}">{{feed.name}}</a>
                <div class="app-navigation-entry-utils">
                    <button class="feed--delete-button icon-delete" title="<?php p($l->t("Delete")); ?>"
                            ng-click="sidebar.unsubscribeFeed(feed.id)"></button>
                </div>
            </li>
        </ul>

        <div id="app-settings">
            <div id="app-settings-header">
                <button class="settings-button generalsettings" data-apps-slide-toggle="#app-settings-content"
                        tabindex="0"></button>
            </div>
            <div id="app-settings-content">
                <button class="settings--mark-played" ng-click="sidebar.markAllPlayed()">
                    <?php p($l->t("Mark all as played")); ?>
                </button>
            </div>
        </div>

    </div>
    <div id="app-content" ng-controller="EpisodeListController as list">
        <div class="podcasts--list">
            <img src="<?php print_unescaped(\OCP\Template::image_path("podcasts", "loading.gif")); ?>" ng-show="loading" />

            <div class="list--item" ng-repeat="episode in episodes">
                <div class="item--cover-container">
                    <img src="{{episode.cover}}" class="cover-container--cover" ng-click="list.select(episode)" ng-class="{'is--active' : list.isSelected(episode)}" ng-dblclick="list.openPlayer(episode)" />
                    <i class="cover-container--icon cover-container--icon-new icon-info-white" ng-show="episode.duration == 0 && episode.played == 0"></i>
                    <i class="cover-container--icon cover-container--icon-playing icon-play" ng-show="episode.duration > 0 && episode.played == 0"></i>
                </div>
                <div class="item--description">
                    {{episode.name}}
                </div>
            </div>
        </div>
    </div>
</div>
