<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'shroo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'KHv8W6VBgC)7@a(L4L+{flND]~:vE6FWk_7Hw+=pIc]MIS[6&B$C0>$rDR%82lms' );
define( 'SECURE_AUTH_KEY',  'yxo_-,=qd(HCGF kK?Bh](~TdybX2q3@-i_++lg8a>LQRrovUxz9?=w4l7o!N4yN' );
define( 'LOGGED_IN_KEY',    'vd^ZEy+p!<n hU`pi/ 3MS8D_$(LX%|X5g;&:5`xBJ~E-_l6Mt,;@[0o[`B-~va$' );
define( 'NONCE_KEY',        '4F|ZU`:@Azj|4-|^7~tcKikX{+w#O^+dp]&fPX~nMOoQ#d^aa]R-UzYhQeor8wB;' );
define( 'AUTH_SALT',        'M0?b6?:cDrN_C9#WdW+LW?D,vbGV<-!q@t}:/ft)G+I_#)9$b5l2F%)M,pgUx|uu' );
define( 'SECURE_AUTH_SALT', 'i[oq$[-PdICpRH>wIu7[]+nOayB6Q(@K{IW{5([T]/jfV||0>FsCe82{`LxR#q^z' );
define( 'LOGGED_IN_SALT',   'BhHk/_p{5)COc1[9s|JfL0i$oLDm6L}@z7^?<,$U.Y}Iy8~:`3O7*LuYsW1qd|UH' );
define( 'NONCE_SALT',       'HDq/!]kx$TspxAi:$G8rGj/z2C4}jW/~pc:q-g)w^xQhs(rn}wV;2H4E^6PlMl7T' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
