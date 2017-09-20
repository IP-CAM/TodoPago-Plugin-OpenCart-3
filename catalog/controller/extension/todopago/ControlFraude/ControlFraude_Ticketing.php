<?php

class ControlFraude_Ticketing extends ControlFraude{

	protected function completeCFVertical(){
		return $this->getMultipleProductsInfo();
	}
}
