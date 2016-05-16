angular.module("Podcasts").factory 'FeedService', ['$http', ($http) ->
  new class FeedService
    all: ->
      url = OC.generateUrl('/apps/podcasts/feeds')
      $http.get(url)
]