<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
// load .env file to getenv
if(!defined('ENV_FILE')) {
    define('ENV_FILE', BASE_PATH.'/.env');
    if(file_exists(ENV_FILE))
        foreach(file(ENV_FILE) as $ec) {
            $ec = trim($ec); if(0<strlen($ec)) putenv($ec);
        }
}

return new \Phalcon\Config([
    'database' => [
        'adapter' => ucfirst(getenv('DATABASE_DRIVER', 'sqlite')),
        'dbname' => getenv('DATABASE_SCHEMA', './database.sqlite'),
        
        'host'        => getenv('DATABASE_HOST'),
        'port'        => getenv('DATABASE_PORT'),
        'username'    => getenv('DATABASE_USER'),
        'password'    => getenv('DATABASE_PASS'),
        'charset'     => getenv('DATABASE_ENCODING'),
    ],
    'application' => [
        'host'       => getenv('APP_HOST'),
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ],
    'services' => [
        'facebook' => [
            'api_host' => getenv('FACEBOOK_API_HOST', 'https://graph.facebook.com/v3.3'),
            'client_id' => getenv('FACEBOOK_CLIENT_ID'),
            'client_secret' => getenv('FACEBOOK_CLIENT_SECRET'),
            'app_access' => getenv('FACEBOOK_APP_ACCESS'),
        ],
        'google' => [
            'api_host' => getenv('GOOGLE_API_HOST', 'https://www.googleapis.com'),
            'client_id' => getenv('GOOGLE_CLIENT_ID'),
            'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
            'dev_token' => getenv('GOOGLE_DEV_TOKEN'),
            'scopes' => getenv('GOOGLE_SCOPES'),
        ],
        'kakao' => [
            'api_host' => getenv('KAKAO_API_HOST'),
            'client_id' => getenv('KAKAO_CLIENT_ID'),
            'client_secret' => getenv('KAKAO_CLIENT_SECRET'),
        ],
        'naver' => [
            'api_host' => getenv('NAVER_API_HOST', 'https://nid.naver.com'),
            'client_id' => getenv('NAVER_CLIENT_ID'),
            'client_secret' => getenv('NAVER_CLIENT_SECRET'),
        ],
    ],
]);
