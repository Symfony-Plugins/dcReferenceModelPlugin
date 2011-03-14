<?php

/**
 * dcReferenceClassInterface
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
interface dcReferenceClassInterface
{
  /**
   * Get the options for this class as an associative array with the
   * identifiers as the keys and the option names as the values.
   *
   * @return array
   */
  public static function getOptions();

  /**
   * Get the option identifiers for this class as an array. This is equivalent
   * to `array_keys(self::getOptions())`.
   *
   * @return array
   */
  public static function getIdentifiers();

  /**
   * Get the name associated to identifier $identifier or null if $identifier
   * is not a valid option identifier for this class.
   *
   * @return string or null
   */
  public static function toString($identifier);

}