(function(){
	var Ranklist = angular.module('Ranklist', []);
	Ranklist.controller('RanklistCtrl', function($scope, $http, $filter){
		$http.get(ranklist_data.upload_path+ranklist_data.file)
			.success(function(data){
				$scope.list = data;

				var orderBy = $filter('orderBy');
				$scope.list = orderBy($scope.list, '-lup', false);
			});
	});
})();