class PlayerController
  @$inject: ['$scope']
  constructor: (@scope) ->
    alert("foo")
    @scope.foo = "bar"

  testFoo: ->
    alert("huhu")

angular.module("Podcasts").controller "PlayerController", PlayerController