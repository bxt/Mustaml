<?php
namespace Mustaml;
require_once 'mustaml.php';

/**
 * This is a test regarding issue #1 template blocks
 * (https://github.com/bxt/Mustaml/issues/1)
 * 
 * We solve the problem of template blocks and template
 * inheritance by registering an autoloader that will
 * by default render the blocks.
 * If we then define child templates we overwrite the
 * blocks by setting a template value to their name
 * in the data passed. This way, the autoloader is not
 * called and instead the overridden template block
 * is rendered. 
 * 
 * See the example below:
 */
class MustamlBricksTest extends \PHPUnit_Framework_TestCase 
    implements \Mustaml\Autoloaders\AutoloaderI,
    \Mustaml\Autoloaders\MustamlDependentAlI {
  
  // first lets define our parent/main template:
  const MAIN_TMPL = <<<'EOM'

%h1
  -title.block
    A great web page
#container
  -container.block
    This is some sample content: 
    =key

EOM;
  
  /**
   * The example is run in this test case
   */
  public function testTemplateBlocks() {
    
    // Define a data map to see data beeing passed
    $data=array('key'=>'val');
    
    // Build a config to register the autoloaders in
    $config=new \Mustaml\Html\CompilerConfig();
    
    // Reigster an instance of this very class as al
    $config->registerAutoloader(new self());
    // This means where blocks were not overridden we render
    // the original contents
    
    // Build our parent template (with original blocks)
    $parent=new Mustaml(self::MAIN_TMPL,$data,$config);
    
    // Overwrite the container for a child:
    $childData=$data+array('container.block'=>new Mustaml("%div No longer sample Content: \n  =key"));
    // And put together child template:
    $child=new Mustaml(self::MAIN_TMPL,$childData,$config);
    
    // The parent template is rendered as expected with the original content. 
    $this->assertEquals('<h1>A great web page</h1>'.
                        '<div id="container">This is some sample content: val</div>',
                        $parent());
    
    // In the child template we get another container, the data is passed:
    $this->assertEquals('<h1>A great web page</h1>'.
                        '<div id="container"><div>No longer sample Content: val</div></div>',
                        $child());
  }
  
  // Now following: The implementation of the autoloaders. 
  
  /**
   * Holds a Mustaml object that can create new Mustaml objects with
   * same properties
   * @var \Mustaml\Mustaml
   */
  private $mustamlBoilerplate = null;
  /**
   * Inject the template that will render its block
   */
  public function autoload($key) {
    if(preg_match('/^(.*)\.block/i',$key,$m)) {
      return $this->mustamlBoilerplate->getWithTemplate('--',array());
    }
  }
  /**
   * Sets the mustaml class to use for rendering the loaded templates
   */
  public function setMustamlBoilerplate($mustamlBoilerplate) {
    $this->mustamlBoilerplate=$mustamlBoilerplate;
  }
  
  
  /**
   * How the data values are passed to child temlates
   */
  public function testNestingAndVarScope() {
    
    // if the same var is defined for the outer and the inner template, the outer template
    // and the block defined in the outer template get the outer value, only the parts
    // defined in the inner template get the inner temlates vars value. 
    $outerAndInner = new Mustaml("%div =var\n%span -inner\n  %b =var",array('var'=>'oV','inner'=>
        new Mustaml("#inner =var\n#yield --",array('var'=>'iV')) ));
    $this->assertEquals('<div>oV</div><span><div id="inner">iV</div><div id="yield"><b>oV</b></div></span>',$outerAndInner());
    
    // If the inner template does not have the var defined it gets the value of the outer template's var. 
    $outerOnly = new Mustaml("%div =var\n%span -inner\n  %b =var",array('var'=>'oV','inner'=>
        new Mustaml("#inner =var\n#yield --",array()) ));
    $this->assertEquals('<div>oV</div><span><div id="inner">oV</div><div id="yield"><b>oV</b></div></span>',$outerOnly());
    
    // If only the inner template has the var defined, the outer template will not see it, in the block defined
    // in the outer template and rendered inside the inner template the var well be set to the value of the inner
    // template. 
    $innerOnly = new Mustaml("%div =var\n%span -inner\n  %b =var",array('inner'=>
        new Mustaml("#inner =var\n#yield --",array('var'=>'iV')) ));
    $this->assertEquals('<div></div><span><div id="inner">iV</div><div id="yield"><b>iV</b></div></span>',$innerOnly());
    
  }
}

