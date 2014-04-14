var SERVER_PATH = "/Troposphir/Server/";
angular.module("troposphir", [])
.controller("navigation", function($scope, $location) {
	$scope.args = [];
	$scope.changePage = function(destination) {
		if (destination.match(/^https?:/)) {
			location.href = destination;
		} else {
			var temp = destination.split("/");
			$scope.page = temp.splice(0, 1);
			$scope.args = temp;
			$location.url(destination);
		}
	};
	var hash = window.location.hash.substring(2);
	if (hash.length) {
		$scope.changePage(hash);
	} else {
		$scope.page = "browser";
	}
})
.controller("levelBrowser", function($scope, $http) {
	var getPage = function(page, size) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				"json": {
					header: {
						_t: "mfheader"
					},
					body: {
						_t: "a_llsReq",
						query: "",
						freq: {
							start: page,
							blockSize: size
						}
					}
				}
			}
		}).then(function(response) {
			return _.map(response.data.body.fres.results, function(level) {
				return {
					id: level.id,
					name: level.name,
					description: level.description,
					playCount: level.dc,
					author: level.author,
					screenshot: SERVER_PATH+"image/maps/?id="+level.screenshotId+"&lid="+level.id,
					rating: level.rating,
					difficulty: level.difficulty,
					ratingPercent: ((level.rating/5)*100),
					difficultyPercent: ((level.difficulty/5)*100)
				};
			});
		});
	};
	$scope.levels = [];
	getPage(0, 10).then(function(data) {
		$scope.levels = data;
	});
	$scope.doSearch = _.debounce(function() {
		//TODO: search
		//$http.post(SERVER_PATH, );
	}, 500);
})
.controller("levelCard", function($scope, $http, $q) {
	var getLevel = function(id) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				"json": {
					header: {
						_t: "mfheader"
					},
					body: {
						_t: "getLevelByIdReq",
						levelId: id
					}
				}
			}
		}).then(function(response) {
			var level = response.data.body.level;
			return {
				id: level.id,
				name: level.name,
				description: level.description,
				playCount: level.downloads,
				author: level.author,
				screenshot: SERVER_PATH+"image/maps/?id="+level.screenshotId+"&lid="+level.id,
				rating: level.rating,
				difficulty: level.difficulty,
				ratingPercent: ((level.rating/5)*100),
				difficultyPercent: ((level.difficulty/5)*100)
			};
		});
	};
	var getUser = function(id) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				"json": {
					header: {
						_t: "mfheader"
					},
					body: {
						_t: "getUserByIdReq",
						uid: id
					}
				}
			}
		}).then(function(response) {
			var user = response.data.body.user;
			return {
				id: id,
				name: user.username
			};
		});
	};
	var getComments = function(id, start, size) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				"json": {
					header: {
						_t: "mfheader"
					},
					body: {
						_t: "getLevelCommentsReq",
						levelId: id,
						freq: {
							start: start,
							blockSize: size || 0
						}
					}
				}
			}
		}).then(function(response) {
			var comments = response.data.body.fres.results;
			if (comments.length > 0) {
				var promises = [];
				for (var i = comments.length-1; i >= 0; i-=1) {
					promises.push(getUser(comments[i].uid));
				}
				return $q.all(promises).then(function(users) {
					var cmts = [];
					for (var i = users.length-1; i >= 0; i-=1) {
						cmts.push({
							user: users[i].name,
							body: comments[i].body
						});
					}
					return cmts;
				});
			} else {
				return [];
			}
		});
	};
	var getScores = function(id, start, size) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				"json": {
					header: {
						_t: "mfheader"
					},
					body: {
						_t: "getLeaderboardReq",
						cid: id,
						freq: {
							start: start,
							blockSize: size || 0
						}
					}
				}
			}
		}).then(function(response) {
			var scores = response.data.body.fres.results;
			if (scores.length > 0) {
				var promises = [];
				for (var i = scores.length-1; i >= 0; i-=1) {
					promises.push(getUser(scores[i].uid));
				}
				return $q.all(promises).then(function(users) {
					var scrs = [];
					for (var i = users.length-1; i >= 0; i-=1) {
						scrs.push({
							ranking: i+1,
							user: users[i].name,
							score: scores[i].s1
						});
					}
					return scrs;
				});
			} else {
				return [];
			}
		});
	};
	$scope.$watch("args", function() {
		if ($scope.args.length > 0 && $scope.page == "level") {
			getLevel($scope.args[0]).then(function(level) {
				$scope.level = level;
			});
			getComments($scope.args[0], 0, 100).then(function(comments) {
				$scope.comments = comments;
			});
			getScores($scope.args[0], 0, 20).then(function(scores) {
				$scope.scores = scores;
			});
		}
	});
});