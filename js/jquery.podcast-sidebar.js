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

(function ($) {
    $.fn.podcastSidebar = function (opts) {

        var settings = $.extend({}, opts);

        this.each(function () {
            init.call(this, settings);
        });
    };

    function init(opts) {
        var sidebar = $(this);

        sidebar.find(".add-feed--button").on("click", function (e) {
            var endpoint = sidebar.find(".add-feed").data("url");

            sidebar.find(".add-feed--button").hide();
            sidebar.find(".navigation--loading-indicator").show();

            $.ajax({
                type: "PUT",
                url: endpoint,
                data: {
                    "url": sidebar.find(".add-feed--input").val()
                },
                success: function (data) {
                    opts.listElement.episodeList("loadEpisodes");
                    loadFeeds();

                    sidebar.find(".add-feed--input").val("");
                },
                error: function (request, status, error) {
                    alert("Feed could not be added");
                    sidebar.find(".navigation--loading-indicator").hide();
                    sidebar.find(".add-feed--button").show();
                }
            }).done(function () {
                sidebar.find(".navigation--loading-indicator").hide();
                sidebar.find(".add-feed--button").show();
            });

            e.preventDefault();
        });

        sidebar.on("click", ".feed--item", function (e) {
            if ($(this).hasClass("feed--item-active")) {
                sidebar.find(".feed--item").removeClass("feed--item-active");
                opts.listElement.episodeList("loadEpisodes");
            } else {
                sidebar.find(".feed--item").removeClass("feed--item-active");
                $(this).addClass("feed--item-active");

                opts.listElement.episodeList("loadEpisodes", {
                    feedId: $(this).data("id")
                });
            }
        });

        sidebar.on("click", ".feed--delete-button", function (e) {
            if (true == confirm("Do you really want to delete the selected feed?")) {
                var deleteEndpoint = sidebar.find(".list--feed-container").data("delete-endpoint");

                sidebar.find(".add-feed--button").hide();
                sidebar.find(".navigation--loading-indicator").show();

                $.ajax({
                    type: "DELETE",
                    url: deleteEndpoint + $(this).data("id"),
                    success: function (data) {
                        opts.listElement.episodeList("loadEpisodes");
                        loadFeeds();
                    },
                    error: function (data) {
                        alert("Could not delete Feed");
                        sidebar.find(".navigation--loading-indicator").hide();
                        sidebar.find(".add-feed--button").show();
                    }
                }).done(function () {
                    sidebar.find(".navigation--loading-indicator").hide();
                    sidebar.find(".add-feed--button").show();
                });
            }
        });

        sidebar.on("click", ".settings--mark-played", function (e) {
            if (true == confirm("Do you really want to mark all items as played?")) {
                var endpoint = $(this).data("endpoint");

                sidebar.find(".add-feed--button").hide();
                sidebar.find(".navigation--loading-indicator").show();

                $.ajax({
                    type: "POST",
                    url: endpoint,
                    success: function (data) {
                        opts.listElement.episodeList("loadEpisodes");
                    },
                    error: function (data) {
                        alert("Could not mark Episodes as played");
                        sidebar.find(".navigation--loading-indicator").hide();
                        sidebar.find(".add-feed--button").show();
                    }
                }).done(function () {
                    sidebar.find(".navigation--loading-indicator").hide();
                    sidebar.find(".add-feed--button").show();
                });
            }
        });

        var loadFeeds = function () {
            $.getJSON(sidebar.find(".list--feed-container").data("endpoint"), function (data) {
                var items = [];
                $.each(data["data"], function (key, val) {
                    var binding = {
                        val: val
                    };

                    items.push(tmpl("feed_tmpl", binding));
                });

                items.push('<div class="item--clearfix"></div>');
                sidebar.find(".list--feed-container").html(items.join(""));
            });
        };

        loadFeeds();
    }
})(jQuery);