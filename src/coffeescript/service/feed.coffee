###
Copyright (c) 2016 David Prandzioch
https://github.com/dprandzioch/owncloud-podcasts

This file is part of owncloud-podcasts.

owncloud-podcasts is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
###

angular.module("Podcasts").factory "FeedService", ["$http", ($http) ->
  new class FeedService
    all: ->
      apiUrl = OC.generateUrl("/apps/podcasts/feeds")
      $http.get apiUrl
    subscribe: (feedUrl) ->
      apiUrl = OC.generateUrl("/apps/podcasts/feeds")
      $http.put apiUrl, url: feedUrl
    unsubscribe: (feedId) ->
      apiUrl = OC.generateUrl("/apps/podcasts/feeds/" + feedId)
      $http.delete apiUrl
    markAllPlayed: () ->
      apiUrl = OC.generateUrl("/episodes/markplayed")
      $http.post apiUrl
]
