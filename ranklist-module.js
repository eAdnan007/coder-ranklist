(function(){
	var Ranklist = angular.module('Ranklist', []);
	Ranklist.controller('RanklistCtrl', function($scope, $http, $filter){
		$http.get(ranklist_data.upload_path+ranklist_data.file)
			.success(function(data){
				$scope.list = data;

				var orderBy = $filter('orderBy');
				$scope.list = orderBy($scope.list, '-lup', false);
				$scope.colorCode = function(judge, rating){
					if( 'cf' == judge ){
						if(rating < 1200 ) return '#808080';
						else if(rating < 1500 ) return '#008000';
						else if(rating < 1700 ) return '#0000FF';
						else if(rating < 1900 ) return '#AA00AA';
						else if(rating < 2200 ) return '#FF8C00';
						else return '#FF0000';
					}
					else if( 'tc' == judge ){
						if( rating < 900 ) return '#E20226';
						else if( rating < 1200 ) return '#EDD30A';
						else if( rating < 1500 ) return '#6E5DFB';
						else if( rating < 2200 ) return '#009500';
						else return '#8BA1B6';
					}
				}
			});
	});
})();