<?php

/**
 * dcValidatorReferenceModelChoice
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcValidatorReferenceModelChoice extends sfValidatorChoice
{
  public function __construct($options = array(), $messages = array())
  {
    $options['choices'] = array();

    parent::__construct($options, $messages);
  }

  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model: The name of the reference model class (required)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorChoice
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('model');

    parent::configure($options, $messages);
  }

  /**
   * Get the valid option identifiers from the reference class set via the
   * 'model' option.
   *
   * @return array
   */
  public function getChoices()
  {
    return call_user_func(array($this->getOption('model'), 'getIdentifiers'));
  }

}