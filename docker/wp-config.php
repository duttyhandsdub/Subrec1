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
define('DB_NAME', getenv('RDS_DB_NAME'));

/** MySQL database username */
define('DB_USER', getenv('RDS_USERNAME'));

/** MySQL database password */
define('DB_PASSWORD', getenv('RDS_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', getenv('RDS_HOSTNAME').':'.getenv('RDS_PORT'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|%@0qw$!}VJ{9xG|nq4|=-~nw#%]T<-ne))>[-C~/ey!rL$vX+n^]JeHcl3bj(+6');
define('SECURE_AUTH_KEY',  'O!vp$MZe|Y=FR]vsM<+|);It~jR;~8biFDmY|=}!Yz@P}K?hKn<AN|^>Q]eTC636');
define('LOGGED_IN_KEY',    'yr-]:+n[!_ZSi2_ftDZx9|%GDYBH~#eKP@kqO}#t5Mq|1h$KA-|~j-6gwg+=aIUT');
define('NONCE_KEY',        ':lt`vJH:QB(y>uF]Y)D.-RP+[Ca|_Di-Q:S3vK|9n/zC$<Rmk.gpeU(+!Y1FHG`D');
define('AUTH_SALT',        '9.^$}S ,>NS&F+5O2,3y-!@rx0Cq]q&@KouD+vj/Iwxz-0EWWpA(+J*_^EaNovk9');
define('SECURE_AUTH_SALT', 'k-ct$%`u(1L?U}j{Qr^iNU&]{o#Z(lE`eC=2~8RjAz<+eBxJ||qI6k@A%/MQ<X!&');
define('LOGGED_IN_SALT',   'q$!=IM$m{oISZb{c:(w3bBSZx`+$q^)qb).jhhyej_6#$7b@knk7+vUJ5z$G-~7U');
define('NONCE_SALT',       'h|fpOgM)xj coTKc]8P!G)|cCcX!D-f9:.+xZWcI+`w3G8|+(+r %|r^*p[7pCFW');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'rv_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

/* domain for permalinks rewrite */
define('WP_DOMAIN', getenv('SITE_URL'));

/* disable autoupdates */
define( 'AUTOMATIC_UPDATER_DISABLED', true );

/* S3 Offload */
define( 'DBI_AWS_ACCESS_KEY_ID', getenv('S3_ACCESS_KEY_ID'));
define( 'DBI_AWS_SECRET_ACCESS_KEY', getenv('S3_SECRET_ACCESS_KEY'));