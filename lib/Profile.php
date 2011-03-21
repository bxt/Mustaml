<?php
/**
 * A simple class for tracking execution times of PHP scripts and logging them into CVS files
 * @author Bernhard Häussner
 * @see https://gist.github.com/846504
 */
class Profile {

  private $_start = array();
  private $_subdata = '';
  private $_file=false;

  /**
   * Initialise, configure
   * @param String CSV-file to log times to
   * @param boolean If or not to measure time from construction on
   */
  public function __construct($file=false,$autostart=true) {
    $this->_file=$file;
    if($autostart) $this->start();
  }

  /**
   * Start profiling and timekeeping
   * @return self this (chainable)
   */
  public function start() {
    // use a stack of start times to enable nesting
    $this->_start[] = microtime(true);
    return $this;
  }

  /**
   * End profiling and dump log into CSV file, implicit info()
   * @param String Appears in log after final time
   * @return float Seconds passed since last call of start()
   */
  public function end( $message ) {
    if(count($this->_start)==0) return false;
    $returnV=$this->info($message);
    array_pop($this->_start);
    if($this->_file && count($this->_start)==0) {
      $fd = fopen($this->_file, "a");
      fwrite($fd, $this->_subdata );
      fclose($fd);
    }
    if(count($this->_start)==0) {
      $this->_subdata='';
    }
    return $returnV;
  }

  /**
   * Write current execution time to log
   * @param String Appears in log after current time
   * @return float Seconds passed since last call of start()
   */
  public function info( $message ) {
    $lvl=count($this->_start);
    if($lvl==0) return false;
    $time=microtime(true)-$this->_start[$lvl-1];
    $this->_subdata .= $time;
    $this->_subdata .=  ",\"" . str_repeat("-",$lvl) . " " . str_replace("\"","\"\"",$message) . "\"\n";
    return $time;
  }

}
?>
