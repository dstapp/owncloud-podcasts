app = angular.module "Podcasts", []

app.config ['$httpProvider', ($httpProvider) ->
  $httpProvider.defaults.headers.common.requesttoken = oc_requesttoken;
]