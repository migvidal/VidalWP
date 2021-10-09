<?php

/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
	<?php vidalwp_the_title(); ?>
		<?php if ('post' === get_post_type()) : ?>

			<div class="entry-meta">
				<?php understrap_posted_on(); ?>
			</div><!-- .entry-meta -->

		<?php endif; ?>


	</header><!-- .entry-header -->

	<?php if (has_post_thumbnail()) : ?>
		<div class="row">
			<div class="col-md-4">
				<?php
				echo_first_image($post->ID);
				/* echo get_the_post_thumbnail(
					$post->ID,
					'thumbnail',
					array(
						'class' => 'rounded my-3'
					)
				); */
				?> </div>
			<div class="col-md-6">
				<?php the_excerpt(); ?>
			</div>
		</div>
	<?php else : ?>
		<?php the_excerpt(); ?>
	<?php endif; ?>




	<div class="entry-content">

		<?php

		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->