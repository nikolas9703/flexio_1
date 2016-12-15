/**
 * Controlador Grid
 */
bluapp.controller("gridCtrl", function($scope, $http, $rootScope, Grid){
	$scope.grid = new Grid();

	//Cargar los cards al inicio de la pagina
	$scope.grid.nextPage();
});

//Grid constructor function to encapsulate HTTP and pagination logic
bluapp.factory('Grid', function($http) {

	var Grid = function() {
		this.items = [];
		this.busy = false;
		this.rows = 9;
		this.button = 'Cargar Mas';
		this.loaded = false;
		this.error = false;
	};

	Grid.prototype.nextPage = function() {

		if (this.busy) return;
		if (this.loaded) return;
		this.busy = true;

		if(typeof grid_url == 'undefined'){
			console.log('No se ha especificado una URL de grid.');
			this.error = true;
			return false;
		}

		var url = window.phost() + grid_url; 

		$http({
			method: 'POST',
			url: url,
			data: $.param({
				erptkn: tkn,
				page: 1,
				rows: this.rows,
				sidx: 'nombre',
				sord: "ASC"
			}),
			cache: false,
			headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (json){

			var total_records = json.data.records;
			var items = json.data.result;
			this.items = [];

			if(items.length < total_records || this.items.length == 0){
				for (var i = 0; i < items.length; i++) {
					this.items.push(items[i]);
				}
				//Sumar 9 registros mas, para la siguiente busqueda
				this.rows = this.rows + 9;
			}else{
				this.loaded = true
			}

			//Ajustar el tamanno de los cards
			setTimeout(function(){
				$('.grid-item').find('.contact-box').matchHeight({
					byRow: true,
	            });
			}, 500);

			this.busy = false;
		}.bind(this));

	};

	return Grid;
});
