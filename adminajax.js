jQuery( function( $ ) {

  $( '.change-views' ).blur( function() {

    let input = $(this);

    $.ajax({
      type : 'POST',
      url : ajaxurl, // localhost/wp-admin/admin-ajax.php
      data : {
        action : 'changeviews',
        post_id : input.data( 'id' ),
        views : input.val(),
        token : input.data( 'token' )
      },
      success : function( data ) {

        input.next().text(data);

      }
    });
    
  } );

} );