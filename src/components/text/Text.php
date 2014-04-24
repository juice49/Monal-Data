<?php
namespace Fruitful\Data\Components;
/**
 * Text Component.
 *
 * A Text component to provide basic text input capabilities.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Components\ComponentInterface;

class Text implements ComponentInterface
{
	/**
	 * The Component's messages.
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
		return 'text';
	}

	/**
	 * Return the Component's name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return 'Text';
	}

	/**
	 * Check an array of settings validate and are suitable to be used as
	 * a template for a new implementations of the Text component.
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
				'limit_length' => 'required_if:limit,1|numeric',
				'limit_type' => 'required_if:limit,1',
			),
			array(
				'type.required' => 'You need to set a Type for this Text Component.',
				'limit_length.required_if' => 'You need to set a Limit Length for this Text Component.',
				'limit_length.numeric' => 'The Limit Length must be a numerical value.',
				'limit_type.required_if' => 'You need to set a Limit Type for this Text Component.',
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
	 * Construct a set of template settings for a Text template.
	 *
	 * @param	Array
	 * @return	Array
	 */
	public function constructTemplateSettings(array $settings)
	{
		$template_settings = array(
			'type' => isset($settings['type']) ? $settings['type'] : null,
			'limit' => isset($settings['limit']) ? $settings['limit'] : 0,
			);
		if ($template_settings['limit']) {
			$template_settings['limit_length'] = isset($settings['limit_length']) ? $settings['limit_length'] : null;
			$template_settings['limit_type'] = isset($settings['limit_type']) ? $settings['limit_type'] : null;
		}
		return $template_settings;
	}

	/**
	 * Return an interface that a user can use to configure settings for
	 * a new or existing Text template.
	 *
	 * @param	String
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function templateView($uri, array $settings = array())
	{
		$types = array(
			'block' => 'Block',
			'single-line' => 'Single Line',
		);
		$limit_types = array(
			'characters' => 'Characters',
			'words' => 'Words',
		);
		return \View::make('components::text.views.template', compact('uri', 'settings', 'types', 'limit_types'));
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
		if (
			isset($settings['limit']) AND
			$settings['limit'] AND
			isset($settings['limit_length']) AND
			isset($settings['limit_type']) AND
			isset($values['text'])
		) {
			if ($settings['limit_type'] === 'characters') {
				if (strlen($values['text']) > $settings['limit_length']) {
					$this->messages->add(
						array(
							'error' => array(
								'The content entered must be less than ' . $settings['limit_length'] . ' characters long.'
							)
						)
					);
					return false;
				}
			} elseif ($settings['limit_type'] === 'words'){
				if (str_word_count($values['text']) > $settings['limit_length']) {
					$this->messages->add(
						array(
							'error' => array(
								'The content entered must be less than ' . $settings['limit_length'] . ' words.'
							)
						)
					);
					return false;
				}
			}
		}
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
		return \Str::limit($this->stripImplementationValues($values), 100);
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
		return isset($values['text']) ? $values['text'] : null;
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
		return array('text' => $values);
	}

	/**
	 * Return an interface that lets a user use a Text Component.
	 *
	 * @param	String
	 * @param	Array
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function implementationView($uri, array $settings = array(), array $values = array())
	{
		return \View::make('components::text.views.implementation', compact('uri', 'settings', 'values'));
	}
}