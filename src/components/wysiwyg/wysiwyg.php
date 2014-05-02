<?php
namespace Monal\Data\Components;
/**
 * WYSIWYG Component.
 *
 * A WYSIWYG component to provide text editing functionality for
 * users. The class provides functions for generating WYSIWYG
 * templates, and implementations of those templates.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Components\ComponentInterface;

class WYSIWYG implements ComponentInterface
{
	/**
	 * The Component's messages.
	 *
	 * @var		 Monal\Core\Contracts\MessagesInterface
	 */
	public $messages;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Monal\Core\Contracts\MessagesInterface');
	}

	/**
	 * Return the Component's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return the Component's URI.
	 *
	 * @return	String
	 */
	public function uri()
	{
		return 'wysiwyg';
	}

	/**
	 * Return the Component's name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return 'WYSIWYG';
	}

	/**
	 * Check an array of settings validate and are suitable to be used as
	 * a template for a new implementations of the WYSIWYG component.
	 *
	 * @param	Array
	 * @return	Boolean
	 */
	public function templateSettingsValidate(array $settings)
	{
		$validation = \Validator::make(
			$settings,
			array(
				'type' => 'required',
			),
			array(
				'type.required' => 'You need to set a Type for this WYSIWYG.'
			)
		);
		if ($validation->passes()) {
			return true;
		} else {
			$this->messages->add($validation->messages()->toArray());
			return false;
		}
	}

	/**
	 * Construct a set of template settings for a WYSIWYG template.
	 *
	 * @param	Array
	 * @return	Array
	 */
	public function constructTemplateSettings(array $settings)
	{
		$template_settings = array(
			'type' => isset($settings['type']) ? $settings['type'] : null,
			);
		if ($template_settings['type'] == 'Custom') {
			if (isset($settings['custom_settings'])) {
				$template_settings['custom_settings'] = $settings['custom_settings'];
			} else {
				$template_settings['custom_settings'] = View::make('components::wysiwyg.views.types.Advanced')->render();
			}
		}
		return $template_settings;
	}

	/**
	 * Return an interface that a user can use to configure settings for
	 * a new or existing WYSIWYG template.
	 *
	 * @param	String
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function templateView($uri, array $settings = array())
	{
		$options = $this->wysiwygTypes();
		if (isset($settings['custom_settings'])) {
			$custom_settings = $settings['custom_settings'];
		} else {
			$custom_settings = \View::make('components::wysiwyg.views.types.Advanced')->render();
		}
		return \View::make('components::wysiwyg.views.template', compact('uri', 'settings', 'options', 'custom_settings'));
	}

	/**
	 * Check an array of implementation values validate against a set of
	 * template settings.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function implementationValuesValidate(array $values = array(), array $settings = array())
	{
		return true;
	}

	/**
	 * Return a summary for a set of valid component values.
	 *
	 * @param	Array
	 * @param	String
	 */
	public function summariseValues(array $values = array())
	{
		return \Str::limit(strip_tags($this->stripImplementationValues($values), 100));
	}

	/**
	 * Convert a set of valid component values into as simple a format as
	 * possible for easy storage.
	 *
	 * @param	Array
	 * @return	String
	 */
	public function stripImplementationValues(array $values = array())
	{
		return isset($values['wysiwyg']) ? $values['wysiwyg'] : null;
	}

	/**
	 * Convert a set of simplified values into a more complex array of
	 * values.
	 *
	 * @param	String
	 * @return	Array
	 */
	public function dressImplementationValues($values)
	{
		return array('wysiwyg' => $values);
	}

	/**
	 * Return an interface that lets a user use a WYSIWYG.
	 *
	 * @param	String
	 * @param	Array
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function implementationView($uri, array $settings = array(), array $values = array())
	{
		$wysiwyg_settings = '';
		if (isset($settings['type'])) {
			if ($settings['type'] === 'Custom') {
				if (isset($settings['custom_settings'])) {
					$wysiwyg_settings = $settings['custom_settings'];
				}
			} else {
				$wysiwyg_settings = \View::make('components::wysiwyg.views.types.' . $settings['type'])->render();
			}
		}
		return \View::make('components::wysiwyg.views.implementation', compact('uri', 'settings', 'wysiwyg_settings', 'values'));
	}

	/**
	 * Return the available WYSIWYG types.
	 *
	 * @return	Array
	 */
	public function wysiwygTypes()
	{
		$settings = array();
		foreach (scandir(__DIR__ . '/views/types') as $file) {
			if (substr($file, -10) === '.blade.php') {
				$file_start = substr_replace($file, '', -10);
				$settings[$file_start] = $file_start;
			}
		}
		$settings['Custom'] = 'Custom';
		return $settings;
	}
}