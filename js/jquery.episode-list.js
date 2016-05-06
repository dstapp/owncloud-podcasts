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

    var methods = {
        init: function (opts) {
            var opts = $.extend({}, opts);

            var episodeListElement = $(this);

            episodeListElement.episodeList("loadEpisodes");

            episodeListElement.on("click", ".cover-container--cover", function (e) {
                episodeListElement.find(".cover-container--cover").removeClass("is--active");
                $(this).addClass("is--active");
            });

            $(document).on("click", "body", function (e) {
                if (!$(e.target).hasClass("cover-container--cover")) {
                    episodeListElement.find(".cover-container--cover").removeClass("is--active");
                }
            });

            episodeListElement.on("dblclick", ".list--item", function (e) {
                url = $(this).data("url");
                window.open(url, "_blank", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,height=240,width=500");
            });
        },

        loadEpisodes: function (opts) {
            var opts = $.extend({
                feedId: null
            }, opts);

            var episodeListElement = $(this);
            var endpoint = episodeListElement.data("endpoint");
            var playerUrl = episodeListElement.data("playerUrl");

            if (opts.feedId != null) {
                endpoint += "?feedId=" + opts.feedId;
            }

            $.getJSON(endpoint, function (data) {
                var items = [];
                $.each(data["data"], function (key, val) {
                    var binding = {
                        playerUrl: playerUrl,
                        val: val
                    };

                    items.push(tmpl("item_tmpl", binding));
                });

                items.push('<div class="item--clearfix"></div>');

                episodeListElement.html(items.join(""));
            });
        }
    };

    $.fn.episodeList = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error("Method does not exist " + method);
        }
    };
})(jQuery);