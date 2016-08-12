<?php
/**
 * Label page
 * Display list of question of a label
 * @package AnsPress
 */
?>
<?php dynamic_sidebar( 'ap-top' ); ?>
<div class="row">
	<div id="ap-lists" class="<?php echo is_active_sidebar( 'ap-label' ) && is_anspress() ? 'col-md-9' : 'col-md-12' ?>">
		<div class="ap-taxo-detail clearfix">
			<h2 class="entry-title"><?php printf(__('Question label: %s','anspress-question-answer'), $question_label->name); ?> <span class="ap-tax-item-count"><?php printf( _n('1 Question', '%s Questions', $question_label->count, 'anspress-question-answer'),  $question_label->count); ?></span></h2>
			<?php if($question_label->description !=''): ?>
				<p class="ap-taxo-description"><?php echo $question_label->description; ?></p>
			<?php endif; ?>
			<?php ap_subscribe_btn_html($question_label->term_id, 'label'); ?>
			<?php ap_question_subscribers($question_label->term_id, 'label'); ?>
		</div>
		<?php ap_get_template_part('list-head'); ?>
		<?php if ( ap_have_questions() ) : ?>
			<div class="ap-questions">
				<?php
					
					/* Start the Loop */
					while ( ap_questions() ) : ap_the_question();
						global $post;
						include(ap_get_theme_location('content-list.php'));
					endwhile;
				?>
			</div>
			<?php ap_questions_the_pagination(); ?>
		<?php
			else : 
				include(ap_get_theme_location('content-none.php'));
			endif; 
		?>	
	</div>
	<?php if ( is_active_sidebar( 'ap-label' ) && is_anspress()){ ?>
		<div class="ap-question-right col-md-3">
			<?php dynamic_sidebar( 'ap-label' ); ?>
		</div>
	<?php } ?>
</div>
