<?php
if (!class_exists('zc_math')) {
	require_once('math.php');
}

/**
 * Linear class
 * 
 * The data format is array in array. 
 * Array containing number of coordinates and the coordinates contain an array of x and y.
 * 
 * Example usage
 * <code>
 * $data = array(array($x1, $y1), array($x2, $y2));
 * $linear = new zc_math_linear($data);
 * $linear->calculate();
 * // y = k*x + m
 * echo $linear->k;
 * echo $linear->m;
 * </code>
 * 
 * Linear regression formula found at {@link http://en.wikipedia.org/wiki/Numerical_methods_for_linear_least_squares#Computation }
 * 
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2010 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package math
 * @version 1.0 - 2010-07-29
 */
class zc_math_linear {
	/**
	 * Contains the coordinates
	 * @var array
	 */
	public $data;
	
	// needed for linear regression formula
	public $sum_x;
	public $sum_x2;
	public $sum_y;
	public $sum_xy;
	public $number_of_coords;
	
	// y = k*x + m
	/**
	 * @var float
	 */
	public $k;
	/**
	 * @var float
	 */
	public $m;
	
	/**
	 * @param array $data
	 */
	function __construct($data) {
		$this->data = $data;
	}
	
	/**
	 * Calculate and make formula
	 * To understand better, {@link http://en.wikipedia.org/wiki/Numerical_methods_for_linear_least_squares#Computation see formula}
	 * @since 1.0
	 */
	public function calculate() {
		$this->number_of_coords = count($this->data);
	
		$this->sum_x = $this->sum_by_formula($this->data, '$x[0]');
		$this->sum_x2 = $this->sum_by_formula($this->data, 'pow($x[0],2)');
		$this->sum_y = $this->sum_by_formula($this->data, '$x[1]');
		$this->sum_xy = $this->sum_by_formula($this->data, '$x[0] * $x[1]');
		
		
		$this->k = ($this->number_of_coords * $this->sum_xy - $this->sum_y * $this->sum_x) / ($this->number_of_coords * $this->sum_x2 - pow($this->sum_x, 2));
		
		$this->m = ($this->sum_y * $this->sum_x2 - $this->sum_x * $this->sum_xy) / ($this->number_of_coords * $this->sum_x2 - pow($this->sum_x, 2));
		
	}
	
	/**
	 * Gets all by a formula
	 * Turn a multidimention array to a ordinary array with values
	 * @param array $data Multidimention array
	 * @param string $formula How to get what
	 * @return array Get values in multidimention by formula
	 * @since 1.0
	 */
	protected function _get_values_by_formula($data, $formula) {
		return array_map(create_function('$x', 'return ' . $formula . ';'), $data);
	}
	
	/**
	 * Sum all by a formula
	 * @param array $data Data to sum
	 * @param string $formula Formula to sum
	 * @return float sum of $data by formula
	 * @since 1.0
	 */
	public function sum_by_formula($data, $formula) {
		return array_sum($this->_get_values_by_formula($data, $formula));
	}
	
}


/**
 * Polynomial regression
 *
 * Example usage
 * <code>
 * $polynomial = new zc_math_polynomial($data);
 * echo print_r($polynomial->calculate_coefficients());
 * echo "<img src='" . $polynomial->render_formula() . "' />";
 * echo "<a href='" . $polynomial->render_formula('wolframalpha') . "'>" . $polynomial->render_formula('') . "</a>";
 * echo $polynomial->get_y(1200);
 * </code>
 * {@link http://www.trentfguidry.net/post/2009/06/30/Matrix-Crout-LU-decomposition.aspx }
 * {@link http://www.trentfguidry.net/post/2009/07/19/Linear-multiple-regression.aspx }
 * {@link http://www.trentfguidry.net/post/2009/08/01/Linear-regression-polynomial-coefficients.aspx }
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2010 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package math
 * @version 1.0 - 2010-07-30
 */
class zc_math_polynomial extends zc_math_linear {
	/**
	 * The calculated formula in array
	 * @var array
	 */
	public $result;
	/**
	 * Order to calculate
	 * @var integer
	 */
	public $order;
	
	/**
	 * Initialize
	 * @param array $data Format array(array($x1, $y2), array($x2, $y2))
	 * @param integer $order optional What order the formula should be
	 * @since 1.0
	 */
	function __construct($data, $order=4) {
		$this->data = $data;
		$this->set_order($order);
	}
	
	/**
	 * Change order
	 * @param integer $order What order the formula should be
	 * @since 1.0
	 */
	public function set_order($order) {
		$this->order = $order;
	}
	
	/**
	 * Find the formula
	 * @param integer $order optional What order the formula should be
	 * @return array The found formula in array
	 * @since 1.0
	 */
	public function calculate_coefficients($order=false) {
		if ($order)
			$this->set_order($order);
			
		$x = $this->_get_values_by_formula($this->data, '$x[0]');
		$y = $this->_get_values_by_formula($this->data, '$x[1]');
		
		$z = array_fill(0, count($y), array());
		
		for ($i = 0 ; $i < count($y) ; $i++) {
			for ($j = 0 ; $j <= $this->order ; $j++) {
				$z[$i][$j] = pow($x[$i],$j);
			}
		}
		$this->result = $this->_polynomial_regress($z, $y);
		return $this->result;
	}
	
	/**
	 * Render formula for wolframalpha, google chart, url or plain math text
	 *
	 * NOTE! {@link zc_math_polynomial::calculate_coefficients()} before calling this method
	 * Example usage
	 * <code>
	 * render_formula()
	 * render_formula('wolframalpha')
	 * render_formula('')
	 * render_formula('text');
	 * </code>
	 *
	 * @param string $type optional Available types wolframalpha, text, google or ''
	 * @param string|boolean $replace_x optional If you want to customize x output
	 * @return string URL or text, depends on $type
	 * @uses zc_math_polynomial::calculate_coefficients() Call it before to have something to render
	 * @since 1.0
	 */
	public function render_formula($type='google', $replace_x=false) {
		$formula = array();
		$text = '';
		if ($type=='text')
			$text = '*';
		if (!$replace_x)
			$x = 'x';
		else 
			$x = $replace_x;
		foreach($this->result AS $k => $v) {
			if ($k == 0)
				$formula[] = $v[0];
			elseif ($k == 1)
				$formula[] = $v[0] . $text . $x;
			elseif ($type!='google')
				$formula[] = $v[0] . $text . $x . "^". $k;
			else
				$formula[] = $v[0] . "x^{". $k . "}";
		}
		
		if ($type=='wolframalpha')
			return 'http://www.wolframalpha.com/input/?i=' . implode("+%2b+", $formula);
		elseif ($type=='text')
			return implode("%2b", $formula);
		elseif ($type!='google')
			return 'y = ' . implode(" + ", $formula);
		else
			return 'http://chart.apis.google.com/chart?cht=tx&chl=y+=+' . implode("%2b", $formula);
	}
	
	/**
	 * Get y value from x coordinate
	 *
	 * NOTE! {@link zc_math_polynomial::calculate_coefficients()} before calling this method
	 * 
	 * @param integer $x X-coordinate
	 * @return integer Y-coordinate
	 * @uses zc_math_polynomial::calculate_coefficients() Call it before to have something to check on
	 * @since 1.0
	 */
	public function get_y($x) {
		$sum = 0;
		foreach($this->result AS $k => $v) {
			if ($k == 0)
				$sum += $v[0];
			else
				$sum += $v[0] * pow($x,$k);
		}
		
		return $sum;
	}
	
	private function _polynomial_regress($z, $y) {
		$z_transpose = zc_math::matrix_transpose($z);
		$l = zc_math::matrix_multiply($z_transpose, $z);
		$r = zc_math::matrix_multiply($z_transpose, zc_math::matrix_transpose(array($y)));
		
		return $this->_solve_for($l, $r);
	}
	
	private function _solve_for($l, $r) {
		$resultMatrix = array_fill(0, count($l[0]), array());
		$resDecomp = $this->_lu_decompose($l);
		
		$nP = $resDecomp['PivotArray'];
		$lMatrix = $resDecomp['L'];
		$uMatrix = $resDecomp['U'];
		for ($k = 0 ; $k < count($r[0]) ; $k++) {
			$sum = 0.0;
			$dMatrix = array_fill(0, count($l), array());
			$dMatrix[0][0] = $r[$nP[0]][$k] / $lMatrix[0][0];
			for ($i = 1; $i < count($l); $i++) {
				$sum = 0.0;
				for ($j = 0; $j < $i; $j++) {
					$sum += $lMatrix[$i][$j] * $dMatrix[$j][0];
				}
				
				$dMatrix[$i][0] = ($r[$nP[$i]][$k] - $sum) / $lMatrix[$i][$i];
			}
			
			$resultMatrix[count($l) - 1][$k] = $dMatrix[count($l) - 1][0];
			for ($i = count($l) - 2; $i >= 0; $i--)	{
				$sum = 0.0;
				for ($j = $i + 1; $j < count($l); $j++)	{
					$sum += $uMatrix[$i][$j] * $resultMatrix[$j][$k];
				}
				$resultMatrix[$i][$k] = $dMatrix[$i][0] - $sum;
			}
		}
		
		return $resultMatrix;
	}
	
	private function _lu_decompose($l) {
		if (!defined('PHP_INT_MIN')) {
			define('PHP_INT_MIN', ~PHP_INT_MAX);
		}
		$_rowCount = count($l);
		$_columnCount = count($l[0]);
		
		$uMatrix = array_fill(0, $_rowCount, array());
		$lMatrix = array_fill(0, $_rowCount, array());
		$workingUMatrix = $l;
		$workingLMatrix = array_fill(0, $_rowCount, array());
		$pivotArray = range(0, $_rowCount-1);
		
		for ($i = 0; $i < $_rowCount; $i++) {
			$maxRowRatio = PHP_INT_MIN;
			$maxRow = -1;
			$maxPosition = -1;
						
			for ($j = $i; $j < $_rowCount; $j++) {
				$rowSum = 0.0;
				
				for ($k = $i; $k < $_columnCount; $k++) {
					$rowSum += abs($workingUMatrix[$pivotArray[$j]][$k]);
				}
				
				$dCurrentRatio = abs($workingUMatrix[$pivotArray[$j]][$i]) / $rowSum;
									
				if ($dCurrentRatio > $maxRowRatio) {
					$maxRowRatio = abs($workingUMatrix[$pivotArray[$j]][$i] / $rowSum);
					$maxRow = $pivotArray[$j];
					$maxPosition = $j;
				}
			}
			if ($maxRow != $pivotArray[$i]) {
				
				$hold = $pivotArray[$i];
				$pivotArray[$i] = $maxRow;
				$pivotArray[$maxPosition] = $hold;
			}
			$rowFirstElementValue = $workingUMatrix[$pivotArray[$i]][$i];
			
			
			for ($j = 0; $j < $_columnCount; $j++) {
				if ($j < $i) {
					$workingUMatrix[$pivotArray[$i]][$j] = 0.0;
                } elseif ($j == $i) {
					$workingLMatrix[$pivotArray[$i]][$j] = $rowFirstElementValue;
					$workingUMatrix[$pivotArray[$i]][$j] = 1.0;
				} else {
					$workingUMatrix[$pivotArray[$i]][$j] /= $rowFirstElementValue;
					$workingLMatrix[$pivotArray[$i]][$j] = 0.0;
                }
			}
				
			for ($k = $i + 1; $k < $_rowCount; $k++) {
			
				$rowFirstElementValue = $workingUMatrix[$pivotArray[$k]][$i];
				
				for ($j = 0; $j < $_rowCount; $j++) {
					if ($j < $i) {
						$workingUMatrix[$pivotArray[$k]][$j] = 0.0;
					} elseif ($j == $i) {
						$workingLMatrix[$pivotArray[$k]][$j] = $rowFirstElementValue;
                        $workingUMatrix[$pivotArray[$k]][$j] = 0.0;
					} else {
						$workingUMatrix[$pivotArray[$k]][$j] = $workingUMatrix[$pivotArray[$k]][$j] - $rowFirstElementValue * $workingUMatrix[$pivotArray[$i]][$j];
					}
				}
				  
			}
			
		}
		
		for ($i = 0; $i < $_rowCount; $i++) {
			for ($j = 0; $j < $_rowCount; $j++) {
				$uMatrix[$i][$j] = $workingUMatrix[$pivotArray[$i]][$j];
                $lMatrix[$i][$j] = $workingLMatrix[$pivotArray[$i]][$j];
			}
		}
		
		return array('U' => $uMatrix, 'L' => $lMatrix, 'PivotArray' => $pivotArray);
	}
}

?>