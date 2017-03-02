<?php

namespace Drupal\email_form\Plugin\ReusableForm;

use Drupal\reusable_forms\ReusableFormPluginBase;

/**
 * Provides an email form.
 *
 * @ReusableForm(
 *   id = "email_form",
 *   name = @Translation("Email Form"),
 *   form = "Drupal\email_form\Form\EmailForm"
 * )
 */
class EmailForm extends ReusableFormPluginBase {

}