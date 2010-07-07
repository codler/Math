<?php
/**
 * Author: Han Lin Yap
 * Website: http://www.zencodez.net
 * Last-modified: 2010-07-07 version 1
 * License: http://creativecommons.org/licenses/by-sa/3.0/
 *
 * Formula found at http://en.wikipedia.org/wiki/Numerical_methods_for_linear_least_squares
 * 
 * Usage - The data format is array in array
 * $data = array(array($x1, $y2), array($x2, $y2));
 * $regression = new linear_regression($data);
 * $regression->calculate();
 * // y = k*x + m
 * echo $regression->k;
 * echo $regression->m;
 * Array containing number of coordinates and the coordinates contain an array of x and y.
 *
 */
class linear_regression {
	var $data;
	
	// needed for linear regression formula
	var $sum_x;
	var $sum_x2;
	var $sum_y;
	var $sum_xy;
	var $number_of_coords;
	
	// y = k*x + m
	var $k;
	var $m;
	
	function __construct($data) {
		$this->data = $data;
	}
	
	function calculate() {
		$this->number_of_coords = count($this->data);
	
		$this->sum_x = $this->sum_by_formula($this->data, '$x[0]');
		$this->sum_x2 = $this->sum_by_formula($this->data, 'pow($x[0],2)');
		$this->sum_y = $this->sum_by_formula($this->data, '$x[1]');
		$this->sum_xy = $this->sum_by_formula($this->data, '$x[0] * $x[1]');
		
		
		$this->k = ($this->number_of_coords * $this->sum_xy - $this->sum_y * $this->sum_x) / ($this->number_of_coords * $this->sum_x2 - pow($this->sum_x, 2));
		
		$this->m = ($this->sum_y * $this->sum_x2 - $this->sum_x * $this->sum_xy) / ($this->number_of_coords * $this->sum_x2 - pow($this->sum_x, 2));
		
	}
	
	// Gets all by a formula
	function _get_values_by_formula($data, $formula) {
		return array_map(create_function('$x', 'return ' . $formula . ';'), $data);
	}
	
	// Sum all by a formula
	function sum_by_formula($data, $formula) {
		return array_sum($this->_get_values_by_formula($data, $formula));
	}
	
}
?>