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

class SidebarController

  @$inject: ['$scope', 'FeedService']
  constructor: ($scope, FeedService) ->
    @scope = $scope
    @feedService = FeedService

    @scope.filteredFeed = null
    @scope.feedUrl = "Feed URL"
    @scope.loading = false

    @loadFeeds()

  filter: (feed) ->
    if @isSelected(feed)
      @scope.filteredFeed = null
    else
      @scope.filteredFeed = feed

  isSelected: (selection) ->
    @scope.filteredFeed == selection

  subscribeFeed: ->
    @loading = yes
    @feedService.subscribe(@scope.feedUrl).then (response) =>
      @loading = no
      @loadFeeds()
    , (error) ->
      alert "Could not subcribe to the feed"

  unsubscribeFeed: (id) ->
    if confirm "Do you really want to unsubscribe the selected feed?"
      @loading = yes
      @feedService.unsubscribe(id).then (response) =>
        @loading = no
        @loadFeeds()
      , (error) ->
        alert "Could not unsubcribe the feed"

  loadFeeds: () ->
    @loading = yes
    @feedService.all().then (response) =>
      @scope.feeds = response.data.data
      @loading = no
    , (error) ->
      alert "Could not load the feeds"

angular.module("Podcasts").controller "SidebarController", SidebarController