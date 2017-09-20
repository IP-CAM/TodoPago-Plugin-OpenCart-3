<?php

class ControlFraude_Digitalgoods extends ControlFraude{

	protected function completeCFVertical(){
		return $this->getMultipleProductsInfo();
	}
}
