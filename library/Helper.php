<?php

/*
** Helper class
*/

class Helper {

	public static function toAscii($string, $allow = null) {
		$string = strtr($string, [
			'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ã' => 'a', 'Ä' => 'a', 'Å' => 'a',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
			'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
			'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i',
			'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
			'ð' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
			'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u',
			'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
			'Ç' => 'c', 'ç' => 'c',
			'Ñ' => 'n', 'ñ' => 'n',
			'Ý' => 'y', 'ý' => 'y', 'ÿ' => 'y',
			' ' => '_'
		]);

		return preg_replace('/([^a-zA-Z0-9_'. $allow .'])/', '', strtolower($string));
	}


	public static function removeEmptyValues($data, $recursive = true) {
		$is_array = is_array($data);

		foreach ((array) $data as $name => $value) {
			if (is_array($value) || is_object($value))
				$data[$name] = $recursive? Helper::removeEmptyValues : $value;

			else if (empty($data[$name]))
				unset($data[$name]);
		}

		return $is_array? $data : (object) $data;
	}

}
