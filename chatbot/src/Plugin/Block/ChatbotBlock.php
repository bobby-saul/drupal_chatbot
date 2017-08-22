<?php

namespace Drupal\chatbot\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\chatbot\Services\ChatbotService;

/**
 * Provides a 'Chatbot' Block.
 *
 * @Block(
 *   id = "chatbot_block",
 *   admin_label = @Translation("Chatbot block"),
 *   category = @Translation("Custom"),
 * )
 */
class ChatbotBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
  	return \Drupal::formBuilder()->getForm('Drupal\chatbot\Form\ChatbotForm');
  }

}