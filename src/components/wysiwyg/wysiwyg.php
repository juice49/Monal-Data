<?php
namespace Fruitful\Data\Components;
/**
 * WYSIWYG Component.
 *
 * A WYSIWYG component to provide text editing functionality for
 * users. The class provides functions for generating WYSIWYG
 * templates, and implementations of those templates.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\ComponentInterface;

class WYSIWYG implements ComponentInterface
{
	/**
	 * Instance of class implementing MessagesInterface.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	public $messages;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
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
	 * Construct the template settings for a WYSIWYG template.
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