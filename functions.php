<?php

// на сайте
add_action( 'wp_enqueue_scripts', function() {

	wp_enqueue_script( 'jquery' );

	wp_register_script( 'getname', get_stylesheet_directory_uri() . '/ajax.js', array( 'jquery' ), time(), true );
	wp_localize_script(
		'getname',
		'newajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		)
	);
	wp_enqueue_script( 'getname' );

} );


// в админке
add_action( 'admin_enqueue_scripts', function() {

	wp_enqueue_script( 'getname', get_stylesheet_directory_uri() . '/adminajax.js', array( 'jquery' ), time(), true );
	

} );



add_action( 'wp_ajax_getpostname', 'ajax_query' );
add_action( 'wp_ajax_nopriv_getpostname', 'ajax_query' );

function ajax_query() {

	$post_id = ! empty( $_POST[ 'post_id' ] ) && $_POST[ 'post_id' ] ? intval( $_POST[ 'post_id' ] ) : 0;

		$post = get_post( $post_id );
	
		if( ! $post ) {
			echo 'Поста с этим ID не существует!';
		} else {
			echo get_the_title( $post );
		}
	
		die;

}


add_action( 'wp_ajax_changeviews', function() {

	$nonce = check_ajax_referer( 'views123', 'token', false );

	if( ! $nonce ) {
		echo 'Нарушение безопасности';
		die;
	}

	if( intval($_POST[ 'views' ]) < 0 ) {
		echo 'Не может быть отрицательным';
	}else {
		update_post_meta( $_POST[ 'post_id' ], 'post_views', intval($_POST[ 'views' ]) );
		echo 'ok';
	}

	die;

} );



// добавление колонки в админку WordPress
add_filter( 'manage_edit-post_columns', 'true_add_post_columns', 10, 1 );
function true_add_post_columns( $my_columns ){
	$my_columns['views'] = 'Просмотры';
	return $my_columns;
}



add_action( 'manage_posts_custom_column', 'true_fill_post_columns', 10, 1 );
function true_fill_post_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'views':
			$views = ( $views = get_post_meta( $post->ID, 'post_views', true ) ) ? $views : 0;
			echo '<input type="number" class="change-views" data-token="' . wp_create_nonce( 'views123' ) . '" data-id="' . $post->ID . '" value="' . intval($views) . '" /><p></p>';
			break;
	}
}



add_action( 'wp_ajax_loadmore', 'true_loadmore' );
add_action( 'wp_ajax_nopriv_loadmore', 'true_loadmore' );

function true_loadmore() {

	$paged = ! empty( $_POST[ 'paged' ] ) ? $_POST[ 'paged' ] : 1;
	$paged++;

	$args = array(
		'paged' => $paged,
		'post_status' => 'publish'
	);

	$taxonomy = ! empty( $_POST[ 'taxonomy' ] ) ? $_POST[ 'taxonomy' ] : '';
	$term_id = ! empty( $_POST[ 'term_id' ] ) ? $_POST[ 'term_id' ] : 0;

	if( $taxonomy && $term_id ) {

		$args[ 'tax_query' ] = array(
			array(
				'taxonomy' => $taxonomy,
				'terms' => $term_id
			)
		);
	}

	query_posts( $args );

	ob_start();

	while( have_posts() ) : the_post();

		get_template_part( 'template-parts/content/content', get_theme_mod( 'display_excerpt_or_full_post', 'excerpt' ) );

	endwhile;

	$posts = ob_get_contents();
	ob_get_clean();

	ob_start();
	twenty_twenty_one_the_posts_navigation();
	$pagination = ob_get_contents();
	ob_get_clean();

	echo json_encode( array(

		'posts' => $posts,
		'pagination' => str_replace( admin_url( 'admin-ajax.php' ), $_POST[ 'pagenumlink' ], $pagination )

	) );
	die;

}

