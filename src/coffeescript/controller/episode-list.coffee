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

class EpisodeListController

  @$inject: [ "$scope", "EpisodeService" ]
  constructor: ($scope, EpisodeService) ->
    @scope = $scope
    @episodeService = EpisodeService
    @scope.loading = no
    @scope.selectedEpisode = null

    @loadEpisodes()

  select: (episode) ->
    if @isSelected(episode)
      @scope.selectedEpisode = null
    else
      @scope.selectedEpisode = episode

  isSelected: (episode) ->
    @scope.selectedEpisode == episode

  loadEpisodes: () ->
    @scope.loading = yes
    @episodeService.all().then (response) =>
      @scope.episodes = response.data.data
      @scope.loading = no
    , (error) ->
      alert "Could not load the episodes"

  openPlayer: (episode) ->
    playerUrl = OC.generateUrl("/apps/podcasts/player/" + episode.id)
    window.open playerUrl, "_blank", "toolbar=no, status=no, menubar=no, resizable=no, height=240,width=500"
    return true


angular.module("Podcasts").controller "EpisodeListController", EpisodeListController
