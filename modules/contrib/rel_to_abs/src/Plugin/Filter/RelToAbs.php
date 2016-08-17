<?php

namespace Drupal\rel_to_abs\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Url;

/**
 * Provides a filter to convert relative paths to absolute URLs.
 *
 * @Filter(
 *   id = "rel_to_abs",
 *   title = @Translation("Convert relative paths to absolute URLs"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class RelToAbs extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $resultText = preg_replace_callback('/(href|background|src)=["\']([\/#][^"\']*)["\']/', function($matches) {
      $url = preg_replace('/\/{2,}/', '/', $matches[2]);
      try {
        $url = Url::fromUserInput($url)->setAbsolute(true)->toString();
      }
      catch(\InvalidArgumentException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
      return $matches[1] . '="' . $url . '"';
    }, $text);

    return new FilterProcessResult($resultText);
  }

}
