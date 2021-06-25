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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tatrooms_db' );

/** MySQL database username */
define( 'DB_USER', 'tatrooms_user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'naantam@123' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'SAVEQUERIES', true );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '>qL/jc2,)`Nc]v.E!@pz]CN!~!9i`P50|}Rih_xU/joQ)IVa<$MzbKu9cAjqz>J%' );
define( 'SECURE_AUTH_KEY',  'iaS]kHy:E+6<Ln#<.dX};r>x3y[N$Px8(v7g%aGrZI^+Gw$sH+7a nKjAi SPqej' );
define( 'LOGGED_IN_KEY',    'M=ss|BJ)}k7/1=c; +sCv7X;9?`4h;jw,o#ms @s0M}NO}BW.;qvs53L}&Pa SQD' );
define( 'NONCE_KEY',        '?*W8e>V-~WG!36aYF2-uNg3k|<Xq%BHu$1;L?B* WTR-mJ<_Ll*kBzJ xOdH(fhv' );
define( 'AUTH_SALT',        '/dQFS8E&AMY=U:UonA:*R>=z;n&_P3?XHZR)<YL2Om((@,,c_jVK<IBa@X,#%{ <' );
define( 'SECURE_AUTH_SALT', '=8M!DP6?>v&#V(<k { D#=U&+J&4da,)K(~ @$)R.AP&i3-74!Y}8a-Q*PUz]$dC' );
define( 'LOGGED_IN_SALT',   'i1l8M&)dHv5$V(Mi*Y}*k`pUb*#@ziU.p_+QaGEM@<7V?{a)+jRe*uWdb5wWlBY&' );
define( 'NONCE_SALT',       '&#%0PX&QjS>EkC-wL<P{7](_+8RNCp1um$PDH3$ #x`2;Lw5$,(VvZ`sGPgt*6]S' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
