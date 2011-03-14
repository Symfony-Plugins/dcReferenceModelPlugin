<?php

/**
 * dcReferenceDefinitionInterface
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
interface dcReferenceDefinitionInterface
{
  /**
   * Convert the object(s) defined in this definition to PHP code.
   *
   * @return string Plain PHP code, without PHP opening tags (<?php).
   */
  public function toPHP();

}