class EpisodeListController
  @$inject: ['$scope']
  constructor: (@scope) ->
    #alert("hey")

angular.module("Podcasts").controller "EpisodeListController", EpisodeListController