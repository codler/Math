<?php 
// Examples

require_once('math.php');

$l = array(
	array(1,2,3),
	array(4,5,6)
);
$r = array(
	array(1,2),
	array(3,4),
	array(5,6)
);
// matrix multiply
print_r(zc_math::matrix_multiply($l, $r));
// matrix transpose
print_r(zc_math::matrix_transpose($l));
?>