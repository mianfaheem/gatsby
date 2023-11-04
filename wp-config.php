<?php
define( 'WP_CACHE', true );


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
define( 'DB_NAME', 'lizahjva_gb' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '123' );

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
define( 'AUTH_KEY',         'AqvHnJ}p A-wWP1e=v Z[f<;v;BF4DDDMT>C0 uHSnK)Km5~Lt2cY<5 CRIW`vB;' );
define( 'SECURE_AUTH_KEY',  '417JSe0ySda;a&+jz!iM^#@2M:JVs[9Fy/lgURGHYW1AiP :KQT*x)Rpt`kA Jy?' );
define( 'LOGGED_IN_KEY',    'I?=P_>f 3 dxnPjF )?Gj_7:@v*1?ZnE `E$^y>*C100#1yAlq?N[Cuaw3Q)vU#e' );
define( 'NONCE_KEY',        '=tq!d}7,9QIK+F]_SyM=j~Z|6,(tr71~k}.jE)hF<Z=:,5yLg73EaSNO1^1jsO]2' );
define( 'AUTH_SALT',        '!ezl8!V~`m:6Fgf)b@d%YZxl.ovr|A*:UXkAbS.mu^>YI$Cz_b_28=os?h(YLo~y' );
define( 'SECURE_AUTH_SALT', 'f]H48~,n+pl?`GrXyE=,03<ipTRh?!SGUr;UH*XpE<i;l%Tht,H4Jrh%S SK{!;P' );
define( 'LOGGED_IN_SALT',   '<7bO-+_Tix:bpkJvDCjW6#K[g/)[<+UsY61Nx*L24OrPk Pe?^N4J,8%gl XR]4?' );
define( 'NONCE_SALT',       '}}ky/U|=E~gBjW}Mu5B,<hKI9KCh5]!>FKF: r|RGRAPHQL_JWT_AUTH_SECRET_KEY', '&O^%<C|JY5@~_4KADR<1#]biZTiOy}uv#eLrM=*JU_aMA-@mxGw?=*u*aH.N&iE_' );
define('JWT_AUTH_CORS_ENABLE', true);
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
