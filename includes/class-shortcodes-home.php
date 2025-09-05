<?php
/**
 * Home page shortcodes.
 */
class IELTS_Shortcodes_Home {
    public function __construct() {
        add_shortcode( 'ielts_hero', [ $this, 'render_hero' ] );
        add_shortcode( 'ielts_paths', [ $this, 'render_paths' ] );
        add_shortcode( 'ielts_latest', [ $this, 'render_latest' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Enqueue home stylesheet when needed.
     */
    public function enqueue_assets() : void {
        if ( ! is_singular() ) {
            return;
        }

        global $post;
        if ( empty( $post->post_content ) ) {
            return;
        }

        foreach ( [ 'ielts_hero', 'ielts_paths', 'ielts_latest' ] as $shortcode ) {
            if ( has_shortcode( $post->post_content, $shortcode ) ) {
                wp_enqueue_style(
                    'ielts-home',
                    IELTS_MIGRATION_URL . 'public/css/home.css',
                    [],
                    IELTS_MIGRATION_VER
                );
                break;
            }
        }
    }

    /**
     * Render hero section.
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_hero( $atts ) : string {
        $atts = shortcode_atts(
            [
                'title'      => '',
                'subtitle'   => '',
                'cta1_text'  => '',
                'cta1_url'   => '',
                'cta2_text'  => '',
                'cta2_url'   => '',
            ],
            $atts,
            'ielts_hero'
        );

        $title      = esc_html( $atts['title'] );
        $subtitle   = esc_html( $atts['subtitle'] );
        $cta1_text  = esc_html( $atts['cta1_text'] );
        $cta1_url   = esc_url( $atts['cta1_url'] );
        $cta2_text  = esc_html( $atts['cta2_text'] );
        $cta2_url   = esc_url( $atts['cta2_url'] );

        ob_start();
        ?>
        <section class="ielts-hero">
            <?php if ( $title ) : ?>
                <h1 class="ielts-hero__title"><?php echo $title; ?></h1>
            <?php endif; ?>

            <?php if ( $subtitle ) : ?>
                <p class="ielts-hero__subtitle"><?php echo $subtitle; ?></p>
            <?php endif; ?>

            <?php if ( $cta1_text || $cta2_text ) : ?>
                <div class="ielts-hero__actions">
                    <?php if ( $cta1_text && $cta1_url ) : ?>
                        <a class="button button-primary" href="<?php echo $cta1_url; ?>"><?php echo $cta1_text; ?></a>
                    <?php endif; ?>

                    <?php if ( $cta2_text && $cta2_url ) : ?>
                        <a class="button" href="<?php echo $cta2_url; ?>"><?php echo $cta2_text; ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
        return trim( ob_get_clean() );
    }

    /**
     * Render paths section.
     *
     * @return string
     */
    public function render_paths() : string {
        $paths = [
            [
                'title' => __( 'IELTS', 'IELTS-IMMIGRATION' ),
                'url'   => esc_url( home_url( '/ielts' ) ),
            ],
            [
                'title' => __( 'مهاجرت استرالیا', 'IELTS-IMMIGRATION' ),
                'url'   => esc_url( home_url( '/australia-immigration' ) ),
            ],
            [
                'title' => __( 'فروشگاه', 'IELTS-IMMIGRATION' ),
                'url'   => esc_url( home_url( '/shop' ) ),
            ],
            [
                'title' => __( 'وبلاگ', 'IELTS-IMMIGRATION' ),
                'url'   => esc_url( home_url( '/blog' ) ),
            ],
        ];

        ob_start();
        ?>
        <div class="ielts-paths">
            <?php foreach ( $paths as $path ) : ?>
                <a class="ielts-paths__item" href="<?php echo $path['url']; ?>">
                    <?php echo esc_html( $path['title'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        return trim( ob_get_clean() );
    }

    /**
     * Render latest posts and lessons.
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_latest( $atts ) : string {
        $atts = shortcode_atts(
            [
                'limit' => 6,
            ],
            $atts,
            'ielts_latest'
        );

        $limit = (int) $atts['limit'];

        $q = new WP_Query(
            [
                'post_type'      => [ 'post', 'ielts_lesson' ],
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]
        );

        ob_start();
        if ( $q->have_posts() ) {
            echo '<div class="ielts-latest">';
            while ( $q->have_posts() ) {
                $q->the_post();
                $permalink = esc_url( get_permalink() );
                $title     = esc_html( get_the_title() );
                echo '<article class="ielts-latest__item"><a class="ielts-latest__link" href="' . $permalink . '">' . $title . '</a></article>';
            }
            echo '</div>';
            wp_reset_postdata();
        }

        return trim( ob_get_clean() );
    }
}
