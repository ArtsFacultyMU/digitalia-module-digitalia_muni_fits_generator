<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_fits_generator\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a pronom info block.
 *
 * @Block(
 *   id = "digitalia_muni_fits_pronom_info",
 *   admin_label = @Translation("PRONOM info"),
 *   category = @Translation("digitalia"),
 * )
 */
final class DigitaliaMuniFitsPronomInfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'example' => $this->t('Hello world!'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    //$form['example'] = [
    //  '#type' => 'textarea',
    //  '#title' => $this->t('Example'),
    //  '#default_value' => $this->configuration['example'],
    //];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['example'] = $form_state->getValue('example');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
	$media = \Drupal::routeMatch()->getParameter('media');
	dump($media);

    $client = \Drupal::httpClient();
    $response = $client->request(
	    'GET',
	    'http://the-fr.org/api/id/puid/fmt/12',
    );

    $build['content'] = [
      '#markup' => $response->getBody()->getContents(),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf(TRUE);
  }

}
