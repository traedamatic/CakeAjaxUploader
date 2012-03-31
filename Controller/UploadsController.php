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

	public $name = "Upload";
	public $uses = null;

	// list of valid extensions, ex. array("jpeg", "xml", "bmp")
	var $allowedExtensions = array();

	public function upload($dir=null) {        	
		// max file size in bytes
		$size = Configure::read ('AMU.filesizeMB');
		debug($size);
		die;
		if (strlen($size) < 1) $size = 4;
		$relPath = Configure::read ('AMU.directory');
		if (strlen($relPath) < 1) $relPath = "files";

		$sizeLimit = $size * 1024 * 1024;
                $this->layout = "ajax";
	        Configure::write('debug', 0);
		$directory = WWW_ROOT . DS . $relPath;
 
		if ($dir === null) {
			$this->set("result", "{\"error\":\"Upload controller was passed a null value.\"}");
			return;
		}
		// Replace underscores delimiter with slash
		$dir = str_replace ("___", "/", $dir);
		$dir = $directory . DS . "$dir/";
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		$uploader = new qqFileUploader($this->allowedExtensions, 
			$sizeLimit);
		$result = $uploader->handleUpload($dir);
		$this->set("result", htmlspecialchars(json_encode($result), ENT_NOQUOTES));
	}


}

?>
