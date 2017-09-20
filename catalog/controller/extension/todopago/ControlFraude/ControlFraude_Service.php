<?php

class ControlFraude_Service extends ControlFraude{

	protected function completeCFVertical(){
		return $this->getMultipleProductsInfo();
	}
}
