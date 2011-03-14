<?php

/**
 * dcReferenceClassDefinition
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcReferenceClassDefinition implements dcReferenceDefinitionInterface
{
  protected
    $name      = null,
    $options   = array(),
    $max_width = 0;

  public function __construct($name, $options)
  {
    $this
      ->setName($name)
      ->setOptions($options)
    ;
  }

  /**
   * Generate the PHP code associated to this class.
   *
   * @return string
   */
  public function toPHP()
  {
    return strtr($this->getTemplate(), array(
      '%class%'       => $this->getName(),
      '%date%'        => date('Y-m-d H:i:s'),
      '%constants%'   => implode(",\n    ", $this->getConstants()),
      '%identifiers%' => implode(",\n      ", $this->getIdentifiers()),
      '%options%'     => implode(",\n      ", $this->getOptionsForPHP())
    ));
  }

  /**
   * Set the name of the defined class.
   *
   * @return dcReferenceClassDefinition This object for a fluent interface.
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the name of the defined class.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the options of the defined class.
   *
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Set the options of the defined class.
   *
   * @param  array $options The options to set.
   *
   * @return dcReferenceClassDefinition This object, for a fluent interface.
   */
  public function setOptions(array $options)
  {
    $this->options = $options;

    // Set the max width of the options names so as to have the code nifty :-)
    $this->max_width = 0;

    foreach ($options as $id => $name)
    {
      $this->max_width = max(strlen($this->generateConstantName($name)), $this->max_width);
    }

    return $this;
  }

  /**
   * Get the filename of the defined class. Please note that this method
   * will *only* return the basename of the file, and not its absolute path.
   *
   * Filename is a concatenation of the name and a suffix '.class.php'.
   *
   * @return string
   */
  public function getFilename()
  {
    return $this->getName().'.class.php';
  }

  /**
   * Get the PHP code template for the class to be generated.
   *
   * @return string
   */
  protected function getTemplate()
  {
    try
    {
      return file_get_contents(dirname(__FILE__).'/templates/class.tpl');
    }
    catch (Exception $error)
    {
      throw $error;
    }
  }

  /**
   * Generate a constant name for $option_name.
   *
   * @param  string $option_name The name of the option.
   *
   * @return string
   */
  protected function generateConstantName($option_name)
  {
    $underscored_class_name  = sfInflector::underscore($this->getName());
    $underscored_option_name = sfInflector::underscore($option_name);

    return $this->normalize($underscored_class_name.'_'.$underscored_option_name);
  }

  /**
   * Get an array of the constants PHP code definition lines.
   *
   * @return array string[]
   */
  protected function getConstants()
  {
    $constants     = array();
    $format_string = '%-'.$this->max_width.'s = %d';

    foreach ($this->getOptions() as $identifier => $name)
    {
      $constants[] = sprintf($format_string,
        $this->generateConstantName($name),
        $identifier
      );
    }

    return $constants;
  }

  /**
   * Get an array with the identifiers of the options of the defined class.
   *
   * @return array int[]
   */
  protected function getIdentifiers()
  {
    return array_keys($this->getOptions());
  }

  /**
   * Get an array of options suitable for PHP array declaration.
   *
   * @return array string[]
   */
  protected function getOptionsForPHP()
  {
    $options       = array();
    $format_string = 'self::%-'.$this->max_width.'s => \'%s\'';

    foreach ($this->getOptions() as $identifier => $name)
    {
      $options[] = sprintf($format_string,
        $this->generateConstantName($name),
        addslashes($name)
      );
    }

    return $options;
  }

  /**
   * Noramlize $string so it can be used as a PHP constant name.
   *
   * @param  string $string    The string to normalize.
   * @param  string $separator The word separator. Defaults to '_'.
   *
   * @return string
   */
  protected function normalize($string, $separator = '_')
  {
    $string = trim(preg_replace('~[^\\pL\d]+~u', $separator, $string), $separator);

    if (function_exists('iconv'))
    {
      $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    }

    return preg_replace('~[^_\w]+~', '', strtoupper($string));
  }

}