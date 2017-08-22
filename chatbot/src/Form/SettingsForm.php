<?php
/**
 * @file
 * Contains \Drupal\chatbot\Form\SettingsForm
 */

namespace Drupal\chatbot\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class SettingsForm extends ConfigFormBase{
	/**
	* {@inheritdoc}
	*/
	public function getFormID() {
		return 'chatbot_settings_form';
	}

	 /**
   	  * {@inheritdoc}
      */
  	protected function getEditableConfigNames() {
    	return [
    	];
  	}

	/**
	  * {@inheridoc}
	  */
	public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

		// add field for each keyword with button to remove: foreach print keyword->function and checkbox for removing
		$form['deletekeyword'] = array(
			'#type' => 'fieldset',
			'#title' => 'Delete Keyword',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);
		$result = db_query("SELECT * FROM chatbot__keywords");
		foreach ( $result as $record ) {
			$form['deletekeyword'][$record->keyword] = array(
				'#type' => 'checkbox',
				'#title' => $this->t(': Delete the keyword \'' . $record->keyword . '\' used in the function ' . $record->function ),
			);
		}

		// add field for each response with button to remove: foreach print function->response and checkbox for removing
		$form['deleteresponse'] = array(
			'#type' => 'fieldset',
			'#title' => 'Delete Response',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);
		$result = db_query("SELECT * FROM chatbot__response");
		foreach ( $result as $record ) {
			$form['deleteresponse'][$record->id] = array(
				'#type' => 'checkbox',
				'#title' => $this->t(': Delete the response \'' . $record->response . '\' from the function ' . $record->function ),
			);
		}

		// add field to add key word: need keyword and function
		$form['add_keyword'] = array(
			'#type' => 'fieldset',
			'#title' => 'Add Keyword',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);
		$form['add_keyword']['keyword'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Keyword'),
			'#description' => $this->t('Enter the keyword that will be used with the function selected'),
		);
		$form['add_keyword']['keywordfunction'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Function'),
			'#description' => $this->t('Enter the function to use for keyword'),
		);

		// add field to add response: need function and response
		$form['add_response'] = array(
			'#type' => 'fieldset',
			'#title' => 'Add Keyword',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);
		$form['add_response']['response'] = array(
			'#type' => 'textarea',
			'#title' => $this->t('Response Message'),
			'#description' => $this->t('Enter the response that will be displayed with the function selected'),
		);
		$functionArr = array();
		$result = db_query("SELECT DISTINCT function FROM chatbot__keywords");
		foreach ( $result as $function )
		{
			$functionArr[$function->function] = $function->function;
		}
		$form['add_response']['responsefunction'] = array(
			'#type' => 'select',
			'#title' => 'Select Function to add random response to:',
			'#options' => $functionArr,
		);
		
		return parent::buildForm($form,$form_state);
	}

	/**
	  * {@inheridoc}
	  */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$values = $form_state->getValues();

		// delete selected keywords
		$result = db_query("SELECT * FROM chatbot__keywords");
		foreach ( $result as $record ) {
			if ($values[$record->keyword]) {
				db_delete('chatbot__keywords')
					->condition('keyword', $record->keyword)
					->execute();
			}
		}

		// delete selected responses
		$result = db_query("SELECT * FROM chatbot__response");
		foreach ( $result as $record ) {
			if ($values[$record->id]) {
				db_delete('chatbot__response')
					->condition('id', $record->id)
					->execute();
			}
		}

		// add keyword
		if ($values['keyword']) {
			db_insert('chatbot__keywords')
				->fields(array(
					'keyword' => $values['keyword'],
					'function' => $values['keywordfunction'],
				))
				->execute();
		}

		// add response 
		if ($values['response']) {
			db_insert('chatbot__response')
				->fields(array(
					'response' => $values['response'],
					'function' => $values['responsefunction'],
				))
				->execute();
		}
		
		drupal_set_message('The keyword and responses are updated!');
	}

	/**
	  * {@inheridoc}
	  */
	public function validateForm(array &$from, FormStateInterface $form_state) {

	}

	
}