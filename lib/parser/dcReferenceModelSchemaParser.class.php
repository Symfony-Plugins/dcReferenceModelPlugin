<?php

/**
 * dcReferenceModelSchemaParser
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcReferenceModelSchemaParser
{
  const DEFAULT_PACKAGE = 'lib.reference';

  protected
    $parsed_data = null,
    $package     = self::DEFAULT_PACKAGE;

  /**
   * Get the package for the last parsed schema.
   *
   * @return string
   */
  public function getPackage()
  {
    return $this->package;
  }

  /**
   * Parse the definition schema stored in the file at $source_path and return
   * it.
   *
   * @throws InvalidArgumentException If an $source_path is not readable.
   * @throws Exception                If an error occurs when parsing $source_path.
   *
   * @see doParse()
   *
   * @param  string $source_path The path of the source definition schema.
   *
   * @return dcReferenceModelDefinition
   */
  public function parse($source_path)
  {
    if (!is_readable($source_path))
    {
      throw new InvalidArgumentException('Unable to parse reference model definition schema file: '.$source_path);
    }

    try
    {
      return $this->parsed_data = $this->doParse($this->read($source_path));
    }
    catch (Exception $error)
    {
      $this->parsed_data = null;

      throw $error;
    }
  }

  /**
   * Read the contents of $path and return an understandeable data structure
   * to parse with `doParse()`.
   *
   * @throws InvalidArgumentException if $path doesn't have the expected format.
   *
   * @param  string $path
   *
   * @return array
   */
  protected function read($path)
  {
    $content = sfYaml::load($path);

    if (
      array_key_exists('reference', $content)
      && is_array($content['reference'])
      && array_key_exists('classes', $content['reference'])
      && is_array($content['reference']['classes'])
      && count($content['reference']['classes']) > 0
    )
    {
      if (!array_key_exists('package', $content['reference']) || '' == trim($content['reference']['package']))
      {
        $content['reference']['package'] = self::DEFAULT_PACKAGE;
      }

      return $content;
    }

    throw new InvalidArgumentException('Invalid schema definition format: '.$path);
  }

  /**
   * Actually parse the schema defined in $source_path. At this point it has
   * been checked whether $source_path exists and is readable, so that kind of
   * checks is not necessary here.
   *
   * This method should be overridden by extending subclasses.
   *
   * @throws Exception on any kind of error.
   *
   * @param  array $source The content of the source definition schema.
   *
   * @return dcReferenceModelDefinition
   */
  protected function doParse($source)
  {
    $root = $source['reference'];

    $this->package = trim($root['package']);

    $definition_classes = array();

    foreach ($root['classes'] as $class_name => $options)
    {
      $definition_classes[] = new dcReferenceClassDefinition($class_name, $options);
    }

    return new dcReferenceModelDefinition($definition_classes);
  }

}