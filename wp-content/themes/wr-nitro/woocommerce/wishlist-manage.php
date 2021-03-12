<?php
/**
 * Wishlist manage template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.5
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'manage' ) ) ?>" method="post">
    <!-- TITLE -->
    <?php
    do_action( 'yith_wcwl_before_wishlist_title' );

    if( ! empty( $page_title ) ) {
        echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . $page_title . '</h2>' );
    }

    do_action( 'yith_wcwl_before_wishlist_manage' );
    ?>

    <table class="shop_table cart wishlist_table wishlist_manage_table" cellspacing="0" >
        <thead>
            <tr>
                <th class="wishlist-name">
                    <span class="nobr">
                        <?php echo apply_filters( 'yith_wcwl_wishlist_manage_name_heading', __( 'Wishlists', 'wr-nitro' ) ) ?>
                    </span>
                </th>
                <th class="wishlist-privacy">
                    <span class="nobr">
                        <?php echo apply_filters( 'yith_wcwl_wishlist_manage_privacy_heading', __( 'Privacy', 'wr-nitro' ) ) ?>
                    </span>
                </th>
                <th class="wishlist-delete">
                    <span class="nobr">
                        <?php echo apply_filters( 'yith_wcwl_wishlist_manage_delete_heading', __( 'Delete', 'wr-nitro' ) ) ?>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="wishlist-name">
                    <a title="<?php echo esc_attr( $default_wishlist_title ) ?>" class="wishlist-anchor" href="<?php echo esc_attr( YITH_WCWL()->get_wishlist_url( 'user' . '/' . $current_user_id ) ) ?>">
                        <?php echo esc_html( $default_wishlist_title ) ?>
                    </a>
                </td>
                <td class="wishlist-privacy">
                    <?php echo apply_filters( 'yith_wcwl_wishlist_manage_default_privacy', __( 'Public', 'wr-nitro' ) )?>
                </td>
                <td class="wishlist-delete"></td>
            </tr>
            <?php
                $wishlist_count = 0;
                if( ! empty( $user_wishlists ) ):
            ?>
                <?php foreach( $user_wishlists as $wishlist ): ?>
                    <?php if( ! $wishlist['is_default'] ): ?>
                    <tr>
                        <td class="wishlist-name">
                            <a title="<?php echo esc_attr( $wishlist['wishlist_name'] ) ?>" class="wishlist-anchor" href="<?php echo esc_attr( YITH_WCWL()->get_wishlist_url( 'view' . '/' . $wishlist['wishlist_token'] ) ) ?>">
                                <?php echo esc_html( $wishlist['wishlist_name'] ) ?>
                            </a>
                        </td>
                        <td class="wishlist-privacy">
                            <select name="wishlist_options[<?php echo esc_attr( $wishlist['ID'] ) ?>][wishlist_privacy]" class="wishlist-visibility selectBox">
                                <option value="0" class="public-visibility" <?php selected( $wishlist['wishlist_privacy'], 0 ) ?> ><?php echo apply_filters( 'yith_wcwl_public_wishlist_visibility', __( 'Public', 'wr-nitro' ) )?></option>
                                <option value="1" class="shared-visibility" <?php selected( $wishlist['wishlist_privacy'], 1 ) ?> ><?php echo apply_filters( 'yith_wcwl_shared_wishlist_visibility', __( 'Shared', 'wr-nitro' ) )?></option>
                                <option value="2" class="private-visibility" <?php selected( $wishlist['wishlist_privacy'], 2 ) ?> ><?php echo apply_filters( 'yith_wcwl_private_wishlist_visibility', __( 'Private', 'wr-nitro' ) )?></option>
                            </select>
                        </td>
                        <td class="wishlist-delete">
                            <a class="button" href="<?php echo esc_url( add_query_arg( 'wishlist_id', $wishlist['ID'], wp_nonce_url( YITH_WCWL()->get_wishlist_url( 'manage' ), 'yith_wcwl_delete_action', 'yith_wcwl_delete' ) ) ) ?>"><?php _e( 'Delete', 'wr-nitro' ) ?></a>
                        </td>
                    </tr>
                    <?php
		                $wishlist_count ++;
	                    endif;
	                ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <a class="button create-new-wishlist" href="<?php echo YITH_WCWL()->get_wishlist_url( 'create' ) ?>">
                        <?php echo apply_filters( 'yith_wcwl_create_new_wishlist_icon', '<i class="icon-plus"></i>' )?>
                        <?php _e( 'Create a new wishlist', 'wr-nitro' )?>
                    </a>
                    <?php if( ! empty( $user_wishlists ) && $wishlist_count ): ?>
                        <button class="submit-wishlist-changes button">
                            <?php echo apply_filters( 'yith_wcwl_mange_wishlist_icon', '<i class="icon-ok"></i>' )?>
                            <?php _e( 'Save Settings', 'wr-nitro' ) ?>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <?php wp_nonce_field( 'yith_wcwl_manage_action', 'yith_wcwl_manage' )?>

    <?php do_action( 'yith_wcwl_after_wishlist_manage' ); ?>
</form>
