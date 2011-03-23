<?php
namespace Mustaml;
require_once 'mustaml.php';

/**
 * Mustaml for PHP
 *
 * Mustaml is a html template language that enforces "logic-less"
 * templates as known from {{ mustache }} but using pythonish indentation
 * like HAML to build html-tags. Here are some Examples of Mustaml usage.
 * 
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
    $data=json_decode('[]',true);
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
    $data=json_decode('[]',true);
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
    $data=json_decode('[]',true);
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
    $expectedHtml='<p id="first" class="nice">Text</p><p class="nice middle">Text too</p><p><span class="inner">Wehah</span></p>';
    $template='%p#first.nice Text
%p.nice.middle Text too
%p
  %span.inner
    Wehah';
    $data=json_decode('[]',true);
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
    $data=json_decode('[]',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * HTML-Attributes
   *
   * Atrtributes are defined as usual but are appended in brackets. Yes,
   * this is HTML-syntax and not some language-specific map with lots of @,
   * => and so on. 
   */
  public function testHAttrs() {
    $expectedHtml='<p lang="en">Yo!</p><input type="text" value="tryna edit me" disabled="disabled"></input>';
    $template='%p(lang="en") Yo!
%input(type="text" value="tryna edit me" disabled)';
    $data=json_decode('[]',true);
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
    $expectedHtml='<html><!--  created by mustaml! --></html>';
    $template='%html
  / created by mustaml!';
    $data=json_decode('[]',true);
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
    $data=json_decode('[]',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Inserting Data
   *
   * Of course a template engine does output strings. They are excaped for
   * html-output by default. 
   */
  public function testBasicData() {
    $expectedHtml='<p>Hello World!</p>';
    $template='%p =varname';
    $data=json_decode('{"varname":"Hello World!"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Inserting More Data
   *
   * The = tag doesn't want sub-nodes, so use parallel =tags like this: 
   */
  public function testBasicData2() {
    $expectedHtml='<p>Hello World!, Hello Venus!</p>';
    $template='%p
  =varname
  , 
  =varname2';
    $data=json_decode('{"varname":"Hello World!","varname2":"Hello Venus!"}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Looping
   *
   * You can loop over arrays. The current value is availible as ".". 
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
   * And you can loop over maps. Inside the loop, you can access the map
   * keys like normal vars. 
   */
  public function testBasicAssocLoop() {
    $expectedHtml='<ul><li>Hello World!</li><li>Ave Venus!</li><li>Hey tiny Pluto!</li></ul>';
    $template='%ul
  -planets
    %li
      =greeting
      =name
      !';
    $data=json_decode('{"planets":[{"name":"World","greeting":"Hello "},{"name":"Venus","greeting":"Ave "},{"name":"Pluto","greeting":"Hey tiny "}]}',true);
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
    $template='-doIt it\'s true
-^doIt it\'s actually false';
    $data=json_decode('{"doIt":true}',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Boolean Switches (false)
   *
   * A ^ indicates not, so the children are only shown if the value is
   * false. 
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
   * Consequently, the text is not rendered, if you get a true value with
   * ^.
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
   * Another use of ^ is checking for unset values. 
   */
  public function testBasicBooleanIsset() {
    $expectedHtml='Absent!';
    $template='-^undefined Absent!';
    $data=json_decode('[]',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Empty Loops
   *
   * Another use of ^ is checking for empty arrays, strings and 0 numbers. 
   */
  public function testEmptryArray() {
    $expectedHtml='<ul><li class="grey">No planats to visit today!</li></ul>';
    $template='%ul
  -planets
    %li =. , G\'day!
  -^planets
    %li.grey No planats to visit today!';
    $data=json_decode('{"planets":[]}',true);
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
  public function testBscaping() {
    $expectedHtml='%p';
    $template='\\%p';
    $data=json_decode('[]',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
  /**
   * Attributes with data
   *
   * You can provide a map for hightly dynamic attributes. 
   */
  public function testAttrData() {
    $expectedHtml='<link rel="stylesheet" src="style/main.css"></link>';
    $template='%link(=linktag)';
    $data=json_decode('{"linktag":{"rel":"stylesheet","src":"style\\/main.css"}}',true);
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
    $data=json_decode('[]',true);
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
    $data=json_decode('[]',true);
    $m=new Mustaml($template,$data);
    $html=$m();
    $this->assertEquals($expectedHtml,$html);
  }
  
}
?>