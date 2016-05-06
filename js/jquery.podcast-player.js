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
    $.fn.podcastPlayer = function (opts) {

        var settings = $.extend({
            positionUpdateInterval: 30000
        }, opts);

        this.each(function () {
            init.call(this, settings);
        });
    };

    function init(opts) {
        var player = $(this);
        var url = player.data("url");
        var name = player.data("name");
        var id = player.data("id");
        var currentTime = player.data("current-time");
        var updateEndpoint = player.data("update-endpoint");

        var audio = player.find(".audio")[0];
        var seekbar = player.find('.seekbar')[0];

        var secondToHumanReadable = function (duration) {
            var hour = 0;
            var min = 0;
            var sec = 0;

            duration = parseInt(duration);

            if (duration) {
                if (duration >= 60) {
                    min = Math.floor(duration / 60);
                    sec = duration % 60;
                } else {
                    sec = duration;
                }

                if (min >= 60) {
                    hour = Math.floor(min / 60);
                    min = min - hour * 60;
                }

                if (hour < 10) {
                    hour = '0' + hour;
                }

                if (min < 10) {
                    min = '0' + min;
                }

                if (sec < 10) {
                    sec = '0' + sec;
                }
            }
            return hour + ":" + min + ":" + sec;
        };

        var updatePosition = function () {
            if (!audio.paused) {
                $.ajax({
                    type: "POST",
                    url: updateEndpoint,
                    data: {
                        "id": id,
                        "second": parseInt(audio.currentTime),
                        "duration": parseInt(audio.duration)
                    }
                });
            }
        };

        player.find(".player--play-btn").on("click", function () {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }

            if (audio.paused) {
                $(this).removeClass("icon-pause-big");
                $(this).addClass("icon-play-big");
            } else {
                $(this).addClass("icon-pause-big");
                $(this).removeClass("icon-play-big");
            }
        });

        audio.addEventListener("timeupdate", function () {
            seekbar.value = audio.currentTime;
            player.find(".player--remaining").html("-" + secondToHumanReadable(audio.duration - audio.currentTime));
        });

        audio.addEventListener("durationchange", function () {
            if (audio.currentTime == 0) {
                audio.currentTime = currentTime;
                audio.play();
                setInterval(updatePosition, opts.positionUpdateInterval);
            }

            seekbar.min = 0;
            seekbar.max = audio.duration;
            seekbar.value = audio.currentTime;
            player.find(".player--loading").hide();
        });


        seekbar.addEventListener("change", function () {
            audio.currentTime = seekbar.value;
        });
    }
})(jQuery);