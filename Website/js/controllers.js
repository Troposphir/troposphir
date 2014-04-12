var SERVER_PATH = "/Troposphir/Server/";
angular.module("troposphir", [])
.controller("navigation", function($scope, $location) {
	$scope.page = "browser";
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
	$scope.changePage(hash);
})
.controller("levelBrowser", function LevelListCtrl($scope, $http) {
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
		console.log(data);
		$scope.levels = data;
	});
	$scope.doSearch = _.debounce(function() {
		//TODO: search
		//$http.post(SERVER_PATH, );
	}, 500);
})
.controller("levelCard", function($scope, $http) {
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
				playCount: level.dc,
				author: level.author,
				screenshot: SERVER_PATH+"image/maps/?id="+level.screenshotId+"&lid="+level.id,
				rating: level.rating,
				difficulty: level.difficulty,
				ratingPercent: ((level.rating/5)*100),
				difficultyPercent: ((level.difficulty/5)*100)
			};
		});
	};
	$scope.$watch("args", function() {
		if ($scope.args.length > 0 && $scope.page == "level") {
			getLevel($scope.args[0]).then(function(level) {
				console.log(level);
				$scope.level = level;
			});
		}
	});
});