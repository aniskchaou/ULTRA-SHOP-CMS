<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ultra-shop' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'cZaNer+ J?7Yd$P0Fj~n8^&6F6vh%7ogBeDQPA5$PNOpTn,%vzXjZN=!H;tr-9wX' );
define( 'SECURE_AUTH_KEY',  'n5bJ50g7K.s0|5lVG):Z^gE.f{(bH<T1g#~10eCp!hab-cO.-K6)t *jDsaR@>Yy' );
define( 'LOGGED_IN_KEY',    'w66H}BNk}am+=:X?44dU{/?#7lqlL`R$q4_H0_DI/Ht0<X(?7{MAvjt:zHvUMgdR' );
define( 'NONCE_KEY',        '10vFu $%TsSM%!mTw4(hxuuT/W5!Tf+N)b)Y{}<&iwIJ<v?Ge=f|%f.8M=,B ai#' );
define( 'AUTH_SALT',        'x!Y_C.-(x4y,u>&C%+R38UMB#7Zi}L[)_Ct75s?Bgf<50TnC}Y(i*j`g:qp1vQ]@' );
define( 'SECURE_AUTH_SALT', '`!wpsYREZ[7)R;IS3+v#>N~5Ebff}4W/(lv|`b8X//Qbg:s.2;}^5n)2/Hz95>|m' );
define( 'LOGGED_IN_SALT',   '}gtFY`x$< &0x|I~qF^~5pyiY.t Gzf*[x^nA+MrnKs|J2)Kz9`P?&OxIw):7=i@' );
define( 'NONCE_SALT',       'np8FM,4!;,NDAp?a}$Yh/ZAR_;TKyibXT@`uqPuW|pU[Nq`MaYv-?D}?Vv/B5W4]' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
