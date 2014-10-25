<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'bearded_buffalo_wp');

/** MySQL database username */
define('DB_USER', 'bearded');

/** MySQL database password */
define('DB_PASSWORD', 'beardedbuffalo');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '{~jvD/SrF*=<~9 U{WM!I.8fGvMX[a~}fN22~QR{kz#&QI;6=n>ZK05SWz`.w$06');
define('SECURE_AUTH_KEY',  '%lo^F4l`3H]#jqqz_!EE`]W!Pt{p)X~^H<].mC-0M9AFL!|:]jG:X)FEtnXH*Gs3');
define('LOGGED_IN_KEY',    'O6dc>Lw`ys4NJB)mK^NQi-tC;s%bYI1&-S:p0&dbNsmo,[Fj_F`o1UCly|FE1XfQ');
define('NONCE_KEY',        '&A[ Dg(sPGyq_{:W(Pk00jJ=Cg[kL yCa@6:pALXeC2S-}N!u(`+ay-Hpa4Jv{V~');
define('AUTH_SALT',        '&/y?U~&ke]eC8k;o5o5%lH@?WyG ^27J6,H7JFac&qoSIyfbK GlL^|%V+=UY$fe');
define('SECURE_AUTH_SALT', '9-qr-#]BM`s4m@.i&9XIZJ`qO0Zb=DsIH+.PNu^)cQ_rhaLP;C]Gh#,]b<S/&^S(');
define('LOGGED_IN_SALT',   '@e)hC8?,/=f4aDJeS7NXk!x%+Ea?&+=.c#[xq2qJwwI3h%O_%7wkwDr6)SL9%v|q');
define('NONCE_SALT',       'IOpS+Q<2^&-|>Z5Dsv h;H@e.40z@[9@Zo+&}h8^NP~|Nf!ucAZj`~@^5Rn@ktQF');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
