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
 */
 
class UploadsController extends CakeAjaxUploaderAppController {
	
	/**
	 *
	 * controller name
	 */	
	public $name = "Upload";
	
	/**
	 * 
	 * what model will be used
	 */
	public $uses = array();

	/**
	 * 
	 * list of valid extensions, ex. array("jpeg", "xml", "bmp")
	 */
	private $allowedExtensions = array();
	
	/**
	 * helpers
	 * 
	 */
	
	public $helpers = array('CakeAjaxUploader.Upload');
	
	/**
 	 *
 	 * components
	 */
	
	public $components = array('Session');
	
	/**
	 *
	 * the upload function
	 * @param string $dir the path of the dir
	 * @access public
	 */	
	public function upload($dir=null) {
		
		
		// max file size in bytes
		$size = Configure::read ('AMU.filesizeMB');
		
		if (strlen($size) < 1) $size = 4;
		$relPath = Configure::read ('AMU.directory');
		if (strlen($relPath) < 1) $relPath = "files";

		$sizeLimit = $size * 1024 * 1024;        
		$directory = WWW_ROOT . $relPath;
 		
		// Replace underscores delimiter with slash
		if($dir != false || !is_null($dir)) {			
			$dir = str_replace ("___", "/", $dir);
			$dir = $directory . DS . "$dir/";
			if (!file_exists($dir)) {
				mkdir($dir, 0777, true);
			}
		} else {
			$dir = $directory . DS;
		}
		
		
		$uploader = new qqFileUploader($this->allowedExtensions,$sizeLimit);
		$result = $uploader->handleUpload($dir);
		
		Configure::write('debug', 0);
		$this->layout = "ajax";	    		
		$this->set("result", htmlspecialchars(json_encode($result), ENT_NOQUOTES));
	}
	
	/**
	 *
	 * index
	 *
	 * overview of all files that were uploaded	
	 */	
	public function index() {
		
		$this->set('filePath', WWW_ROOT.Configure::read('AMU.directory'));		
		
	}
	
	/**
	 *
	 * delete a file
	 *
	 */
	
	public function delete($file = null) {
		if(is_null($file)) {
			$this->Session->setFlash(__('File parameter is missing'));
			$this->redirect(array('action' => 'index'));
		}
		
		$file = base64_decode($file);
		if(file_exists($file)) {
			if(unlink($file)) {
				$this->Session->setFlash(__('File deleted!'));				
			} else {
				$this->Session->setFlash(__('Unable to delete File'));					
			}
		} else {
			$this->Session->setFlash(__('File does not exists!'));					
		}
		
		$this->redirect(array('action' => 'index'));
		
	}


}

?>
