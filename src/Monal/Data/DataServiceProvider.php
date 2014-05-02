<?php
namespace Monal\Data;

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
		$this->package('monal/data');

		$routes = __DIR__.'/../../routes.php';
		if (file_exists($routes)){
			include $routes;
		}
		
		\View::addLocation(__DIR__ . '/../../components');
		\View::addNamespace('components', __DIR__ . '/../../components');

		\Monal::registerMenuOption('Data', 'Data Sets', 'data-sets', 'data_sets');
		\Monal::registerMenuOption('Data', 'Data Streams', 'data-streams', 'data_streams');
		\Monal::registerPermissionSet(
			'Data',
			'data_sets',
			array(
				'Create Data Set' => 'create_data_set',
				'Edit Data Set' => 'edit_data_set',
				'Delete Data Set' => 'delete_data_set',
			)
		);
		\Monal::registerPermissionSet(
			'Data Set Templates',
			'data_set_templates',
			array(
				'Create Data Set Template' => 'create_data_set_template',
				'Edit Data Set Template' => 'edit_data_set_template',
				'Delete Data Set Template' => 'delete_data_set_template',
			)
		);
		\Monal::registerPermissionSet(
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
			'Monal\Data\Libraries\ComponentsInterface',
			function () {
				return new \Monal\Data\Libraries\Components;
			}
		);
		$this->app->bind(
			'Monal\Data\Models\DataSet',
			function () {
				return new \Monal\Data\Models\MonalDataSet;
			}
		);
		$this->app->bind(
			'Monal\Data\Models\DataSetTemplate',
			function () {
				return new \Monal\Data\Models\MonalDataSetTemplate;
			}
		);
		$this->app->bind(
			'Monal\Data\Models\DataStream',
			function () {
				return new \Monal\Data\Models\MonalDataStream;
			}
		);
		$this->app->bind(
			'Monal\Data\Models\DataStreamEntry',
			function () {
				return new \Monal\Data\Models\MonalDataStreamEntry;
			}
		);
		$this->app->bind(
			'Monal\Data\Models\DataStreamTemplate',
			function () {
				return new \Monal\Data\Models\MonalDataStreamTemplate;
			}
		);
		$this->app->bind(
			'Monal\Data\Repositories\DataSetsRepository',
			function () {
				return new \Monal\Data\Repositories\MonalDataSetsRepository;
			}
		);
		$this->app->bind(
			'Monal\Data\Repositories\DataSetTemplatesRepository',
			function () {
				return new \Monal\Data\Repositories\MonalDataSetTemplatesRepository;
			}
		);
		$this->app->bind(
			'Monal\Data\Repositories\DataStreamsRepository',
			function () {
				return new \Monal\Data\Repositories\MonalDataStreamsRepository;
			}
		);
		$this->app->bind(
			'Monal\Data\Repositories\DataStreamTemplatesRepository',
			function () {
				return new \Monal\Data\Repositories\MonalDataStreamTemplatesRepository;
			}
		);

		// Register Facades
		$this->app['datasetshelper'] = $this->app->share(
			function ($app) {
				return new \Monal\Data\Libraries\DataSetsHelper;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('DataSetsHelper', 'Monal\Data\Facades\DataSetsHelper');
		});

		$this->app['datasettemplateshelper'] = $this->app->share(
			function ($app) {
				return new \Monal\Data\Libraries\DataSetTemplatesHelper;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('DataSetTemplatesHelper', 'Monal\Data\Facades\DataSetTemplatesHelper');
		});

		$this->app['monalstreamschema'] = $this->app->share(
			function ($app) {
				return new \Monal\Data\Libraries\MonalStreamSchema;
			}
		);
		$this->app->booting(
			function () {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('StreamSchema', 'Monal\Data\Facades\StreamSchema');
		});
	}
}