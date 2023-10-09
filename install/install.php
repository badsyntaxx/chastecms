<?php 

/**
 * 
 */
class Install
{  
    private $mysqli;
    private $data;

    public function __construct()
    {
        error_reporting(0);
        ini_set('display_errors', 'Off');

        if (!empty($_POST)) {
            $this->processPost();
        }
    }

    private function processPost()
    {
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }  else {
            $username = '';
        }
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
        } else {
            $email = '';
        }
        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        } else {
            $password = '';
        }
        if (isset($_POST['confirm'])) {
            $confirm = $_POST['confirm'];
        } else {
            $confirm = '';
        }
        if (isset($_POST['host'])) {
            $host = $_POST['host'];
        } else {
            $host = '';
        }
        if (isset($_POST['db'])) {
            $db = $_POST['db'];
        } else {
            $db = '';
        }
        if (isset($_POST['db_username'])) {
            $db_username = $_POST['db_username'];
        } else {
            $db_username = '';
        }
        if (isset($_POST['db_password'])) {
            $db_password = $_POST['db_password'];
        } else {
            $db_password = '';
        }
        
        $key = md5(mt_rand());
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date('Y-m-d g:i:s', time());
        $config = $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

        if ($password !== $confirm) {
            exit('Password match error!');
        } 

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

        if (file_exists($config)) {
            if (!defined('HOSTNAME')) {
                $file = fopen($config, 'a+') or exit('Unable to open config file!');
                fwrite($file, PHP_EOL . '// Database' . PHP_EOL);
                fwrite($file, 'define(\'HOSTNAME\', \'' . $host . '\');' . PHP_EOL);
                fwrite($file, 'define(\'DATABASE\', \'' . $db . '\');' . PHP_EOL);
                fwrite($file, 'define(\'USERNAME\', \'' . $db_username . '\');' . PHP_EOL);
                fwrite($file, 'define(\'PASSWORD\', \'' . $db_password . '\');');
                fclose($file);
            }
        }

        $this->data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'key' => $key,
            'ip' => $ip,
            'time' => $time,
            'host' => $host,
            'db' => $db,
            'db_username' => $db_username,
            'db_password' => $db_password
        ];

        $this->mysqli = new mysqli($this->data['host'], $this->data['db_username'], $this->data['db_password'], $this->data['db']);

        if ($this->mysqli->connect_error) {
            $output = ['alert' => 'error', 'message' => $this->mysqli->connect_error . '.'];
            exit(json_encode($output));
        }

        $this->populateDatabase();
    }

    /**
     * Verify Table
     *
     * Check the database for a specific table. If the table does not exist, create the table.
     * @param string $table
     * @return boolean
     */
    private function confirmTable($query, $table) 
    {
        if ($query) {
            $this->log('SUCCESS! (' . $table . ') table created.');
        } else {
            $output = ['alert' => 'error', 'message' => $this->mysqli->error . '.'];
            exit(json_encode($output));
        }
    }

    /**
     * Confirm Row
     *
     * Check the database for a specific table row. If the row does not exist, insert the row.
     * @param string $table
     * @param string $column     
     * @param mixed $data   
     * @param mixed $values             
     */
    private function confirmRow($table, $id, $values)
    {
        $row = $this->mysqli->query('SELECT * FROM `' . $table . '` WHERE `' . $table . '_id` = ' . $id);
        if ($row->num_rows <= 0) {
            $query = $this->mysqli->query('INSERT INTO `' . $table . '` VALUES (' . $values . ')');
            if ($query) {
                $this->log('SUCCESS! New row inserted into ' . $table . '.');
            } else {
                $output = ['alert' => 'error', 'message' => $this->mysqli->error . '.'];
                exit(json_encode($output));
            }
        }
    }

    private function createAnalyticsTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `analytics` (
                `analytics_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `code` text CHARACTER SET utf8
            )'
        );
        $this->confirmTable($query, 'analytics');
    }

    private function createBlogTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `blog` (
                `blog_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `author` int(11) NOT NULL,
                `title` varchar(255) CHARACTER SET utf8 NOT NULL,
                `body` text CHARACTER SET utf8,
                `preview_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `views` int(11) DEFAULT 0,
                `last_view` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `last_edit` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `post_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL
            )'
        );
        $this->confirmTable($query, 'blog');
    }

    private function createCountryTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `countries` (
                `countries_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `code` varchar(2) CHARACTER SET utf8 NULL,
                `name` varchar(255) CHARACTER SET utf8 NULL
            )'
        );
        $this->confirmTable($query, 'countries');
    }

    private function createErrorsTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `errors` (
                `errors_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `time` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
                `event` text CHARACTER SET utf8
            )'
        );
        $this->confirmTable($query, 'errors');
    }

    private function createLanguageTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `language` (
                `language_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `language` varchar(255) CHARACTER SET utf8 DEFAULT NULL
            )'
        );
        $this->confirmTable($query, 'language');
    }

    private function createLogTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `log` (
                `log_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `time` varchar(255) CHARACTER SET utf8,
                `event` text CHARACTER SET utf8
            )'
        );
        $this->confirmTable($query, 'log');
    }

    private function createLoginTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `logins` (
                `logins_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `email` varchar(255) CHARACTER SET utf8 NOT NULL,
                `ip` varchar(255) CHARACTER SET utf8 NOT NULL,
                `lock_time` varchar(255) CHARACTER SET utf8 NOT NULL,
                `attempts` int(2) NOT NULL
            )'
        );
        $this->confirmTable($query, 'logins');
    }

    private function createMailTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `mail` (
                `mail_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `host` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `port` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `username` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL
            )'
        );
        $this->confirmTable($query, 'mail');
    }

    private function createMenuTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `menus` (
                `menu_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `menu_anchor` int(11) NOT NULL,
                `main_menu` boolean DEFAULT 0
            )'
        );
        $this->confirmTable($query, 'countries');
    }

    private function createMessagesTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `messages` (
                `messages_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `chain_id` int(11) NOT NULL,
                `subject` varchar(255) CHARACTER SET utf8 NOT NULL,
                `sender` int(11) NOT NULL,
                `receiver` int(11) NOT NULL,
                `message` text CHARACTER SET utf8,
                `timestamp` varchar(255) CHARACTER SET utf8 NOT NULL,
                `viewed` int(11) NOT NULL
            )'
        );
        $this->confirmTable($query, 'messages');
    }

    private function createNavigationTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `navigation` (
                `navigation_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `nav_anchor` int(11) NOT NULL,
                `nav_name` varchar(255) CHARACTER SET utf8 NOT NULL,
                `sort_order` int(11) NOT NULL,
                `parent` int(11) DEFAULT 0,
                `children` boolean DEFAULT 0,
                `top` boolean DEFAULT 1,
                `bottom` boolean DEFAULT 0
            )'
        );
        $this->confirmTable($query, 'navigation');
    }

    private function createPagesTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `pages` (
                `pages_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `controller_file` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `content` text CHARACTER SET utf8,
                `views` int(11) DEFAULT 0,
                `last_view` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `last_edit` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `creation_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `locked` int(1) DEFAULT NULL
            )'
        );
        $this->confirmTable($query, 'pages');
    }

    private function createResetTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `resets` (
                `resets_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `email` varchar(255) CHARACTER SET utf8 NOT NULL,
                `token` varchar(255) CHARACTER SET utf8 NOT NULL,
                `creation_date` varchar(255) CHARACTER SET utf8 NOT NULL
            )'
        );
        $this->confirmTable($query, 'resets');
    }

    private function createRobotsTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `robots` (
                `robots_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `route` varchar(255) CHARACTER SET utf8 NOT NULL
            )'
        );
        $this->confirmTable($query, 'robots');
    }

    private function createRoutesTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `routes` (
                `routes_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `route_anchor` int(11) NOT NULL,
                `route` varchar(255) CHARACTER SET utf8 NOT NULL
            )'
        );
        $this->confirmTable($query, 'routes');
    }

    private function createUsersTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `users` (
                `users_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `key` varchar(255) CHARACTER SET utf8 NOT NULL,
                `group` boolean NOT NULL DEFAULT 0,
                `firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `lastname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `username` varchar(255) CHARACTER SET utf8 NOT NULL,
                `email` varchar(255) CHARACTER SET utf8 NOT NULL,
                `password` varchar(255) CHARACTER SET utf8 NOT NULL,
                `website` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `birthday` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
                `country` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `gender` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `privacy` boolean NOT NULL DEFAULT 0,
                `avatar` varchar(255) CHARACTER SET utf8 DEFAULT "default_avatar.png" NOT NULL,
                `bio` text CHARACTER SET utf8,
                `signup_date` varchar(255) CHARACTER SET utf8 NOT NULL,
                `last_active` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `ip` varchar(255) CHARACTER SET utf8 NOT NULL,
                `verify_logout` boolean NOT NULL DEFAULT 1,
                `theme` boolean NOT NULL DEFAULT 0
            )'
        );
        $this->confirmTable($query, 'users');
    }

    private function createSitemapTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `sitemap` (
                `sitemap_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `route_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `route` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `sort_order` int(11) NOT NULL,
                `visibility` boolean NOT NULL DEFAULT 0
            )'
        );
        $this->confirmTable($query, 'sitemap');
    }

    private function createSettingsTable()
    {
        $query = $this->mysqli->query(
            'CREATE TABLE `settings` (
                `settings_id` int(11) NOT NULL auto_increment PRIMARY KEY,
                `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                `value` varchar(255) CHARACTER SET utf8 DEFAULT NULL
            )'
        );
        $this->confirmTable($query, 'settings');
    }

    private function log($message)
    {
        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/cache/installed.txt', 'a+') or exit('Unable to open file!');
        fwrite($file, $message . PHP_EOL);
    }

    private function populateDatabase()
    {
        // Create Tables
        $this->createAnalyticsTable();
        $this->createBlogTable();
        $this->createCountryTable();
        $this->createErrorsTable();
        $this->createLanguageTable();
        $this->createLogTable();
        $this->createLoginTable();
        $this->createMailTable();
        $this->createMenuTable();
        $this->createMessagesTable();
        $this->createNavigationTable();
        $this->createPagesTable();
        $this->createResetTable();
        $this->createRobotsTable();
        $this->createRoutesTable();
        $this->createUsersTable();
        $this->createSitemapTable();
        $this->createSettingsTable();

        // Confirm Analytics rows
        $this->confirmRow('analytics', 1, 'NULL, ""');

        // Confirm Countries rows
        $num = 1;
        $countries = ['AF' => 'Afghanistan','AX' => 'Aland Islands','AL' => 'Albania','DZ' => 'Algeria','AS' => 'American Samoa','AD' => 'Andorra','AO' => 'Angola','AI' => 'Anguilla','AQ' => 'Antarctica','AG' => 'Antigua And Barbuda','AR' => 'Argentina','AM' => 'Armenia','AW' => 'Aruba','AU' => 'Australia','AT' => 'Austria','AZ' => 'Azerbaijan','BS' => 'Bahamas','BH' => 'Bahrain','BD' => 'Bangladesh','BB' => 'Barbados','BY' => 'Belarus','BE' => 'Belgium','BZ' => 'Belize','BJ' => 'Benin','BM' => 'Bermuda','BT' => 'Bhutan','BO' => 'Bolivia','BA' => 'Bosnia And Herzegovina','BW' => 'Botswana','BV' => 'Bouvet Island','BR' => 'Brazil','IO' => 'British Indian Ocean Territory','BN' => 'Brunei Darussalam','BG' => 'Bulgaria','BF' => 'Burkina Faso','BI' => 'Burundi','KH' => 'Cambodia','CM' => 'Cameroon','CA' => 'Canada','CV' => 'Cape Verde','KY' => 'Cayman Islands','CF' => 'Central African Republic','TD' => 'Chad','CL' => 'Chile','CN' => 'China','CX' => 'Christmas Island','CC' => 'Cocos (Keeling) Islands','CO' => 'Colombia','KM' => 'Comoros','CG' => 'Congo','CD' => 'Congo, Democratic Republic','CK' => 'Cook Islands','CR' => 'Costa Rica','CI' => 'Cote D\'Ivoire','HR' => 'Croatia','CU' => 'Cuba','CY' => 'Cyprus','CZ' => 'Czech Republic','DK' => 'Denmark','DJ' => 'Djibouti','DM' => 'Dominica','DO' => 'Dominican Republic','EC' => 'Ecuador','EG' => 'Egypt','SV' => 'El Salvador','GQ' => 'Equatorial Guinea','ER' => 'Eritrea','EE' => 'Estonia','ET' => 'Ethiopia','FK' => 'Falkland Islands (Malvinas)','FO' => 'Faroe Islands','FJ' => 'Fiji','FI' => 'Finland','FR' => 'France','GF' => 'French Guiana','PF' => 'French Polynesia','TF' => 'French Southern Territories','GA' => 'Gabon','GM' => 'Gambia','GE' => 'Georgia','DE' => 'Germany','GH' => 'Ghana','GI' => 'Gibraltar','GR' => 'Greece','GL' => 'Greenland','GD' => 'Grenada','GP' => 'Guadeloupe','GU' => 'Guam','GT' => 'Guatemala','GG' => 'Guernsey','GN' => 'Guinea','GW' => 'Guinea-Bissau','GY' => 'Guyana','HT' => 'Haiti','HM' => 'Heard Island & Mcdonald Islands','VA' => 'Holy See (Vatican City State)','HN' => 'Honduras','HK' => 'Hong Kong','HU' => 'Hungary','IS' => 'Iceland','IN' => 'India','ID' => 'Indonesia','IR' => 'Iran, Islamic Republic Of','IQ' => 'Iraq','IE' => 'Ireland','IM' => 'Isle Of Man','IL' => 'Israel','IT' => 'Italy','JM' => 'Jamaica','JP' => 'Japan','JE' => 'Jersey','JO' => 'Jordan','KZ' => 'Kazakhstan','KE' => 'Kenya','KI' => 'Kiribati','KR' => 'Korea','KW' => 'Kuwait','KG' => 'Kyrgyzstan','LA' => 'Lao People\'s Democratic Republic','LV' => 'Latvia','LB' => 'Lebanon','LS' => 'Lesotho','LR' => 'Liberia','LY' => 'Libyan Arab Jamahiriya','LI' => 'Liechtenstein','LT' => 'Lithuania','LU' => 'Luxembourg','MO' => 'Macao','MK' => 'Macedonia','MG' => 'Madagascar','MW' => 'Malawi','MY' => 'Malaysia','MV' => 'Maldives','ML' => 'Mali','MT' => 'Malta','MH' => 'Marshall Islands','MQ' => 'Martinique','MR' => 'Mauritania','MU' => 'Mauritius','YT' => 'Mayotte','MX' => 'Mexico','FM' => 'Micronesia, Federated States Of','MD' => 'Moldova','MC' => 'Monaco','MN' => 'Mongolia','ME' => 'Montenegro','MS' => 'Montserrat','MA' => 'Morocco','MZ' => 'Mozambique','MM' => 'Myanmar','NA' => 'Namibia','NR' => 'Nauru','NP' => 'Nepal','NL' => 'Netherlands','AN' => 'Netherlands Antilles','NC' => 'New Caledonia','NZ' => 'New Zealand','NI' => 'Nicaragua','NE' => 'Niger','NG' => 'Nigeria','NU' => 'Niue','NF' => 'Norfolk Island','MP' => 'Northern Mariana Islands','NO' => 'Norway','OM' => 'Oman','PK' => 'Pakistan','PW' => 'Palau','PS' => 'Palestinian Territory, Occupied','PA' => 'Panama','PG' => 'Papua New Guinea','PY' => 'Paraguay','PE' => 'Peru','PH' => 'Philippines','PN' => 'Pitcairn','PL' => 'Poland','PT' => 'Portugal','PR' => 'Puerto Rico','QA' => 'Qatar','RE' => 'Reunion','RO' => 'Romania','RU' => 'Russian Federation','RW' => 'Rwanda','BL' => 'Saint Barthelemy','SH' => 'Saint Helena','KN' => 'Saint Kitts And Nevis','LC' => 'Saint Lucia','MF' => 'Saint Martin','PM' => 'Saint Pierre And Miquelon','VC' => 'Saint Vincent And Grenadines','WS' => 'Samoa','SM' => 'San Marino','ST' => 'Sao Tome And Principe','SA' => 'Saudi Arabia','SN' => 'Senegal','RS' => 'Serbia','SC' => 'Seychelles','SL' => 'Sierra Leone','SG' => 'Singapore','SK' => 'Slovakia','SI' => 'Slovenia','SB' => 'Solomon Islands','SO' => 'Somalia','ZA' => 'South Africa','GS' => 'South Georgia And Sandwich Isl.','ES' => 'Spain','LK' => 'Sri Lanka','SD' => 'Sudan','SR' => 'Suriname','SJ' => 'Svalbard And Jan Mayen','SZ' => 'Swaziland','SE' => 'Sweden','CH' => 'Switzerland','SY' => 'Syrian Arab Republic','TW' => 'Taiwan','TJ' => 'Tajikistan','TZ' => 'Tanzania','TH' => 'Thailand','TL' => 'Timor-Leste','TG' => 'Togo','TK' => 'Tokelau','TO' => 'Tonga','TT' => 'Trinidad And Tobago','TN' => 'Tunisia','TR' => 'Turkey','TM' => 'Turkmenistan','TC' => 'Turks And Caicos Islands','TV' => 'Tuvalu','UG' => 'Uganda','UA' => 'Ukraine','AE' => 'United Arab Emirates','GB' => 'United Kingdom','US' => 'United States','UM' => 'United States Outlying Islands','UY' => 'Uruguay','UZ' => 'Uzbekistan','VU' => 'Vanuatu','VE' => 'Venezuela','VN' => 'Viet Nam','VG' => 'Virgin Islands, British','VI' => 'Virgin Islands, U.S.','WF' => 'Wallis And Futuna','EH' => 'Western Sahara','YE' => 'Yemen','ZM' => 'Zambia','ZW' => 'Zimbabwe'];

        foreach ($countries as $key => $value) {
            $this->confirmRow('countries', $num++, 'NULL, "' . $key . '", "' . $value . '"');
        }

        // Confirm user rows
        $num = 1;
        $this->confirmRow('users', $num, 'NULL, "' . $this->data['key'] . '", 4, NULL, NULL, "' . $this->data['username'] . '", "' . $this->data['email'] . '", "' . $this->data['password'] . '", NULL, NULL, NULL, NULL, 0, "default_avatar.png", NULL, NOW(), "' . $this->data['time'] . '", "' . $this->data['ip'] . '", 1, 0');

        // Confirm Language rows
        $num = 1;
        $this->confirmRow('language', $num++, 'NULL, "en", "English"');
        $this->confirmRow('language', $num++, 'NULL, "ja", "Japanese"');

        // Confirm Mail rows
        $this->confirmRow('mail', 1, 'NULL, "", "", "", ""');

        // Confirm Menu rows
        $this->confirmRow('menus', 1, 'NULL, "1", "0"');

        // Confirm Navigation rows
        $num = 1;
        $this->confirmRow('navigation', $num++, 'NULL, 1, "Home", 1, 0, 0, 1, 0');
        $this->confirmRow('navigation', $num++, 'NULL, 2, "Blog", 2, 0, 0, 1, 0');
        $this->confirmRow('navigation', $num++, 'NULL, 3, "Contact", 3, 0, 0, 1, 0');

        // Confirm Pages rows
        $num = 1;
        $this->confirmRow('pages', $num++, 'NULL, "Home", NULL, "home", "HomeController.php", NULL, NULL, NULL, NULL, "' . $this->data['time'] . '", 1');
        $this->confirmRow('pages', $num++, 'NULL, "Blog", NULL, "blog", "BlogController.php", NULL, NULL, NULL, NULL, "' . $this->data['time'] . '", 1');
        $this->confirmRow('pages', $num++, 'NULL, "Contact", NULL, "contact", "ContactController.php", NULL, NULL, NULL, NULL, "' . $this->data['time'] . '", 1');
        $this->confirmRow('pages', $num++, 'NULL, "Signup", NULL, "signup", "SignupController.php", NULL, NULL, NULL, NULL, "' . $this->data['time'] . '", 1');
        $this->confirmRow('pages', $num++, 'NULL, "Login", NULL, "login", "LoginController.php", NULL, NULL, NULL, NULL, "' . $this->data['time'] . '", 1');

        // Confirm Robots rows
        $this->confirmRow('robots', 1, 'NULL, "/admin"');

        // Confirm Routes rows
        $num = 1;
        $this->confirmRow('routes', $num++, 'NULL, 1, "/home"');
        $this->confirmRow('routes', $num++, 'NULL, 2, "/blog"');
        $this->confirmRow('routes', $num++, 'NULL, 3, "/contact"');
        $this->confirmRow('routes', $num++, 'NULL, 4, "/signup"');
        $this->confirmRow('routes', $num++, 'NULL, 5, "/login"');

        // Confirm Settings rows
        $num = 1;
        $this->confirmRow('settings', $num++, 'NULL, "sitename", "Chaste"');
        $this->confirmRow('settings', $num++, 'NULL, "owners_email", "' . $this->data['email'] . '"');
        $this->confirmRow('settings', $num++, 'NULL, "strong_pw", 0');
        $this->confirmRow('settings', $num++, 'NULL, "verify_logout", 1');
        $this->confirmRow('settings', $num++, 'NULL, "inactivity_limit", 5');
        $this->confirmRow('settings', $num++, 'NULL, "language", "en"');
        $this->confirmRow('settings', $num++, 'NULL, "maintenance_mode", 0');
        $this->confirmRow('settings', $num++, 'NULL, "theme", "Chaste"');

        $this->log('Installation has completed! DON\'T FORGET TO DELETE THE INSTALL FOLDER!');

        $output = ['alert' => 'success', 'message' => 'Installation has completed! DON\'T FORGET TO DELETE THE INSTALL FOLDER!'];
        exit(json_encode($output));
    }
}