<?php

namespace Phing\CarinaMarinaBackup;

/**
 * A Docker CarinaMarina Backup task for Phing.
 *
 * @package Phing\CarinaMarinaBackup
 */
class RestoreTask extends Task {

  /**
   * The name of the Cloud Files container in which to store the backup.
   * Ignored if --stdout is used (not implemented)
   *
   * @var string
   */
  private $container = '';

  /**
   * Restore destination for archive contents (restore only).
   *
   * @var string
   */
  private $destination = '';

  /**
   *  Read from stdin instead of Cloud Files (restore only)
   *
   * @var string
   */
  private $stdin = '';

  /**
   * Compress the archive using gzip.
   *
   * @var bool
   */
  private $zip = FALSE;

  /**
   * Name of archive object in a container (not implemented).
   *
   * @var string
   */
  private $object = '';

  /**
   * Volumes from which container.
   *
   * @var string
   */
  private $volumesFrom = "";

  /**
   * Configures PHP CodeSniffer.
   */
  public function main() {

    if ($this->bin instanceof \PhingFile) {
      $this->setBin($this->bin);
    }
    $this->commandline->setExecutable($this->bin);

    $options = array(
      'run',
      '--rm',
      '--volumes-from=' . $this->volumesFrom,
      'carinamarina/backup',
      'restore',
    );

    foreach ($this->options as $option) {
      // Trick to ensure no option duplicates.
      $options[$option->getName()] = $option->toString();
    }

    $this->commandline->addArguments(array_values($options));
    $this->buildCommand();
    $this->log('Executing command: ' . $this->realCommand);
    parent::main();
  }

  /**
   * Checks if all properties required for generating the config are present.
   *
   * @throws \BuildException
   *   Thrown when a required property is not present.
   */
  protected function checkRequirements() {
    $required_properties = array('destination', 'stdin');
    foreach ($required_properties as $required_property) {
      if (empty($this->$required_property)) {
        throw new \BuildException("Missing required property '$required_property'.");
      }
    }
  }

  /**
   * Sets the container.
   *
   * @param string $container
   */
  public function setContainer($container) {
    $this->createOption()
      ->setName('container')
      ->addText($container);
  }

  /**
   * Sets the destnation of the restore.
   *
   * @param string $destination
   */
  public function setDestination($destination) {
    $this->createOption()
      ->setName('destination')
      ->addText($destination);
  }

  /**
   * Sets the stdin.
   *
   * @param string $stdin
   */
  public function setStdin($stdin) {
    $this->createOption()
      ->setName('stdin')
      ->addText($stdin);
  }

  /**
   * Sets the volumes from which to backup.
   *
   * @param string $volumesFrom
   */
  public function setVolumesFrom($volumesFrom) {
    $this->volumesFrom = $volumesFrom;
  }

  /**
   * Sets whether or not to zip or unzip the file.
   *
   * @param bool $zip
   */
  public function setZip($zip)
  {
    if ($zip) {
      $this->createOption()
        ->setName('zip');
    }
  }
}
