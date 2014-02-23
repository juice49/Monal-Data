<?php
namespace Fruitful\Data;

use Illuminate\Support\ServiceProvider;

class DataServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var		Boolean
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return	Void
	 */
	public function boot()
	{
		$this->package('fruitful/data');

		$routes = __DIR__.'/../../routes.php';
		if (file_exists($routes)){
			include $routes;
		}
		
		\View::addLocation(__DIR__ . '/../../components');
		\View::addNamespace('components', __DIR__ . '/../../components');

		\Fruitful::registerMenuOption('Data', 'Data Sets', 'data-sets', 'data_sets');
		\Fruitful::registerMenuOption('Data', 'Data Streams', 'data-streams', 'data_streams');
		\Fruitful::registerPermissionSet(
			'Data',
			'data_sets',
			array(
				'Create Data Set' => 'create_data_set',
				'Edit Data Set' => 'edit_data_set',
				'Delete Data Set' => 'delete_data_set',
			)
		);
		\Fruitful::registerPermissionSet(
			'Data Set Templates',
			'data_set_templates',
			array(
				'Create Data Set Template' => 'create_data_set_template',
				'Edit Data Set Template' => 'edit_data_set_template',
				'Delete Data Set Template' => 'delete_data_set_template',
			)
		);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Fruitful\Data\Contracts\ComponentsInterface',
			function() {
				return new \Fruitful\Data\Libraries\Components;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Contracts\DataSetInterface',
			function() {
				return new \Fruitful\Data\Libraries\DataSet;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Contracts\DataSetsInterface',
			function() {
				return new \Fruitful\Data\Libraries\DataSets;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Contracts\DataSetTemplatesInterface',
			function() {
				return new \Fruitful\Data\Libraries\DataSetTemplates;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Contracts\DataSetTemplateInterface',
			function() {
				return new \Fruitful\Data\Libraries\DataSetTemplate;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\Contracts\DataSetsRepository',
			function() {
				return new \Fruitful\Data\Repositories\EloquentDataSetsRepository;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\Contracts\DataSetTemplatesRepository',
			function() {
				return new \Fruitful\Data\Repositories\EloquentDataSetTemplatesRepository;
			}
		);
	}
}