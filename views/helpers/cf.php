<?php
/**
 * Cloud Front or CDN asset helper
 * 
 * Allows to load assets from remote server(s) in production mode.
 * Supports: 
 *  - Multiple hosts (for faster load time)
 *  - Caching and timestamps
 *  - Serving via SSL
 */
class CfHelper extends AppHelper {
  
/**
 * Let's load required helpers
 */
  public $helpers = array('Html', 'Javascript');
  
/**
 * Where are the assets hosted?
 * Possible options: 'assets.example.com', if you only have one host
 * Or: 'assets%d.example.com', if you have multiple hosts. %d gets replaced with host number
 */
  private $assetHost = 'assets%d.example.com';
  
/**
 * If above is 'assets%d.example.com' will generate host names from 0 - 3
 * i.e. assets0.example.com
 */
  private $numHostsMin = 0;

/**
 * If above is 'assets%d.example.com' will generate host names from 0 - 3
 * i.e. assets0.example.com
 */
  private $numHostsMax = 3;

/**
 * Serving assets via SSL is slow, let's use a unique host (for better caching) 
 */
  private $sslHost = 'sslhost.example.com';

/**
 * Where are the images relative to web root (local should mirror remote)
 * Try to stick to cake conventions.
 */
  private $imgDir = 'img';
  
/**
 * Where are the JS files relative to web root (local should mirror remote)
 * Try to stick to cake conventions.
 */
  private $jsDir = 'js';

/**
 * Where are the CSS files relative to web root (local should mirror remote)
 * Try to stick to cake conventions.
 */
  private $cssDir = 'css';

/**
 * Will set asset directory depending on the asset type (css, js, img)
 */  
  private $assetDir = NULL; 
  
/**
 * We should really force the timestamp to improve caching.
 * Trun on the option in core.php
 */  
  private $forceTimestamp = FALSE;
 
 /**
 * Are we forcing the timestamp (based on core.php setting)?
 * (We really, really should)
 */   
  public function beforeRender() {
    if ((Configure::read('Asset.timestamp') == true && Configure::read() > 0) || Configure::read('Asset.timestamp') === 'force') {
      $this->forceTimestamp = TRUE;
    }
  } 
 
 /**
 * Return image path/URL either remote or local based on the debug level
 */     
  public function image($assets, $options = array()) {  
    $this->setAssetDir($this->imgDir);      
    return $this->Html->image($this->setAssetPath($assets), $options);
  }
 
 /**
 * Return JS link path/URL either remote or local based on the debug level
 */   
  public function jsLink($assets, $inline = true) {
    $this->setAssetDir($this->jsDir);
    return $this->Javascript->link($this->setAssetPath($assets), $inline);
  }
  
 /**
 * Return CSS link path/URL either remote or local based on the debug level
 */  
  public function css($assets, $rel = null, $htmlAttributes = array(), $inline = true) {
    $this->setAssetDir($this->cssDir);
    return $this->Html->css($this->setAssetPath($assets), $rel, $htmlAttributes, $inline);
  }
 
 /**
 * Prepare the asset path or URL and tack on the timestamp (if $this->forceTimestamp == TRUE) 
 * Works for arrays of assets (like with JS or CSS) or single files
 */  
  private function setAssetPath($assets = NULL) {
    if($assets && Configure::read() == 0) {
      if(is_array($assets)) {
        for($i = 0; $i < count($assets); $i++) {
          $assets[$i] = $this->pathPrep() . $assets[$i] . $this->getAssetTimestamp(); 
        }  
      } else {
        return $this->pathPrep() . $assets . $this->getAssetTimestamp();
      }
    }    
    return $assets;
  }
 
 /**
 * Build asset URL
 */  
  private function pathPrep() {
    return $this->getProtocol() . $this->getAssetHost($this->assetHost) . $this->assetDir;    
  }
 
 /**
 * Set proper asset directory (relative to web root), based on the asset type
 */  
  private function setAssetDir($dir = NULL) {
    if($dir) {
      $this->assetDir = '/' . $dir . '/';  
    }    
  }
 
 /**
 * Get asset timestamp
 * We assume that local filesystem has the same assets (and dir structure) as the remote one
 * (It really should to make managment and version controll painless)
 */   
  private function getAssetTimestamp() {
    if($this->forceTimestamp == TRUE) {
      return '?' . @filemtime(str_replace('/', DS, WWW_ROOT . $this->assetDir));
    }    
    return FALSE;
  } 
 
 /**
 * HTTPS or not?
 */    
  private function getProtocol() {
    if(env('HTTPS')) {
      return 'https://';
    }    
    return 'http://';
  }
 
 /**
 * Return host name.
 * Options: 
 * - multiple hosts (generate random host names based on $this->numHostsMin and $this->numHostsMax
 * - single host
 * - SSL host
 * 
 */  
  private function getAssetHost() {
    if(!env('HTTPS')) {
      if(strstr($this->assetHost, '%d')) {
        $randomHost = rand($this->numHostsMin, $this->numHostsMax);
        return sprintf($this->assetHost, $randomHost);  
      } else {
        return $this->assetHost;
      }
    } elseif (env('HTTPS')) {
        return $this->sslHost;      
    }
  }
  
}
?>