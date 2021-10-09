<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function understrap_remove_scripts()
{
    wp_dequeue_style('understrap-styles');
    wp_deregister_style('understrap-styles');

    wp_dequeue_script('understrap-scripts');
    wp_deregister_script('understrap-scripts');

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action('wp_enqueue_scripts', 'understrap_remove_scripts', 20);

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
function theme_enqueue_styles()
{

    // Get the theme data
    $the_theme = wp_get_theme();
    wp_enqueue_style('child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get('Version'));
    wp_enqueue_script('jquery');
    wp_enqueue_script('child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get('Version'), true);
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

function add_child_theme_textdomain()
{
    load_child_theme_textdomain('understrap-child', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'add_child_theme_textdomain');



/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function understrap_posted_on()
{
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s"> (%4$s) </time>';
    }
    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date('c')),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date('c')),
        esc_html(get_the_modified_date())
    );
    $posted_on   = apply_filters(
        'understrap_posted_on',
        sprintf(
            '<span class="posted-on">%1$s <a class="dotted-link" href="%2$s" rel="bookmark">%3$s</a></span>',
            esc_html_x('Posted on', 'post date', 'understrap'),
            esc_url(get_permalink()),
            apply_filters('understrap_posted_on_time', $time_string)
        )
    );
    $byline      = apply_filters(
        'understrap_posted_by',
        sprintf(
            '<span class="byline"> %1$s<span class="author vcard"> <a class="url fn n dotted-link" href="%2$s">%3$s</a></span></span>',
            $posted_on ? esc_html_x('by', 'post author', 'understrap') : esc_html_x('Posted by', 'post author', 'understrap'),
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_html(get_the_author())
        )
    );
    echo $posted_on . $byline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Adds a custom read more link to all excerpts, manually or automatically generated
 *
 * @param string $post_excerpt Posts's excerpt.
 *
 * @return string
 */
function understrap_all_excerpts_get_more_link($post_excerpt)
{
    if (!is_admin()) {
        $post_excerpt = $post_excerpt . '<a class="understrap-read-more-link m-3" href="' . esc_url(get_permalink(get_the_ID())) . '">' . __(
            'Read More',
            'understrap'
        ) . '<i class = "fa fa-chevron-circle-right fa-lg ml-1 indicator-icon"></i></a>';
    }
    return $post_excerpt;
}



function understrap_comment_navigation($nav_id)
{
    if (get_comment_pages_count() <= 1) {
        // Return early if there are no comments to navigate through.
        return;
    }
?>
    <nav class="comment-navigation" id="<?php echo esc_attr($nav_id); ?>">

        <h1 class="sr-only"><?php esc_html_e('Comment navigation', 'understrap'); ?></h1>

        <?php if (get_previous_comments_link()) { ?>
            <div class="nav-previous">
                <?php previous_comments_link(__('&larr; Older Comments', 'understrap')); ?>
            </div>
        <?php } ?>

        <?php if (get_next_comments_link()) { ?>
            <div class="nav-next">
                <?php next_comments_link(__('Newer Comments &rarr;', 'understrap')); ?>
            </div>
        <?php } ?>

    </nav><!-- #<?php echo esc_attr($nav_id); ?> -->


    <?php
}

/**
 * Displays the edit post link for post.
 */
function understrap_edit_post_link()
{
    edit_post_link(
        sprintf(
            '<i class="fa fa-edit fa-lg ml-2 pr-1"></i>' .
                /* translators: %s: Name of current post */
                esc_html__('Edit %s', 'understrap'),
            the_title('<span class="sr-only">"', '"</span>', false)
        ),
        '<span class="edit-link">',
        '</span>'
    );
}


function understrap_entry_footer()
{
    // Hide category and tag text for pages.
    if ('post' === get_post_type()) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list(esc_html__(', ', 'understrap'));
        if ($categories_list && understrap_categorized_blog()) {
            /* translators: %s: Categories of current post */
            printf('<span class="cat-links">' . esc_html__('Posted in %s', 'understrap') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list('', esc_html__(', ', 'understrap'));
        if ($tags_list) {
            /* translators: %s: Tags of current post */
            printf('<span class="tags-links">' . esc_html__('Tagged %s', 'understrap') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
    $commentIcon = '<i class="fa fa-comment-o fa-lg pr-1"></i>';
    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '
        <span class="comments-link">';
        comments_popup_link(
            $commentIcon . esc_html__('0', 'understrap'),
            $commentIcon . esc_html__('1', 'understrap'),
            $commentIcon . esc_html__('%', 'understrap')
        );
        echo '</span>';
    }

    understrap_edit_post_link();
}


// COMMENTS

/**
 * Add Bootstrap classes to WP's comment form default fields.
 *
 * @param array $fields {
 *     Default comment fields.
 *
 *     @type string $author  Comment author field HTML.
 *     @type string $email   Comment author email field HTML.
 *     @type string $url     Comment author URL field HTML.
 *     @type string $cookies Comment cookie opt-in field HTML.
 * }
 *
 * @return array
 */
function understrap_bootstrap_comment_form_fields($fields)
{

    $replace = array(
        '<p class="' => '<div class="form-group ',
        '<input'     => '<input class="form-control" ',
        '</p>'       => '</div>',
    );

    if (isset($fields['author'])) {
        $fields['author'] = strtr($fields['author'], $replace);
    }
    if (isset($fields['email'])) {
        $fields['email'] = strtr($fields['email'], $replace);
    }
    if (isset($fields['url'])) {
        $fields['url'] = strtr($fields['url'], $replace);
    }

    $replace = array(
        '<p class="' => '<div class="form-group form-check ',
        '<input'     => '<input class="form-check-input" ',
        '<label'     => '<label class="form-check-label" ',
        '</p>'       => '</div>',
    );
    if (isset($fields['cookies'])) {
        $fields['cookies'] = strtr($fields['cookies'], $replace);
    }

    return $fields;
}


// Add Bootstrap classes to comment form submit button and comment field.
add_filter('comment_form_defaults', 'understrap_bootstrap_comment_form');
/**
 * Adds Bootstrap classes to comment form submit button and comment field.
 *
 * @param string[] $args Comment form arguments and fields.
 *
 * @return string[]
 */
function understrap_bootstrap_comment_form($args)
{
    $replace = array(
        '<p class="' => '<div class="form-group ',
        '<textarea'  => '<textarea class="form-control txta" ', // autosize
        '</p>'       => '</div>',
    );

    if (isset($args['comment_field'])) {
        $args['comment_field'] = strtr($args['comment_field'], $replace);
    }

    if (isset($args['class_submit'])) {
        $args['class_submit'] = 'btn btn-secondary';
    }

    return $args;
}



// Add note if comments are closed.
add_action('comment_form_comments_closed', 'understrap_comment_form_comments_closed');

/**
 * Displays a note that comments are closed if comments are closed and there are comments.
 */
function understrap_comment_form_comments_closed()
{
    if (get_comments_number() && post_type_supports(get_post_type(), 'comments')) {
    ?>
        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'understrap'); ?></p>
    <?php
    }
}


/**
 * Display navigation to next/previous post when applicable.
 */
function understrap_post_nav()
{
    // Don't print empty markup if there's nowhere to navigate.
    $previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
    $next     = get_adjacent_post(false, '', false);
    if (!$next && !$previous) {
        return;
    }
    ?>
    <nav class="container navigation post-navigation border-top border-bottom p-2">
        <h2 class="sr-only"><?php esc_html_e('Post navigation', 'understrap'); ?></h2>
        <div class="row nav-links justify-content-between">
            <?php
            if (get_previous_post_link()) {
                previous_post_link('<span class="nav-previous">%link</span>', _x('<i class="fa fa-angle-left"></i>&nbsp;%title', 'Previous post link', 'understrap'));
            }
            if (get_next_post_link()) {
                next_post_link('<span class="nav-next">%link</span>', _x('%title&nbsp;<i class="fa fa-angle-right"></i>', 'Next post link', 'understrap'));
            }
            ?>
        </div><!-- .nav-links -->
    </nav><!-- .navigation -->
<?php
}



/**
 * Displays the navigation to next/previous set of posts.
 *
 * @param string|array $args {
 *     (Optional) Array of arguments for generating paginated links for archives.
 *
 *     @type string $base               Base of the paginated url. Default empty.
 *     @type string $format             Format for the pagination structure. Default empty.
 *     @type int    $total              The total amount of pages. Default is the value WP_Query's
 *                                      `max_num_pages` or 1.
 *     @type int    $current            The current page number. Default is 'paged' query var or 1.
 *     @type string $aria_current       The value for the aria-current attribute. Possible values are 'page',
 *                                      'step', 'location', 'date', 'time', 'true', 'false'. Default is 'page'.
 *     @type bool   $show_all           Whether to show all pages. Default false.
 *     @type int    $end_size           How many numbers on either the start and the end list edges.
 *                                      Default 1.
 *     @type int    $mid_size           How many numbers to either side of the current pages. Default 2.
 *     @type bool   $prev_next          Whether to include the previous and next links in the list. Default true.
 *     @type bool   $prev_text          The previous page text. Default '&laquo;'.
 *     @type bool   $next_text          The next page text. Default '&raquo;'.
 *     @type string $type               Controls format of the returned value. Possible values are 'plain',
 *                                      'array' and 'list'. Default is 'array'.
 *     @type array  $add_args           An array of query args to add. Default false.
 *     @type string $add_fragment       A string to append to each link. Default empty.
 *     @type string $before_page_number A string to appear before the page number. Default empty.
 *     @type string $after_page_number  A string to append after the page number. Default empty.
 *     @type string $screen_reader_text Screen reader text for the nav element. Default 'Posts navigation'.
 * }
 * @param string       $class           (Optional) Classes to be added to the <ul> element. Default 'pagination'.
 */
function understrap_pagination($args = array(), $class = 'pagination justify-content-center')
{

    if (!isset($args['total']) && $GLOBALS['wp_query']->max_num_pages <= 1) {
        return;
    }

    $args = wp_parse_args(
        $args,
        array(
            'mid_size'           => 2,
            'prev_next'          => true,
            'prev_text'          => __('&laquo;', 'understrap'),
            'next_text'          => __('&raquo;', 'understrap'),
            'type'               => 'array',
            'current'            => max(1, get_query_var('paged')),
            'screen_reader_text' => __('Posts navigation', 'understrap'),
        )
    );

    $links = paginate_links($args);
    if (!$links) {
        return;
    }

?>

    <nav aria-labelledby="posts-nav-label">

        <h2 id="posts-nav-label" class="sr-only">
            <?php echo esc_html($args['screen_reader_text']); ?>
        </h2>

        <ul class="<?php echo esc_attr($class); ?>">

            <?php
            foreach ($links as $key => $link) {
            ?>
                <li class="page-item <?php echo strpos($link, 'current') ? 'active' : ''; ?>">
                    <?php echo str_replace('page-numbers', 'page-link', $link); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                    ?>
                </li>
            <?php
            }
            ?>

        </ul>

    </nav>

<?php
}

// CUSTOM 

/**
 * Displays an icon depending on the post format
 *
 * @param [string] $format
 * @return string
 */
function vidalwp_post_format_icon($format)
{
    $icon = "";
    switch ($format) {
        case 'aside':
            $icon = 'fa-info';
            break;
        case 'video':
            $icon = 'fa-play';
            break;
        case 'quote':
            $icon = 'fa-quote-left';
            break;
        case 'link':
            $icon = 'fa-link';
            break;
        default:
            break;
    }
    // echo icon
    return sprintf(
        '<i class="col-1 fa %s fa-lg text-extremely-muted mr-3 mr-lg-0 mr-xl-0"></i>',
        $icon
    );
}


/**
 * Prints custom title with post type icon
 *
 * @param string $heading    Heading tag (h1, h2, h3...). Default h1.
 * @param array $classes     Additional classes.
 * 
 * @return void
 */
function vidalwp_the_title($heading = 'h1', $classes = array())
{
    $format = get_post_format() ?: 'standard';
    $post_icon = vidalwp_post_format_icon($format);
    $classes = implode(' ', $classes);

    echo '<div class="row">'

        . the_title(
            sprintf(
                '<%s class="col entry-title %s"><a href="%s" rel="bookmark">',
                $heading,
                $classes,
                esc_url(get_permalink())
            ),
            sprintf(
                '<i class = "fa fa-chevron-right ml-3 indicator-icon"></i></a>
                </a></%s>',
                $heading
            ),
            false
        )
        . $post_icon
        . '</div>';
}

/**
 * Returns first image of the post
 * @return string
 * @author Chris Coyier <https://chriscoyier.net>
 * @link https://css-tricks.com/snippets/wordpress/get-the-first-image-from-a-post/
 * 
 */
function catch_that_image()
{
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $post->post_content, $matches);
    $first_img = $matches[1][0];

    if (empty($first_img)) {
        $first_img = "/path/to/default.png";
    }
    return $first_img;
}
function echo_first_image ($postID)
{                   
    $args = array(
    'numberposts' => 1,
    'order'=> 'ASC',
    'post_mime_type' => 'image',
    'post_parent' => $postID,
    'post_status' => null,
    'post_type' => 'attachment'
    );

    $attachments = get_children( $args );

    //print_r($attachments);

    if ($attachments) {
        foreach($attachments as $attachment) {
            $image_attributes = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' )  ? wp_get_attachment_image_src( $attachment->ID, 'thumbnail' ) : wp_get_attachment_image_src( $attachment->ID, 'full' );

            echo '<img src="'.wp_get_attachment_thumb_url( $attachment->ID ).'" class="current">';

        }
    }
}
