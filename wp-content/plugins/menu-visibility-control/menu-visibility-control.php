<?php
/**
 * Plugin Name: Menu Visibility Control
 * Plugin URI:  https://knowledge.buzz/menu-visibility-control
 * Description: Control WordPress menu item visibility based on login status, user roles, device type, or specific pages — lightweight and theme-agnostic.
 * Version:     1.0.9
 * Author:      davisw3
 * Author URI:  https://knowledge.buzz
 * License:     GPLv2 or later
 * Text Domain: menu-visibility-control
 * Requires at least: 5.8
 * Tested up to: 6.9
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Visibility_Control {

    public function __construct() {
        add_filter( 'wp_nav_menu_objects', [ $this, 'filter_menu_items' ], 10, 2 );
        add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_custom_fields' ], 10, 4 );
        add_action( 'wp_update_nav_menu_item', [ $this, 'save_custom_fields' ], 10, 3 );
        add_action( 'admin_footer-nav-menus.php', [ $this, 'admin_inline_scripts' ] );
    }

    /**
     * Filter menu items visibility (frontend)
     */
    public function filter_menu_items( $items, $args ) {
        $filtered   = [];
        $current_id = get_queried_object_id();

        foreach ( $items as $item ) {

            $state = get_post_meta( $item->ID, '_menu_item_mvc_state', true );
            $roles = get_post_meta( $item->ID, '_menu_item_mvc_roles', true );

            $device = get_post_meta( $item->ID, '_menu_item_mvc_device', true );
            $mode   = get_post_meta( $item->ID, '_menu_item_mvc_page_mode', true );
            $pages  = get_post_meta( $item->ID, '_menu_item_mvc_pages', true );

            $show = true;

            /* Login / Role logic (unchanged & fast) */
            if ( $state === 'logged_in' && ! is_user_logged_in() ) {
                $show = false;
            } elseif ( $state === 'logged_out' && is_user_logged_in() ) {
                $show = false;
            } elseif ( $state === 'roles' ) {
                if ( ! is_user_logged_in() ) {
                    $show = false;
                } else {
                    $user  = wp_get_current_user();
                    $roles = is_array( $roles ) ? $roles : [];
                    $show  = (bool) array_intersect( $roles, (array) $user->roles );
                }
            }

            /* Device visibility (cheap check) */
            if ( $show && $device ) {
                if ( wp_is_mobile() && $device === 'desktop' ) {
                    $show = false;
                } elseif ( ! wp_is_mobile() && $device === 'mobile' ) {
                    $show = false;
                }
            }

            /* Page visibility (only if configured) */
            if ( $show && $mode && $current_id && is_array( $pages ) ) {
                $in_list = in_array( $current_id, $pages, true );

                if ( $mode === 'show' && ! $in_list ) {
                    $show = false;
                } elseif ( $mode === 'hide' && $in_list ) {
                    $show = false;
                }
            }

            if ( $show ) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * Admin UI fields
     */
    public function add_custom_fields( $item_id, $item, $depth, $args ) {

        $state  = get_post_meta( $item_id, '_menu_item_mvc_state', true );
        $roles  = get_post_meta( $item_id, '_menu_item_mvc_roles', true );
        $device = get_post_meta( $item_id, '_menu_item_mvc_device', true );
        $mode   = get_post_meta( $item_id, '_menu_item_mvc_page_mode', true );
        $pages  = get_post_meta( $item_id, '_menu_item_mvc_pages', true );

        $pages = is_array( $pages ) ? $pages : [];

        wp_nonce_field( 'mvc_nonce_action', 'mvc_nonce' );
        ?>

        <p class="description description-wide">
            <strong><?php esc_html_e( 'Visibility', 'menu-visibility-control' ); ?></strong><br>
            <select class="widefat mvc-state" name="menu-item-mvc-state[<?php echo esc_attr( $item_id ); ?>]">
                <option value="everyone" <?php selected( $state, 'everyone' ); ?>>Everyone</option>
                <option value="logged_in" <?php selected( $state, 'logged_in' ); ?>>Logged In</option>
                <option value="logged_out" <?php selected( $state, 'logged_out' ); ?>>Logged Out</option>
                <option value="roles" <?php selected( $state, 'roles' ); ?>>User Roles</option>
            </select>
        </p>

        <p class="description description-wide mvc-roles-wrap">
            <strong><?php esc_html_e( 'User Roles', 'menu-visibility-control' ); ?></strong><br>
            <?php
            global $wp_roles;
            foreach ( $wp_roles->roles as $role_key => $role ) :
            ?>
                <label style="margin-right:10px;">
                    <input type="checkbox"
                        name="menu-item-mvc-roles[<?php echo esc_attr( $item_id ); ?>][]"
                        value="<?php echo esc_attr( $role_key ); ?>"
                        <?php checked( in_array( $role_key, (array) $roles, true ) ); ?>>
                    <?php echo esc_html( $role['name'] ); ?>
                </label>
            <?php endforeach; ?>
        </p>

        <p class="description description-wide">
            <strong><?php esc_html_e( 'Device Visibility', 'menu-visibility-control' ); ?></strong><br>
            <select class="widefat" name="menu-item-mvc-device[<?php echo esc_attr( $item_id ); ?>]">
                <option value="">All Devices</option>
                <option value="desktop" <?php selected( $device, 'desktop' ); ?>>Desktop Only</option>
                <option value="mobile" <?php selected( $device, 'mobile' ); ?>>Mobile Only</option>
            </select>
        </p>

        <p class="description description-wide">
            <strong><?php esc_html_e( 'Page Visibility', 'menu-visibility-control' ); ?></strong><br>
            <select class="widefat mvc-page-mode" name="menu-item-mvc-page-mode[<?php echo esc_attr( $item_id ); ?>]">
                <option value="">All Pages</option>
                <option value="show" <?php selected( $mode, 'show' ); ?>>Show only on selected pages</option>
                <option value="hide" <?php selected( $mode, 'hide' ); ?>>Hide on selected pages</option>
            </select>

            <div class="mvc-pages-wrap" style="margin-top:6px; max-height:120px; overflow:auto; border:1px solid #ccd0d4; padding:6px;">
                <?php
                foreach ( get_pages() as $page ) :
                ?>
                    <label style="display:block;">
                        <input type="checkbox"
                            name="menu-item-mvc-pages[<?php echo esc_attr( $item_id ); ?>][]"
                            value="<?php echo esc_attr( $page->ID ); ?>"
                            <?php checked( in_array( $page->ID, $pages, true ) ); ?>>
                        <?php echo esc_html( $page->post_title ); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </p>

        <?php
    }

    /**
     * Save fields
     */
    public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {

        if (
            ! isset( $_POST['mvc_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mvc_nonce'] ) ), 'mvc_nonce_action' )
        ) {
            return;
        }

        $map = [
            '_menu_item_mvc_state'     => 'menu-item-mvc-state',
            '_menu_item_mvc_device'    => 'menu-item-mvc-device',
            '_menu_item_mvc_page_mode' => 'menu-item-mvc-page-mode',
        ];

        foreach ( $map as $meta => $key ) {
            if ( isset( $_POST[ $key ][ $menu_item_db_id ] ) ) {
                update_post_meta(
                    $menu_item_db_id,
                    $meta,
                    sanitize_text_field( wp_unslash( $_POST[ $key ][ $menu_item_db_id ] ) )
                );
            }
        }

        if ( isset( $_POST['menu-item-mvc-roles'][ $menu_item_db_id ] ) ) {
            update_post_meta(
                $menu_item_db_id,
                '_menu_item_mvc_roles',
                array_map( 'sanitize_text_field', wp_unslash( $_POST['menu-item-mvc-roles'][ $menu_item_db_id ] ) )
            );
        }

        if ( isset( $_POST['menu-item-mvc-pages'][ $menu_item_db_id ] ) ) {
            update_post_meta(
                $menu_item_db_id,
                '_menu_item_mvc_pages',
                array_map( 'absint', wp_unslash( $_POST['menu-item-mvc-pages'][ $menu_item_db_id ] ) )
            );
        }
    }

    /**
     * Admin inline JS (auto-hide UI)
     */
    public function admin_inline_scripts() {
        ?>
        <script>
        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('mvc-state')) {
                const wrap = e.target.closest('.menu-item-settings');
                wrap.querySelector('.mvc-roles-wrap').style.display =
                    (e.target.value === 'roles') ? 'block' : 'none';
            }

            if (e.target.classList.contains('mvc-page-mode')) {
                const wrap = e.target.closest('.menu-item-settings');
                wrap.querySelector('.mvc-pages-wrap').style.display =
                    (e.target.value === '') ? 'none' : 'block';
            }
        });

        document.querySelectorAll('.menu-item-settings').forEach(function(wrap){
            const state = wrap.querySelector('.mvc-state');
            const mode  = wrap.querySelector('.mvc-page-mode');
            if (state) {
                wrap.querySelector('.mvc-roles-wrap').style.display =
                    (state.value === 'roles') ? 'block' : 'none';
            }
            if (mode) {
                wrap.querySelector('.mvc-pages-wrap').style.display =
                    (mode.value === '') ? 'none' : 'block';
            }
        });
        </script>
        <?php
    }
}

new Menu_Visibility_Control();

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/admin-notice.php';
}
