var app = angular.module('app', ['ngAnimate']);

app.controller('storeController', function($scope, APIresult, $timeout, $http, $q){
	var vm = this;
	vm.showLimit = 3;
	vm.showCars = false;
	vm.showOrder = false;
	vm.orderBtn = false;
	
	vm.getCars = function(){
		var argumentsCars = {
			method: 'get', 
			url: 'http://api.primaprodukcija.si/carshop/cars', 
			parameters: {}
		};
		vm.error = false;
		vm.errorsCars = '';
		APIresult.getAPI(argumentsCars).then(
			function(respond){
				vm.notify = false;
				vm.showCars = ( respond.status == 200 ? true : false );
				vm.cars = respond.data;
				
			}, function(respond) {
				vm.errorsCars = respond.data.errors;
				vm.notify = false;
				vm.error = true;
			}, function(respond) {
				vm.notify = respond;
		});
	}
	vm.getCars();
	
	vm.getTimeslots = function(){
		var argumentsTimeslot = {
			method: 'get', 
			url: 'http://api.primaprodukcija.si/carshop/timeslots', 
			parameters: {}
		};
		vm.errorTimeslot = false;
		vm.errorsTimeslots = '';
		APIresult.getAPI(argumentsTimeslot).then(
			function(respond){
				vm.notifyTimeslot = false;
				vm.showTimeslot = ( respond.status == 200 ? true : false );
				vm.timeslots = respond.data;
					vm.res = respond;
				$timeout(function(){
					vm.selectedTimeslot=vm.filtered[0];
				}, 10);
			}, function(respond) {
				vm.errorsTimeslots = respond.data.errors;
				vm.notifyTimeslot = false;
				vm.errorTimeslot = true;
			}, function(respond) {
				vm.notifyTimeslot = respond;
		});
	}
	
	vm.sendOrder = function(){
		var argumentsTimeslot = {
			method: 'post', 
			url: 'http://api.primaprodukcija.si/carshop/reservations', 
			data: {
				timeslotId: vm.selectedTimeslot.id,
				token: 'b326b5062b2f0e69046810717534cb09', 
				email: vm.email
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' } 
		};
		vm.errorOrder = false;
		APIresult.getAPI(argumentsTimeslot).then(
			function(respond){
				vm.notifyOrder = false;
				vm.showOrder = ( respond.status == 200 ? true : false );
				vm.sended = respond.data[0].success;
				$timeout(function(){
					vm.sended.status = false;
					vm.sended = '';
				}, 8000);
			}, function(respond) {
				vm.notifyOrder = false;
				vm.errorOrder = true;
				vm.errorsSended = respond.data.errors;
				$timeout(function(){
					vm.errorOrder = false;
					vm.errorsSended = '';
				}, 8000);
			}, function(respond) {
				vm.notifyOrder = respond;
		});
	}
	
	vm.showMore = function(){
		vm.showLimit += 3;
	}
	
	vm.order = function(id){
		vm.showCars = false;
		vm.showOrder = true;
		vm.selectedCarId = id;
		vm.selectedCar = APIresult.idsData(id, vm.cars);
		vm.getTimeslots();
	}
	
	vm.changeCarTab = function(){
		vm.showCars = true;
		vm.showOrder = false;
		vm.showTimeslot = false;
		vm.selectedTimeslot = '';
		vm.sended.status = '';
		vm.store.errorOrder = '';
		vm.errorsTimeslots = '';
	}
});

app.filter('newLine',function() {
	return function(value){
		return value.replace('\\n', ", "); 
	}
});

app.factory('APIresult', function($rootScope, $http, $q, $timeout){
	function getCarsAPI(args){
		var deferred = $q.defer();
		var resultsSuccess, resultsError;
		$timeout(function(){
			deferred.notify({notify: true});
		}, 1);
		$http({
			url: args.url, 
			method: args.method,
			data: args.data,
			headers: args.headers
		}).success(function(data, status, headers, config){
			$timeout(function(){
				deferred.resolve({ data: data, status: status, headers: headers, config: config });
			}, 1010);
		})
    .error(function(data, status, headers, config){
			$timeout(function(){	
				deferred.reject({ data: data, status: status, headers: headers, config: config });
			}, 1500);
		});
		return deferred.promise;
	}
	
	function getTimeSlotsAPI(args){
		return args;
	}
	
	function idsIndex(id, data){
    for (var i = 0, len = data.length; i < len; i++) {
      if (data[i].id == id) {
        break;
      }
    }
		return i;
	}
	
	function idsData(id, data){
    var output = '';
		for (var i = 0, len = data.length; i < len; i++) {
      if (data[i].id == id) {
				output = data[i];
        break;
      }
    }
		return output;
	}
			
	return { 
		idsIndex: idsIndex,
		idsData: idsData,
		getAPI: getCarsAPI
	};
});