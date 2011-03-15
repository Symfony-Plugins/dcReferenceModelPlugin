<?php

/**
 * dcReferenceModelDefinition
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcReferenceModelDefinition implements dcReferenceDefinitionInterface
{
  protected $classes = array();

  public function __construct($classes)
  {
    $this->classes = $classes;
  }

  /**
   * Get the class definitions contained by this model definition.
   * @return array
   */
  public function getClasses()
  {
    return $this->classes;
  }

  /**
   * Set the class definitions for this model definition.
   *
   * @param array $classes dcReferenceClassDefinition[]
   */
  public function setClasses(array $classes)
  {
    $this->classes = $classes;
  }

  /**
   * Add a class definition to this model definition.
   *
   * @param  dcReferenceClassDefinition $class_definition The class definition to add.
   *
   * @return dcReferenceModelDefinition This object, for a fluent interface.
   */
  public function addClass(dcReferenceClassDefinition $class_definition)
  {
    $this->classes[] = $class_definition;

    return $this;
  }

  /**
   * Get the number of class definitions contained by this model definition.
   *
   * @return int
   */
  public function count()
  {
    return count($this->classes);
  }

  /**
   * Gather the PHP code for every class definition in this model definition
   * and return it.
   *
   * @return string
   */
  public function toPHP()
  {
    $php = '';

    foreach ($this->getClasses() as $class_definition)
    {
      $php .= "\n\n// ".$class_definition->getName()."\n";
      $php .= $class_definition->toPHP();
      $php .= "\n\n// End ".$class_definition->getName()."\n";
    }

    return $php;
  }

  /**
   * Convert this model definition schema to PHP code, returning an associative
   * array with the filenames as keys an the PHP code as the values.
   *
   * @return array
   */
  public function toFiles()
  {
    $files = array();

    foreach ($this->getClasses() as $class_definition)
    {
      $files[$class_definition->getBaseFilename()] = "<?php\n\n".$class_definition->toBasePHP();
      $files[$class_definition->getFilename()] = "<?php\n\n".$class_definition->toPHP();
    }

    return $files;
  }

}