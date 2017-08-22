<?php

namespace Drupal\chatbot\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\chatbot\Services\ChatbotService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChatbotController extends ControllerBase {
	private $chatbot;
	private $history;

	public function __construct(ChatbotService $chatbot) {
		$this->chatbot = $chatbot;
	}

	/**
	 * Display the markup.
	 *
	 * @return array
	 *
	 */
	public function chat() {
		return \Drupal::formBuilder()->getForm('Drupal\chatbot\Form\ChatbotForm');
	}

	public static function create(ContainerInterface $container) {
		$chatbot = $container->get('chatbot.randomresponse');

		return new static($chatbot);
	}
}