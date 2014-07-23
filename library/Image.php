<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Image {

	public static function imagecreate($file, $image = false) {
		$ext = self::getExtension($file);
		$ext = strtolower($ext);
		switch ($ext) {
			case 'jpeg' :
			case 'jpg' :
				return imagecreatefromjpeg($file);
			case 'png' :
				$new_image = imagecreatefrompng($file);
				if ($image != false) {
					self::setTransparency($new_image, $image);
				}
				return $new_image;
			case 'gif' :
				$new_image = imagecreatefromgif($file);
				if ($image != false) {
					self::setTransparency($new_image, $image);
				}
				return $new_image;
		}
	}

	public static function _image($image, $file = null, $quality = null) {

		$ext = self::getExtension($file);
		$ext = strtolower($ext);
		switch ($ext) {
			case 'jpeg' :
			case 'jpg' :
				return imagejpeg($image, $file, $quality);
			case 'png' :
				return imagepng($image, $file, 1);
			case 'gif' :
				return imagegif($image, $file, $quality);
		}
	}

	public static function getExtension($str) {

		$i = strrpos($str, ".");
		if ($i === false) {
			return "";
		}
		$l = strlen($str) - $i;
		$ext = substr($str, $i + 1, $l);
		return $ext;
	}

	public static function setTransparency($new_image, $image_source) {

		$transparencyIndex = imagecolortransparent($image_source);
		$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

		if ($transparencyIndex >= 0) {
			$transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
		}

		$transparencyIndex = imagecolorallocatealpha($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue'], 127);
		imagesavealpha($new_image, true);
		imagefill($new_image, 0, 0, $transparencyIndex);
		imagealphablending($new_image, true);
		imagecolortransparent($new_image, $transparencyIndex);
	}

	public static function createthumb($name, $newname, $new_w, $new_h, $border = false, $transparency = true, $base64 = false) {

		if (file_exists($newname))
			@unlink($newname);
		if (!file_exists($name))
			return false;
		$arr = explode("\.", $name);
		$ext = $arr[count($arr) - 1];
		$ext = self::getExtension($name);

		if ($ext == "jpeg" || $ext == "jpg") {
			$img = @imagecreatefromjpeg($name);
		} elseif ($ext == "png") {
			$img = @imagecreatefrompng($name);
		} elseif ($ext == "gif") {
			$img = @imagecreatefromgif($name);
		}
		if (!$img)
			return false;
		$old_x = imageSX($img);
		$old_y = imageSY($img);
		if ($old_x < $new_w && $old_y < $new_h) {
			$thumb_w = $old_x;
			$thumb_h = $old_y;
		} elseif ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = floor(($old_y * ($new_h / $old_x)));
		} elseif ($old_x < $old_y) {
			$thumb_w = floor($old_x * ($new_w / $old_y));
			$thumb_h = $new_h;
		} elseif ($old_x == $old_y) {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
		$thumb_w = ($thumb_w < 1) ? 1 : $thumb_w;
		$thumb_h = ($thumb_h < 1) ? 1 : $thumb_h;
		$thumb_w = $new_w;
		$thumb_h = $new_h;

		$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);

		if ($transparency) {
			if ($ext == "png") {
				imagealphablending($new_img, false);
				$colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
				imagefill($new_img, 0, 0, $colorTransparent);
				imagesavealpha($new_img, true);
			} elseif ($ext == "gif") {
				$trnprt_indx = imagecolortransparent($img);
				if ($trnprt_indx >= 0) {
					//its transparent
					$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_img, 0, 0, $trnprt_indx);
					imagecolortransparent($new_img, $trnprt_indx);
				}
			}
		} else {
			Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		}

		imagecopyresampled($new_img, $img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
		if ($border) {
			$black = imagecolorallocate($new_img, 0, 0, 0);
			imagerectangle($new_img, 0, 0, $thumb_w, $thumb_h, $black);
		}
		if ($base64) {
			ob_start();
			imagepng($new_img);
			$img = ob_get_contents();
			ob_end_clean();
			$return = base64_encode($img);
		} else {
			if ($ext == "jpeg" || $ext == "jpg") {
				imagejpeg($new_img, $newname);
				$return = true;
			} elseif ($ext == "png") {
				imagepng($new_img, $newname);
				$return = true;
			} elseif ($ext == "gif") {
				imagegif($new_img, $newname);
				$return = true;
			}
		}
		imagedestroy($new_img);
		imagedestroy($img);
		return $return;
	}

	/*
	 * Get the correct size by giving the wanted height
	 */

	public static function get_size_for_height($old_height, $old_width, $new_height) {
		$width = ($old_width / $old_height) * $new_height;
		return array($width, $new_height);
	}

	/**
	 * Get the correct size by giving the correct wanted width
	 *
	 * @param type $old_height
	 * @param type $old_width
	 * @param type $new_width
	 * @return type
	 */
	public static function get_size_for_width($old_height, $old_width, $new_width) {
		$height = ($old_height / $old_width) * $new_width;
		return array($new_width, $height);
	}

}

?>
