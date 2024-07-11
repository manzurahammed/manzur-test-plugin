<?php

namespace MANZUR\TestPlugin\App\Commands;

use MANZUR\TestPlugin\App\Data_Manager;

/**
 * Class Refresh_Data
 
 * Handles the refreshing of API data by resetting the transient.
 *
 * @package Manzur\TestPlugin\App\Commands
 */
class Refresh_Data {
	/**
	 * Invoke the command to refresh API data.
	 *
	 * This method is called when the command is executed. It resets the transient
	 * to fetch new API data by calling the Data_Manager::refresh_data() method.
	 */
	public function __invoke() {
		Data_Manager::refresh_data();
	}
}
