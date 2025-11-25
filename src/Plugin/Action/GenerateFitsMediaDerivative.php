<?php

namespace Drupal\digitalia_muni_fits_generator\Plugin\Action;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\islandora\Plugin\Action\AbstractGenerateDerivativeMediaFile;

/**
 * Emits a Media for generating fits derivatives event.
 *
 * @Action(
 *   id = "generate_media_fits_derivative",
 *   label = @Translation("Generate a Technical metadata derivative and save it to same media"),
 *   type = "media"
 * )
 */
class GenerateFitsMediaDerivative extends AbstractGenerateDerivativeMediaFile {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['path'] = '[date:custom:Y]-[date:custom:m]/[media:mid]-FITS.xml';
    $config['mimetype'] = 'application/xml';
    $config['queue'] = 'islandora-connector-fits';
    $config['destination_field_name'] = 'field_fits_file';
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  protected function generateData(EntityInterface $entity) {
    // test
    \Drupal::logger("DEBUG")->debug("Generating data");
    $data = parent::generateData($entity);
    \Drupal::logger("DEBUG")->debug(print_r($data, TRUE));
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $map_file = $this->entityFieldManager->getFieldMapByFieldType('file');
    $map_image = $this->entityFieldManager->getFieldMapByFieldType('image');
    $file_fields = array_merge($map_file['media'], $map_image['media']);
    $file_options = array_combine(array_keys($file_fields), array_keys($file_fields));

    $form = parent::buildConfigurationForm($form, $form_state);
    $form['mimetype']['#description'] = t('Mimetype to convert to (e.g. application/xml, etc...)');
    $form['mimetype']['#value'] = 'application/xml';
    $form['mimetype']['#type'] = 'hidden';

    $form['source_field_name']['#default_value'] = $this->configuration['source_field_name'];
    $form['source_field_name']['#description'] = t('Machine name of source field');
    $form['source_field_name']['#title'] = t('Source field name');
    $form['source_field_name']['#type'] = 'select';
    $form['source_field_name']['#options'] = $file_options;

    $form['destination_field_name']['#default_value'] = $this->configuration['destination_field_name'];
    $form['destination_field_name']['#description'] = t('Machine name of destination field');
    $form['destination_field_name']['#title'] = t('Destination field name');

    unset($form['args']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
    $exploded_mime = explode('/', $form_state->getValue('mimetype'));
    if ($exploded_mime[0] != 'application') {
      $form_state->setErrorByName(
        'mimetype',
        t('Please enter file mimetype (e.g. application/xml.)')
      );
    }
  }

}
