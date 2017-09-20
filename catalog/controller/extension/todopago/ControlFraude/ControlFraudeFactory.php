<?php
class ControlFraudeFactory {

	const RETAIL = "Retail";
	const SERVICE = "Service";
	const DIGITAL_GOODS = "Digital Goods";
	const TICKETING = "Ticketing";

	public static function getControlFraudeExtractor($vertical, $order, $customer, $model, $logger){
		$instance;
		switch ($vertical) {
			case ControlFraudeFactory::RETAIL:
				$instance = new ControlFraude_Retail($order, $customer, $model, $logger);
			break;

			case ControlFraudeFactory::SERVICE:
				$instance = new ControlFraude_Service($order, $customer, $model, $logger);
			break;

			case ControlFraudeFactory::DIGITAL_GOODS:
				$instance = new ControlFraude_DigitalGoods($order, $customer, $model, $logger);
			break;

			default:
				$instance = new ControlFraude_Retail($order, $customer, $model, $logger);
			break;
		}
		return $instance;
	}
}
