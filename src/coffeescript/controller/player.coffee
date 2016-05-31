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

'use strict'

class PlayerController

  @$inject: [ "$scope", "$sce", "EpisodeService", "$attrs" ]
  constructor: ($scope, $sce, EpisodeService, $attrs) ->
    @scope = $scope
    @sce = $sce
    @episodeService = EpisodeService

    @config = {}
    @id = $attrs.id
    @lastUpdateTime = 0
    @updateLocked = no
    @api = null

    @vendorPath = OC.getRootPath() + "/apps/podcasts/vendor"

    @episodeService.get(@id).then (response) =>
      episode = response.data.data
      console.log episode.cover
      @lastUpdateTime = episode.currentSecond
      @scope.startTime = @lastUpdateTime

      @config =
        sources: [
          {
            src: $sce.trustAsResourceUrl episode.url
            type: episode.mimeType
          }
        ]
        theme:
          url: @vendorPath + "/videogular-themes-default/videogular.css"
    , (error) ->
      alert "Could not load the episode"

    @scope.loading = no

  onUpdateTime: ($currentTime, $duration) ->
    console.log "update"
    if (($currentTime - @lastUpdateTime) > 30 || @lastUpdateTime > $currentTime) && @updateLocked == no
      @updateLocked = yes
      @episodeService.updatePosition(@id, $currentTime, $duration).then (response) =>
        console.log "time updated"
        @lastUpdateTime = $currentTime
        @updateLocked = no
      , (error) ->
        console.log "could not persist current location"
        @updateLocked = no

  onPlayerReady: ($API) =>
    @api = $API

angular.module("Podcasts").controller "PlayerController", PlayerController
