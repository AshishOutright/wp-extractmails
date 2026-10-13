<?php
/**
 * Plugin Name: EM Docs Endpoint
 * Description: Install on EVERY site that should receive content. Adds the "Docs" post type (/docs/category/post-title/) and a key-protected JSON endpoint (/wp-json/extractmails/v1/docs) for create/fetch/update/publish/draft/delete on blog posts and docs. It has no controls of its own: the main site's "EM Network Hub" plugin uses this endpoint.
 * Version: 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const EM_DOCS_CPT = 'doc';
const EM_DOCS_TAX = 'doc_category';

/* ==================================================================
 * 1. REGISTER POST TYPE + CATEGORY + REWRITE RULES
 * ================================================================== */
function em_docs_register() {

	register_taxonomy( EM_DOCS_TAX, EM_DOCS_CPT, [
		'labels'            => [ 'name' => 'Doc Categories', 'singular_name' => 'Doc Category' ],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => false,
		'query_var'         => EM_DOCS_TAX,
	] );

	register_post_type( EM_DOCS_CPT, [
		'labels'       => [ 'name' => 'Docs', 'singular_name' => 'Doc', 'add_new_item' => 'Add New Doc' ],
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-media-document',
		'show_in_rest' => true,
		'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ],
		'taxonomies'   => [ EM_DOCS_TAX ],
		'rewrite'      => false,
		'query_var'    => false,
	] );

	add_rewrite_rule( '^docs/?$', 'index.php?post_type=' . EM_DOCS_CPT, 'top' );
	add_rewrite_rule( '^docs/page/([0-9]+)/?$', 'index.php?post_type=' . EM_DOCS_CPT . '&paged=$matches[1]', 'top' );
	add_rewrite_rule( '^docs/(.+?)/page/([0-9]+)/?$', 'index.php?em_docs_path=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^docs/(.+?)/?$', 'index.php?em_docs_path=$matches[1]', 'top' );
}
add_action( 'init', 'em_docs_register', 0 );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'em_docs_path';
	return $vars;
} );

register_activation_hook( __FILE__, function () {
	em_docs_register();
	flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );


/* ==================================================================
 * 2. PATH HELPERS (docs URLs)
 * ================================================================== */
function em_docs_term_path( $term ): string {
	$path = $term->slug;
	foreach ( get_ancestors( $term->term_id, EM_DOCS_TAX, 'taxonomy' ) as $parent_id ) {
		$parent = get_term( $parent_id, EM_DOCS_TAX );
		if ( $parent && ! is_wp_error( $parent ) ) $path = $parent->slug . '/' . $path;
	}
	return $path;
}

function em_docs_primary_term( int $post_id ) {
	$terms = get_the_terms( $post_id, EM_DOCS_TAX );
	if ( ! $terms || is_wp_error( $terms ) ) return null;
	usort( $terms, function ( $a, $b ) {
		return count( get_ancestors( $b->term_id, EM_DOCS_TAX, 'taxonomy' ) ) <=> count( get_ancestors( $a->term_id, EM_DOCS_TAX, 'taxonomy' ) );
	} );
	return $terms[0];
}

function em_docs_post_path( int $post_id, string $slug = '' ): string {
	$slug = $slug ?: get_post_field( 'post_name', $post_id );
	$term = em_docs_primary_term( $post_id );
	return $term ? em_docs_term_path( $term ) . '/' . $slug : $slug;
}

function em_docs_term_from_path( array $parts ) {
	$parent = 0;
	$term   = null;
	foreach ( $parts as $slug ) {
		$found = get_terms( [
			'taxonomy'   => EM_DOCS_TAX,
			'slug'       => $slug,
			'parent'     => $parent,
			'hide_empty' => false,
			'number'     => 1,
		] );
		if ( empty( $found ) || is_wp_error( $found ) ) return null;
		$term   = $found[0];
		$parent = $term->term_id;
	}
	return $term;
}


/* ==================================================================
 * 3. PERMALINKS (docs)
 * ================================================================== */
add_filter( 'post_type_link', function ( $link, $post, $leavename = false ) {
	if ( EM_DOCS_CPT !== $post->post_type ) return $link;
	if ( ! in_array( $post->post_status, [ 'publish', 'private' ], true ) ) return $link;

	$term = em_docs_primary_term( $post->ID );
	$name = $leavename ? '%postname%' : $post->post_name;
	$path = $term ? em_docs_term_path( $term ) . '/' . $name : $name;

	return user_trailingslashit( home_url( 'docs/' . $path ) );
}, 10, 3 );

add_filter( 'term_link', function ( $link, $term, $taxonomy ) {
	if ( EM_DOCS_TAX !== $taxonomy ) return $link;
	return user_trailingslashit( home_url( 'docs/' . em_docs_term_path( $term ) ) );
}, 10, 3 );

add_filter( 'post_type_archive_link', function ( $link, $post_type ) {
	return EM_DOCS_CPT === $post_type ? user_trailingslashit( home_url( 'docs' ) ) : $link;
}, 10, 2 );


/* ==================================================================
 * 4. RESOLVE /docs/... URLS
 * ================================================================== */
add_filter( 'request', function ( $qv ) {
	if ( ! isset( $qv['em_docs_path'] ) ) return $qv;

	$path  = trim( $qv['em_docs_path'], '/' );
	$parts = array_values( array_filter( array_map( 'sanitize_title', explode( '/', $path ) ) ) );
	$paged = $qv['paged'] ?? null;

	if ( ! $parts ) return [ 'post_type' => EM_DOCS_CPT ];

	$ids = get_posts( [
		'post_type'   => EM_DOCS_CPT,
		'name'        => end( $parts ),
		'post_status' => [ 'publish', 'private' ],
		'fields'      => 'ids',
		'numberposts' => 1,
	] );
	if ( $ids && ! $paged ) {
		$canonical = em_docs_post_path( $ids[0] );
		if ( implode( '/', $parts ) !== $canonical ) {
			wp_safe_redirect( get_permalink( $ids[0] ), 301 );
			exit;
		}
		return [ 'p' => $ids[0], 'post_type' => EM_DOCS_CPT ];
	}

	if ( $term = em_docs_term_from_path( $parts ) ) {
		$out = [ 'post_type' => EM_DOCS_CPT, EM_DOCS_TAX => $term->slug ];
		if ( $paged ) $out['paged'] = $paged;
		return $out;
	}

	return [ 'error' => 404 ];
} );


/* ==================================================================
 * 5. JSON ACTION ENDPOINT   POST /wp-json/extractmails/v1/docs
 *
 *    "table": "docs"  -> Docs post type   (doc_category)
 *    "table": "blog"  -> Blog posts       (category + tags)
 *       aliases: doc, blogs, post, posts
 *
 * AUTH (wp-config.php):
 *   define( 'EM_DOCS_API_KEY',     'long-random-string' );
 *   define( 'EM_DOCS_API_USER_ID', 1 );
 * Send as header  X-API-Key: ...   or JSON  "api_key": "..."
 * ================================================================== */
add_action( 'rest_api_init', function () {
	register_rest_route( 'extractmails/v1', '/docs', [
		'methods'             => 'POST',
		'callback'            => 'em_docs_handle',
		'permission_callback' => 'em_docs_authorize',
	] );
} );

function em_docs_authorize( WP_REST_Request $req ) {
	if ( current_user_can( 'edit_posts' ) ) return true;

	if ( ! defined( 'EM_DOCS_API_KEY' ) || strlen( EM_DOCS_API_KEY ) < 16 ) {
		return new WP_Error( 'no_key', 'EM_DOCS_API_KEY is not set in wp-config.php (min 16 chars).', [ 'status' => 500 ] );
	}

	$params = $req->get_json_params() ?: [];
	$sent   = $req->get_header( 'x-api-key' ) ?: ( $params['api_key'] ?? '' );

	if ( ! $sent || ! hash_equals( EM_DOCS_API_KEY, (string) $sent ) ) {
		return new WP_Error( 'bad_key', 'Invalid API key.', [ 'status' => 401 ] );
	}

	$user_id = defined( 'EM_DOCS_API_USER_ID' ) ? (int) EM_DOCS_API_USER_ID : 1;
	if ( ! get_userdata( $user_id ) ) {
		return new WP_Error( 'bad_user', 'EM_DOCS_API_USER_ID is not a valid user.', [ 'status' => 500 ] );
	}
	wp_set_current_user( $user_id );
	return true;
}

/* ---------- table config ---------- */
function em_docs_table_cfg( string $table ): ?array {
	$tables = [
		'docs' => [ 'key' => 'docs', 'post_type' => EM_DOCS_CPT, 'tax' => EM_DOCS_TAX, 'tag_tax' => null ],
		'blog' => [ 'key' => 'blog', 'post_type' => 'post',      'tax' => 'category',  'tag_tax' => 'post_tag' ],
	];
	$aliases = [ 'docs' => 'docs', 'doc' => 'docs', 'blog' => 'blog', 'blogs' => 'blog', 'post' => 'blog', 'posts' => 'blog' ];
	$key = $aliases[ strtolower( trim( $table ) ) ] ?? null;
	return $key ? $tables[ $key ] : null;
}

function em_docs_handle( WP_REST_Request $req ) {
	$p      = $req->get_json_params() ?: [];
	$action = $p['action'] ?? '';
	$cfg    = em_docs_table_cfg( (string) ( $p['table'] ?? 'docs' ) );

	if ( ! $cfg ) {
		return new WP_Error( 'bad_table', 'Unknown table. Use "docs" or "blog".', [ 'status' => 400 ] );
	}

	switch ( $action ) {
		case 'fetch':   return em_docs_fetch( $p, $cfg );
		case 'create':  return em_docs_create( $p, $cfg );
		case 'update':  return em_docs_update( $p, $cfg );
		case 'publish': return em_docs_set_status( $p, $cfg, 'publish' );
		case 'draft':   return em_docs_set_status( $p, $cfg, 'draft' );
		case 'delete':  return em_docs_delete( $p, $cfg );
	}
	return new WP_Error( 'bad_action', 'Unknown action. Use fetch|create|update|publish|draft|delete.', [ 'status' => 400 ] );
}

/* ---------- helpers ---------- */
function em_docs_terms_out( int $post_id, string $tax ): array {
	$terms = wp_get_post_terms( $post_id, $tax );
	if ( is_wp_error( $terms ) ) return [];
	return array_map( function ( $t ) {
		return [ 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug, 'parent' => $t->parent ];
	}, $terms );
}

function em_docs_row( WP_Post $post, array $cfg ): array {
	$row = [
		'id'            => $post->ID,
		'table'         => $cfg['key'],
		'title'         => $post->post_title,
		'slug'          => $post->post_name,
		'content'       => $post->post_content,
		'excerpt'       => $post->post_excerpt,
		'status'        => $post->post_status,
		'author_id'     => (int) $post->post_author,
		'date_entered'  => $post->post_date,
		'date_modified' => $post->post_modified,
		'url'           => get_permalink( $post ),
		'categories'    => em_docs_terms_out( $post->ID, $cfg['tax'] ),
	];
	if ( $cfg['tag_tax'] ) {
		$row['tags'] = em_docs_terms_out( $post->ID, $cfg['tag_tax'] );
	}
	return $row;
}

function em_docs_get_doc( $id, array $cfg ) {
	$post = get_post( (int) $id );
	if ( ! $post || $cfg['post_type'] !== $post->post_type ) {
		return new WP_Error( 'not_found', 'Item not found in table "' . $cfg['key'] . '".', [ 'status' => 404 ] );
	}
	return $post;
}

function em_docs_date_bounds( array $p ): ?array {
	$range = $p['date_range'] ?? '';
	if ( ! $range ) return null;

	$tz  = wp_timezone();
	$now = new DateTimeImmutable( 'now', $tz );
	$sow = (int) get_option( 'start_of_week', 1 );
	$dow = (int) $now->format( 'w' );
	$week_start = $now->modify( '-' . ( ( $dow - $sow + 7 ) % 7 ) . ' days' )->setTime( 0, 0 );

	switch ( $range ) {
		case 'today':      $from = $now->setTime( 0, 0 ); $to = $now->setTime( 23, 59, 59 ); break;
		case 'yesterday':  $from = $now->modify( '-1 day' )->setTime( 0, 0 ); $to = $now->modify( '-1 day' )->setTime( 23, 59, 59 ); break;
		case 'this_week':  $from = $week_start; $to = $week_start->modify( '+6 days' )->setTime( 23, 59, 59 ); break;
		case 'last_week':  $from = $week_start->modify( '-7 days' ); $to = $week_start->modify( '-1 day' )->setTime( 23, 59, 59 ); break;
		case 'this_month': $from = $now->modify( 'first day of this month' )->setTime( 0, 0 ); $to = $now->modify( 'last day of this month' )->setTime( 23, 59, 59 ); break;
		case 'last_month': $from = $now->modify( 'first day of last month' )->setTime( 0, 0 ); $to = $now->modify( 'last day of last month' )->setTime( 23, 59, 59 ); break;
		case 'custom':
			if ( empty( $p['date_from'] ) || empty( $p['date_to'] ) ) return null;
			$from = new DateTimeImmutable( $p['date_from'] . ' 00:00:00', $tz );
			$to   = new DateTimeImmutable( $p['date_to'] . ' 23:59:59', $tz );
			break;
		default: return null;
	}
	return [ $from->format( 'Y-m-d H:i:s' ), $to->format( 'Y-m-d H:i:s' ) ];
}

/* ---------- FETCH ---------- */
function em_docs_fetch( array $p, array $cfg ) {
	$per_page = min( 100, max( 1, (int) ( $p['per_page'] ?? 20 ) ) );
	$page     = max( 1, (int) ( $p['page'] ?? 1 ) );

	$args = [
		'post_type'           => $cfg['post_type'],
		'posts_per_page'      => $per_page,
		'paged'               => $page,
		'post_status'         => 'any',
		'ignore_sticky_posts' => true,
		'order'               => ( strtoupper( $p['order_dir'] ?? 'DESC' ) === 'ASC' ) ? 'ASC' : 'DESC',
	];

	$order_map = [ 'date_entered' => 'date', 'date_modified' => 'modified', 'title' => 'title', 'id' => 'ID', 'slug' => 'name' ];
	$args['orderby'] = $order_map[ $p['order_by'] ?? 'date_entered' ] ?? 'date';

	$f = $p['filters'] ?? [];
	if ( ! empty( $f['status'] ) ) {
		$map = [ 'New' => 'draft', 'Draft' => 'draft', 'Published' => 'publish' ];
		$args['post_status'] = $map[ $f['status'] ] ?? sanitize_key( $f['status'] );
	}
	if ( ! empty( $f['author_id'] ) || ! empty( $f['assigned_user_id'] ) ) {
		$args['author'] = (int) ( $f['author_id'] ?? $f['assigned_user_id'] );
	}

	$tax_query = [];
	if ( ! empty( $f['category'] ) ) {
		$tax_query[] = [ 'taxonomy' => $cfg['tax'], 'field' => 'slug', 'terms' => (array) $f['category'] ];
	}
	if ( ! empty( $f['tag'] ) && $cfg['tag_tax'] ) {
		$tax_query[] = [ 'taxonomy' => $cfg['tag_tax'], 'field' => 'slug', 'terms' => (array) $f['tag'] ];
	}
	if ( $tax_query ) {
		$args['tax_query'] = count( $tax_query ) > 1 ? array_merge( [ 'relation' => 'AND' ], $tax_query ) : $tax_query;
	}
	if ( ! empty( $f['id'] ) ) $args['p'] = (int) $f['id'];

	if ( $bounds = em_docs_date_bounds( $p ) ) {
		$col = ( ( $p['date_field'] ?? 'date_entered' ) === 'date_modified' ) ? 'post_modified' : 'post_date';
		$args['date_query'] = [ [ 'column' => $col, 'after' => $bounds[0], 'before' => $bounds[1], 'inclusive' => true ] ];
	}

	$search_filter = null;
	if ( ! empty( $p['search'] ) ) {
		$term   = (string) $p['search'];
		$allow  = [ 'title' => 'post_title', 'content' => 'post_content', 'excerpt' => 'post_excerpt' ];
		$wanted = array_intersect_key( $allow, array_flip( $p['search_fields'] ?? [ 'title', 'content', 'excerpt' ] ) ) ?: $allow;

		$search_filter = function ( $search ) use ( $term, $wanted ) {
			global $wpdb;
			$like = '%' . $wpdb->esc_like( $term ) . '%';
			$or   = [];
			foreach ( $wanted as $col ) $or[] = $wpdb->prepare( "{$wpdb->posts}.$col LIKE %s", $like );
			return ' AND (' . implode( ' OR ', $or ) . ') ';
		};
		add_filter( 'posts_search', $search_filter );
	}

	$q = new WP_Query( $args );
	if ( $search_filter ) remove_filter( 'posts_search', $search_filter );

	$fields = $p['fields'] ?? [];
	$rows   = array_map( function ( $post ) use ( $fields, $cfg ) {
		$row = em_docs_row( $post, $cfg );
		return $fields ? array_intersect_key( $row, array_flip( $fields ) ) : $row;
	}, $q->posts );

	return [
		'success'     => true,
		'table'       => $cfg['key'],
		'total'       => (int) $q->found_posts,
		'total_pages' => (int) $q->max_num_pages,
		'page'        => $page,
		'per_page'    => $per_page,
		'data'        => $rows,
	];
}

/* ---------- CREATE ---------- */
function em_docs_create( array $p, array $cfg ) {
	if ( empty( $p['title'] ) ) return new WP_Error( 'no_title', 'title is required.', [ 'status' => 400 ] );

	$status = in_array( $p['status'] ?? 'draft', [ 'draft', 'publish', 'pending', 'private' ], true ) ? $p['status'] : 'draft';
	if ( 'publish' === $status && ! current_user_can( 'publish_posts' ) ) $status = 'pending';

	$id = wp_insert_post( [
		'post_type'    => $cfg['post_type'],
		'post_title'   => sanitize_text_field( $p['title'] ),
		'post_content' => wp_kses_post( $p['content'] ?? '' ),
		'post_excerpt' => sanitize_textarea_field( $p['excerpt'] ?? '' ),
		'post_name'    => sanitize_title( $p['slug'] ?? '' ),
		'post_status'  => $status,
		'post_author'  => get_current_user_id(),
	], true );
	if ( is_wp_error( $id ) ) return $id;

	if ( ! empty( $p['categories'] ) ) em_docs_set_categories( $id, (array) $p['categories'], $cfg['tax'] );
	if ( ! empty( $p['tags'] ) && $cfg['tag_tax'] ) em_docs_set_tags( $id, (array) $p['tags'], $cfg['tag_tax'] );
	if ( ! empty( $p['featured_image_id'] ) ) set_post_thumbnail( $id, (int) $p['featured_image_id'] );
	elseif ( ! empty( $p['featured_image_url'] ) ) em_docs_sideload_thumb( $id, (string) $p['featured_image_url'] );

	return [ 'success' => true, 'data' => em_docs_row( get_post( $id ), $cfg ) ];
}

/* ---------- UPDATE ---------- */
function em_docs_update( array $p, array $cfg ) {
	$post = em_docs_get_doc( $p['id'] ?? 0, $cfg );
	if ( is_wp_error( $post ) ) return $post;
	if ( ! current_user_can( 'edit_post', $post->ID ) ) return new WP_Error( 'forbidden', 'Not allowed.', [ 'status' => 403 ] );

	$upd = [ 'ID' => $post->ID ];
	if ( isset( $p['title'] ) )   $upd['post_title']   = sanitize_text_field( $p['title'] );
	if ( isset( $p['content'] ) ) $upd['post_content'] = wp_kses_post( $p['content'] );
	if ( isset( $p['excerpt'] ) ) $upd['post_excerpt'] = sanitize_textarea_field( $p['excerpt'] );
	if ( isset( $p['slug'] ) )    $upd['post_name']    = sanitize_title( $p['slug'] );
	if ( isset( $p['status'] ) && in_array( $p['status'], [ 'draft', 'publish', 'pending', 'private' ], true ) ) {
		$upd['post_status'] = ( 'publish' === $p['status'] && ! current_user_can( 'publish_posts' ) ) ? 'pending' : $p['status'];
	}

	$res = wp_update_post( $upd, true );
	if ( is_wp_error( $res ) ) return $res;

	// docs: "categories": [] -> URL becomes /docs/title/
	// blog: "categories": [] -> falls back to the default category (Uncategorized)
	if ( isset( $p['categories'] ) ) em_docs_set_categories( $post->ID, (array) $p['categories'], $cfg['tax'] );
	if ( isset( $p['tags'] ) && $cfg['tag_tax'] ) em_docs_set_tags( $post->ID, (array) $p['tags'], $cfg['tag_tax'] );
	if ( isset( $p['featured_image_id'] ) ) {
		$p['featured_image_id'] ? set_post_thumbnail( $post->ID, (int) $p['featured_image_id'] ) : delete_post_thumbnail( $post->ID );
	} elseif ( ! empty( $p['featured_image_url'] ) ) {
		em_docs_sideload_thumb( $post->ID, (string) $p['featured_image_url'] );
	}

	return [ 'success' => true, 'data' => em_docs_row( get_post( $post->ID ), $cfg ) ];
}

/* ---------- PUBLISH / DRAFT ---------- */
function em_docs_set_status( array $p, array $cfg, string $status ) {
	$post = em_docs_get_doc( $p['id'] ?? 0, $cfg );
	if ( is_wp_error( $post ) ) return $post;
	if ( ! current_user_can( 'edit_post', $post->ID ) ) return new WP_Error( 'forbidden', 'Not allowed.', [ 'status' => 403 ] );
	if ( 'publish' === $status && ! current_user_can( 'publish_posts' ) ) return new WP_Error( 'forbidden', 'You cannot publish.', [ 'status' => 403 ] );

	$res = wp_update_post( [ 'ID' => $post->ID, 'post_status' => $status ], true );
	if ( is_wp_error( $res ) ) return $res;
	return [ 'success' => true, 'data' => em_docs_row( get_post( $post->ID ), $cfg ) ];
}

/* ---------- DELETE (trash by default, "force": true = permanent) ---------- */
function em_docs_delete( array $p, array $cfg ) {
	$post = em_docs_get_doc( $p['id'] ?? 0, $cfg );
	if ( is_wp_error( $post ) ) return $post;
	if ( ! current_user_can( 'delete_post', $post->ID ) ) return new WP_Error( 'forbidden', 'Not allowed.', [ 'status' => 403 ] );

	$force = ! empty( $p['force'] );
	$done  = $force ? wp_delete_post( $post->ID, true ) : wp_trash_post( $post->ID );
	if ( ! $done ) return new WP_Error( 'delete_failed', 'Could not delete.', [ 'status' => 500 ] );
	return [ 'success' => true, 'table' => $cfg['key'], 'id' => $post->ID, 'deleted' => $force ? 'permanently' : 'trashed' ];
}

/* ---------- categories: IDs, slugs or names; creates missing top-level ones ---------- */
function em_docs_set_categories( int $post_id, array $cats, string $tax ) {
	$ids = [];
	foreach ( $cats as $c ) {
		if ( is_numeric( $c ) ) { $ids[] = (int) $c; continue; }
		$term = get_term_by( 'slug', sanitize_title( $c ), $tax ) ?: get_term_by( 'name', $c, $tax );
		if ( ! $term && current_user_can( 'manage_categories' ) ) {
			$new = wp_insert_term( sanitize_text_field( $c ), $tax );
			if ( ! is_wp_error( $new ) ) $ids[] = (int) $new['term_id'];
		} elseif ( $term ) {
			$ids[] = $term->term_id;
		}
	}

	if ( 'category' === $tax ) {
		wp_set_post_categories( $post_id, $ids ); // empty -> default category
	} else {
		wp_set_object_terms( $post_id, $ids, $tax );
	}
}

/* ---------- tags (blog only): IDs or names; missing names are created ---------- */
function em_docs_set_tags( int $post_id, array $tags, string $tax ) {
	$clean = array_map( function ( $t ) {
		return is_numeric( $t ) ? (int) $t : sanitize_text_field( $t );
	}, $tags );
	wp_set_object_terms( $post_id, $clean, $tax ); // [] removes all tags
}

/* ---------- featured image from a URL (used when another site pushes a post here) ---------- */
function em_docs_sideload_thumb( int $post_id, string $url ) {
	$url = esc_url_raw( $url );
	if ( ! $url ) return;
	// Same image already downloaded for this post -> skip
	if ( get_post_meta( $post_id, '_em_source_thumb_url', true ) === $url && has_post_thumbnail( $post_id ) ) return;

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$att = media_sideload_image( $url, $post_id, null, 'id' );
	if ( is_wp_error( $att ) ) return;
	set_post_thumbnail( $post_id, (int) $att );
	update_post_meta( $post_id, '_em_source_thumb_url', $url );
}