(function(){
	var Ranklist = angular.module('Ranklist', []);
	Ranklist.controller('RanklistCtrl', function($scope, $http, $filter){
		$http.get(ranklist_data.upload_path+ranklist_data.file)
			.success(function(data){
				$scope.list = data;
				console.log(ranklist_data.upload_path+ranklist_data.file);
				var orderBy = $filter('orderBy');
				$scope.list = orderBy($scope.list, '-lup', false);
				$scope.colorClass = function(judge, rating) {
					if( 'cf' == judge ){
						if( !rating || rating < 1200 ) return 'cf-newbie';
						else if( rating < 1400 ) return 'cf-pupil';
						else if( rating < 1600 ) return 'cf-specialist';
						else if( rating < 1900 ) return 'cf-expert';
						else if( rating < 2100 ) return 'cf-candidate-master';
						else if( rating < 2300 ) return 'cf-master';
						else if( rating < 2400 ) return 'cf-international-master';
						else if( rating < 2600 ) return 'cf-grandmaster';
						else if( rating < 3000 ) return 'cf-internation-grandmaster';
						else return 'cf-legendary-grandmaster';
					} else if ('cc' == judge) {
						if( !rating || rating < 1400 ) return 'cc-1star';
						else if( rating < 1600 ) return 'cc-2star';
						else if( rating < 1800 ) return 'cc-3star';
						else if( rating < 2000 ) return 'cc-4star';
						else if( rating < 2200 ) return 'cc-5star';
						else if( rating < 2500 ) return 'cc-6star';
						else return 'cc-7star';
					}
				}

				$scope.index = function( $index ){
					if( $index == 0 ) return 1;
					if( $scope.list[$index].lup != $scope.list[$index-1].lup ) $scope.i = $index;

					return $scope.i+1;
				}
			});
	});
})();