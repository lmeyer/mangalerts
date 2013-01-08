<?php


class mangalertsFunctions {
	static public function generateKey($length)
	{
		$options = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-";
		$code = "";
		for($i = 0; $i < $length; $i++) {
			$key = rand(0, strlen($options) - 1);
			$code .= $options[$key];
		}

		return $code;
	}

	static public function slugify($text, $separator = '-')
	{
		$slug = $text;
		// transliterate
		if (function_exists('iconv')) {
			$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
		}

		// lowercase
		if (function_exists('mb_strtolower')) {
			$slug = mb_strtolower($slug);
		} else {
			$slug = strtolower($slug);
		}

		// remove accents resulting from OSX's iconv
		$slug = str_replace(array('\'', '`', '^'), '', $slug);

		// replace non letter or digits with separator
		$slug = preg_replace('/\W+/', $separator, $slug);

		// trim
		$slug = trim($slug, $separator);
		return $slug;
	}
}