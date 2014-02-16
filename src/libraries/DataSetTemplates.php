<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Set Templates.
 *
 * Implementation of the DataSetTemplatesInterface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\DataSetTemplatesInterface;

class DataSetTemplates implements DataSetTemplatesInterface
{
	/**
	 * Create a new instance of a Data Set Template.
	 *
	 * @param	String
	 * @return	Fruitful\Data\Libraries\DataSetTemplate
	 */
	public function make()
	{
		return \App::make('Fruitful\Data\Contracts\DataSetTemplateInterface');
	}

	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Set Template. Group the values together and
	 * create a new Data Set Template for each unique Data Set Temaplte
	 * identified.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetTemplatesFromInput($input)
	{
		$data_sets = new \Illuminate\Database\Eloquent\Collection;
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
				$data_set_template = $this->make();
				$data_set_template->setName($data_set['name']);
				$data_set_template->setComponent($data_set['component']);
				$data_set_template->setComponentSettings($data_set['component_settings']);
				$data_sets->add($data_set_template);
			}
		}
		return $data_sets;
	}
}