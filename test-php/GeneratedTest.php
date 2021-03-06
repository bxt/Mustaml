<?php
/**
 * Unittests generated by build/gen-unittests.php
 */
namespace Mustaml;
require_once 'mustaml.php';

/**
 * Mustaml for PHP
 *
 * Mustaml is a html template language that enforces "logic-less"
 * templates as known from {{ mustache }} but using pythonish
 * indentation like HAML to build html-tags. Here are some Examples of
 * Mustaml usage. 
 */
class GeneratedTest extends \PHPUnit_Framework_TestCase {
  /**
   * Creating HTML-tags quickly
   *
   * You can create tags with the %tag styntax. Nesting is done via
   * indentation. Tags are automatically closed. 
   */
  public function testBasicIndented() {
    $expectedHtml='<html><head><title>Yippiyeah!</title></head><body><p>Everything closing automatically. </p></body></html>';
    $template='%html
  %head
    %title Yippiyeah!
  %body
   %p
     Everything closing automatically. ';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Creating HTML-tags even quicker
   *
   * You can append child nodes directly to save lines:
   */
  public function testBasicNonIndented() {
    $expectedHtml='<html><head><title>Yippiyeah!</title></head><body><p>Everything closing automatically. </p></body></html>';
    $template='%html
  %head %title Yippiyeah!
  %body %p Everything closing automatically. ';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Mixing nesting and appending
   *
   * For every node you append, we're nesting all child nodes that level
   * deeper. This happens when mixing appending and nesting: 
   */
  public function testBasicIndentedAndNonIndented() {
    $expectedHtml='<p><span><b>Wow. </b>Really. </span></p>';
    $template='%p %span
  %b Wow. 
  Really. ';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Adding classes and ids in CSS-syntax
   *
   * One of the Key features is quickly adding the common class and id
   * attributes, with a syntax well known from CSS. 
   */
  public function testBasicClassAndId() {
    $expectedHtml='<p id="first" class="nice">Text</p><p class="nice middle">Text too</p><p><span class="inner">Weeehah</span></p>';
    $template='%p#first.nice Text
%p.nice.middle Text too
%p
  %span.inner
    Weeehah';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Implicit Divs
   *
   * To make things even easier, you can leave out %tag and Mustaml will
   * guess it's a %div
   */
  public function testImplicitDivs() {
    $expectedHtml='<div id="page" class="container"><div id="header"></div><div id="content"><div id="sidebar"></div></div><div id="footer"></div></div>';
    $template='#page.container
  #header
  #content
    #sidebar
  #footer';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Generating a Doctype
   *
   * Mustaml provides the short triple-bang for inserting your doctype
   * declaration. It currently supports only the HTML5 one. 
   */
  public function testBasicDoctype() {
    $expectedHtml='<!DOCTYPE html><html></html>';
    $template='!!!
%html';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Self-Closing tags
   *
   * You can configure a list of tags that should be self-closed when
   * empty. Here are the defaults: 
   */
  public function testSelfClosingTags() {
    $expectedHtml='<br /><img /><input /><meta /><link /><hr /><frame /><param />';
    $template='%br
%img
%input
%meta
%link
%hr
%frame
%param';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * HTML-Attributes
   *
   * Attributes are defined as usual but are appended in brackets. Yes,
   * this is HTML-syntax and not some language-specific map with lots of :,
   * @, => and so on. 
   */
  public function testHAttrs() {
    $expectedHtml='<p lang="en">Yo!</p><input type="text" value="tryna edit me" disabled="disabled" />';
    $template='%p(lang="en") Yo!
%input(type=text value="tryna edit me" disabled)';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Old-School HTML-Attributes
   *
   * You can use some alternate syntax and whitepace as you like: 
   */
  public function testHAttrsAlternates() {
    $expectedHtml='<p lang="en">Yo!</p><input type="text" value="tryna edit me" disabled="disabled" />';
    $template='%p(lang=>"en") Yo!
%input(type=>"text",value => "tryna edit me", disabled)';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Overriding HTML-Attributes
   *
   * When you specify id attributes in your attribute hash, it is
   * overridden by the latter. 
   */
  public function testHAttrsOverride() {
    $expectedHtml='<div id="newer"></div>';
    $template='#old(id=new id=newer)';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * HTML-Array-Attributes
   *
   * Some attributes can have a space-separated list of values. Currently
   * these 3 are supported. 
   */
  public function testHArrayAttrs() {
    $expectedHtml='<link class="foo bar" rev="prev index" rel="shortlink home up" />';
    $template='%link.foo(class=bar,rev=prev,rev=index,rel=shortlink,rel="home up")';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * HTML-Comments
   *
   * You can insert html-comments too. 
   */
  public function testHComments() {
    $expectedHtml='<html><!--  created by Mustaml! --></html>';
    $template='%html
  / created by Mustaml!';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * HTML-Comment-blocks
   *
   * End even put whole parts of your template into comment tags. This
   * might come in handy if you don't want your users to see this part, but
   * be able to check the rendering output. 
   */
  public function testHCommentBlock() {
    $expectedHtml='<html><!--  temporarily disabled:<body&gt;<p&gt;</p&gt;</body&gt; --></html>';
    $template='%html
  / temporarily disabled:
    %body %p';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Inserting Data
   *
   * Of course a template engine does output strings. They are escaped for
   * HTML-output by default. 
   */
  public function testBasicData() {
    $expectedHtml='<p>&lt;&quot;Hello World!&quot;&gt; &amp;</p>';
    $template='%p =varname';
    $data=json_decode('{"varname":"<\\"Hello World!\\"> &"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Undefined Data
   *
   * If you do not provide a piece of data, it will be silently ignored. 
   */
  public function testBasicDataUndefined() {
    $expectedHtml='<p></p>';
    $template='%p =nonexisting';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Inserting More Data
   *
   * You can define text and vars as children.  (The = tag doesn't want
   * sub-nodes. )
   */
  public function testBasicData2() {
    $expectedHtml='<p>Hello World, hello Venus!</p>';
    $template='%p
  =varname
  , 
  =varname2';
    $data=json_decode('{"varname":"Hello World","varname2":"hello Venus!"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Maps
   *
   * The minus operator (-) marks special blocks that behave depending on
   * the var content. For maps, their entries will be availible in the
   * scope
   */
  public function testBasicMap() {
    $expectedHtml='Athlon, 2.2';
    $template='-cpu
  =name
  , 
  =ghz';
    $data=json_decode('{"cpu":{"name":"Athlon","ghz":2.2}}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Looping
   *
   * The minus operator (-) on arrays (vectors) loops the block. The
   * current value is available as ".". 
   */
  public function testBasicLoop() {
    $expectedHtml='<ul><li>Hello World!</li><li>Hello Venus!</li><li>Hello Pluto!</li></ul>';
    $template='%ul
  -planets
    %li 
     Hello 
     =.
     !';
    $data=json_decode('{"planets":["World","Venus","Pluto"]}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Looping Maps
   *
   * And you can loop over arrays containing maps. Inside the loop, you can
   * access the map keys like normal vars. 
   */
  public function testBasicAssocLoop() {
    $expectedHtml='<ul><li>Hello World!</li><li>Ave Venus!</li><li>Hey tiny Pluto!</li></ul>';
    $template='%ul
  -planets
    %li
      =greeting
      =name
      !';
    $data=json_decode('{"planets":[
  {"name":"World","greeting":"Hello "},
  {"name":"Venus","greeting":"Ave "},
  {"name":"Pluto","greeting":"Hey tiny "}
]}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Boolean Switches
   *
   * You can check for true/false values with the very same syntax. 
   */
  public function testBasicBoolean() {
    $expectedHtml='it\'s true';
    $template='-doIt it\'s true';
    $data=json_decode('{"doIt":true}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Boolean Switches (false)
   *
   * A caret operator (^) indicates not, so the children are only shown if
   * the value is false. 
   */
  public function testBasicBooleanFalseNot() {
    $expectedHtml='it\'s actually false';
    $template='-doIt it\'s true
-^doIt it\'s actually false';
    $data=json_decode('{"doIt":false}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Boolean Switches (false positive)
   *
   * Consequently, the text is not rendered, if you get a true value after
   * the caret.
   */
  public function testBasicBooleanNot() {
    $expectedHtml='';
    $template='-^doItNot but is was not false';
    $data=json_decode('{"doItNot":true}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Boolean Switches (false positive)
   *
   * Another use of the caret is checking for unset values. 
   */
  public function testBasicBooleanIsset() {
    $expectedHtml='Absent!';
    $template='-^undefined Absent!';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Empty Loops
   *
   * Another use of the caret is checking for empty arrays, strings and 0
   * numbers. 
   */
  public function testEmptryArray() {
    $expectedHtml='<ul><li class="grey">No planets to visit today!</li></ul>';
    $template='%ul
  -planets
    %li =.
  -^planets
    %li.grey No planets to visit today!';
    $data=json_decode('{"planets":[]}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Checking for empty Loops
   *
   * The notayim operator (^^) will inverse the not-operator. You can check
   * if a loop would render at least one item. In this example the <ul> tag
   * is only rendered if there are any "planets". 
   */
  public function testEmptyArrayNot() {
    $expectedHtml='<p class="grey">No planets to visit today!</p>';
    $template='-^^planets %ul
  -planets
    %li =.
-^planets
    %p.grey No planets to visit today!';
    $data=json_decode('{"planets":[]}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Notayim ignored for nonemty loop
   *
   * I you use the  notayim operator on an defined value it will just
   * render its subblocks as nothing had happended. 
   */
  public function testEmptryArrayNotWithValue() {
    $expectedHtml='<ul><li>World</li></ul>';
    $template='-^^planets %ul
  -planets
    %li =.
-^planets
    %p.grey No planets to visit today!';
    $data=json_decode('{"planets":["World"]}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Checking for empty strings
   *
   * The notayim operator (^^) can be used to check if a string is ""
   * (empty). In this example no paragraph is created if the string is
   * empty. 
   */
  public function testEmptyStringNot() {
    $expectedHtml='';
    $template='-^^stingray %p =stingray
';
    $data=json_decode('{"stingray":""}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Notayim ignored for nonempty string
   *
   * If the string is not empty the subblock remains unaffected and is
   * rendered as usual. 
   */
  public function testEmptyStringNotWithValue() {
    $expectedHtml='<p>Corvette C2</p>';
    $template='-^^stingray %p =stingray
';
    $data=json_decode('{"stingray":"Corvette C2"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Unset Vars with Minus
   *
   * When using minus operator with a not existing var name. it's children
   * are not rendered. 
   */
  public function testUnsetVarsMinus() {
    $expectedHtml='(rendered)';
    $template='(rendered)
-undefined (not rendered)';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Unset Vars with Equal
   *
   * When using equals operator with a not existing var's name, it does not
   * output anything. 
   */
  public function testUnsetVarsEqual() {
    $expectedHtml='nothing: ';
    $template='nothing: 
=undefined';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Escaping
   *
   * If you want to start a text line with a meta-character otherwise
   * interpreted as some kind of node, you can escape it with a backslash. 
   */
  public function testEscaping() {
    $expectedHtml='%p';
    $template='\\%p';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Printing verbatim HTML
   *
   * The minus operator applied on string values prints them verbatim. Say
   * you've preprocessed some Markdown and want to display it on a page,
   * just insert the string value's varname after the minus. 
   */
  public function testVerbatimHtml() {
    $expectedHtml='<b>I\'m bold!</b>';
    $template='-html';
    $data=json_decode('{"html":"<b>I\'m bold!</b>"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Using anything (e.g. Strings) with blocks
   *
   * Don't get the above confused with calling blocks with strings. You can
   * use every type of value to initialize blocks, it won't be rendered but
   * it will be availible as "." inside the block. 
   */
  public function testBlockToStrong() {
    $expectedHtml='<b>Big Mike</b>';
    $template='-string
  %b =.';
    $data=json_decode('{"string":"Big Mike"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Using srtings with with blocks vs notayim
   *
   * However, other than the notayim-operator, using only minus it will
   * render its block for empty strings too. 
   */
  public function testBlockToStrongEmpty() {
    $expectedHtml='<b></b>';
    $template='-string
  %b =.';
    $data=json_decode('{"string":""}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with data
   *
   * You can provide a map for highly dynamic attributes, while still
   * defining others in your template. Last specified overrides. 
   */
  public function testAttrData() {
    $expectedHtml='<link rel="stylesheet" href="style/main.css" type="text/css" />';
    $template='%link(=linktag type="text/css")';
    $data=json_decode('{"linktag":{"rel":"stylesheet","href":"style/main.css"}}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with data-values
   *
   * You can even fill only the attribute's values with dynamic strings,
   * and mix this with other attributes and old syntax.  
   */
  public function testAttrDataValues() {
    $expectedHtml='<link rel="stylesheet" href="style/main.css" />';
    $template='%link(rel=>stylesheet, href=>=style)';
    $data=json_decode('{"style":"style/main.css"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with data-values and text
   *
   * You can mix usual text and dynamic values in your attributes. Place an
   * = between the end of your varname and the text. 
   */
  public function testAttrDataValuesWithText() {
    $expectedHtml='<a href="#12-headline">go</a>';
    $template='%a(href=#=anchorNo=-=anchor) go';
    $data=json_decode('{"anchor":"headline","anchorNo":"12"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with unset data-values
   *
   * When you use unset vars in attributes, the attributes will exist, but
   * with empty contents at the vars. 
   */
  public function testAttrDataValuesUnset() {
    $expectedHtml='<a href="">foo</a>';
    $template='%a(=unset,href==unset2) foo';
    $data=json_decode('{}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with boolean data
   *
   * If the referenced data-values point to boolean values only, the
   * attribute will be set if all of them are true. 
   */
  public function testAttrDataBooleans() {
    $expectedHtml='<input type="checkbox" checked="checked" />';
    $template='%input(type=checkbox,checked==test1==test2)';
    $data=json_decode('{"test1":true,"test2":true}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with equal signs in them
   *
   * To avoid parsing the equal sings as vars just quote the attribute
   * value. 
   */
  public function testAttrDataValuesWithTextQuoted() {
    $expectedHtml='<a href="#=anchorNo=-=anchor">go</a>';
    $template='%a(href="#=anchorNo=-=anchor") go';
    $data=json_decode('{"anchor":"headline","anchorNo":"12"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Mustaml-Comments
   *
   * Mustaml-Comments are not rendered at all. 
   */
  public function testmustamlComments() {
    $expectedHtml='';
    $template='-/ never rendered';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Mustaml-Comment-blocks
   *
   * Get rid of whole subtrees with Mustaml-Comments. 
   */
  public function testMustamlCommentBlock() {
    $expectedHtml='';
    $template='-/ temporarily disabled:
    %body %p';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Empty tag operators
   *
   * If you use the tag creating operators without a name, they will just
   * create a div. 
   */
  public function testEmptyOperatorDivCreating() {
    $expectedHtml='<div><div><div>some divs</div></div></div>';
    $template='. # % some divs';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Separation with multiple spaces
   *
   * If you separate two seemingly elements with more then one space, the
   * latter turns out as text. 
   */
  public function testSeparationWithMultipleSpaces() {
    $expectedHtml='<p> %b</p>';
    $template='%p  %b';
    $data=array();
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
}
?>