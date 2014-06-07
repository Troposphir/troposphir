var SERVER_PATH = "/Troposphir/Server/";
var requests = function(http_module, promise_module) {
	var doRequest = function($http, body) {
		return $http({
			method: "POST",
			url: SERVER_PATH,
			params: {
				json: {
					header: {
						_t: "mfheader"
					},
					body: body
				}
			}
		});
	};
	return {
		getPage: function(page, size) {
			return doRequest(http_module, {
				_t: "a_llsReq",
				query: "",
				freq: {
					start: page,
					blockSize: size
				}
			}).then(function(response) {
				return _.map(response.data.body.fres.results, function(level) {
					return {
						id: level.id,
						name: level.name,
						description: level.description,
						playCount: level.dc,
						author: level.author,
						screenshot: SERVER_PATH+"image/?id="+level.screenshotId+"&lid="+level.id,
						rating: level.rating,
						difficulty: level.difficulty,
						ratingPercent: ((level.rating/5)*100),
						difficultyPercent: ((level.difficulty/5)*100)
					};
				});
			});
		},
		getLevel: function(id) {
			return doRequest(http_module, {
				_t: "getLevelByIdReq",
				levelId: id
			}).then(function(response) {
				var level = response.data.body.level;
				return {
					id: level.id,
					name: level.name,
					description: level.description,
					playCount: level.downloads,
					author: level.author,
					screenshot: SERVER_PATH+"image/?id="+level.screenshotId+"&lid="+level.id,
					rating: level.rating,
					difficulty: level.difficulty,
					ratingPercent: ((level.rating/5)*100),
					difficultyPercent: ((level.difficulty/5)*100)
				};
			});
		},
		getUser: function(id) {
			return doRequest(http_module, {
				_t: "getUserByIdReq",
				uid: id
			}).then(function(response) {
				var user = response.data.body.user;
				return {
					id: id,
					name: user.username
				};
			});
		},
		getComments: function(id, start, size) {
			return doRequest(http_module, {
				_t: "getLevelCommentsReq",
				levelId: id,
				freq: {
					start: start,
					blockSize: size || 0
				}
			}).then(function(response) {
				var comments = response.data.body.fres.results;
				if (comments.length > 0) {
					var promises = [];
					for (var i = comments.length-1; i >= 0; i-=1) {
						promises.push(requests.getUser(comments[i].uid));
					}
					return promise_module.all(promises).then(function(users) {
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
		},
		getScores: function(id, start, size) {
			return doRequest(http_module, {
				_t: "getLeaderboardReq",
				cid: id,
				freq: {
					start: start,
					blockSize: size || 0
				}
			}).then(function(response) {
				var scores = response.data.body.fres.results;
				if (scores.length > 0) {
					var promises = [];
					for (var i = scores.length-1; i >= 0; i-=1) {
						promises.push(this.getUser(scores[i].uid));
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
		}
	};
};
//START ANGULAR
angular.module("troposphir", [])
.directive("searchFilter", function($compile) {
	return {
		restrict: "E",
		scope: false,
		terminal: true,
		priority: 1000,
		compile: function(element, attrs) {
			element = angular.element(element);
			function fillTemplate(template, valGetter) {
				return template.replace(/\{([-a-zA-Z]*)\}/g, function(match, field) {
					return valGetter(field);
				});
			}
			element.addClass("filter");
			switch (element.attr("type")) {
			case "text":
				var html = fillTemplate('<input type="search">', element.attr.bind(element));
				element.html(html);
				break;
			case "checkbox":
				var html = fillTemplate('<input type="{type}" id="{name}_checkbox"><label for="{name}_checkbox">{label}</label>', function(attr) {
					if (attr == "label") {
						return element.html();
					} else if (attr == "name") {
						
						return element.attr(attr).replace(".", "_");
					} else {
						return element.attr(attr);
					}
				});
				element.html(html);
				break;
			}
			return function postLink($scope, element, attributes) {
				function onChange(newValue) {
					$scope.$eval(attributes.name+" = "+JSON.stringify(newValue));
					return $scope.$eval(attributes.update);
				}
				var input = element.children("input");
				if (attributes.type == "checkbox") {
					input[0].indeterminate = true;
					input.on("mouseup", function(event) {
						var elm = input[0];
						if (event.which == 3) {
							elm.checked = false;
							elm.indeterminate = true;
						} else {
							elm.indeterminate = false;
						}
						onChange(elm.indeterminate? null : elm.checked);
					});
				} else {
					input.on("input", function(event) {
						onChange(input.val());
					});
				}
				input.on("contextmenu", function(event) {
					event.preventDefault();
				});
			};
		}
	};
})
.controller("navigation", function($scope, $location) {
	var updatePage = function() {
		var temp = $location.path().substring(1).split("/");
		$scope.page = temp.splice(0,1);
		$scope.args = temp;
	};
	$scope.changePage = function(path) {
		if (path.match("^https?:")) {
			window.location = path;
		} else {
			$location.path(path);
		}
	};
	$scope.args = [];
	$scope.$on("$locationChangeSuccess", function(event, data) {
		updatePage();
	});
	$scope.changePage("browser");
})
.controller("levelBrowser", function($scope, $http) {
	var request = requests($http);
	$scope.levels = [];
	$scope.search = {};
	window.__$scp = $scope;
	request.getPage(0, 10).then(function(data) {
		$scope.levels = data;
	});
	$scope.doSearch = _.debounce(function() {
		//TODO: do search
	}, 500);
})
.controller("levelCard", function($scope, $http, $q) {
	var request = requests($http, $q);
	$scope.$watch("args", function() {
		if ($scope.args.length > 0 && $scope.page == "level") {
			request.getLevel($scope.args[0]).then(function(level) {
				$scope.level = level;
			});
			request.getComments($scope.args[0], 0, 100).then(function(comments) {
				$scope.comments = comments;
			});
			request.getScores($scope.args[0], 0, 20).then(function(scores) {
				$scope.scores = scores;
			});
		}
	});
})
.filter("reverse", function() {
	return function(array) {
		var copy = [].concat(array);
		return copy.reverse();
	};
});