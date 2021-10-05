<?php
/**
 * The template for displaying search results pages
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

?>

<div class="wrapper" id="search-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<?php get_search_form(); 
						//.d-none .d-md-block .d-xl-none?>

							<h1 class="page-title h6 font-weight-bold mt-2 mb-4 text-muted">

								<?php
								printf(
									/* translators: %s: query term */
									esc_html__( 'Search Results for: %s', 'understrap' ),
									'<span>"' . get_search_query() . '"</span>'
								);
								?>
								<span class="ml-1">
								<?php
									echo '(' . $wp_query->found_posts . ')';
									?>
								</span>
							</h1>

					</header><!-- .page-header -->
					
					<!-- Top pagination component -->
					<?php understrap_pagination(); ?>

					<?php /* Start the Loop */ ?>
					<?php
					while ( have_posts() ) :
						the_post();

						/*
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'loop-templates/content', 'search' );
						?>
						<hr>
						<?php
					endwhile;
					?>

				<?php else : ?>

					<?php get_template_part( 'loop-templates/content', 'none' ); ?>

				<?php endif; ?>

				<?php understrap_pagination(); ?>
			</main><!-- #main -->

			<!-- Bottom pagination component -->

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #search-wrapper -->

<?php
get_footer();
