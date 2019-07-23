<?php

namespace Drupal\Tests\workflow\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test the functionality of the Hooks Example module.
 *
 * @ingroup workflow
 *
 * @group workflow
 * @group examples
 */
class WorkflowTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['help', 'workflow'];

  /**
   * {@inheritdoc}
   */
  protected $profile = 'minimal';
}
