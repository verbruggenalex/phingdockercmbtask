<?php

namespace Phing\CarinaMarinaBackup;

/**
 * A Docker CarinaMarina Backup task for Phing.
 *
 * @package Phing\CarinaMarinaBackup
 */
class Task extends \ExecTask {

  /**
   * All Docker options to be used to create the command.
   *
   * @var Option[]
   */
  protected $options = array();

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
   * Restore destination for archive contents (restore only).
   *
   * @var string
   */
  private $destination = '';

  /**
   * Output the archive to stdout instead of uploading it to a Cloud Files
   * container (backup only)
   *
   * @var string
   */
  private $stdout = '';

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
   * Whether to backup or restore.
   *
   * @var string
   */
  private $type = "";


  /**
   * Path the the Docker executable.
   *
   * @var PhingFile
   */
  protected $bin = 'docker';

  /**
   * Task constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setExecutable($this->bin);
  }

  /**
   * Configures PHP CodeSniffer.
   */
  public function main() {

    if ($this->bin instanceof \PhingFile) {
      $this->setBin($this->bin);
    }
    $this->commandline->setExecutable($this->bin);

    //$this->realCommand = "run --rm  --volumes-from=" . $this->volumesFrom . " carinamarina/backup " . $this->type;

    $options = array(
      'run',
      '--rm',
      '--volumes-from=' . $this->volumesFrom,
      'carinamarina/backup',
      $this->type,
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
    if (!in_array($this->type, array('backup', 'restore'))) {
      throw new \BuildException("Required property 'type' must be either 'backup' or 'restore'.");
    }
    else {
      if ($this->type == "backup") {
        $required_properties = array('source', 'stdout');
        foreach ($required_properties as $required_property) {
          if (empty($this->$required_property)) {
            throw new \BuildException("Missing required property '$required_property'.");
          }
        }
      }
      if ($this->type == "backup") {
        $required_properties = array('destination', 'stdin');
        foreach ($required_properties as $required_property) {
          if (empty($this->$required_property)) {
            throw new \BuildException("Missing required property '$required_property'.");
          }
        }
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
   * Sets the carinamarina type.
   *
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
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

  /**
   * Set the path to the Docker executable.
   *
   * @param string $bin
   *   The docker executable file.
   */
  public function setBin($bin) {
    $this->bin = new \PhingFile($bin);
    $this->setExecutable($this->bin);
  }

  /**
   * Options of the CarinaMarina command.
   *
   * @return Option
   *   The created option.
   */
  public function createOption() {
    $num = array_push($this->options, new Option());
    return $this->options[$num - 1];
  }


  /**
   * Check if an option exists.
   *
   * @param string $optionName
   *   The option name.
   *
   * @return array|\Phing\Behat\Option[]
   *   The option if exists, an empty array otherwise.
   */
  private function optionExists($optionName) {
    return array_filter($this->options, function ($option) use ($optionName) {
      return $option->getName() == $optionName;
    });
  }
  /**
   * Remove an option.
   *
   * @param string $optionName
   *   The option name.
   *
   * @return \Phing\Behat\Option[]
   *   The option array without the option to remove.
   */
  private function optionRemove($optionName) {
    $this->options = array_filter($this->options, function ($option) use ($optionName) {
      return $option->getName() != $optionName;
    });
    return $this->options;
  }
}
