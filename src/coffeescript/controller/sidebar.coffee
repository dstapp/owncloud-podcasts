angular.module("Podcasts").controller "SidebarController", ['$scope', 'FeedService', ($scope, FeedService) ->

  FeedService.all().then (response) ->
    $scope.feeds = response.data.data
  , (error) ->
      alert "Could not load the feeds"
]