<?php
/**
 * @file
 * Contains Drupal/acquia_lift_rest/Form/AcquiaLiftConfigForm
 */


namespace Drupal\acquia_lift_rest\Form;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Create form to gather Lift specific configuration
 */
class AcquiaLiftConfigForm extends ConfigFormBase {
	/**
	 * Constructor
	 */ 
	public function __construct(ConfigFactoryInterface $config_factory) {
		parent::__construct($config_factory);
	}

	/**
	 * set the formId
	 */
	public function getFormId() {
		return 'acquia_lift_admin_form';
	}

	/**
	 * get the editable configuration names
	 */
	protected function getEditableConfigNames() {
		return ['config.acquia_lift_rest'];
	}

	/**
	 * Form constructor
	 *
	 * @param array $form
	 * Assoc array containing the form structure
	 * @param \Drupal\Core\Form\FormStateInterface $form_state
	 * the current state of the form
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$acquia_lift_rest = $this->config('config.acquia_lift_rest');

		// settings for the user and secret keys
		$form['acquia_lift_rest']['user_key'] = array(
			'#type' => 'textfield',
			'#title' => t('Access Key ID'),
			'#maxlength' => 255,
			'#default_value' => $acquia_lift_rest->get('user_key_title') ? $acquia_lift_rest->get('user_key_title') : 'empty',
			'#description' => t('Enter the Access Key ID provided from LIFT'),
		);

		$form['acquia_lift_rest']['secret_key'] = array(
			'#type' => 'textfield',
			'#title' => t('Secret Key'),
			'#maxlength' => 255,
			'#default_value' => $acquia_lift_rest->get('secret_key_title') ? $acquia_lift_rest->get('secret_key_title') : 'empty',
			'#description' => t('Enter the Secret Key provided from LIFT'),
		);

		return parent::buildForm($form, $form_state);
	}

	/**
	 * Form submission handler
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {

		$this->config('config.acquia_lift_rest')
			->set('user_key_title', $form_state->getValue('user_key'))
			->set('secret_key_title', $form_state->getValue('secret_key'))
			->save();
		parent::submitForm($form, $form_state);
	}

}