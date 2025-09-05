<?php
/**
 * Admin menu and settings scaffold.
 */
class IELTS_Admin_Menu {
    /**
     * Menu slug for the plugin dashboard.
     */
    private const MENU_SLUG = 'ielts_migration';

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Register top-level menu.
     */
    public function register_menu() : void {
        add_menu_page(
            __( 'IELTS Immigration', 'IELTS-IMMIGRATION' ),
            __( 'IELTS Immigration', 'IELTS-IMMIGRATION' ),
            'manage_options',
            self::MENU_SLUG,
            [ $this, 'render_dashboard' ],
            'dashicons-welcome-learn-more'
        );

        add_submenu_page(
            self::MENU_SLUG,
            __( 'Lessons', 'IELTS-IMMIGRATION' ),
            __( 'Lessons', 'IELTS-IMMIGRATION' ),
            'edit_posts',
            'edit.php?post_type=ielts_lesson'
        );

        add_submenu_page(
            self::MENU_SLUG,
            __( 'Kits', 'IELTS-IMMIGRATION' ),
            __( 'Kits', 'IELTS-IMMIGRATION' ),
            'edit_posts',
            'edit.php?post_type=ielts_kit'
        );

        add_submenu_page(
            self::MENU_SLUG,
            __( 'Practices', 'IELTS-IMMIGRATION' ),
            __( 'Practices', 'IELTS-IMMIGRATION' ),
            'edit_posts',
            'edit.php?post_type=ielts_practice'
        );
    }

    /**
     * Enqueue admin assets only on our page.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( string $hook ) : void {
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( self::MENU_SLUG !== $page ) {
            return;
        }

        wp_enqueue_style(
            'ielts-admin',
            IELTS_MIGRATION_URL . 'public/css/admin.css',
            [],
            IELTS_MIGRATION_VER
        );
    }

    /**
     * Render dashboard page.
     */
    public function render_dashboard() : void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'IELTS Immigration', 'IELTS-IMMIGRATION' ); ?></h1>

            <h2 class="title ielts-admin-section"><?php echo esc_html__( 'Overview', 'IELTS-IMMIGRATION' ); ?></h2>
            <p><?php echo esc_html__( 'Coming soon…', 'IELTS-IMMIGRATION' ); ?></p>

            <h2 class="title ielts-admin-section"><?php echo esc_html__( 'Shortcodes', 'IELTS-IMMIGRATION' ); ?></h2>
            <p><?php echo esc_html__( 'Coming soon…', 'IELTS-IMMIGRATION' ); ?></p>

            <h2 class="title ielts-admin-section"><?php echo esc_html__( 'REST', 'IELTS-IMMIGRATION' ); ?></h2>
            <p><?php echo esc_html__( 'Coming soon…', 'IELTS-IMMIGRATION' ); ?></p>

            <h2 class="title ielts-admin-section"><?php echo esc_html__( 'Logs', 'IELTS-IMMIGRATION' ); ?></h2>
            <p><?php echo esc_html__( 'Coming soon…', 'IELTS-IMMIGRATION' ); ?></p>
        </div>
        <?php
    }
}
