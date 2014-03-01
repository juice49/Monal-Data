<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Sets.
 *
 * A Data Sets helper library implementing the DataSetsInterface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Libraries\DataSetsInterface;

class DataSets implements DataSetsInterface
{
	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Sets and Group the values together.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetValuesFromInput(array $input)
	{
		$data_sets = \App::make('Illuminate\Database\Eloquent\Collection');
		foreach ($input as $key => $value) {
			if (strpos($key, '-data_set') !== false) {
				$data_set = array(
					'name' => null,
					'component_values' => array(),
				);
				$uri = $value;
				foreach ($input as $key => $value) {
					if (strpos($key, $uri) !== false AND strpos($key, '-name') !== false) {
						$data_set['name'] = $value;
					} elseif (strpos($key, $uri) !== false AND strpos($key, '-data_set') === false) {
						$data_set['component_values'][explode($uri . '-', $key)[1]] = $value;
					}
				}
				$data_sets->add($data_set);
			}
		}
		return $data_sets;
	}
}