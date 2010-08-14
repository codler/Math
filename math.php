<?php
require_once('regression.php');
/* function zc_math_autoloader($class) {
	require_once($class . ".php");
}

spl_autoload_register('zc_math_autoloader'); */

/**
 * Math class
 *
 * Math helper functions
 * -- INDEX --
 * matrix_multiply
 * matrix_transpose
 *
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2010 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package math
 * @version 1.0 - 2010-08-13
 */
class zc_math {
	/**
	 * Factory
	 * @param string $classname optional math class to initialize
	 * @param mixed $args optional arguments of the class constructor
	 * @return instance of math class
	 * @since 1.0
	 */
	public static function factory() {
		$args = func_get_args();
		$class = array_shift($args);
		$reflection = new ReflectionClass(__CLASS__ . '_' . $class);
		return $reflection->newInstanceArgs($args);
	}

	/**
	 * Matrix multiplication
	 * @link http://en.wikipedia.org/wiki/Matrix_multiplication Wikipedia
	 * @param matrix $left Left matrix
	 * @param matrix $right Right matrix
	 * @return matrix
	 * @since 1.0
	 */
	public static function matrix_multiply($left, $right) {
		$m = array_fill(0, count($left), array());

		foreach ($right[0] as $r_col => $v) {
			foreach ($left as $l_row => $l_col) {
				$z = 0;
				foreach ($l_col as $col => $l_value) {
					$z += $l_value * $right[$col][$r_col];
				}
				$m[$l_row][$r_col] = $z;
			}
		}
		
		return $m;
	}
	/**
	 * Matrix transpose
	 *
	 * Eg.
	 * [1,2,3]
	 * [4,5,6]
	 * becomes
	 * [1,4]
	 * [2,5]
	 * [3,6]
	 * @link http://en.wikipedia.org/wiki/Transpose Wikipedia
	 * @link http://stackoverflow.com/questions/797251/transposing-multidimensional-arrays-in-php/797268#797268
	 * @param matrix $transpose matrix to transpose
	 * @return matrix Transposed matrix
	 * @since 1.0
	 */
	public static function matrix_transpose($transpose) {
		/* array_unshift($transpose, null);
		return call_user_func_array('array_map', $transpose); */
	
		$out = array();
		foreach ($transpose as $key => $subarr) {
			foreach ($subarr as $subkey => $subvalue) {
					$out[$subkey][$key] = $subvalue;
			}
		}
		return $out;
	}
}

?>