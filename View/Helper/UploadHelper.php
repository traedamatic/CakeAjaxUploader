<?php 
/**
 *
 * Dual-licensed under the GNU GPL v3 and the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Suman (srs81 @ GitHub)
 * @package       plugin
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *                and/or GNU GPL v3 (http://www.gnu.org/copyleft/gpl.html)
 *
 *
 * @modified Nicolas Traeder (traedamatic@github) 
 */
 
class UploadHelper extends AppHelper {

	/**
	 *
	 * core helpers 
	 */
	public $helpers = array('Html');
	
	/**
 	 * upload dir 
	 */
	private $dir = null;
	
	/**
	 * Cake Js engine active
	 */
	
	private $jsEngine = false;
	
	/**
	 *
	 * contruct function
	 *
	 */	
	public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        debug($view->helpers);
		
		if(array_key_exists('Js',$view->helpers)) {
			$this->jsEngine = true;
			$this->Js = $view->Helpers->load('Js',array('Jquery'));
		}		
		
		$this->dir = Configure::read('AMU.directory');			
		
    }	
	
	
	/**
	 *
	 * renders the view of all files in on dir.
	 *
	 * @param string $model the model
	 * @param integer $id the id of the file
	 * @access public
	 */
	public function view ($model, $id) {
				
		$lastDir = $this->_lastDir($model, $id);
		$directory = WWW_ROOT . DS . $this->dir . DS . $lastDir;
		$baseUrl = Router::url("/") . $this->dir . DS . $lastDir;
		$files = glob ("$directory/*");
		$str = "<dt>" . __("Files") . "</dt>\n<dd>";
		$count = 0;
		foreach ($files as $file) {
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$str .= "<img src='" . Router::url("/") . CAKEAJAXUPLOADERPATH."/img/fileicons/$type.png' /> ";
			$filesize = $this->_formatBytes (filesize ($file));
			$file = basename($file);
			$url = $baseUrl . "/$file";
			$str .= "<a href='$url'>" . $file. "</a> ($filesize)";
			$str .= "<br />\n";
		}
		$str .= "</dd>\n"; 
		return $str;
	}
	
	/**
	 *
	 * renders the upload button
	 *
	 * @param string $model the model
	 * @param integer $id the id of the file
	 * @access public
	 */	
	public function edit ($model, $id) {		

		$str = $this->view ($model, $id);
				
		$webroot = Router::url("/") . CAKEAJAXUPLOADERPATH;
		
		// Replace / with underscores for Ajax controller
		$lastDir = str_replace ("/", "___", $this->_lastDir ($model, $id));
		
		$this->Html->css($webroot.DS.'css/fileuploader.css',null,array('inline' => false));
		$this->Html->script($webroot.DS.'js/fileuploader.js',array('inline' => false));
		
		$str .= '
			<div id="AjaxMultiUpload">
				<noscript>
					 <p>Please enable JavaScript to use file uploader.</p>
				</noscript>
			</div>
		';

		if($this->jsEngine) {

			$script = "	          
				var uploader = new qq.FileUploader({
					element: document.getElementById('AjaxMultiUpload'),
					action: '$webroot/uploads/upload/$lastDir/',
					debug: true
				});           
			";
		
			$this->Js->buffer($script);
			
		} else {
	
			$script = "
				function createUploader(){            
					var uploader = new qq.FileUploader({
						element: document.getElementById('AjaxMultiUpload'),
						action: '$webroot/uploads/upload/$lastDir/',
						debug: true
					});           
				}
				window.onload = createUploader;     
			";

			$this->Html->scriptBlock($script,array('inline' => false));
		}

		return $str;
	}

	// Function to create the "last" set of directories for uploading
	private function _lastDir ($model, $id) {
		return $model . "/" . $id;
	}

	// From http://php.net/manual/en/function.filesize.php
	private function _formatBytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}
}
