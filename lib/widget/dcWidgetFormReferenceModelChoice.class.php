<?php

/**
 * dcWidgetFormReferenceModelChoice
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcWidgetFormReferenceModelChoice extends sfWidgetFormChoice
{
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = array();

    parent::__construct($options, $attributes);
  }

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:     The name of the reference model class (required)
   *  * add_empty: Whether to add an empty value to the choices
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');

    $this->addOption('add_empty', false);

    parent::configure($options, $attributes);
  }

  /**
   * Get the options from the reference class set via the 'model' option.
   *
   * @return array
   */
  public function getChoices()
  {
    $choices = call_user_func(array($this->getOption('model'), 'getOptions'));

    if (false !== $this->getOption('add_empty'))
    {
      if (true === $this->getOption('add_empty'))
      {
        $extra_option = '';
      }
      else
      {
        $extra_option = $this->getOption('add_empty');
      }

      $choices = array('' => $extra_option) + $choices;
    }

    return $this->translateAll($choices);
  }

}