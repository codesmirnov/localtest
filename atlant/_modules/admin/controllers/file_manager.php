<?php

class FileManager extends Controller {
  
	var $viewPath = 'tools/file_manager';
  
  function _preview() {
    $result = array();
    $files = scandir(ROOT);
    foreach ($files as $file) {
      if (! in_array($file, array('.','..'))) {
        $result[] = array(
          'title' => $file,
          'url'   => $this->params['url']['url'] . '/_' . $file
        );
      }
    }
    
    return $result;
  }

	function file_type($file) {
		if (preg_match('/\.([^\.]+)$/s', $file, $matches))
			return strtolower($matches[1]);
	}

	function create_zip($files = array(),$destination = '',$overwrite = false) {
		if(file_exists($destination) && !$overwrite) return false;
		$valid_files = array();
		if(is_array($files))
			foreach($files as $file)
				if(file_exists($file))
					$valid_files[] = $file;

		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true)
				return false;

			foreach($valid_files as $file)
				$zip->addFile($file,$file);

			$zip->close();
			return file_exists($destination);
		}	else
			return false;
	}

	function scandir($path, $hide_important = true) {
		$result = array(
			'dir' => array(),
			'files' => array()
		);
		$files = scandir($path);
    
		if (! empty($files))
		foreach ($files as $file)
			if ($file == '.' || $file == '..') {

			//}	elseif ($file[0] == '_' && $hide_important) {

			}	elseif (is_dir($path . DS . $file))
				$result['dir'][] = $file;
			else
				$result['files'][] = $file;

		if (! empty($result['files']))
			$types = array();
			foreach ($result['files'] as $file) {
				$type = $this->file_type($file);
				$types[$type][] = $file;
			}

		$result['files-by-type'] = $types;
		return $result;
	}

	function path() {
		$path = '';
		if (! empty($this->params['pass']))
			foreach ($this->params['pass'] as $i => $pass) {
				$pass = preg_replace('/^\_/s', '', $pass);
				#$relativePath .= ($i > 0 ? '/' : '') . $pass;
				$path .= DS . $pass;
				$this->crumb($pass, '_' . $pass);
				$this->params['current']['title'] = $pass;
			}
		return $path;
	}

	function files() {
		$path = $this->path($path);
		if (empty($path))
			$path = ROOT . $this->path();

		$this->index($path);
	}
	
	function fileperm($path) {
		return substr(sprintf('%o', fileperms($path)), -4);
			
	}		

	function index($path = '', $hide_important = true) {
		$relativePath = '';

		if (empty($path))
			$path = ROOT . $this->path();

		if (is_dir($path)) {
			$files = $this->scandir($path, $hide_important);
			$this->set('files', $files, false);
			$this->render('index');
		} else {
			if (! empty($this->data)) {
				$error = file_put_contents($path, $this->data['content']);
				$this->set('alertMessage', 'Сохранено');
			}

			$content = file_get_contents($path);
			$this->set('fileperm',  $this->fileperm($path));
			$this->set('filename', basename($path));
			$this->set('file_content', $content);
			$this->set('path', $path);

			$type = $this->file_type($path);
			if (in_array($type, array('php', 'ctp', 'html', 'js', 'css'))) {
				$mode = 'application/x-httpd-php';
				$modes = array(
					'css' => 'text/css',
					'js' => 'text/javascript'
				);

				if (isset($modes[$type]))
					$mode = $modes[$type];

				$this->set('mode', $mode);

				$this->render('edit-code-mirror');
			} elseif (in_array($type, array('jpg', 'jpeg', 'png', 'gif'))) {
        $src = str_replace(ROOT, '', $path);
        $src = str_replace('\\', '/', $src);
				$this->set('src', $src);
				$this->set('path', $path);
				$this->render('edit-img');
			} else {
				$content = file_get_contents($path);
				$this->set('file_content', $content);
				$this->render('edit-blank');
			}

		}

		$this->set('root', $path);
		$this->set('relative_path', $relativePath);
	}

	function save() {
		$this->render('/clear', 'clear');
		if (! empty($this->data)) {
			foreach ($this->data['files'] as &$file)
				$file = (! empty($this->data['path']) ? $this->data['path'] . '/' : '') . $file;
			$this->create_zip($this->data['files'], 'download.zip', false);
		}
	}

	function file_del ($folderPath)	{
		if (is_dir($folderPath))	{
			foreach (scandir($folderPath) as $value) {
				if ($value != "." && $value != "..") {
					$value = $folderPath . "/" . $value;

					if (is_dir($value)) {
						$this->file_del($value);
					}
					elseif (is_file($value))	{
						@unlink ($value);
					}
				}
			}

			return rmdir ( $folderPath );
		}
		else {
			unlink($file);
		}
	}

	function del() {
		$this->render('/clear', 'clear');
		if (! empty($this->data)) {
			foreach ($this->data['files'] as &$file) {
				$file = (! empty($this->data['path']) ? $this->data['path'] . '/' : '') . $file;
				$this->file_del($file);
			}
		}
	}

	function elfinder() {
	}

	function templates() {
		$file = $this->path();

		if (isset($file) && ! empty($file)) {
			$path = ROOT . DS . APP_DIR . '/views/client/' . $file;
		} else
			$path = ROOT . DS . APP_DIR . '/views/client/';

		$this->index($path, false);
	}
}

?>