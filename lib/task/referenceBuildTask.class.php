<?php

class referenceBuildTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace        = 'reference';
    $this->name             = 'build';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [reference:build|INFO] task generates reference model classes from your
schema definition(s).
Call it with:

  [php symfony reference:build|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $parser = new dcReferenceModelSchemaParser();

    foreach ($this->getDefinitionFiles() as $definition_file)
    {
      $this->logSection('Build', 'Reading definition schema in '.$definition_file.'.');

      $model_definition = $parser->parse($definition_file);
      $package          = $parser->getPackage();

      $generator = new dcReferenceModelGenerator($model_definition);

      $result = $generator->generate($this->getTargetDirectory($package));

      if (false === $result)
      {
        $this->logSection('Error', 'There were problems while building definitions in '.$definition_file, null, 'ERROR');
      }
      else
      {
        $this->logSection('Build', sprintf('Done. Generated %d reference class(es) for package %s.', $model_definition->count(), $package));
      }
    }
  }

  protected function getDefinitionFiles()
  {
    return sfFinder::type('file')->ignore_version_control()->sort_by_name()->name('*reference.yml')->in(sfConfig::get('sf_config_dir'));
  }

  protected function getTargetDirectory($package)
  {
    return sfConfig::get('sf_root_dir').'/'.str_replace('.', '/', $package);
  }

}