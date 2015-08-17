<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mvc_Router_CallebleRouter implements Mvc_Router_Interface {

	private $callable;

	public function __construct(callable $callable) {
		$this->callable = $callable;
	}
	
	public function route($request_params) {
		$callable = $this->callable;
		return $callable($request_params);
	}

}