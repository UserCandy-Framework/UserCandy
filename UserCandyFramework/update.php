<?php
/**
* UserCandy Framework Updater
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

/** Add Data needed to Database - Oldest Top**/
$install_db_data[]['1.0.4'] = "
INSERT INTO `uc_pages` (url`, `pagefolder`, `pagefile`, `arguments`, `sitemap`, `stock`, `template`) VALUES
('custom', 'Home', 'custom', NULL, 'false', 'true', 'Default');
";
