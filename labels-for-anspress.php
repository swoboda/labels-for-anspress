<?php
/**
 * Labels extension for AnsPress
 *
 * AnsPress - Question and answer plugin for WordPress
 *
 * @package   Labels for AnsPress
 * @author    Rahul Aryan <support@anspress.io>
 * @license   GPL-2.0+
 * @link      http://anspress.io/labels-for-anspress
 * @copyright 2014 anspress.io & Rahul Aryan
 *
 * @wordpress-plugin
 * Plugin Name:       Labels for AnsPress
 * Plugin URI:        http://anspress.io/labels-for-anspress
 * Description:       Extension for AnsPress. Add labels in AnsPress.
 * Donate link: https://www.paypal.com/cgi-bin/webscr?business=rah12@live.com&cmd=_xclick&item_name=Donation%20to%20AnsPress%20development
 * Version:           999.0
 * Author:            Rahul Aryan
 * Author URI:        http://anspress.io
 * Text Domain:       labels-for-anspress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Labels_For_AnsPress
{

	/**
	 * Class instance
	 * @var object
	 * @since 1.0
	 */
	private static $instance;


	/**
	 * Get active object instance
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @static
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new Categories_For_AnsPress(); }

		return self::$instance;
	}
	/**
	 * Initialize the class
	 * @since 2.0
	 */
	public function __construct() {

		if ( ! class_exists( 'AnsPress' ) ) {
			return; // AnsPress not installed.
		}

		$this->includes();

		add_action( 'ap_option_groups', array( $this, 'option_fields' ), 20 );
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'widgets_init', array( $this, 'widget_positions' ) );

		add_action( 'init', array( $this, 'register_question_label' ), 1 );
		add_action( 'ap_admin_menu', array( $this, 'admin_labels_menu' ) );
		add_action( 'ap_display_question_metas', array( $this, 'ap_display_question_metas' ), 10, 2 );
		add_action( 'ap_question_info', array( $this, 'ap_question_info' ) );
		add_action( 'ap_enqueue', array( $this, 'ap_enqueue' ) );
		add_action( 'ap_enqueue', array( $this, 'ap_localize_scripts' ) );
		add_filter( 'term_link', array( $this, 'term_link_filter' ), 10, 3 );
		add_action( 'ap_ask_form_fields', array( $this, 'ask_from_label_field' ), 10, 2 );
		add_action( 'ap_ask_fields_validation', array( $this, 'ap_ask_fields_validation' ) );
		add_action( 'ap_processed_new_question', array( $this, 'after_new_question' ), 0, 2 );
		add_action( 'ap_processed_update_question', array( $this, 'after_new_question' ), 0, 2 );
		add_filter( 'ap_page_title', array( $this, 'page_title' ) );
		add_filter( 'ap_breadcrumbs', array( $this, 'ap_breadcrumbs' ) );
		add_action( 'ap_list_head', array( $this, 'ap_list_head' ) );
		add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );
		add_filter( 'get_terms', array( $this, 'get_terms' ), 10, 3 );
		add_action( 'ap_user_subscription_tab', array( $this, 'subscription_tab' ) );
		add_action( 'ap_user_subscription_page', array( $this, 'subscription_page' ) );
		add_action( 'wp_ajax_ap_labels_suggestion', array( $this, 'ap_labels_suggestion' ) );
	    add_action( 'wp_ajax_nopriv_ap_labels_suggestion', array( $this, 'ap_labels_suggestion' ) );
	    add_action( 'ap_rewrite_rules', array( $this, 'rewrite_rules' ), 10, 3 );
		add_filter( 'ap_default_pages', array( $this, 'labels_default_page' ) );
		add_filter( 'ap_default_page_slugs', array( $this, 'default_page_slugs' ) );
		add_filter( 'ap_subscribe_btn_type', array( $this, 'subscribe_type' ) );
		add_filter( 'ap_subscribe_btn_action_type', array( $this, 'subscribe_btn_action_type' ) );
		add_filter( 'ap_current_page_is', array( $this, 'ap_current_page_is' ) );
		add_filter( 'ap_list_filters', array( $this, 'ap_list_filters' ) );
		add_filter( 'ap_main_questions_args', array( __CLASS__, 'ap_main_questions_args' ) );
		add_action( 'ap_list_filter_search_label', array( __CLASS__, 'filter_search_label' ) );
		add_filter( 'ap_question_subscribers_action_id', array( __CLASS__, 'subscribers_action_id' ) );
	}

	/**
	 * Include required files
	 */
	public function includes() {
		if ( ! defined( 'LABELS_FOR_ANSPRESS_DIR' ) ) {
			define( 'LABELS_FOR_ANSPRESS_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'LABELS_FOR_ANSPRESS_URL' ) ) {
			define( 'LABELS_FOR_ANSPRESS_URL', plugin_dir_url( __FILE__ ) );
		}
		require_once( LABELS_FOR_ANSPRESS_DIR . 'functions.php' );
	}

	/**
	 * Load plugin text domain
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @return void
	 */
	public static function textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

		// Load the translations
		load_plugin_textdomain( 'labels-for-anspress', false, $lang_dir );

	}

	public function widget_positions() {

		register_sidebar( array(
			'name'          => __( 'AP Labels', 'labels-for-anspress' ),
			'id'            => 'ap-labels',
			'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
			'after_widget'  => '</div>',
			'description'   => __( 'Widgets in this area will be shown in anspress labels page.', 'labels-for-anspress' ),
			'before_title'  => '<h3 class="ap-widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	/**
	 * Register label taxonomy for question cpt
	 * @return void
	 * @since 2.0
	 */
	public function register_question_label() {

		/**
		 * Labesl for label taxonomy
		 * @var array
		 */

		$label_labels = array(
			'name' 				=> __( 'Question Labels', 'labels-for-anspress' ),
			'singular_name' 	=> _x( 'Label', 'labels-for-anspress' ),
			'all_items' 		=> __( 'All Labels', 'labels-for-anspress' ),
			'add_new_item' 		=> _x( 'Add New Label', 'labels-for-anspress' ),
			'edit_item' 		=> __( 'Edit Label', 'labels-for-anspress' ),
			'new_item' 			=> __( 'New Label', 'labels-for-anspress' ),
			'view_item' 		=> __( 'View Label', 'labels-for-anspress' ),
			'search_items' 		=> __( 'Search Label', 'labels-for-anspress' ),
			'not_found' 		=> __( 'Nothing Found', 'labels-for-anspress' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'labels-for-anspress' ),
			'parent_item_colon' => '',
		);

		/**
		 * FILTER: ap_question_label_labels
		 * Filter ic called before registering question_label taxonomy
		 */
		$label_labels = apply_filters( 'ap_question_label_labels',  $label_labels );

		/**
		 * Arguments for label taxonomy
		 * @var array
		 * @since 2.0
		 */
		$label_args = array(
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'labels'            => $label_labels,
			'rewrite'           => false,
		);

		/**
		 * FILTER: ap_question_label_args
		 * Filter ic called before registering question_label taxonomy
		 */
		$label_args = apply_filters( 'ap_question_label_args',  $label_args );

		/**
		 * Now let WordPress know about our taxonomy
		 */
		register_taxonomy( 'question_label', array( 'question' ), $label_args );

	}

	/**
	 * Apppend default options
	 * @param   array $defaults
	 * @return  array
	 * @since   1.0
	 */
	public static function ap_default_options($defaults) {

		$defaults['max_labels']       	= 5;
		$defaults['min_labels']       	= 1;
		$defaults['labels_page_title']   	= __('Labels', 'labels-for-anspress' );
		$defaults['labels_per_page']   	= 20;
		$defaults['labels_page_slug']   	= 'labels';
		$defaults['label_page_slug']   	= 'label';

		return $defaults;
	}

	/**
	 * Add labels menu in wp-admin
	 * @return void
	 * @since 2.0
	 */
	public function admin_labels_menu() {
		add_submenu_page( 'anspress', __( 'Questions Labels', 'labels-for-anspress' ), __( 'Labels', 'labels-for-anspress' ), 'manage_options', 'edit-tags.php?taxonomy=question_label' );
	}
	/**
	 * Register option fields
	 * @return void
	 * @since 1.2.1
	 */
	public function option_fields() {

		if ( ! is_admin() ) {
			return; }

		$settings = ap_opt();
		ap_register_option_group( 'labels', __( 'Labels', 'labels-for-anspress' ), array(
			array(
				'name'              => 'labels_per_page',
				'label'             => __( 'Labels to show', 'labels-for-anspress' ),
				'description'       => __( 'Numbers of labels to show in labels page.', 'labels-for-anspress' ),
				'type'              => 'number',
			),
			array(
				'name'              => 'max_labels',
				'label'             => __( 'Maximum labels', 'labels-for-anspress' ),
				'description'       => __( 'Maximum numbers of labels that user can add when asking.', 'labels-for-anspress' ),
				'type'              => 'number',
			),
			array(
				'name'              => 'min_labels',
				'label'             => __( 'Minimum labels', 'labels-for-anspress' ),
				'description'       => __( 'minimum numbers of labels that user must add when asking.', 'labels-for-anspress' ),
				'type'              => 'number',
			),
			array(
				'name' 		=> 'labels_page_title',
				'label' 	=> __( 'Labels page title', 'labels-for-anspress' ),
				'desc' 		=> __( 'Title for labels page', 'labels-for-anspress' ),
				'type' 		=> 'text',
				'show_desc_tip' => false,
			),
			array(
				'name' 		=> 'labels_page_slug',
				'label' 	=> __( 'Labels page slug', 'labels-for-anspress' ),
				'desc' 		=> __( 'Slug labels page', 'labels-for-anspress' ),
				'type' 		=> 'text',
				'show_desc_tip' => false,
			),

			array(
				'name' 		=> 'label_page_slug',
				'label' 	=> __( 'Label page slug', 'labels-for-anspress' ),
				'desc' 		=> __( 'Slug for label page', 'labels-for-anspress' ),
				'type' 		=> 'text',
				'show_desc_tip' => false,
			),
		));
	}


	/**
	 * Append meta display
	 * @param  array $metas
	 * @param array $question_id
	 * @return array
	 * @since 2.0
	 */
	public function ap_display_question_metas($metas, $question_id) {

		if ( ap_question_have_labels( $question_id ) && ! is_singular( 'question' ) ) {
			$metas['labels'] = ap_question_labels_html( array( 'label' => ap_icon( 'label', true ), 'show' => 1 ) ); }

		return $metas;
	}

	/**
	 * Hook labels after post
	 * @param   object $post
	 * @return  string
	 * @since   1.0
	 */
	public function ap_question_info($post) {

		if ( ap_question_have_labels() ) {
			echo '<div class="widget"><span class="ap-widget-title">'.__( 'Labels' ).'</span>';
			echo '<div class="ap-post-labels clearfix">'. ap_question_labels_html( array( 'list' => true, 'label' => '' ) ) .'</div></div>';
		}
	}

	/**
	 * Enqueue scripts
	 * @since 1.0
	 */
	public function ap_enqueue() {
		//wp_enqueue_script( 'labels_js', ap_get_theme_url( 'js/labels_js.js', LABELS_FOR_ANSPRESS_URL ) );
        //wp_enqueue_style( 'labels_css', ap_get_theme_url( 'css/labels.css', LABELS_FOR_ANSPRESS_URL ) );
	}

	/**
	 * Add translated strings to the javascript files
	 * @since 1.0
	 */
	public function ap_localize_scripts() {
		$l10n_data = array(
			'deleteLabel' => __( 'Delete Label', 'labels-for-anspress' ),
			'addLabel' => __( 'Add Label', 'labels-for-anspress' ),
			'labelAdded' => __( 'added to the labels list.', 'labels-for-anspress' ),
			'labelRemoved' => __( 'removed from the labels list.', 'labels-for-anspress' ),
			'suggestionsAvailable' => __( 'Suggestions are available. Use the up and down arrow keys to read it.', 'labels-for-anspress' ),
		);

		wp_localize_script(
			'labels_js',
			'apLabelsTranslation',
			$l10n_data
		);
	}

	/**
	 * Filter label term link
	 * @param  string $url      Default URL of taxonomy.
	 * @param  array  $term     Term array.
	 * @param  string $taxonomy Taxonomy type.
	 * @return string           New URL for term.
	 */
	public function term_link_filter( $url, $term, $taxonomy ) {
		if ( 'question_label' == $taxonomy ) {
			if ( get_option( 'permalink_structure' ) != '' ) {
				return ap_get_link_to( array( 'ap_page' => ap_get_label_slug(), 'q_label' => $term->slug ) );
			} else {
				return ap_get_link_to( array( 'ap_page' => ap_get_label_slug(), 'q_label' => $term->term_id ) );
			}
		}
		return $url;
	}

	/**
	 * add label field in ask form
	 * @param  array $validate
	 * @return void
	 * @since 2.0
	 */
	public function ask_from_label_field($args, $editing) {
		global $editing_post;

		if ( $editing ) {
			$labels = get_the_terms( $editing_post->ID, 'question_label' );
		}

		$labels_post = isset( $_POST['labels'] ) ? $_POST['labels'] : '';
		$label_val = $editing ? $labels : $labels_post;

		$label_field = '<div class="ap-field-labels ap-form-fields">';
			$label_field .= '<label class="ap-form-label" for="labels">'.__('Labels', 'labels-for-anspress' ).'</label>';
			$label_field .= '<div data-role="ap-labelsinput" class="ap-labels-input">';
				$label_field .= '<div id="ap-labels-add">';
					$label_field .= '<input id="labels" class="ap-labels-field ap-form-control" placeholder="'.__('Type and hit enter', 'labels-for-anspress' ).'" autocomplete="off" />';
					$label_field .= '<ul id="ap-labels-suggestion">';
					$label_field .= '</ul>';
				$label_field .= '</div>';

				$label_field .= '<ul id="ap-labels-holder" aria-describedby="ap-labels-list-title">';
		foreach ( (array) $label_val as $label ) {
			if( !empty( $label->slug ) ){
				$label_field .= '<li class="ap-labelssugg-item"><button role="button" class="ap-label-remove"><span class="sr-only"></span> <span class="ap-label-item-value">'. $label->slug .'</span><i class="apicon-x"></i></button><input type="hidden" name="labels[]" value="'. $label->slug .'" /></li>';
			}
		}
				$label_field .= '</ul>';

			$label_field .= '</div>';

		$label_field .= '</div>';

		$args['fields'][] = array(
			'name' 		=> 'label',
			'label' 	=> __( 'Labels', 'labels-for-anspress' ),
			'type'  	=> 'custom',
			'taxonomy' 	=> 'question_label',
			'desc' 		=> __( 'Slowly type for suggestions', 'labels-for-anspress' ),
			'order' 	=> 11,
			'html' 		=> $label_field,
		);

		return $args;
	}

	/**
	 * add label in validation field
	 * @param  array $fields
	 * @return array
	 * @since  1.0
	 */
	public function ap_ask_fields_validation($args) {
		$args['labels'] = array(
			'sanitize' => array( 'sanitize_labels' ),
			'validate' => array( 'comma_separted_count' => ap_opt( 'min_labels' ) ),
		);

		return $args;
	}

	/**
	 * Things to do after creating a question
	 * @param  int    $post_id
	 * @param  object $post
	 * @return void
	 * @since 1.0
	 */
	public function after_new_question($post_id, $post) {

		global $validate;

		if ( empty( $validate ) ) {
			return;
		}

		$fields = $validate->get_sanitized_fields();
		if ( isset( $fields['labels'] ) ) {
			$labels = explode(',', $fields['labels'] );
			wp_set_object_terms( $post_id, $labels, 'question_label' );
		}
	}

	/**
	 * Labels page title
	 * @param  string $title
	 * @return string
	 */
	public function page_title($title) {
		if ( is_question_labels() ) {
			$title = ap_opt('labels_page_title' );
		} elseif ( is_question_label() ) {
			$label_id = sanitize_title( get_query_var( 'q_label' ) );
			$label = get_term_by( 'slug', $label_id, 'question_label' );
			$title = $label->name;
		}

		return $title;
	}

	/**
	 * Hook into AnsPress breadcrums to show labels page.
	 * @param  array $navs Breadcrumbs navs.
	 * @return array
	 */
	public function ap_breadcrumbs($navs) {
		if ( is_question_label() ) {
			$label_id = sanitize_title( get_query_var( 'q_label' ) );
			$label = get_term_by( 'slug', $label_id, 'question_label' );
			$navs['page'] = array();
			$navs['label'] = array( 'title' => $label->name, 'link' => get_term_link( $label, 'question_label' ), 'order' => 8 );
		} elseif ( is_question_labels() ) {
			$navs['page'] = array( 'title' => __( 'Labels', 'labels-for-anspress' ), 'link' => ap_get_link_to( 'labels' ), 'order' => 8 );

		}

		return $navs;
	}

	public function ap_list_head() {

		global $wp;

		if ( ! isset( $wp->query_vars['ap_sc_atts_labels'] ) ) {
			ap_label_sorting(); }
	}

	public function terms_clauses($query, $taxonomies, $args) {
		if ( isset( $args['ap_labels_query'] ) && $args['ap_labels_query'] == 'num_rows' ) {
			$query['fields'] = 'SQL_CALC_FOUND_ROWS '. $query['fields'];
		}

		if ( in_array( 'question_label', $taxonomies ) && isset( $args['ap_query'] ) && $args['ap_query'] == 'labels_subscription' ) {
			global $wpdb;

			$query['join']     = $query['join'].' INNER JOIN '.$wpdb->prefix.'ap_meta apmeta ON t.term_id = apmeta.apmeta_actionid';
			$query['where']    = $query['where']." AND apmeta.apmeta_type='subscriber' AND apmeta.apmeta_param='label' AND apmeta.apmeta_userid='".$args['user_id']."'";
		}

		return $query;
	}

	public function get_terms($terms, $taxonomies, $args) {
		if ( isset( $args['ap_labels_query'] ) && $args['ap_labels_query'] == 'num_rows' ) {
			global $labels_rows_found,  $wpdb;

			$labels_rows_found = $wpdb->get_var( apply_filters( 'ap_get_terms_found_rows', 'SELECT FOUND_ROWS()', $terms, $taxonomies, $args ) );
			// wp_cache_set( $this->cache_key.'_count', $this->total_count, 'labels-for-anspress' );
		}
		return $terms;
	}

	public function subscription_tab($active) {

		echo '<li class="'.($active == 'label' ? 'active' : '').'"><a href="?tab=label">'.__( 'Label', 'labels-for-anspress' ).'</a></li>';
	}

	public function subscription_page($active) {

		$active = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'question';

		if ( $active != 'label' ) {
			return; }

		global $question_labels, $ap_max_num_pages, $ap_per_page, $labels_rows_found;

		$paged              = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page           = ap_opt( 'labels_per_page' );
		$total_terms        = $labels_rows_found;
		$offset             = $per_page * ( $paged - 1) ;
		$ap_max_num_pages   = ceil( $total_terms / $per_page );

		$label_args = array(
			'ap_labels_query' => 'num_rows',
			'ap_query'      => 'labels_subscription',
			'parent'        => 0,
			'number'        => $per_page,
			'offset'        => $offset,
			'hide_empty'    => false,
			'order'         => 'DESC',
			'user_id'       => get_current_user_id(),
		);

		if ( @$_GET['ap_sort'] == 'new' ) {
			$label_args['orderby'] = 'id';
			$label_args['order']      = 'ASC';
		} elseif ( @$_GET['ap_sort'] == 'name' ) {
			$label_args['orderby']    = 'name';
			$label_args['order']      = 'ASC';
		} else {
			$label_args['orderby'] = 'count';
		}

		if ( isset( $_GET['ap_s'] ) ) {
			$label_args['search'] = sanitize_text_field( $_GET['ap_s'] );
		}

		$question_labels = get_terms( 'question_label' , $label_args );

		include ap_get_theme_location( 'labels.php', LABELS_FOR_ANSPRESS_DIR );
	}

	/**
	 * Handle labels suggestion on question form
	 */
	public function ap_labels_suggestion() {
		$keyword = sanitize_text_field( wp_unslash( $_POST['q'] ) );

		$labels = get_terms('question_label', array(
			'orderby' => 'count',
			'order' => 'DESC',
			'hide_empty' => false,
			'search' => $keyword,
			'number' => 8,
		));

		if ( $labels ) {
			$items = array();
			foreach ( $labels as $k => $t ) {
				$items [ $k ] = $t->slug;
			}

			$result = array( 'status' => true, 'items' => $items );
			die( json_encode( $result ) );
		}

		die( json_encode( array( 'status' => false ) ) );
	}

	/**
	 * Add category pages rewrite rule
	 * @param  array $rules AnsPress rules.
	 * @return array
	 */
	public function rewrite_rules($rules, $slug, $base_page_id) {
		global $wp_rewrite;

		$labels_rules = array();

		$labels_rules[$slug. ap_get_label_slug() .'/([^/]+)/?'] = 'index.php?page_id='.$base_page_id.'&ap_page='. ap_get_label_slug() .'&q_label='.$wp_rewrite->preg_index( 1 );

		$labels_rules[$slug. ap_get_label_slug() . '/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?page_id='.$base_page_id.'&ap_page='. ap_get_label_slug() .'&q_label='.$wp_rewrite->preg_index( 1 ).'&paged='.$wp_rewrite->preg_index( 2 );

		$labels_rules[$slug. ap_get_labels_slug() . '/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?page_id='.$base_page_id.'&ap_page='. ap_get_labels_slug() .'&q_label='.$wp_rewrite->preg_index( 1 ).'&paged='.$wp_rewrite->preg_index( 2 );

		return $labels_rules + $rules;
	}

	/**
	 * Add default labels page, so that labels page should work properly after
	 * Changing labels page slug.
	 * @param  array $default_pages AnsPress default pages.
	 * @return array
	 */
	public function labels_default_page($default_pages) {
		$default_pages['labels'] = array();
		$default_pages['label'] = array();

		return $default_pages;
	}

	/**
	 * Add default page slug
	 * @param  array $default_slugs AnsPress pages slug.
	 * @return array
	 */
	public function default_page_slugs($default_slugs) {
		$default_slugs['labels'] 	= ap_get_labels_slug();
		$default_slugs['label'] 	= ap_get_label_slug();
		return $default_slugs;
	}

	public function subscribe_type($type) {
		if ( is_question_label() ) {
			$subscribe_type = 'label'; } else {
			return $type; }
	}

	public function subscribe_btn_action_type($args) {
		if ( is_question_label() ) {
			global $question_label;
			$args['action_id'] 	= $question_label->term_id;
			$args['type'] 		= 'label';
		}

		return $args;
	}

	/**
	 * Override ap_current_page_is function to check if labels or label page.
	 * @param  string $page Current page slug.
	 * @return string
	 */
	public function ap_current_page_is($page) {
		if ( is_question_labels() ) {
			$template = 'labels';
		} elseif ( is_question_label() ) {
			$template = 'label';
		}

		return $page;
	}
	/**
	 * Filter main questions query args. Modify and add label args.
	 * @param  array $args Questions args.
	 * @return array
	 */
	public static function ap_main_questions_args( $args ) {
		global $questions, $wp;
		$query = $wp->query_vars;

		$filters = ap_list_filters_get_active( 'label' );
		$labels_operator = !empty( $wp->query_vars['ap_labels_operator'] ) ? $wp->query_vars['ap_labels_operator'] : 'IN';

		if ( isset( $query['ap_labels'] ) && is_array( $query['ap_labels'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'question_label',
				'field'    => 'slug',
				'terms'    => $query['ap_labels'],
				'operator' => $labels_operator,
			);
		} elseif ( false !== $filters ) {
			$filters = (array) wp_unslash( $filters );
			$filters = array_map( 'sanitize_text_field', $filters );
			$args['tax_query'][] = array(
				'taxonomy' => 'question_label',
				'field'    => 'term_id',
				'terms'    => $filters,
			);
		}

		return $args;
	}

	/**
	 * Add labels sorting in list filters
	 * @return array
	 */
	public static function ap_list_filters( $filters ) {
		global $wp;

		if ( ! isset( $wp->query_vars['ap_labels'] ) ) {
			$filters['label'] = array(
				'title' => __( 'Label', 'anspress-question-answer' ),
				'items' => ap_get_label_filter(),
				'search' => true,
				'multiple' => true,
			);
		}

		return $filters;
	}

	/**
	 * Send ajax response for filter search.
	 * @param  string $search_query Search string.
	 */
	public static function filter_search_label( $search_query ) {
		ap_ajax_json( [
			'apData' => array(
			'filter' => 'label',
			'searchQuery' => $search_query,
			'items' => ap_get_label_filter( $search_query ),
			),
		] );
	}

	/**
	 * Subscriber action ID.
	 * @param  integer $action_id Current action ID.
	 * @return integer
	 */
	public static function subscribers_action_id( $action_id ) {
		if ( is_question_label() ) {
			global $question_label;
			$action_id = $question_category->term_id;
		}

		return $action_id;
	}
}

/**
 * Get everything running
 *
 * @since 1.0
 *
 * @access private
 * @return void
 */

function labels_for_anspress() {
	if ( ! version_compare(AP_VERSION, '2.3', '>' ) ) {
		function ap_label_admin_error_notice() {
		    echo '<div class="update-nag error"> <p>'.sprintf(__('Labels extension require AnsPress 2.4-RC or above. Download from Github %shttp://github.com/anspress/anspress%s', 'labels-for-anspress' ), '<a target="_blank" href="http://github.com/anspress/anspress">', '</a>' ).'</p></div>';
		}
		add_action( 'admin_notices', 'ap_label_admin_error_notice' );
		return;
	}

	if ( apply_filters( 'anspress_load_ext', true, 'labels-for-anspress' ) ) {
		$ap_labels = new Labels_For_AnsPress();
	}
}
add_action( 'plugins_loaded', 'labels_for_anspress' );

/**
 * Load extensions files before loading AnsPress
 * @return void
 * @since  1.0
 */
function anspress_loaded_labels_for_anspress() {
	add_filter( 'ap_default_options', array( 'Labels_For_AnsPress', 'ap_default_options' ) );
}
add_action( 'before_loading_anspress', 'anspress_loaded_labels_for_anspress' );
