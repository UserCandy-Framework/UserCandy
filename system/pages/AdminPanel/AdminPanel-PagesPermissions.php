<?php
/**
* Admin Panel Pages Permissions View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/** Check to see if user is logged in */
if($data['isLoggedIn'] = $auth->isLogged()){
    /** User is logged in - Get their data */
    $u_id = $auth->user_info();
    $data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
    if($data['isAdmin'] = $usersModel->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out */
        ErrorMessages::push('You are Not Admin', '');
    }
}else{
    /** User Not logged in - kick them out */
    ErrorMessages::push('You are Not Logged In', 'Login');
}

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Get data from URL **/
(empty($viewVars[0])) ? $set_order_by = 'URL-ASC' : $set_order_by = $viewVars[0];

/** Setup Page Info */
$data['title'] = "Pages Permissions";
$data['welcomeMessage'] = "Welcome to the Pages Permission Admin Page.";
/** Check for orderby selection */
$data['orderby'] = $set_order_by;

/** Get Data from System Routes */
$getRoutes = Routes::all();

/** Check database to see if any new pages need added */
$new_routes = null;
if(isset($getRoutes)){
  foreach ($getRoutes as $single_page) {
    /** Check to see if page exist in database */
    if(!$AdminPanelModel->checkForPage($single_page['pagefolder'], $single_page['pagefile'])){
      /** Page Does Not Exist in Database */
      /** Add Page to Database */
      if($page_id = $AdminPanelModel->addPage($single_page['pagefolder'], $single_page['pagefile'], $single_page['url'])){
        /** New Page added to database.  Let Admin Know it was added */
        $new_pages[] = "<b>URL: ".$single_page['url']."</b> (".$single_page['pagefolder']." - ".$single_page['pagefile'].")<Br>";
        /** Add new permission for the page and set as public */
        $AdminPanelModel->addPagePermission($page_id, '0');
      }
    }
  }
}
/** Search Common Directories for new Pages/Files **/
$custom_pages_dir = ROOTDIR.'custom/pages';
$scan_custom_pages_dir = array_diff(scandir($custom_pages_dir), array('..', '.'));

/** Extract the methods from the classes */
foreach ($scan_custom_pages_dir as $filename) {
    /** Remove the .php from the files */
    $dir_pagefile = str_replace('.php', '', str_replace('-', ' ', $filename));
    $dir_pagefolder = 'custompages';
    /** Check to see if page is in database **/
    if(!$AdminPanelModel->checkForPage($dir_pagefolder, $dir_pagefile)){
      /** Add New Page to Database **/
      /** Check to see if URL is already in the database and add numbers if it is **/
      if($AdminPanelModel->checkPagesURL($dir_pagefile)){
        /** URL Exist - Make it different **/
        $page_url = $dir_pagefile.rand(10, 99);
      }else{
        /** URL Does Not Exist - Keep it the same **/
        $page_url = $dir_pagefile;
      }
      /** Add Page to Database */
      if($page_id = $AdminPanelModel->addPage($dir_pagefolder, $dir_pagefile, $page_url)){
        /** New Page added to database.  Let Admin Know it was added */
        $new_pages[] = "<b>URL: ".$page_url."</b> (".$dir_pagefolder." - ".$dir_pagefile.")<Br>";
        /** Add new permission for the page and set as public */
        $AdminPanelModel->addPagePermission($page_id, '0');
        /** New Route added to database.  Add to site Links */
        if($AdminPanelModel->addSiteLink($dir_pagefile, $page_url, $dir_pagefolder." - ".$dir_pagefile, 'header_main', '0', '')){
          /** Success */
          $new_pages[] = $page_url." Added to Site Links<Br>";
        }
      }
    }
}

/** Search for New Plugin Display Page **/
/** Get Enabled Helper Folders from Dispenser DB **/
$DispenserEnabledPlugins = $DispenserModel->getDispenserByType('plugin');
/** Get Array of Plugin Display Pages **/
if(isset($DispenserEnabledPlugins)){
  foreach($DispenserEnabledPlugins as $deh) {
    $plugin_display_page = CUSTOMDIR.'plugins/'.$deh->folder_location.'/display.php';
    if(!file_exists($plugin_display_page)){
      $plugin_display_page = CUSTOMDIR.'plugins/'.$deh->folder_location.'/'.$deh->folder_location.'.php';
    }
    /** Check to make sure Display page exists **/
    if(file_exists($plugin_display_page)){
      /** Check to see if Plugin is already in Pages **/
      if(!$AdminPanelModel->checkPagesURL($deh->folder_location)){
        /** Add Page to Database */
        if($page_id = $AdminPanelModel->addPluginPage('plugins/'.$deh->folder_location, $deh->folder_location, $deh->folder_location)){
          /** New Page added to database.  Let Admin Know it was added */
          $new_pages[] = "<b>URL: ".$deh->folder_location."</b> (Plugin - ".$deh->folder_location.")<Br>";
          /** Add new permission for the page and set as public */
          $AdminPanelModel->addPagePermission($page_id, '1');
          $AdminPanelModel->addPagePermission($page_id, '2');
          $AdminPanelModel->addPagePermission($page_id, '3');
          $AdminPanelModel->addPagePermission($page_id, '4');
          /** New Route added to database.  Add to site Links */
          if($AdminPanelModel->addSiteLink($deh->folder_location, $deh->folder_location, $deh->folder_location." - ".$deh->folder_location, 'header_main', '0', '')){
            /** Success */
            $new_pages[] = $page_url." Added to Site Links<Br>";
          }
        }
      }
    }
  }
}

/** Check to see if any new routes were added to database */
if(isset($new_pages)){
    /** Format Data for Success Message */
    $new_pages_display = implode(" ", $new_pages);
    /** Success */
    SuccessMessages::push('New Pages Have Been Added to Database!<Br><br>'.$new_pages_display, 'AdminPanel-PagesPermissions');
}

/** Get All Pages Data */
$data['all_pages'] = $AdminPanelModel->getAllPages($data['orderby']);

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('pages_permissions');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fas fa-fw fa-unlock-alt'></i> ".$data['title']."</li>";


    /** Setup Sort By URL **/
    if(empty($data['orderby'])){
    	$ob_value = "URL-DESC";
    	$ob_icon = "";
    }
    else if($data['orderby'] == "URL-DESC"){
    	$ob_value = "URL-ASC";
    	$ob_icon = "<i class='fa fa-fw  fa-caret-down'></i>";
    }
    else if($data['orderby'] == "URL-ASC"){
    	$ob_value = "URL-DESC";
    	$ob_icon = "<i class='fa fa-fw  fa-caret-up'></i>";
    }else{
    	$ob_value = "URL-ASC";
    	$ob_icon = "";
    }
    /** Setup Sort By Page Folder **/
    if(empty($data['orderby'])){
    	$obc_value = "CON-DESC";
    	$obc_icon = "";
    }
    else if($data['orderby'] == "CON-DESC"){
    	$obc_value = "CON-ASC";
    	$obc_icon = "<i class='fa fa-fw  fa-caret-down'></i>";
    }
    else if($data['orderby'] == "CON-ASC"){
    	$obc_value = "CON-DESC";
    	$obc_icon = "<i class='fa fa-fw  fa-caret-up'></i>";
    }else{
    	$obc_value = "CON-ASC";
    	$obc_icon = "";
    }
    /** Setup Sort By Page File **/
    if(empty($data['orderby'])){
    	$obm_value = "MET-DESC";
    	$obm_icon = "";
    }
    else if($data['orderby'] == "MET-DESC"){
    	$obm_value = "MET-ASC";
    	$obm_icon = "<i class='fa fa-fw  fa-caret-down'></i>";
    }
    else if($data['orderby'] == "MET-ASC"){
    	$obm_value = "MET-DESC";
    	$obm_icon = "<i class='fa fa-fw  fa-caret-up'></i>";
    }else{
    	$obm_value = "MET-ASC";
    	$obm_icon = "";
    }

?>


<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?=$data['title']?>
      <?php echo PageFunctions::displayPopover('Site Pages Permissions', 'Site Pages Permissions allows admin to set the permissions for each page on the site.', false, 'btn btn-sm btn-light'); ?>
		</div>
        <div class='card-body'>
            <?=$data['welcomeMessage']?>
        </div>
		<table class='table table-hover responsive'>
			<tr>
        <th><?php echo "<a href='".SITE_URL."AdminPanel-PagesPermissions/$ob_value/' class='btn btn-info btn-sm'>URL Name $ob_icon</button>"; ?></th>
				<th class='d-none d-md-table-cell'><?php echo "<a href='".SITE_URL."AdminPanel-PagesPermissions/$obc_value/' class='btn btn-info btn-sm'>Folder $obc_icon</button>"; ?></th>
        <th class='d-none d-md-table-cell'><?php echo "<a href='".SITE_URL."AdminPanel-PagesPermissions/$obm_value/' class='btn btn-info btn-sm'>Page $obm_icon</button>"; ?></th>
        <th>Allowed User Groups</th>
        <th class='d-none d-md-table-cell'>SiteMap</th>
        <th></th>
			</tr>
			<?php
				if(isset($data['all_pages'])){
					foreach($data['all_pages'] as $row) {
            echo "<tr>";
              echo "<td>$row->url</td>";
              echo "<td class='d-none d-md-table-cell'>$row->pagefolder</td>";
              echo "<td class='d-none d-md-table-cell'>$row->pagefile</td>";
              echo "<td>".PageFunctions::getPageGroupName($row->id)."</td>";
              echo "<td class='d-none d-md-table-cell text-center'>";
                if($row->sitemap == 'true'){
                  echo "<i class='dot bg-success'></i>";
                }else{
                  echo "<i class='dot bg-danger'></i>";
                }
              echo "</td>";
              echo "<td align='right'>";
              echo "<a href='".SITE_URL."AdminPanel-PagePermissions/$row->id' class='btn btn-sm btn-primary'><span class='fas fa-fw fa-edit'></span></a>";
              echo "</td>";
            echo "</tr>";
					}
				}
			?>
		</table>
  </div>
</div>
