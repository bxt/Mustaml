<?php
/**
 * This script generates a series of test from the examples used in the docs
 */

$data=json_decode(file_get_contents($argv[1]),true);

echo '<?php'."\n";
echo 'namespace Mustaml;'."\n";
echo 'require_once \'mustaml.php\';'."\n"."\n";
echo '/**'."\n";
echo ' * Mustaml for PHP'."\n";
if(isset($data["desc"])):
echo ' *'."\n";
echo ' * '.wordwrap($data["desc"],70,"\n * ")."\n";
endif;
echo ' */'."\n";
echo 'class GeneratedTest extends \PHPUnit_Framework_TestCase {'."\n";

foreach($data["unittests"] as $test) {
 echo '  /**'."\n";
 echo '   * '.$test["title"]."\n";
 if(isset($test["desc"])):
 echo '   *'."\n";
 echo '   * '.wordwrap($test["desc"],70,"\n   * ")."\n";
 endif;
 echo '   */'."\n";
 echo '  public function test'.$test["testname"].'() {'."\n";
 echo '    $expectedHtml='.escape_php_str($test["html"]).';'."\n";
 echo '    $template='.escape_php_str($test["mustaml"]).';'."\n";
 echo '    $data=json_decode('.escape_php_str(json_encode($test["data"])).',true);'."\n";
 echo '    $m=new Mustaml($template,$data);'."\n";
 echo '    $html=$m();'."\n";
 echo '    $this->assertEquals($expectedHtml,$html);'."\n";
 echo '  }'."\n";
 echo "  \n";
}

echo '}'."\n";
echo '?';
echo '>';

function escape_php_str($str) {
	return '\''.str_replace(array('\\','\''),array('\\\\','\\\''),$str).'\'';
}

?>