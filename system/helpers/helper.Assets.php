<?php
/**
* Assets Plugin
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class Assets {

    public static function css($css_url){
        if(isset($css_url)){
            foreach ($css_url as $value) {
                $css[] = '<link rel="stylesheet" href="'.$value.'" />';
            }
            return implode("", $css);
        }
    }

    public static function js($js_url){
        if(isset($js_url)){
            foreach ($js_url as $value) {
                $js[] = '<script src="'.$value.'" type="text/javascript"></script>';
            }
            return implode("", $js);
        }
    }

    public static function loadFile($extRoutes = null, $location = null){
        /* Check to make sure a file is properly requested */
        if(isset($extRoutes)){
            $mimes = array
            (
                'jpg' => 'image/jpg',
                'jpeg' => 'image/jpg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'css' => 'text/css',
                'js' => 'application/javascript'
            );

            if(isset($extRoutes[4])){
                (isset($location)) ? $filename = $extRoutes[4] : $filename = $extRoutes[4] ;
            }else if(isset($extRoutes[3])){
                (isset($location)) ? $filename = $extRoutes[3] : $filename = $extRoutes[3] ;
            }else if(isset($extRoutes[2])){
                (isset($location)) ? $filename = $extRoutes[2] : $filename = $extRoutes[2] ;
            }else if(isset($extRoutes[1])){
                (isset($location)) ? $filename = $extRoutes[1] : $filename = $extRoutes[1] ;
            }else{
                (isset($location)) ? $filename = $extRoutes[0] : $filename = $extRoutes[0] ;
            }

            $ext = strtolower(@end((explode('.', $filename))));

            if($location == 'themes'){
                if(isset($extRoutes[2])){
                    $file = CUSTOMDIR.'themes/'.$extRoutes[0].'/assets/'.$extRoutes[1].'/'.$filename;
                }else{
                    $file = CUSTOMDIR.'themes/'.$extRoutes[0].'/assets/'.$filename;
                }
                $file = preg_replace('{/$}', '', $file);
            }else if(isset($location)){
                if(isset($extRoutes[3])){
                    $file = ROOTDIR.'assets/'.$extRoutes[0].'/'.$extRoutes[1].'/'.$extRoutes[2].'/'.$filename;
                }else{
                    $file = ROOTDIR.'assets/'.$extRoutes[0].'/'.$extRoutes[1].'/'.$filename;
                }
                $file = preg_replace('{/$}', '', $file);
            }else{
                if(isset($extRoutes[4])){
                    $file = SYSTEMDIR.'templates/'.$extRoutes[0].'/Assets/'.$extRoutes[2].'/'.$extRoutes[3].'/'.$filename;
                }else{
                    $file = SYSTEMDIR.'templates/'.$extRoutes[0].'/Assets/'.$extRoutes[2].'/'.$filename;
                }
            }

            if(file_exists($file)){
                header('Content-Type: '. $mimes[$ext]);
                header('Content-Disposition: inline; filename="'.$filename.'";');
                readfile($file);
            }else{
                ErrorHandler::show(404);
            }
        }else{
            ErrorHandler::show(404);
        }
    }

}
