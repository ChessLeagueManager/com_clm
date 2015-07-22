<?php
function clm_function_load_class($class, $args = array(), $new = false) {
	if (!class_exists("clm_class_" . $class)) {
		$path = clm_core::$path . DS . "classes" . DS . $class . '.php';
		require_once ($path);
	}
	if ($new) {
		$rc = new ReflectionClass("clm_class_" . $class);
		$class = $rc->newInstanceArgs($args);
		return $class;
	}
}
?>