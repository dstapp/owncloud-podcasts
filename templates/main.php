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

script("podcasts", "vendor/angular.min");
script("podcasts", "vendor/js_tpl");

//script("podcasts", "jquery.podcast-sidebar");
//script("podcasts", "jquery.episode-list");
//script("podcasts", "episodelist");

script("podcasts", "app");
script("podcasts", "service/feed");
script("podcasts", "controller/sidebar");
script("podcasts", "controller/episode-list");

style("podcasts", "default");
?>
<div ng-app="Podcasts">
    <div id="app-navigation" class="app--navigation" ng-controller="SidebarController as sidebar">
        <ul id="navigation-list" class="with-icon">
            <li class="navigation--add-new">
                <div class="add-new--container">
                    <form class="add-feed" data-url="<?php echo $_["add_url"] ?>">
                        <input type="text" class="add-feed--input" value=""
                               placeholder="<?php p($l->t("Enter Feed URL")); ?>"/>
                        <button class="add-feed--button" ng-click="sidebar.testFoo()"
                                title="<?php p($l->t('Add Feed')); ?>"><?php p($l->t("Add Feed")); ?></button>
                        <img class="navigation--loading-indicator"
                             src="<?php print_unescaped(\OCP\Template::image_path("podcasts", "loading.gif")); ?>"/>
                    </form>
                </div>
            </li>

            <li class="list--feed-container" data-endpoint="<?php echo $_["feed_endpoint"]; ?>"
                data-delete-endpoint="<?php echo $_["feed_delete_endpoint"]; ?>">
            </li>

            <li class="feed-container--item" ng-repeat="feed in feeds">
                <a href="#" class="feed--item" data-id="{{feed.id}}">{{feed.name}}</a>
                <div class="app-navigation-entry-utils">
                    <button class="feed--delete-button icon-delete" title="<?php p($l->t("Delete")); ?>"
                            data-id="{{feed.id}}"></button>
                </div>
            </li>
        </ul>

        <div id="app-settings">
            <div id="app-settings-header">
                <button class="settings-button generalsettings" data-apps-slide-toggle="#app-settings-content"
                        tabindex="0"></button>
            </div>
            <div id="app-settings-content">
                <button class="settings--mark-played" data-endpoint="<?php echo $_["mark_all_as_played_endpoint"]; ?>">
                    <?php p($l->t("Mark all as played")); ?>
                </button>
            </div>
        </div>

    </div>
    <div id="app-content" ng-controller="EpisodeListController as list">
        <div class="podcasts--list" data-endpoint="<?php echo $_["episode_endpoint"]; ?>"
             data-player-url="<?php echo $_["player_url"]; ?>">
            <img src="<?php print_unescaped(\OCP\Template::image_path("podcasts", "loading.gif")); ?>"/>
        </div>
    </div>

    <script type="text/html" id="item_tmpl">
        <div class="list--item" data-url="<&= escapeHTML(playerUrl) &><&= escapeHTML(val.id) &>"
             data-feed-id="<&= escapeHTML(val.feed_id) &>">
            <div class="item--cover-container">
                <img src="<&= val.cover &>" class="cover-container--cover"/>
                <& if (val.duration == 0 && val.played == 0) { &>
                <i class="cover-container--icon cover-container--icon-new icon-info-white"></i>
                <& } &>

                <& if (val.duration > 0 && val.played == 0) { &>
                <i class="cover-container--icon cover-container--icon-playing icon-play"></i>
                <& } &>
            </div>
            <div class="item--description">
                <&= escapeHTML(val.name) &>
            </div>
        </div>
    </script>
</div>