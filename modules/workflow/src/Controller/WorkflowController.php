<?php

namespace Drupal\workflow\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for Workflow description page.
 *
 * This class uses the DescriptionTemplateTrait to display text we put in the
 * templates/description.html.twig file.
 */
class WorkflowController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Workflow!'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'workflow';
  }

}
