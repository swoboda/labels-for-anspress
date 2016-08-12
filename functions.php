<?php

/**
 * Output labels html
 * @param  array $args
 * @return string
 * @since 1.0
 */
function ap_question_labels_html($args = array()) {

	$defaults  = array(
		'question_id'   => get_the_ID(),
		'list'          => false,
		'tag'           => 'span',
		'class'         => 'question-labels label-' . get_the_ID(),
		'label'         => __( 'Labeled', 'labels-for-anspress' ),
		'echo'          => false,
		'show'          => 0,
	);

	if ( ! is_array( $args ) ) {
		$defaults['question_id '] = $args;
		$args = $defaults;
	} else {
		$args = wp_parse_args( $args, $defaults );
	}

	$labels = get_the_terms( $args['question_id'], 'question_label' );

	if ( $labels && count( $labels ) > 0 ) {
		$o = '';
		if ( $args['list'] ) {
			$o = '<ul class="'.$args['class'].'">';
			foreach ( $labels as $t ) {
				$o .= '<li>'. $t->name .' &times; <i class="tax-count">'.$t->count.'</i></li>';
			}
			$o .= '</ul>';
		} else {
			$o = $args['label'];
			$o .= '<'.$args['tag'].' class="'.$args['class'].'">';
			$i = 1;
			foreach ( $labels as $t ) {
				$o .= ''. $t->name .' ';
				/*
                if($args['show'] > 0 && $i == $args['show']){
                    $o_n = '';
                    foreach($labels as $label_n)
                        $o_n .= '<a href="'.esc_url( get_term_link($label_n)).'" title="'.$label_n->description.'">'. $label_n->name .'</a> ';

                    $o .= '<a class="ap-tip" data-tipclass="labels-list" title="'.esc_html($o_n).'" href="#">'. sprintf(__('%d More', 'labels-for-anspress'), count($labels)) .'</a>';
                    break;
                }*/
				$i++;
			}
			$o .= '</'.$args['tag'].'>';
		}

		if ( $args['echo'] ) {
			echo $o; }

		return $o;
	}
}


function ap_label_details() {

	$var = get_query_var( 'question_label' );

	$label = get_term_by( 'slug', $var, 'question_label' );
	echo '<div class="clearfix">';
	echo '<h3><a href="'.get_label_link( $label ).'">'. $label->name .'</a></h3>';
	echo '<div class="ap-taxo-meta">';
	echo '<span class="count">'. $label->count .' '.__( 'Questions', 'labels-for-anspress' ).'</span>';
	echo '<a class="aicon-rss feed-link" href="' . get_term_feed_link( $label->term_id, 'question_label' ) . '" title="Subscribe to '. $label->name .'" rel="nofollow"></a>';
	echo '</div>';
	echo '</div>';

	echo '<p class="desc clearfix">'. $label->description .'</p>';
}

function ap_question_have_labels($question_id = false) {
	if ( ! $question_id ) {
		$question_id = get_the_ID(); }

	$labels = wp_get_post_terms( $question_id, 'question_label' );

	if ( ! empty( $labels ) ) {
		return true; }

	return false;
}


if ( ! function_exists( 'is_question_label' )) {
	function is_question_label() {

		if ( ap_get_label_slug() == get_query_var( 'ap_page' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'is_question_labels' ) ) {
	function is_question_labels() {

		if ( ap_get_labels_slug() == get_query_var( 'ap_page' ) ) {
			return true;
		}

		return false;
	}
}

function ap_label_sorting() {
	$args = array(
		'hierarchical'      => true,
		'hide_if_empty'     => true,
		'number'            => 10,
	);

	$terms = get_terms( 'question_label', $args );

	$selected = isset( $_GET['ap_label_sort'] ) ? sanitize_text_field( $_GET['ap_label_sort'] ) : '';

	if ( $terms ) {
		echo '<div class="ap-dropdown">';
			echo '<a id="ap-sort-anchor" class="ap-dropdown-toggle'.($selected != '' ? ' active' : '').'" href="#">'.__( 'Labels', 'labels-for-anspress' ).'</a>';
			echo '<div class="ap-dropdown-menu">';
		foreach ( $terms as $t ) {
			echo '<li '.($selected == $t->term_id ? 'class="active" ' : '').'><a href="#" data-value="'.$t->term_id.'">'. $t->name .'</a></li>';
		}
			echo '<input name="ap_label_sort" type="hidden" value="'.$selected.'" />';
			echo '</div>';
		echo '</div>';
	}
}

function ap_labels_tab() {
	$active = isset( $_GET['ap_sort'] ) ? $_GET['ap_sort'] : 'popular';

	$link = ap_get_link_to( 'labels' ).'?ap_sort=';

	?>
    <ul class="ap-questions-tab ap-ul-inline clearfix" role="tablist">
        <li class="<?php echo $active == 'popular' ? ' active' : ''; ?>"><a href="<?php echo $link.'popular'; ?>"><?php _e( 'Popular', 'labels-for-anspress' ); ?></a></li>
        <li class="<?php echo $active == 'new' ? ' active' : ''; ?>"><a href="<?php echo $link.'new'; ?>"><?php _e( 'New', 'labels-for-anspress' ); ?></a></li>
        <li class="<?php echo $active == 'name' ? ' active' : ''; ?>"><a href="<?php echo $link.'name'; ?>"><?php _e( 'Name', 'labels-for-anspress' ); ?></a></li>
        <?php
			/**
			 * ACTION: ap_labels_tab
			 * Used to hook into labels page tab
			 */
			do_action( 'ap_labels_tab', $active );
		?>
    </ul>
    <?php
}

/**
 * Slug for label page
 * @return string
 */
function ap_get_label_slug() {
	$slug = ap_opt('label_page_slug');
	$slug = sanitize_title( $slug );

	if(empty($slug)){
		$slug = 'label';
	}
	/**
	 * FILTER: ap_label_slug
	 */
	return apply_filters( 'ap_label_slug', $slug );
}

/**
 * Slug for label page
 * @return string
 */
function ap_get_labels_slug() {
	$slug = ap_opt('labels_page_slug');
	$slug = sanitize_title( $slug );

	if(empty($slug)){
		$slug = 'labels';
	}
	/**
	 * FILTER: ap_label_slug
	 */
	return apply_filters( 'ap_labels_slug', $slug );
}

/**
 * Return labels for sorting dropdown.
 * @return array|boolean
 */
function ap_get_label_filter( $search = false ) {
	$args = array(
		'hierarchical'      => false,
		'hide_if_empty'     => true,
		'number'            => 10,
	);

	if ( false !== $search ) {
		$args['search'] = $search;
	}

	$terms = get_terms( 'question_label', $args );
	$selected = array();
	if ( isset( $_GET['ap_filter'], $_GET['ap_filter']['label'] ) ) {
		$selected = (array) wp_unslash( $_GET['ap_filter']['label'] );
	}

	if ( ! $terms ) {
		return false;
	}

	$items = array();
	foreach ( (array) $terms as $t ) {
		$item = [ 'key' => $t->term_id, 'title' => $t->name ];
		// Check if active.
		if ( in_array( $t->term_id, $selected ) ) {
			$item['active'] = true;
		}
		$items[] = $item;
	}

	return $items;
}
