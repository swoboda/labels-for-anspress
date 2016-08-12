<?php
	/**
	 * Labels page layout
	 *
	 * [Long Description.]
	 *
	 * @link http://wp3.in
	 * @since 1.0
	 *
	 * @package AnsPress
	 * @subpackage Labels for AnsPress
	 */

	global $question_labels;
?>
<?php dynamic_sidebar( 'ap-top' ); ?>

<div id="ap-labels" class="row">
	<div class="<?php echo is_active_sidebar( 'ap-labels' ) && is_anspress() ? 'col-md-9' : 'col-md-12' ?>">

		<div class="ap-list-head clearfix">
			<form id="ap-search-form" class="ap-search-form pull-left" action="<?php echo ap_get_link_to('labels'); ?>?type=labels">
			    <input name="ap_s" type="text" class="ap-form-control" placeholder="<?php _e('Search labels...', 'ap'); ?>" value="<?php echo sanitize_text_field( get_query_var('ap_s') ); ?>" />
			    <input name="type" type="hidden" value="labels" />
			</form>
			<?php ap_labels_tab(); ?>
		</div><!-- close .ap-list-head.clearfix -->

		<ul class="ap-term-label-box clearfix">
			<?php foreach($question_labels as $key => $label) : ?>
				<li class="clearfix">
					<div class="ap-labels-item">
						<div class="ap-term-title">
							<a class="term-title" href="<?php echo get_label_link( $label );?>">
								<?php echo $label->name; ?>
							</a>
							<span class="ap-labelq-count">
								&times; <?php printf(_n('%d Question', '%d Questions', $label->count, 'labels-for-anspress'), $label->count) ?>
							</span>
						</div>

						<div class="ap-taxo-description">
							<?php
								if($label->description != '')
									echo $label->description;
								else
									_e('No description.', 'labels-for-anspress');
							?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul><!-- close .ap-term-label-box.clearfix -->

		<?php ap_pagination(); ?>
	</div><!-- close #ap-labels -->

	<?php if ( is_active_sidebar( 'ap-labels' ) && is_anspress()){ ?>
		<div class="ap-labels-sidebar col-md-3">
			<?php dynamic_sidebar( 'ap-labels' ); ?>
		</div>
	<?php } ?>

</div><!-- close .row -->

