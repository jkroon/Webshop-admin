<?php

class imageHandlerPlugin extends plugin {
	
	protected $data;
	protected $error;
	
	protected $allowedExt = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
	
	public function set($data) {
		
		if (is_array($data)) {

			if (!empty($data['name']) && !empty($data['tmp_name'])) {
				$this -> data = $data;
			} else {
				$this -> error = 'U heeft geen bestand geselecteerd';
			}
			
		} else {
			$this -> error = 'U heeft geen bestand geselecteerd';
		}
	}
	
	
	public function minWidth($width) {
		
		if (is_numeric($width) && $width > 0) {
			$this -> minWidth = $width;
		}
		
	}
	
	
	public function minHeight($height) {
		
		if (is_numeric($height) && $height > 0) {
			$this -> minHeight = $height;
		}
		
	}
	
	
	public function maxFileSize($size) {
		
		if (is_numeric($size) && $size) {
			$this -> maxFileSize = $size;
		}
		
	}
	
	
	public function save($destination) {
		
		// Er wordt gekeken of er geen error aanwezig is
		if (!$this -> error) {
			
			// De bestandsnaam wordt gecreerd
			$filename = $this -> data['name'];
			$tmp_name = $this -> data['tmp_name'];
			$file_size = floor($this -> data['size'] / 1000);
			
			// De extentie wordt van de bestandsnaam gestript
			$ext = explode('.', $filename);
			$ext = end($ext);
			$ext = strtolower($ext);
			
			// De extentie wordt van de bestandsnaam afgehaald
			$filename = preg_replace("/." . $ext . "$/", "", $filename);
			
			
			// Er wordt een timestamp voor de bestandsnaam te staan
			$filename = microtime() . '-' . $filename;
			$filename = preg_replace("/[^a-zA-Z0-9\-_]/", "", $filename);
			$filename = substr($filename, 10);
			
			if (strlen($filename) > 40) {
				$filename = substr($filename, 0, 40);
			}
			
			
			// De bestandsextentie wordt gecontroleerd
			if (!in_array($ext, $this -> allowedExt)) {
				$this -> error = 'U mag enkel afbeeldingen uploaden!';
				return false;
			}
			
			
			// De grootte van de afbeelding wordr gecontroleerd
			if (isset($this -> maxFileSize) && $file_size > $this -> maxFileSize) {
				$this -> error = 'Het door u opgegeven bestand is te groot om te uploaden. De maximale bestandsgrootte is ' . $this -> maxFileSize . 'Kb';
				return false;
			}
			
			
			// De afmetingen van het bestand worden, indien nodig, gecontroleerd
			if (isset($this -> minWidth) OR isset($this -> minHeight)) {
				
				// Het bestand wordt in de TMP map van de plugin geschreven
				$file = dirname(__FILE__) . DS . 'tmp/' . $filename . '.' . $ext;
				move_uploaded_file($tmp_name, $file);
				list($width, $height) = getimagesize($file);
				$uploaded = true;
				
				// De minimale breedte wordt gechecked
				if (isset($this -> minWidth) && $width < $this -> minWidth) {
					$this -> error = 'De door u geuploade afbeelding is te klein, de afbelding dient minimaal ' . $this -> minWidth . ' pixels breed zijn.';
					unlink($file);
					return false;
				}
				
				// De minimale hoogte wordt gechecked
				if (isset($this -> minHeight) && $height < $this -> minHeight) {
					$this -> error = 'De door u geuploade afbeelding is te klein, de afbeelding dient minimaal ' . $this -> minHeight . ' pixels hoog te zijn';
					unlink($file);
					return false;
				}
				
			}
			
			
			// De afbeelding wordt weggeschreven
			if (isset($uploaded)) {
				
				if (copy($file, $destination . $filename . '.' . $ext)) {
					unlink($file);
					$this -> fileName = $filename . '.' . $ext;
					
					// Indien noodzakelijk worden de thumbnails gemaakt
					if (isset($this -> thumbnails)) {
						$this -> createThumbs($destination . $filename . '.' . $ext, $filename . '.' . $ext, $destination);
					}
					
					return true;
				} else {
					unlink($file);
					$this -> error = 'Het bestand kon niet verplaatst worden';
					return false;
				}
				
			} else {
				
				if (move_uploaded_file($tmp_name, $destination . $filename . '.' . $ext)) {
					$this -> fileName = $filename . '.' . $ext;
					
					// Indien noodzakelijk worden de thumbnails gemaakt
					if (isset($this -> thumbnails)) {
						$this -> createThumbs($destination . $filename . '.' . $ext, $filename . '.' . $ext, $destination);
					}
					
					return true;
				} else {
					$this -> error = 'Het bestand kon niet worden opgeslagen.';
					return false;
				}
				
			}
			
		}
		
	}
	
	
	public function getFileName() {
		if (isset($this -> fileName)) {
			return $this -> fileName;
		}
	}
	
	
	public function getError() {
		if ($this -> error) {
			return $this -> error;
		} else {
			return false;
		}
	}
	
	
	public function thumbnails($array) {
		
		if (is_array($array)) {
			
			$this -> thumbnails = array();
			
			foreach($array as $thumb) {
								
				// De afmetingen worden afgelezen
				if ($exploded = explode('x', $thumb)) {
					if (is_numeric($exploded[0]) && is_numeric($exploded[1])) {
						$this -> thumbnails[] = array('height' => $exploded[0], 'width' => $exploded[1]);
					}
				}	
							
			}
			
		}
		
	}
	
	
	protected function createThumbs($file, $fileName, $destination) {
		
		
		// De huidige formaten van de afbeelding worden bepaald
		list($width, $height) = getimagesize($file);
		
		
		// De extentie wordt bepaald
		$ext = explode(".", $file);
		$ext = end($ext);
		$ext = strtolower($ext);
		$ext = ($ext == 'jpeg' ? 'jpg' : $ext);
		
		
		// De afbeelding wordt geopend
		switch($ext) {
			case "jpg":
				$image = imagecreatefromjpeg($file);
			break;
			
			case "png":
				$image = imagecreatefrompng($file);
			break;
			
			case "bmp":
				$image = imagecreatefromwbmp($file); 
			break;
			
			case "gif":
				$image = imagecreatefromgif($file);
			break;
		}
		
		
		// De counter wordt op 0 gezet
		$i = 1;
		
		
		// De thumbnail formaten worden door de loop gehaald
		foreach($this -> thumbnails as $thumbnail) {
			
			// Er wordt bepaald hoe groot de nieuwe afbeelding moet worden
			$newX = 100 / $width * $thumbnail['width'];
			$newY = 100 / $height * $thumbnail['height'];
			
			
			// Het grootste percentage wordt bepaald
			$percent = ($newX > $newY ? $newY : $newX);
			
			
			// Wanneer het percentage niet hoger is dan 100% wordt de thumbnail gecreerd
			if ($percent <= 100) {
				
							
				// De nieuwe hoogte en breedte worden definitief bepaald
				$imgX = ceil($width / 100 * $percent);
				$imgY = ceil($height / 100 * $percent);
				
				
				// De nieuwe afbeelding worde gecreerd
				$new = imagecreatetruecolor($imgX, $imgY);
				imagecopyresampled($new, $image, 0, 0, 0, 0, $imgX, $imgY, $width, $height);
				
				
				// De nieuwe locatie wordt bepaald
				$newDestination = $destination . $i . '__' . $fileName;
				
				
				// De thumbnail wordt gegenereerd en weggeschreven
				switch($ext) {
					case "jpg":
						imagejpeg($new, $newDestination, 100);
					break;
					
					case "png":
						imagepng($new, $newDestination);
					break;
					
					case "bmp":
						imagewbmp($new, $newDestination);
					break;
					
					case "gif":
						imagegif($new, $newDestination);
					break;
				}
				
				
				// De tijdelijke afbeelding wordt verwijderd
				imagedestroy($new);
				
				// De counter wordt opgeteld
				++$i;
				
			}
			
		}
		
	}
	
}