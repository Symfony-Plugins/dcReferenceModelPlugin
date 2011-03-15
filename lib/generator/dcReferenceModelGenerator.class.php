<?php

/**
 * dcReferenceModelGenerator
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcReferenceModelGenerator
{
  protected $definition = null;

  public function __construct(dcReferenceModelDefinition $definition)
  {
    $this->definition = $definition;
  }

  /**
   * Get this generator's model definition.
   *
   * @return dcReferenceModelDefinition
   */
  public function getDefinition()
  {
    return $this->definition;
  }

  /**
   * Set this generator's model definition.
   *
   * @param  dcReferenceModelDefinition $definition The definition to set.
   *
   * @return dcReferenceModelGenerator This object for a fluent interface.
   */
  public function setDefinition($definition)
  {
    $this->definition = $definition;

    return $this;
  }

  /**
   * Generate the model defined in the schema definition set to this generator
   * object.
   * This method only performs checks before calling the `doGenerate()` method
   * which actually generates the model classes.
   *
   * @throws BadMethodCallException if an empty definition is set.
   * @throws InvalidArgumentException if $target_dir is not writeable.
   * @throws Exception if an error occurs when generating the classes.
   *
   * @see doGenerate()
   *
   * @param  string $target_dir
   *
   * @return bool True on success.
   */
  public function generate($target_dir)
  {
    $base_dir = $target_dir.'/base';

    if (null === $this->definition || $this->definition->count() == 0)
    {
      throw new BadMethodCallException('Trying to generate an empty reference model. Aborting.');
    }

    if (!is_writable($target_dir))
    {
      if (is_dir($target_dir))
      {
        throw new InvalidArgumentException('Target directory is not writeable: '.$target_dir);
      }
      else
      {
        $created = mkdir($target_dir);

        if ($created === false)
        {
          throw new InvalidArgumentException('Unable to create target directory: '.$target_dir);
        }
      }
    }

    if (!is_writable($base_dir))
    {
      if (is_dir($base_dir))
      {
        throw new InvalidArgumentException('Target directory for base classes is not writeable: '.$base_dir);
      }
      else
      {
        $created = mkdir($base_dir);

        if ($created === false)
        {
          throw new InvalidArgumentException('Unable to create target directory for base classes: '.$base_dir);
        }
      }
    }

    try
    {
      return $this->doGenerate($target_dir);
    }
    catch (Exception $error)
    {
      throw $error;
    }
  }

  /**
   * Actually generate the classes defined in the definition schema previously
   * set to this generator.
   * At this point, it has already been checked whether $target_dir exists and
   * is writeable, so that kind of checks is not necessary here.
   *
   * This method should be overridden by extending subclasses.
   *
   * @throws Exception on any kind of error.
   *
   * @param  string $target_dir The path to the target directory.
   *
   * @return bool True on success
   */
  protected function doGenerate($target_dir)
  {
    $result = true;

    foreach ($this->definition->toFiles() as $filename => $code)
    {
      if (0 == preg_match('#^base/#', $filename) && file_exists($target_dir.'/'.$filename))
      {
        continue;
      }

      $bytes  = file_put_contents($target_dir.'/'.$filename, $code);
      $result = $result && false !== $bytes;
    }

    return $result;
  }
  
}