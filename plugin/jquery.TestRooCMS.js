/**
 * This is prototype
 * not used
 */

(function( $ ){

	var options = jQuery.extend({
		bgEven: '#FFC080', // бэкграунд для четных строк
		bgOdd: '#FFDFBF', // бэкграунд для нечетных строк
		fontEven: '#AA7239', // цвет шрифта четных строк
		fontOdd: '#AA7239', // цвет шрифта нечетных строк
		bgHover: '#FF8000', // бэкграунд при hover
		fontHover: '#55391C' // цвет шрифта при hover
	},options);

	var methods = {
		init : function( options ) {

			return this.each(function(){
				$(window).bind('resize.tooltip', methods.reposition);
			});

		},
		destroy : function( ) {

			return this.each(function(){

				var $this = $(this), data = $this.data('tooltip');

				// пространства имён рулят!!11
				$(window).unbind('.tooltip');
				data.tooltip.remove();
				$this.removeData('tooltip');

			})

		},
		reposition : function( ) {
			console.log(this); // jQuery
			console.log(this.length); // число элементов
		},
		show : function( ) {
			console.log(this); // jQuery
			console.log(this.length); // число элементов
		},
		hide : function( ) {
			console.log(this); // jQuery
			console.log(this.length); // число элементов
		},
		update : function( content ) {
			console.log(this); // jQuery
			console.log(content); // jQuery

			console.log(this.length); // число элементов
		}
	};

	$.fn.TestRooCMS = function( method ) {

		// логика вызова метода
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Метод с именем ' +  method + ' не существует для jQuery.TestRooCMS' );
		}
	};

})( jQuery );