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
		\Fruitful::registerPermissionSet(
			'Data Stream Templates',
			'data_stream_templates',
			array(
				'Create Data Stream Template' => 'create_data_stream_template',
				'Edit Data Stream Template' => 'edit_data_stream_template',
				'Delete Data Stream Template' => 'delete_data_stream_template',
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
			'Fruitful\Data\Libraries\ComponentsInterface',
			function () {
				return new \Fruitful\Data\Libraries\Components;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Models\DataSet',
			function () {
				return new \Fruitful\Data\Models\FruitfulDataSet;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Models\DataSetTemplate',
			function () {
				return new \Fruitful\Data\Models\FruitfulDataSetTemplate;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Models\DataStream',
			function () {
				return new \Fruitful\Data\Models\FruitfulDataStream;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Models\DataStreamTemplate',
			function () {
				return new \Fruitful\Data\Models\FruitfulDataStreamTemplate;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\DataSetsRepository',
			function () {
				return new \Fruitful\Data\Repositories\EloquentDataSetsRepository;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\DataSetTemplatesRepository',
			function () {
				return new \Fruitful\Data\Repositories\EloquentDataSetTemplatesRepository;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\DataStreamsRepository',
			function () {
				return new \Fruitful\Data\Repositories\EloquentDataStreamsRepository;
			}
		);
		$this->app->bind(
			'Fruitful\Data\Repositories\DataStreamTemplatesRepository',
			function () {
				return new \Fruitful\Data\Repositories\EloquentDataStreamTemplatesRepository;
			}
		);

		// Register Facades
		$this->app['datasetshelper'] = $this->app->share(
			function ($app) {
				return new \Fruitful\Data\Libraries\DataSetsHelper;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('DataSetsHelper', 'Fruitful\Data\Facades\DataSetsHelper');
		});

		$this->app['datasettemplateshelper'] = $this->app->share(
			function ($app) {
				return new \Fruitful\Data\Libraries\DataSetTemplatesHelper;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('DataSetTemplatesHelper', 'Fruitful\Data\Facades\DataSetTemplatesHelper');
		});

		$this->app['fruitulstreamschema'] = $this->app->share(
			function ($app) {
				return new \Fruitful\Data\Libraries\FruitulStreamSchema;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('StreamSchema', 'Fruitful\Data\Facades\StreamSchema');
		});
	}
}