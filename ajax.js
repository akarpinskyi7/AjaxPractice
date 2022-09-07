// jQuery( function( $ ) {

// 	$( '#true_get_id' ).submit( function() {

// 		let form = $(this);

// 		$.ajax({
// 			type : 'POST',
// 			url : newajax.ajax_url,
// 			data : form.serialize(),
// 			beforeSend : function( xhr ) {
// 				form.find('button').text( 'Отправляем запрос...' );
// 			},
// 			success : function( data ){

// 				$('#result').html(data);
// 				form.find('button').text( 'Узнать заголовок' );
// 			}

// 		});

// 		return false;

// 	});


// } );


let form = document.getElementById( 'true_get_id' );
let result = document.getElementById( 'result' );

form.addEventListener( 'submit', (event) => {
	
	event.preventDefault();

	form.querySelector('button').textContent = 'Отправляем запрос...';


	var request = new XMLHttpRequest();
  request.open('POST', newajax.ajax_url, true);

	request.send( new FormData( form ) );

	request.onload = function () {
			     
		let resp = this.response;
		result.innerHTML = resp;

		form.querySelector('button').textContent = 'Узнать заголовок';

	}
	request.send();

} );





jQuery( function( $ ) {

	var button = $( '#loadmore a' ),
	paged = button.data( 'paged' ),
	maxPages = button.data( 'max_pages' );


	// $( window ).scroll( function() {
	//
	// 	if( $(document).scrollTop() > ( $( document ).height() - 1000 ) {
	//
	// 		// выполняем ajax запрос
	//
	// 	}
	//
	// } );

	button.click( function( event ) {

		event.preventDefault();


		if( ! $( 'body' ).hasClass( 'loading' ) ) {
		$.ajax({
			type : 'POST',
			url : newajax.ajax_url,
			data : {
				paged : paged,
				action : 'loadmore',
				taxonomy : button.data( 'taxonomy' ),
				term_id : button.data( 'term_id' ),
				pagenumlink : button.data( 'pagenumlink' )
			},
			dataType: 'json',
			beforeSend : function( xhr ) {
				button.text( 'Загружаем...' );
				$( 'body' ).addClass( 'loading' );
			},
			success : function( data ){
				//console.log(data);
				paged++;
				button.parent().before( data.posts );
				$( '#pagination' ).html( data.pagination );
				button.text( 'Загрузить ещё' );

				if( paged == maxPages ) {
					button.remove();
				}

				$( 'body' ).removeClass( 'loading' );

			}

		}); 

	}

	} );

} );