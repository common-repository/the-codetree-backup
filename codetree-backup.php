<?php
/*
Plugin Name: CodeTree Backup
Version: 3.6.2
Plugin URI: http://www.mycodetree.com
Donate link: http://mycodetree.com/donations/
Description: Works with an active http://mycodetree.com subscription to allow offsite backups by mycodetree.com 
Author: Mycodetree
Author URI: http://www.mycodetree.com/

Copyright 2010 mycodetree.com.  (email: support@mycodetree.com)

While this software is free of charge, it is designed to work with an active
subscription from http://mycodetree.com.

*/
function getPhpVersion($supportedVersion) {
    (function_exists('phpversion'))?$phpVer = explode('.', floatval(phpversion())):$phpVer=false;
    (is_array($phpVer) && $phpVer[0] >= $supportedVersion)?$phpVer=true:$phpVer=false;
    return $phpVer;
}                             
add_action('admin_menu', 'codetree_backup');
add_filter( 'plugin_action_links', 'codetree_backup_add_action_link', 10, 2 );

function codetree_backup() {
    add_options_page('Codetree Backup Options', 'Codetree Backup', 'administrator', 'codetree_backup_options', 'codetree_backup_options');  
}

function codetree_backup_add_action_link( $links, $file ) {
    static $this_plugin;
     if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
    if ( $file == $this_plugin ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=codetree_backup_options' ) . '">' . __('Settings') . '</a>';
        array_unshift( $links, $settings_link ); // before other links
    }
    return $links;
}

function codetree_postbox($id, $title, $content) {
    ?>
        <div id="<?php echo $id; ?>" class="postbox">
            <div class="handlediv" title="Click to toggle"><br /></div>
            <h3 class="hndle"><span><?php echo $title; ?></span></h3>
            <div class="inside">
                <?php echo $content; ?>
            </div>
<?php 
$donerTest = apiBinder(get_option('codetree-backup-api')); 
if (!$donerTest[0]) {
?>
<div style="text-align: right;margin-right: 3px;">
Consider a small donation :)
<br />
<a href='http://mycodetree.com/donations' target='_blank'><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif"></a>
<br /><br />
</div>
<?php } ?>
        </div>
    <?php
}

function codetree_form_table($rows) {
    $content = '<table class="form-table" width="100%">';
    foreach ($rows as $row) {
        $content .= '<tr><th valign="top" scope="row" style="width:50%">';
        if (isset($row['id']) && $row['id'] != '')
            $content .= '<label for="'.$row['id'].'" style="font-weight:bold;">'.$row['label'].':</label>';
        else
            $content .= $row['label'];
        if (isset($row['desc']) && $row['desc'] != '')
            $content .= '<br/><small>'.$row['desc'].'</small>';
        $content .= '</th><td valign="top">';
        $content .= $row['content'];
        $content .= '</td></tr>'; 
    }
    $content .= '</table>';
    return $content;
}
function mct_backup_outbounder() {
    $outbound = true;
    $outboundReason = 'Network Conflict: Server rejected attemp to contact mycodetree.com server on port 80';
    if (!file_get_contents('http://mycodetree.com')) {
        $outbound = false;
    }
    if (ini_get('allow_url_fopen') == 0) {
        $outboundReason = "The PHP directive <em>ALLOW_URL_FOPEN</em> is turned off. The CodeTree Backup requires this option to be on because of it's URL trigger methods, please consult your hosting provider for help with this issue. If you consider moving your website to a different web host, MyCodeTree recomends <a href='http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=rthcon' target='_blank'>HostGator.com</a>.</p><p>If you would like further assistance please feel free to contact MyCodeTree at <a href='mailto:support@mycodetree.com?subject=PHP Version with CodeTree Backup Plugin'>support@mycodetree.com</a>.";
    }
    return array($outbound, $outboundReason);
}
function get_target($string, $start, $end){ 
    $string = " ".$string; 
    $ini = strpos($string,$start); 
    if ($ini == 0) return ""; 
    $ini += strlen($start); 
    $len = strpos($string,$end,$ini) - $ini; 
    return substr($string,$ini,$len); 
} 

function apiBinder($api) {   
  $errmsg = array();   
  $response = @file_get_contents("http://mycodetree.com/backup-manager/authentication.php?motion=bind&api=$api");
  $error = get_target($response, "<error>", "</error>");
  if (!empty($error)) {
      $errmsg = array(false, "<div style='background-color: Gold; color: Maroon; padding: 5px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;'>" . $error . "</div>");
  }
  else {
      $fn = get_target($response, "<firstname>", "</firstname>");
      $dn = get_target($response, "<domain>", "</domain>");
      $site = str_replace('http://', '', get_option('siteurl'));
      if (substr($site,0,4) == 'www.' OR substr($site,0,4) == 'WWW.') {
          $site = str_replace('www.', '', $site);
          $site = str_replace('WWW.', '', $site);
      }
      if ($site == $dn) {
         
         $errmsg = array(true, "<div style='background-color: Green; color: White; padding: 5px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;'>Hey " . ucfirst(strtolower($fn)) . ", we think it is pretty awesome that you are using the Codetree Backup from <a href='http://mycodetree.com' style='color: white; font-weight: bold;' target='_blank'>http://mycodetree.com</a>! Thanks !!!</div>");
      }
      else {
          $errmsg = array(false, "<div style='background-color: Gold; color: Maroon; padding: 5px; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;'>The API key does not match the domain</div>");
      }
  }
  return $errmsg;      
}
function codetree_backup_options() {
?>
 <div class="wrap">
    <h2>Codetree Backup for WordPress</h2>
    <div class="postbox-container" style="width:70%;">
        <div class="metabox-holder">    
            <div class="meta-box-sortables">
            <?php 
            $outbound = mct_backup_outbounder();
            if (getPhpVersion(5) && $outbound[0]) { 
            ?>
            <form id='apiKey' action="#" method="post">
            <?php       
                $manualdownload = NULL;
                $manualexist = finder();
                if ( function_exists('wp_nonce_field') ) wp_nonce_field('codetree-backup-update-options');
                if (trim($_GET['curcl']) == 1) {
                    update_option('codetree-backup-extrafolders', NULL);
                }     
                if (isset($_POST['codetree-backup-api']) && !empty($_POST['codetree-backup-api'])) {
                  $apikey = trim(stripslashes($_POST['codetree-backup-api'])); 
                  update_option('codetree-backup-api', $apikey);             
                }
                if (isset($_POST['codetree-backup-path']) && !empty($_POST['codetree-backup-path'])) {
                  $absopt = trim(stripslashes($_POST['codetree-backup-path'])); 
                  update_option('codetree-backup-path', $absopt);             
                }
                 if (isset($_POST['codetree-backup-urltype']) && !empty($_POST['codetree-backup-urltype'])) {
                  $urltype = trim(stripslashes($_POST['codetree-backup-urltype'])); 
                  update_option('codetree-backup-urltype', $urltype);             
                }
                if (isset($_POST['codetree-backup-extrafolders']) && !empty($_POST['codetree-backup-extrafolders'])) {    
                    $folders = explode(",", trim($_POST['codetree-backup-extrafolders']));                            
                    foreach ($folders as $key => $folder) {
                        $folders[$key] = $folder;
                    }
                    $putaway = implode(",", $folders);
                    update_option('codetree-backup-extrafolders', $putaway);
                }
                $urltype = get_option('codetree-backup-urltype');
                if (isset($_POST['codetree-backup-manual']) && $_POST['codetree-backup-manual'] == 'Manual Backup') {
                        if ($urltype == 'rewrite') { $result = @file_get_contents(get_option('siteurl') . '/codetree-manual'); $linkAlter = 'codetree-cleanup'; } else { $result = @file_get_contents(get_option('siteurl') . '/?mctdir=codetree-manual'); $linkAlter = '?mctdir=codetree-cleanup'; } 
                        $manualdownload = "<a href='" . get_option('siteurl') . '/' . get_target($result, '<target>', '</target>')  . "'>[Click to download: " . get_target($result, '<target>', '</target>') . "]</a>&nbsp;&nbsp<a href='" . get_option('siteurl') . "/" . $linkAlter . "' target='_self'>[Remove Manual Backup]</a><br /><small>(The database backup is located in the archive in /wp-content/plugins/the-codetree-backup/)</small><br /><br />";
                        
                }
                ($urltype == 'rewrite') ? $linkAlter='codetree-cleanup' : $linkAlter='?mctdir=codetree-cleanup';
                if ($manualexist[0]) {
                    $manualdownload = "<a href='" . get_option('siteurl') . '/' . $manualexist[1]  . "'>[Click to download: " . $manualexist[1] . "]</a>&nbsp;&nbsp<a href='" . get_option('siteurl') . "/" . $linkAlter . "' target='_self'>[Remove Manual Backup]</a><br /><small>(The database backup is located in the archive in /wp-content/plugins/the-codetree-backup/)</small><br /><br />";
                }
                $isvalid = apiBinder(get_option('codetree-backup-api'));
                $getAPI = NULL;
                if (!$isvalid[0]) {
                    $getAPI = "&nbsp;&nbsp;&nbsp;&nbsp;(<a href='http://mycodetree.com' target='_blank'>Get API Key</a>)";
                }
                $rows[] = array(
                        'id' => 'codetree-backup-api',
                        'label' => 'Codetree Backup API Key',
                        'desc' => 'Your API key is found in the subscription profile area at http://mycodetree.com',
                        'content' => "<input type='text' name='codetree-backup-api' id='codetree-backup-api' value='" . get_option('codetree-backup-api') . "'/>$getAPI"
                    );
                $relOnChk = get_option('codetree-backup-path');
                if ($relOnChk == 'relpath') { $relOnChk="checked='checked'"; $absOnChk=NULL; } else { $absOnChk="checked='checked'"; $relOnChk=NULL; }
                $rows[] = array(
                        'id' => 'codetree-backup-path',
                        'label' => 'Use Absolute Paths',
                        'desc' => 'Whether or not to use absolute or relative paths when making the backup. <strong><em>Most MS Windows&reg;/IIS web servers WILL NEED TO USE RELATIVE PATHS</em></strong>. If you find that the backup zip file is either empty or missing, try the corresponding option to the current selection.',
                        'content' => "Absolute Paths: <input type='radio' name='codetree-backup-path' id='codetree-backup-path' $absOnChk value='abspath'/>&nbsp;&nbsp;Relative Paths: <input type='radio' name='codetree-backup-path' id='codetree-backup-path' $relOnChk value='relpath'/>"
                    );
                $manualexist = finder();
                if (!$manualexist[0]) {
                $rewriteOnChk = get_option('codetree-backup-urltype');
                if ($rewriteOnChk == 'rewrite') { $rewriteOnChk="checked='checked'"; $variableOnChk=NULL; } else { $variableOnChk="checked='checked'"; $rewriteOnChk=NULL; }
                 $rows[] = array(
                        'id' => 'codetree-backup-urltype',
                        'label' => 'URL Type',
                        'desc' => 'Which type of URL system to use. Most servers can use <strong>Rewrite</strong> but if your server does not support <em>url rewriting</em> you may need to switch to <strong>Variable</strong>.',
                        'content' => "Rewrite: <input type='radio' name='codetree-backup-urltype' id='codetree-backup-urltype' $rewriteOnChk value='rewrite'/>&nbsp;&nbsp;Variable: <input type='radio' name='codetree-backup-urltype' id='codetree-backup-urltype' $variableOnChk value='variable'/>"
                    );    
                $rows[] = array(
                        'id' => 'codetree-backup-manual',
                        'label' => 'Codetree Manual Backup',
                        'desc' => 'Check this option and then <em>Save Changes</em> to execute a manual backup that <span style="color: maroon; font-weight: bold;">IS NOT</span> stored at http://mycodetree.com.<br /><br />All manual backups are stored on your server, if your server allows read/write operations. A download link will be provided below if a manual backup is able to be made successfully.',
                        'content' => "<input type='submit' class='button-primary' id='codetree-backup-manual' name='codetree-backup-manual' value='Manual Backup' />"
                    );  
                }
                $extracurr = NULL;
                $test = get_option('codetree-backup-extrafolders');
                if (!empty($test)) {
                    $extracurr = "<br /><br /><div style='background-color: Maroon; color: White; padding: 5px; font-family: Helvetica;'>Current Extra Folders: " . $test . "</div><br /><div><a href='/wp-admin/options-general.php?page=codetree_backup_options&curcl=1'>[Reset Extra Backup Folders]</a></div>";
                }
                if ($isvalid[0]) {
                $rows[] = array (
                        'id' => 'codetree-backup-extrafolders',
                        'label' => 'Additional Folders To Backup',
                        'desc' => 'Specify additional folders to backup in a comma deliminated list. Your server\'s absolute path is assumed. Start your paths from the base of the wordpress install. Do not use a begininning or ending slash.<br /><br /><strong>Example:</strong><br />\'my-first-level-folder\'<br />\'wp-content/my-second-level-folder\'<br />\'wp-content/my-special-folder/my-third-level-folder/\' ... etc' . $extracurr,
                        'content' => "<input type='text' name='codetree-backup-extrafolders' id='codetree-backup-extrafolders''>"
                );
                }
                ?>        
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="action" value="update" />
                <?php codetree_postbox('codetreesettings','Codetree Backup Settings (<a href=\'http://wordpress.org/extend/plugins/search.php?q=the+codetree\' target=\'_blank\'>see all CodeTree plugins</a>)', codetree_form_table($rows)); ?>
                <?php echo $manualdownload; ?>
                <?php  
                echo $isvalid[1];
                ?>
                <p class="submit">
                <input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>" />
                </p>
            </form>                  
            <?php }
            else {
                if (!$outbound[0]) {
                    echo "<p>" . $outbound[1] . "</p>";   
                }
                else {
                    echo "<p>The minimum required PHP Version for The CodeTree Backup to function is <strong><em>PHP Version 5.0</em</strong> and we've detected that your server is using <strong><em>PHP Version "; if (function_exists('phpversion')) { echo floatval(phpversion()); } echo "</em></strong>. please upgrade your PHP version or talk to your web host. If you consider moving your website to a different web host, MyCodeTree recomends <a href='http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=rthcon' target='_blank'>HostGator.com</a>.</p><p>If you would like further assistance please feel free to contact MyCodeTree at <a href='mailto:support@mycodetree.com?subject=PHP Version with CodeTree Backup Plugin'>support@mycodetree.com</a>.</p>";
                }
            }
             ?>
            </div>
        </div>
    </div>
</div>
<?php

}

$dbbackup = ABSPATH . 'wp-content/plugins/the-codetree-backup/db-backup-'.time().'.sql';

function cleaner($folder, $extension) {
    if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {  
            $ext = substr($file, strpos($file, '.'), strlen($file));               
                if ($extension == '.zip') {                        
                   $test = explode('-', $file);
                   if ($test[0] == 'mycodetreebu') {
                       unlink($folder . $file);
                   } 
                }
                else {
                    if ($ext == $extension) {
                        unlink($folder . $file);                
                    }
                }
            }
        }
        closedir($handle);
    }
}

function finder() {
    if ($handle = opendir(ABSPATH)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {  
            $ext = substr($file, strpos($file, '.'), strlen($file));               
                if ($ext == '.zip') {                        
                   $test = explode('-', $file);
                   if ($test[0] == 'mycodetreebu') {
                       return array(true, $file);
                   } 
                }
            }
        }
        closedir($handle);
    }
    
    return array(false);
}

function backup_tables($host,$user,$pass,$name,$dbbackup,$tables = '*') {

    $link = mysql_connect($host,$user,$pass);
    mysql_select_db($name,$link);

    //get all of the tables
    if($tables == '*'){
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    }
    else {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    //cycle through
    foreach($tables as $table){
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
        
        for ($i = 0; $i < $num_fields; $i++){
            while($row = mysql_fetch_row($result)){
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j<$num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }
    //save file
    $handle = fopen($dbbackup,'w+');
    fwrite($handle,$return);
    fclose($handle);
}
function browse($dir) {
global $filenames;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && is_file($dir.'/'.$file)) {
                $filenames[] = $dir.'/'.$file;
            }
            else if ($file != "." && $file != ".." && is_dir($dir.'/'.$file)) {
                browse($dir.'/'.$file);
            }
        }
        closedir($handle);
    }
    return $filenames;
}
$pathAppend = get_option('codetree-backup-path');
($pathAppend == 'abspath') ? $pathAppend = ABSPATH : $pathAppend = NULL; 
$urltype = get_option('codetree-backup-urltype');
($urltype == 'rewrite') ? $uri = explode('/', $_SERVER['REQUEST_URI']) : $uri = array("",trim(stripslashes($_GET['mctdir'])));
if (array_search('codetree-execute', $uri) OR array_search('codetree-manual', $uri)) {
    backup_tables(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $dbbackup); 
    $uploads = get_option('upload_path');
    if (empty($uploads) OR $uploads == '') {
        $uploads = $pathAppend . 'wp-content/uploads';                 
    }
    $directory = array($pathAppend . 'wp-content/themes', $pathAppend . 'wp-content/plugins', $uploads);
    $extras = get_option('codetree-backup-extrafolders');
    if (!empty($extras) && $extras != " ") {
        
        $otherfolders = explode(",", $extras);
        $directory = array_merge($directory, $otherfolders);
    }
    $fname = 'mycodetreebu-' . date('mdY-his', time()) . '.zip';
    $zipfile = ABSPATH . $fname;
    $filenames = array();
    foreach($directory as $dir){
        browse($dir);
    }      
    $zip = new ZipArchive();
    if ($zip->open($zipfile, ZIPARCHIVE::CREATE)!==TRUE) {

    }
    foreach ($filenames as $filename) {
        $zip->addFile($filename,$filename);
    }
    $zip->close();
    if (array_search('codetree-manual', $uri)){
        die("<target>$fname</target>");
    }
    else {
        file_get_contents("http://mycodetree.com/backup-manager/bu-processor.php?api=" . get_option('codetree-backup-api') . "&bu=" . $fname);
        die("<center><span style='font-family: Helvetica;'>All finished!</span><br /><a href='http://mycodetree.com' target='_blank' title='http://mycodetree.com'><img src='http://mycodetree.com/wp-content/uploads/2010/06/vsmall_MyCodetree.jpg' width='800px' border='0' alt='http://mycodetree.com'></center>");
    }
}
if (array_search('codetree-cleanup', $uri)) {
    
    cleaner(ABSPATH . 'wp-content/plugins/the-codetree-backup/', '.sql');
    cleaner(ABSPATH, '.zip');
    die("<center><span style='font-family: Helvetica;'>All cleaned up! <a href='" . get_option('siteurl') . "/wp-admin/options-general.php?page=codetree_backup_options' target='_self'>Go back to the Dashboard!</a></span><br /><a href='http://mycodetree.com' target='_blank' title='http://mycodetree.com'><img src='http://mycodetree.com/wp-content/uploads/2010/06/vsmall_MyCodetree.jpg' width='800px' border='0' alt='http://mycodetree.com'></center>");
}
?>