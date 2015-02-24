<?php

interface G2_FormBuilder_Field_Interface {
	
	function __construct($fieldname, $classes);
	function render($return = true);
	function set_enviroment(Twig_Environment $twig);
}

