<?php

namespace Phing\CarinaMarinaBackup;

/**
 * A Docker CarinaMarina Backup task for Phing.
 *
 * @package Phing\CarinaMarinaBackup
 */
class BackupTask extends Task {

  /**
   * The name of the Cloud Files container in which to store the backup.
   * Ignored if --stdout is used (not implemented)
   *
   * @var string
   */
  private $container = '';

  /**
   * The source directory for the backup. All files in this directory will
   * be added to the archive (backup only).
   *
   * @var string
   */
  private $source = '';

  /**
   * Output the archive to stdout instead of uploading it to a Cloud Files
   * container (backup only)
   *
   * @var string
   */
  private $stdout = '';

  /**
   * Compress the archive using gzip.
   *
   * @var bool
   */
  private $zip = FALSE;

  /**
   * Volumes from which container.
   *
   * @var string
   */
  private $volumesFrom = "";

  /**
   * Whether to backup or restore.
   *
   * @var string
   */
  private $type = "";


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
      'backup',
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
    $required_properties = array('source', 'stdout');
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
   * Sets the source of the backup.
   *
   * @param string $source
   */
  public function setSource($source) {
    $this->createOption()
      ->setName('source')
      ->addText($source);
  }

  /**
   * Sets the backup package.
   *
   * @param string $stdout
   */
  public function setStdout($stdout) {
    $this->createOption()
      ->setName('stdout')
      ->addText($stdout);
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
