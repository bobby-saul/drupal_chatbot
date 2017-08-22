<?php

/**
  * @file
  * Contains \Drupal\chatbot\Form\ChatbotForm
  */
namespace Drupal\chatbot\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chatbot\Services\ChatbotService;

/**
  * 
  */
class ChatbotForm extends FormBase {
	/**
	  * {@inheridoc}
	  */
	public function getFormId() {
		return 'chatbot_form';
	}

	/**
	  * {@inheridoc}
	  */

	public function buildForm(array $form, FormStateInterface $form_state) {
		// history print out
		$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$query = db_query("SELECT history FROM chatbot__history WHERE uid = :user",array(':user' => $user->id()))->fetchField();

		if($query) {	// if there is history, concatenate onto it 
			$history = $query;
		} else {		// else start new history
			$history = '<p class="chatbot">Hello -Chatbot</p>';
		}

		$form['history'] = array(
			"#markup" => '<div class="chatbot-history" id="chatbothistory">' . $history . '</div>',
			'#allowed_tags' => ['p','a','div'],
		);

		// makes an form with the user text and submit button
		$form['usertext'] = array(
			'#type' => 'textfield',
			'#size' => 35,
			'#required' => TRUE,

		);
		$form['submit'] =array(
			'#type' => 'submit',
			'#value' => t('Enter'),
		);
		$form['#attached'] = array(
			'library' => array(
				'chatbot/chatbot-history'
			),
		);

		return $form;
	}

	/**
	  * {@inheridoc}
	  */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		// get user id
		$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

		// get chatbot service for responses
		$chatbot = new ChatbotService();
		$response = $chatbot->getResponse($form_state->getValue('usertext'));

		// get chat history of user from the database
		$query = db_query("SELECT history FROM chatbot__history WHERE uid = :user",array(':user' => $user->id()))->fetchField();

		$history = $query . '<p class="usertext">' . $form_state->getValue('usertext') . '</p><p class="chatbot">' . $response . '</p>' ;

		// merge = insert if non exists or update if it does
		db_merge('chatbot__history')
			->key(array('uid' => $user->id()))
			->fields(array(
				'history' => substr($history, 0, 6999),
				'lastentry' => $form_state->getValue('usertext'),
				'updated' => time(),
			))
			->execute();
	}
} 