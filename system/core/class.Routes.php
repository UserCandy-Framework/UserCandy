<?php
/**
* Core System Routes
* Editing the core routes may crash your site
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/*
* self::add($url, $pagefolder, $pagefile, $params);
* self::add('Home', 'Home', '');
*/

namespace Core;

use Helpers\Database;

class Routes {

    private static $db;

    static function setRoutes(){
        $routes = array();

        /* Default Routing */
        $routes[] = self::add('Home', 'Home', 'Home', '(:any)/(:num)');
        $routes[] = self::add('Terms', 'Home', 'Terms');
        $routes[] = self::add('Privacy', 'Home', 'Privacy');
        $routes[] = self::add('Templates', 'Home', 'Templates');
        $routes[] = self::add('assets', 'Home', 'assets');
        $routes[] = self::add('themes', 'Home', 'themes');
        $routes[] = self::add('About', 'Home', 'About');
        $routes[] = self::add('Contact', 'Home', 'Contact');
        /* End default routes */

        /* Auth Routing */
        $routes[] = self::add('Register', 'Auth', 'Register');
        $routes[] = self::add('Activate', 'Auth', 'Activate', '(:any)/(:any)/(:any)/(:any)');
        $routes[] = self::add('Forgot-Password', 'Auth', 'Forgot-Password');
        $routes[] = self::add('Reset-Password', 'Auth', 'Reset-Password', '(:any)/(:any)/(:any)/(:any)');
        $routes[] = self::add('Resend-Activation-Email', 'Auth', 'Resend-Activation-Email');
        $routes[] = self::add('Login', 'Auth', 'Login');
        $routes[] = self::add('Logout', 'Auth', 'Logout');
        /* End Auth Routing */

        /* Members Routing */
        $routes[] = self::add('Change-Email', 'Members', 'Change-Email');
        $routes[] = self::add('Change-Password', 'Members', 'Change-Password');
        $routes[] = self::add('Edit-Profile','Members', 'Edit-Profile');
        $routes[] = self::add('Edit-Profile-Images','Members', 'Edit-Profile-Images', '(:any)/(:num)');
        $routes[] = self::add('Privacy-Settings','Members', 'Privacy-Settings');
        $routes[] = self::add('Account-Settings','Members', 'Account-Settings');
        /* End Members Routing */

        /* Live Checks */
        $routes[] = self::add('LiveCheckEmail', 'Members', 'LiveCheckEmail');
        $routes[] = self::add('LiveCheckUserName', 'Members', 'LiveCheckUserName');
        /* End Live Checks */

        /* Member Routing */
        $routes[] = self::add('Members', 'Members', 'Members', '(:any)/(:any)/(:any)');
        $routes[] = self::add('Online-Members', 'Members', 'Online-Members',  '(:any)/(:any)');
        $routes[] = self::add('Profile', 'Members', 'Profile', '(:any)/(:num)/(:num)');
        /* End Member Routing */

        /* Admin Panel Routing */
        $routes[] = self::add('AdminPanel', 'AdminPanel', 'AdminPanel');
        $routes[] = self::add('AdminPanel-Settings', 'AdminPanel', 'AdminPanel-Settings');
        $routes[] = self::add('AdminPanel-AdvancedSettings', 'AdminPanel', 'AdminPanel-AdvancedSettings');
        $routes[] = self::add('AdminPanel-EmailSettings', 'AdminPanel', 'AdminPanel-EmailSettings');
        $routes[] = self::add('AdminPanel-Users', 'AdminPanel', 'AdminPanel-Users' , '(:any)/(:num)/(:any)');
        $routes[] = self::add('AdminPanel-User', 'AdminPanel', 'AdminPanel-User', '(:any)');
        $routes[] = self::add('AdminPanel-Groups', 'AdminPanel', 'AdminPanel-Groups');
        $routes[] = self::add('AdminPanel-Group', 'AdminPanel', 'AdminPanel-Group', '(:any)');
        $routes[] = self::add('AdminPanel-MassEmail', 'AdminPanel', 'AdminPanel-MassEmail');
        $routes[] = self::add('AdminPanel-AuthLogs', 'AdminPanel', 'AdminPanel-AuthLogs', '(:any)');
        $routes[] = self::add('AdminPanel-SiteLinks', 'AdminPanel', 'AdminPanel-SiteLinks', '(:any)/(:any)/(:any)');
        $routes[] = self::add('AdminPanel-SiteLink', 'AdminPanel', 'AdminPanel-SiteLink', '(:any)/(:any)/(:any)');
        $routes[] = self::add('AdminPanel-Upgrade', 'AdminPanel', 'AdminPanel-Upgrade');
        $routes[] = self::add('AdminPanel-PagesPermissions', 'AdminPanel', 'AdminPanel-PagesPermissions', '(:any)');
        $routes[] = self::add('AdminPanel-PagePermissions', 'AdminPanel', 'AdminPanel-PagePermissions', '(:num)');
        $routes[] = self::add('AdminPanel-TermsPrivacy', 'AdminPanel', 'AdminPanel-TermsPrivacy');
        $routes[] = self::add('AdminPanel-Dispenser-Settings', 'AdminPanel', 'AdminPanel-Dispenser-Settings');
        $routes[] = self::add('AdminPanel-Dispenser', 'AdminPanel', 'AdminPanel-Dispenser', '(:any)/(:any)/(:any)/(:any)');
        $routes[] = self::add('AdminPanel-Dispenser-Widgets-Settings', 'AdminPanel', 'AdminPanel-Dispenser-Widgets-Settings', '(:num)/(:num)');
        /* End Admin Panel Routing */

        /* Language Code Change */
        $routes[] = self::add('ChangeLang', 'Home', 'ChangeLang', '(:any)');
        /* End Language Code Change Routing */

        /* Site Map Route */
        $routes[] = self::add('sitemap', 'Home', 'sitemap');
        $routes[] = self::add('sitemap.xml', 'Home', 'sitemap');
        /* End Site Map Route */

        /** Get Routes from Database **/
        self::$db = Database::get();
        $db_routes = self::$db->select("
            SELECT
                *
            FROM
                ".PREFIX."pages
            WHERE
                stock = 'false'
            ");
        foreach ($db_routes as $db_route) {
            ($db_route->headfoot == 1) ? $set_headfoot = true : $set_headfoot = false;
            $routes[] = self::add($db_route->url, $db_route->pagefolder, $db_route->pagefile, $db_route->arguments, $set_headfoot, $db_route->template);
        }
        /** End Get Routes From Database **/

        /* Send the routes to system */
        return $routes;
    }

    static function add($url, $pagefolder, $pagefile, $arguments = null, $headfoot = true, $template = 'Default'){
        $routes = array(
            "url" => $url,
            "pagefolder" => $pagefolder,
            "pagefile" => $pagefile,
            "arguments" => $arguments,
            "headfoot" => $headfoot,
            "template" => $template
        );
        return $routes;
    }

    static function all(){
        $routes = self::setRoutes();
        return $routes;
    }

}
