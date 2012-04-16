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
 	 * @author Nicolas Traeder <traeder@codebility.com>
	 */
	private $dir = null;
	
	/**
	 * Cake Js engine active
	 * @author Nicolas Traeder <traeder@codebility.com>
	 */
	
	private $jsEngine = false;
	
	/**
	 *
	 * contruct function
	 * @author Nicolas Traeder <traeder@codebility.com>
	 */	
	public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        		
		if(array_key_exists('Js',$view->helpers)) {
			$this->jsEngine = true;
			$this->Js = $view->Helpers->load('Js',array('Jquery'));
		}		
		
		$this->dir = Configure::read('AMU.directory');			
		
		
		$this->Html->css(DS.CAKEAJAXUPLOADERPATH.DS.'css/cakeajaxuploader.css',null,array('inline' => false));
    }	
	
	
	/**
	 *
	 * renders the view of all files in on dir.
	 *
	 * @param string $model the model
	 * @param integer $id the id of the file
	 * @access public
	 */
	public function view ($path = null) {
		if(is_null($path)) {
			$fileDir = "";
		} else {			
			$fileDir = $path;
		}
		
		$directory = WWW_ROOT . DS . $this->dir . DS . $fileDir;
		$wwwFilepath = Router::url("/") . $this->dir . DS . $fileDir;
		$files = glob ("$directory/*");
		
		$htmlResult = "<h1>" . __("Files - $wwwFilepath") . "</h1>\n";
		$htmlResult .= '<ul class="filelisting">';
		$count = 0;
		foreach ($files as $file) {
			$htmlResult .= '<li class="file">'.$this->preview($file).'</li>';
		}
		$htmlResult .= "</ul>\n"; 
		return $htmlResult;
	}
	
	/**
	 *
	 * return file of one dir as array
	 * 	 
	 * @param string the path of the dir
	 * @return array a array of all files in that dir
	 * @access public
	 */
	public function returnFiles($path = null) {
	  if(is_null($path)) {
			$fileDir = "";
		} else {			
			$fileDir = $path;
		}
		$directory = WWW_ROOT . DS . $this->dir . DS . $fileDir;
		$files = glob ("$directory/*");
		
		return array_map(function($file){
					return str_replace(WWW_ROOT,"",$file);
			 },$files);		
	}
	
	/**
	 *
	 * renders preview element of the file
	 * 
	 * @author Nicolas Traeder <traeder@codebility.com> 
	 * @access public
	 * @return string the html markup of the preview element 
	 */
	public function preview($file = null) {
		if(is_null($file)) return "";
		$htmlResult = "";
		$wwwFilepath = str_replace(WWW_ROOT,'/',$file);
		$type = pathinfo($file, PATHINFO_EXTENSION);
		$filesize = $this->_formatBytes(filesize ($file));
		$filename = basename($file);
		
		$htmlResult .= $this->Html->image(DS.CAKEAJAXUPLOADERPATH.DS."/img/fileicons/$type.png");
		$htmlResult .= $this->Html->link($filename,$wwwFilepath);
		$htmlResult .= " ($filesize) ";
		
		$url = array(
						 'controller' => 'uploads',
						  'action' => 'delete',
						  'plugin' => 'cake_ajax_uploader',base64_encode($file),						  
						  );
		
		if(isset($this->request->params['prefix'])) {
			 $url[$this->request->params['prefix']] = false;	 
		}		
		
		$htmlResult .= $this->Html->link(__('Delete'),$url);		
		return $htmlResult;
	}
	
	/**
	 *	 
	 * renders a list of all files in the upload directory.
	 * including subdirs
	 * 
	 * @author Nicolas Traeder <traeder@codebility.com> 
	 * @access public
	 * @return string the html markup of the list 
	 */
	public function listing() {
		App::uses('File','Utility');
		App::uses('Folder','Utility');
		
		$htmlResult = "<ul class=\"filelisting\">";				
		$directory = WWW_ROOT  . $this->dir;
		
		$Folder = new Folder($directory);				
		$dirsAndFiles = $Folder->read();
		
		//folders
		foreach($dirsAndFiles[0] as $dir) {			
			$Folder = new Folder($directory.DS.$dir);
			//does not search an depper dirs..
			$filesInDir = $Folder->findRecursive();			
			$htmlResult .= '<li class="folder">'.$dir.'</li>';
			foreach($filesInDir as $file) {
				$htmlResult .= '<li class="level_1 file">'.$this->preview($file).'</li>';
			}
		}
		
		//files
		foreach($dirsAndFiles[1] as $file) {			
			$htmlResult .= '<li class="file">'.$this->preview($directory.DS.$file).'</li>';
		}
		
		$htmlResult .= "</ul>";
		
		return $htmlResult;
	}
	
	/**
	 *
	 * renders the upload button
	 *
	 * @param string $model the model
	 * @param integer $id the id of the file
	 * @access public
	 * @modified Nicolas Traeder <traeder@codebility.com>
	 */	
	public function edit ($path = "") {		

		//$str = $this->view ($model, $id);
		$str = "";
				
		$webroot = Router::url("/") . CAKEAJAXUPLOADERPATH;
				
		$uploadDir = $this->_parseDir($path);
		
		$this->Html->css($webroot.DS.'css/fileuploader.css',null,array('inline' => false));
		$this->Html->script($webroot.DS.'js/fileuploader.js',array('inline' => false));
		
	  $ajaxUploaderId = "ajaxuploader-".md5($path.time());		
		
		$str .= '
			<div class="ajaxuploader-container" id="'.$ajaxUploaderId.'">
				<noscript>
					 <p>Please enable JavaScript to use file uploader.</p>
				</noscript>
			</div>
		';

		if($this->jsEngine) {

			$script = "	          
				var uploader = new qq.FileUploader({
					element: document.getElementById('$ajaxUploaderId'),
					action: '$webroot/uploads/upload/$uploadDir/',
					debug: true
				});           
			";
		
			$this->Js->buffer($script);
			
		} else {
	
			$script = "
				function createUploader(){            
					var uploader = new qq.FileUploader({
						element: document.getElementById('$ajaxUploaderId'),
						action: '$webroot/uploads/upload/$uploadDir/',
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
	private function _parseDir ($path = "") {
		if(empty($path) || strlen($path) == 0) {
			return false;
		}
		$return = str_replace ("/", "___", $path);
		return $return;
	}

	// From http://php.net/manual/en/function.filesize.php
	private function _formatBytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}
}
