<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Set Templates.
 *
 * A helper library for working with Data Set Templates.
 *
 * @author	Arran Jacques
 */

class DataSetTemplatesHelper
{
	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Set Template. Group the values together and
	 * create a new Data Set Template for each unique Data Set Template
	 * identified.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetTemplatesFromInput(array $input)
	{
		$data_set_templates = \App::make('Illuminate\Database\Eloquent\Collection');
		foreach ($input as $key => $value) {
			if (strpos($key, '-component') !== false) {
				$data_set = array(
					'name' => null,
					'component' => null,
					'component_settings' => array(),
				);
				$uri = explode('-component', $key)[0];
				foreach ($input as $key => $value) {
					if (strpos($key, $uri) !== false AND strpos($key, '-name') !== false) {
						$data_set['name'] = $value;
					} elseif (strpos($key, $uri) !== false AND strpos($key, '-component') !== false) {
						$data_set['component'] = $value;
					} elseif (strpos($key, $uri) !== false) {
						$data_set['component_settings'][explode($uri . '-', $key)[1]] = $value;
					}
				}
				$data_set_template = \App::make('Fruitful\Data\Models\DataSetTemplate');
				$data_set_template->setURI($uri);
				$data_set_template->setName($data_set['name']);
				$data_set_template->setComponent($data_set['component']);
				$data_set_template->setComponentSettings($data_set['component_settings']);
				$data_set_templates->add($data_set_template);
			}
		}
		return $data_set_templates;
	}
}