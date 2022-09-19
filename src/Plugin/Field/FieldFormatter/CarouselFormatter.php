<?php

namespace Drupal\splide_paragraph\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

/**
 * Plugin implementation of the 'carousel_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "carousel_formatter",
 *   label = @Translation("Carousel"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class CarouselFormatter extends EntityReferenceEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'view_mode' => 'default',
      'type' => 'carousel',
      'image_field' => 'field_image',
      'carousel_type' => 'slide',
      'speed' => 400,
      'manual' => FALSE,
      'nav_type' => 'thumbnails',
      'control_type' => 'show',
      'autoplay' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['view_mode'] = [
      '#type' => 'select',
      '#options' => $this->entityDisplayRepository->getViewModeOptions($this->getFieldSetting('target_type')),
      '#title' => $this->t('View mode'),
      '#default_value' => $this->getSetting('view_mode'),
      '#required' => TRUE,
    ];

    $elements['type'] = [
      '#type' => 'select',
      '#options' => [
        'carousel' => $this->t('Carousel'),
        'slider' => $this->t('Slider'),
      ],
      '#title' => $this->t('Type'),
      '#default_value' => $this->getSetting('type'),
      '#required' => TRUE,
      '#name' => 'carousel_type',
    ];

    $elements['image_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image field'),
      '#default_value' => $this->getSetting('image_field'),
      '#required' => TRUE,
    ];

    $elements['carousel_type'] = [
      '#type' => 'select',
      '#options' => [
        'loop' => $this->t('Slide'),
        'fade' => $this->t('Fade'),
      ],
      '#title' => $this->t('Type'),
      '#default_value' => $this->getSetting('carousel_type'),
      '#required' => TRUE,
    ];

    $elements['nav_type'] = [
      '#type' => 'select',
      '#options' => [
        'thumbnails' => $this->t('Thumbnails'),
        'number' => $this->t('Numbers'),
        'none' => $this->t('None'),
      ],
      '#title' => $this->t('Nav type'),
      '#default_value' => $this->getSetting('nav_type'),
    ];

    $elements['control_type'] = [
      '#type' => 'select',
      '#options' => [
        'show' => $this->t('Show arrows'),
        'focus' => $this->t('Arrows on focus'),
      ],
      '#title' => $this->t('Arrows'),
      '#default_value' => $this->getSetting('control_type'),
      '#states' => [
        'invisible' => [
          ':input[name="carousel_type"]' => ['value' => 'slider'],
        ],
      ],
    ];

    $elements['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $this->getSetting('autoplay'),
      '#states' => [
        'invisible' => [
          ':input[name="carousel_type"]' => ['value' => 'carousel'],
        ],
      ],
    ];

    $elements['speed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Speed'),
      '#default_value' => $this->getSetting('speed') ?: 400,
    ];

    $elements['manual'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Custom JS'),
      '#default_value' => $this->getSetting('manual'),
      '#description' => $this->t('Attach the JS class separately to allow manual overrides.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $view_modes = $this->entityDisplayRepository->getViewModeOptions($this->getFieldSetting('target_type'));
    $view_mode = $this->getSetting('view_mode');
    $summary[] = $this->t('Rendered as @mode', ['@mode' => isset($view_modes[$view_mode]) ? $view_modes[$view_mode] : $view_mode]);
    $summary[] = $this->t('Type: @mode', ['@mode' => $this->getSetting('type')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    // Render first item as usual.
    $elements = parent::view($items, $langcode);

    $elements['#image_field'] = $this->getSetting('image_field');
    $elements['#theme'] = $this->getSetting('type');
    $elements['#settings'] = [
      'type' => $this->getSetting('carousel_type'),
      'speed' => $this->getSetting('speed'),
    ];
    $elements['#settings']['rewind'] = $this->getSetting('carousel_type') === 'fade';
    $elements['#settings']['autoplay'] = $this->getSetting('type') === 'slider' ? $this->getSetting('autoplay') : NULL;
    $elements['#override'] = $this->getSetting('manual');
    $elements['#nav_type'] = $this->getSetting('nav_type');
    $elements['#control_type'] = $this->getSetting('control_type');

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($elements as $delta => $entity) {
      $elements[$delta]['#theme'] = 'carousel_slide';
    }

    return $elements;
  }

}
