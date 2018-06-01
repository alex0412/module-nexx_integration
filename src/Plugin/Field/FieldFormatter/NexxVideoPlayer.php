<?php

namespace Drupal\nexx_integration\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'nexx_video_player' formatter.
 *
 * @FieldFormatter(
 *   id = "nexx_video_player",
 *   module = "nexx_integration",
 *   label = @Translation("Javascript Video Player"),
 *   field_types = {
 *     "nexx_video_data"
 *   }
 * )
 */
class NexxVideoPlayer extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Autoplay: @value',
      array('@value' => $this->getSetting('autoplay'))
    );
    
    return $summary;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'autoplay' => '0',
      ] + parent::defaultSettings();
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['autoplay'] = [
      '#title' => $this->t('Autoplay'),
      '#type' => 'select',
      '#options' => [
        //@todo: add option to consider default setting in Omnia
        '0' => $this->t('Off'),
        '1' => $this->t('On'),
      ],
      '#default_value' => $this->getSetting('autoplay'),
    ];
    
    return $element;
  }
  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = [];
    
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'nexx_player',
        '#container_id' => 'player--' . Crypt::randomBytesBase64(8),
        '#video_id' => $item->item_id,
        '#autoplay' => $this->getSetting('autoplay'),
        '#attached' => [
          'library' => [
            'nexx_integration/base',
          ],
        ],
        /*
        '#cache' => [
          'tags' => $user->getCacheTags(),
        ],
         */
      ];
    }
    
    return $elements;
  }
}
