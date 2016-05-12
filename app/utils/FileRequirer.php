<?php

namespace TU\Utils;

class FileRequirer {

	public static function requireDirectory($path) {

		if(substr($path, -1) != "/") {

			$path = $path . "/";

		}

		foreach (glob($path . "*.php") as $filename) {
		
			require_once $filename;
		
		}		

	}

}
