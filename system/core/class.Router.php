<?php

/*
* System Router Class
* The goal of the router is to handle all the url requests.
* When you type in ?page=About it will load the About Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class Router {

    private $routes;

    function __construct(){
        $this->routes = Routes::all();
        $route = $this->findRoute();
        $crc_array = array();
        $crc_array = $route['pagefolder'];
        /** Check if Plugin is being Requested **/
        $check_for_plugin = explode("/", $route['pagefolder']);
        if($check_for_plugin[0] == "plugins"){
            $pagefolder = CUSTOMDIR."{$route['pagefolder']}/";
        }else if($crc_array == "custompages"){
            $pagefolder = CUSTOMDIR."pages/";
        }else{
            $pagefolder = SYSTEMDIR."pages/".$route['pagefolder']."/";
        }
        if($route['url'] == "Templates"){
          $params = array_slice(SELF::extendedRoutes(), 1);
          Assets::loadFile($params);
        }else if($route['url'] == "assets"){
          $params = array_slice(SELF::extendedRoutes(), 1);
          Assets::loadFile($params, 'assets');
        }else if($route['url'] == "themes"){
          $params = array_slice(SELF::extendedRoutes(), 1);
          Assets::loadFile($params, 'themes');
        }else if($route['url'] == "LiveCheckEmail"){
          if(Request::post('email') !== null){
            $email = Request::post('email');
          }else if(Request::post('newemail') !== null){
            $email = Request::post('newemail');
          }
          if(isSet($email))
          {
            $this->db = Database::get();
            $query = $this->db->select('SELECT email FROM '.PREFIX.'users WHERE email=:email',
                array(':email' => $email));
            $count = count($query);
            if($count == "0")
            {
              // Check input to be sure it meets the site standards for emails
              if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['welcomeMessage'] = "OK";
              }else{
                $data['welcomeMessage'] = "BAD";
              }
            }
            else
            {
              $data['welcomeMessage'] = "INUSE";
            }
            unset($email, $ttl_un_rows);
          }else{
            $data['welcomeMessage'] = "BAD";
          }
          echo $data['welcomeMessage'];
        }else if($route['url'] == "LiveCheckUserName"){
          (Request::post('username') !== null) ? $username = Request::post('username') : $username = "";
          if(isSet($username))
          {
            $this->db = Database::get();
            $query = $this->db->select('SELECT username FROM '.PREFIX.'users WHERE username=:username',
                array(':username' => $username));
            $count = count($query);
            if($count == "0")
            {
              // Check input to be sure it meets the site standards for usernames
              if(!preg_match("/^[a-zA-Z\p{Cyrillic}0-9]+$/u", $username)){
                // UserName Chars wrong
                $data['welcomeMessage'] = "CHAR";
              }else{
                // UserName is good
                $data['welcomeMessage'] = "OK";
              }
            }
            else
            {
              $data['welcomeMessage'] = "INUSE";
            }
            unset($username, $ttl_un_rows);
          }
          echo $data['welcomeMessage'];
        }else if($route['url'] == "sitemap" || $route['url'] == "sitemap.xml"){
          /** Load Home Model **/
          $Home = new HomeModel();

          header('Content-type: text/xml');
          echo "<?xml version='1.0' encoding='UTF-8'?>\n";
          echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

          /** Get Enabled Pages from Pages **/
          $getPublicURLs = $Home->getPublicURLs();
          if(isset($getPublicURLs)){
            foreach ($getPublicURLs as $key => $value) {
              if(isset($value->edit_timestamp)){
                $loc_date = $value->edit_timestamp;
              }else{
                $loc_date = $value->timestamp;
              }
              echo "<url>\n";
                echo "<loc>".SITE_URL.$value->url."</loc>\n";
                echo "<lastmod>".date('Y-m-d',strtotime($loc_date))."</lastmod>\n";
              echo "</url>\n";
            }
          }

          /** Get Forum Posts **/
          //$getForumPosts = $Home->getForumPosts();
          if(isset($getForumPosts)){
            foreach ($getForumPosts as $key => $value) {
              /** Check Forum Post Replies for latest post date **/
              $latest_forum_reply = $Home->getLatestForumReply($value->forum_post_id);
              if(isset($latest_forum_reply)){
                $loc_date = $latest_forum_reply;
              }else if(isset($value->forum_edit_date)){
                $loc_date = $value->forum_edit_date;
              }else{
                $loc_date = $value->forum_timestamp;
              }
              /** Check to see if topic has url set **/
              if(isset($value->forum_url)){
                $url_link = $value->forum_url;
              }else{
                $url_link = $value->forum_post_id;
              }
              echo "<url>\n";
                echo "<loc>".SITE_URL."Topic/".$url_link."/</loc>\n";
                echo "<lastmod>".date('Y-m-d',strtotime($loc_date))."</lastmod>\n";
              echo "</url>\n";
            }
          }

          echo "</urlset>";
        }else if(is_dir($pagefolder)){
            if(file_exists($pagefolder.$route["pagefile"].".php")){
                if(isset($route["arguments"])){
                    /** Split up the arguments from routes **/
                    $arguments = array();
                    $arg_paths = array();
                    $arg = rtrim($route["arguments"],'/');
                    $arguments = explode("/", $arg);
                    /** For each argument we get data from url **/
                    $params = array_slice(SELF::extendedRoutes(), 1);
                    foreach ($arguments as $key => $value) {
                        /** Check to see if argument is any **/
                        if($value == "(:any)"){
                            if(isset($params[$key])){
                                if(preg_match('#^[^/]+(?:\?.*)?$#i', $params[$key])){
                                    $new_params[] = $params[$key];
                                }else{
                                    $error_check = true;
                                }
                            }
                        }
                        /** Check to see if argument is a number **/
                        if($value == "(:num)"){
                            if(isset($params[$key])){
                                if(preg_match('#^-?[0-9]+(?:\?.*)?$#i', $params[$key])){
                                    $new_params[] = $params[$key];
                                }else{
                                    $error_check = true;
                                }
                            }
                        }
                        /** Check to see if argument is all **/
                        if($value == "(:all)"){
                            if(isset($params[$key])){
                                if(preg_match('#^.*(?:\?.*)?$#i', $params[$key])){
                                    $new_params[] = $params[$key];
                                }else{
                                    $error_check = true;
                                }
                            }
                        }
                    }
                    (isset($error_check)) ? '' : $error_check = false;
                    if($error_check != true){
                        if(isset($new_params)){
                            /** Execute the Controller's Method with the given arguments **/
                            if($route['pagefolder'] == 'AdminPanel'){
                              $load_pagefolder = $pagefolder;
                              $load_pagefile = $route['pagefile'];
                              Load::View("$load_pagefolder::$load_pagefile", $new_params, 'AdminPanel', $route['headfoot']);
                            }else{
                              $load_pagefolder = $pagefolder;
                              $load_pagefile = $route['pagefile'];
                              Load::View("$load_pagefolder::$load_pagefile", $new_params, $route['template'], $route['headfoot']);
                            }
                        }else{
                            if($route['pagefolder'] == 'AdminPanel'){
                              $load_pagefolder = $pagefolder;
                              $load_pagefile = $route['pagefile'];
                              Load::View("$load_pagefolder::$load_pagefile", array(), 'AdminPanel', $route['headfoot']);
                            }else{
                              $load_pagefolder = $pagefolder;
                              $load_pagefile = $route['pagefile'];
                              Load::View("$load_pagefolder::$load_pagefile", array(), $route['template'], $route['headfoot']);
                            }
                        }
                    }else{
                        ErrorHandler::show(404);
                    }
                }else{
                    if($route['pagefolder'] == 'AdminPanel'){
                      $load_pagefolder = $pagefolder;
                      $load_pagefile = $route['pagefile'];
                      Load::View("$load_pagefolder::$load_pagefile", array(), 'AdminPanel', $route['headfoot']);
                    }else{
                      $load_pagefolder = $pagefolder;
                      $load_pagefile = $route['pagefile'];
                      Load::View("$load_pagefolder::$load_pagefile", array(), $route['template'], $route['headfoot']);
                    }
                }
            }else{
                ErrorHandler::show(404);
            }
        }else{
            ErrorHandler::show(404);
        }
    }

    private function routePart($route){
        if(is_array($route)){
            $route = $route['url'];
        }
        $parts = explode("/", $route);
        return $parts;
    }

    static function uri($part){
        $routes = Routes::all();
        if(Request::get("url") !== null){
            $url = Request::get('url');
		    $url = rtrim($url,'/');
            $parts = explode("/", $url);
            if($parts[0] == $routes){
                $part++;
            }
            return (isset($parts[$part])) ? $parts[$part] : "";
        }else{
            return "";
        }
    }

    private function findRoute(){
        $uri = Router::uri(0);
        if(empty($uri)){
            $route = array(
                "url" => "",
                "pagefolder" => DEFAULT_HOME_PAGE_FOLDER,
                "pagefile" => DEFAULT_HOME_PAGE,
                "headfoot" => true,
            );
            return $route;
        }
        foreach ($this->routes as $route) {
            $parts = $this->routePart($route);
            $match = false;
            foreach($parts as $value){
                if($value == $uri){
                    $match = true;
                }
                if($match){
                    return $route;
                }
            }
        }
    }

    public static function extendedRoutes(){
        if(!empty(Request::get('url'))){
            $url = Request::get('url');
            $url = rtrim($url,'/');
            $parts = explode("/", $url);
            return $parts;
        }
    }

}
