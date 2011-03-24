<?php
namespace Mustaml;
require_once 'mustaml.php';

class AttrParserTest extends \PHPUnit_Framework_TestCase {
	public function testOneSimpleAttr() {
		$s=new Scanner('foo=bar');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(1,count($attr[0]->children));
		$this->assertEquals('text',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->contents);
	}
	public function testTwoSimpleAttr() {
		$s=new Scanner('foo=bar baz=bang');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(2,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(1,count($attr[0]->children));
		$this->assertEquals('text',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->contents);
		
		$this->assertEquals('attr',$attr[1]->type);
		$this->assertEquals('baz',$attr[1]->name);
		$this->assertEquals(1,count($attr[1]->children));
		$this->assertEquals('text',$attr[1]->children[0]->type);
		$this->assertEquals('bang',$attr[1]->children[0]->contents);
	}
	public function testOneValAttr() {
		$s=new Scanner('foo==bar');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(1,count($attr[0]->children));
		$this->assertEquals('val',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->varname);
	}
	public function testOneQuoteAttr() {
		$s=new Scanner('foo="bar"');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(1,count($attr[0]->children));
		$this->assertEquals('text',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->contents);
	}
	public function testVarAndSimpleAttr() {
		$s=new Scanner('foo=bar=biz');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		
		$this->assertEquals(2,count($attr[0]->children));
		$this->assertEquals('text',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->contents);
		$this->assertEquals('val',$attr[0]->children[1]->type);
		$this->assertEquals('biz',$attr[0]->children[1]->varname);
	}
	public function testSimpleAndVarAttr() {
		$s=new Scanner('foo==biz,bar');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		
		$this->assertEquals(2,count($attr[0]->children));
		$this->assertEquals('val',$attr[0]->children[0]->type);
		$this->assertEquals('biz',$attr[0]->children[0]->varname);
		$this->assertEquals('text',$attr[0]->children[1]->type);
		$this->assertEquals('bar',$attr[0]->children[1]->contents);
	}
	public function testSimpleVarAndSimpleAttr() {
		$s=new Scanner('foo=bar=biz boo');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		
		$this->assertEquals(3,count($attr[0]->children));
		$this->assertEquals('text',$attr[0]->children[0]->type);
		$this->assertEquals('bar',$attr[0]->children[0]->contents);
		$this->assertEquals('val',$attr[0]->children[1]->type);
		$this->assertEquals('biz',$attr[0]->children[1]->varname);
		$this->assertEquals('text',$attr[0]->children[2]->type);
		$this->assertEquals('boo',$attr[0]->children[2]->contents);
	}
	public function testOneBooleanAttr() {
		$s=new Scanner('foo');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(1,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(0,count($attr[0]->children));
	}
	public function testTwoBooleanAttr() {
		$s=new Scanner('foo selected');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(2,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(0,count($attr[0]->children));
		$this->assertEquals('attr',$attr[1]->type);
		$this->assertEquals('selected',$attr[1]->name);
		$this->assertEquals(0,count($attr[1]->children));
	}
	public function testTwoBooleanAttrCommaSeparated() {
		$s=new Scanner('foo,selected');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals(2,count($attr));
		
		$this->assertEquals('attr',$attr[0]->type);
		$this->assertEquals('foo',$attr[0]->name);
		$this->assertEquals(0,count($attr[0]->children));
		$this->assertEquals('attr',$attr[1]->type);
		$this->assertEquals('selected',$attr[1]->name);
		$this->assertEquals(0,count($attr[1]->children));
	}
	public function testTheKingDiscipline() {
		$s=new Scanner('foo=bar baz bam="boom bah" su==bi  ko=hu=ma  to val=true =dyn');
		$p=new AttrParser();
		$attr=$p->parse_attr($s);
		$this->assertEquals('[{"attributes":[],"name":"foo","type":"attr","children":[{"contents":"bar","type":"text","children":[]}]},{"attributes":[],"name":"baz","type":"attr","children":[]},{"attributes":[],"name":"bam","type":"attr","children":[{"contents":"boom bah","type":"text","children":[]}]},{"attributes":[],"name":"su","type":"attr","children":[{"varname":"bi","type":"val","children":[]}]},{"attributes":[],"name":"ko","type":"attr","children":[{"contents":"hu","type":"text","children":[]},{"varname":"ma","type":"val","children":[]}]},{"attributes":[],"name":"to","type":"attr","children":[]},{"attributes":[],"name":"val","type":"attr","children":[{"contents":"true","type":"text","children":[]}]},{"varname":"dyn","type":"val","children":[]}]',json_encode($attr));
	}
}
?>