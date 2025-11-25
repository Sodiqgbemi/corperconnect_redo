<?php

/*
 * SMTP Debug.
 */
define('SMTP_DEBUG', false);
/*
 * Mail Encoding charset
 */
define('CHARSET', 'UTF-8');

/*
 * Mail Host
 */
define('MAIL_HOST', 'smtp.gmail.com');

/*
 * Mail Serve Port
 */
define('MAIL_PORT', 587);

/*
 * Use SMTP Authentication
 */
define('SMTP_AUTH', true);

/*
 * SMTP Authentication Type
 */
define('SMTP_AUTHTYPE', 'XOAUTH2');

/*
 * SMTP secure protocol
 */
define('SMTP_SECURE', 'tls');

/*
 * Oauth Authentication Email
 */
define('OAUTH_USEREMAIL', '***@gmail.com');

/*
 * Oauth Authentication Client ID
 */
define('OAUTH_CLIENTID', '***************');

/*
 * Oauth Authentication Client Secret
 */
define('OAUTH_CLIENT_SECRET', '************');

/*
 * Oauth Authentication Refresh Token
 */
define('OAUTH_REFRESH_TOKEN', '*********************');

/*
 * Mail Authentication Username
 */
define('MAIL_USERNAME', 'fresher.dev01@gmail.com');

/*
 * Mail From Email
 */
define('MAIL_FROMEMAIL', 'fresher.dev01@gmail.com');
// define('MAIL_FROMEMAIL', 'admin@topupdrive.com');


/*
 * Mail Reply To Email
 */
define('MAIL_REPLY_TO', 'noreply@topupdrive.com');

/*
 * Mail From Name
 */
define('MAIL_FROMNAME', 'Topup Drive');

/*
 * Mail Authentication Password
 */
define('MAIL_PASSWORD', 'D3vfr3$#3r');

/*
 * Use HTML Content
 */
define('HTML_BODY', true);

/*
 * Keep SMTP Connection Alive
 */
define('SMTP_ALIVE', true);

date_default_timezone_set("Africa/Lagos");

?>