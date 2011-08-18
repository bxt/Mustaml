
var htmlCompiler=require('../js/htmlCompiler.js')();

var a=require('assert');

var testsCalled={};

testsCalled.testNestingError=false;
htmlCompiler.render(null,'%p normal\n  %p 2xindent\n %p 1xindent (bad)',{},function(err,html){
  a.ok(err);
  a.ok(!html);
  testsCalled.testNestingError=true;
});

testsCalled.testAttributeNoVarnameError=false;
htmlCompiler.render(null,'%p(=)',{"foo":"bar"},function(err,html){
  a.ok(err);
  a.ok(!html);
  testsCalled.testAttributeNoVarnameError=true;
});

testsCalled.testAttributeNoVarnameErrorInAttrValSimple=false;
htmlCompiler.render(null,'%p(foo==)',{},function(err,html){
  a.ok(err);
  a.ok(!html);
  testsCalled.testAttributeNoVarnameErrorInAttrValSimple=true;
});

testsCalled.testAttributeNoVarnameErrorInAttrVal=false;
htmlCompiler.render(null,'%p(foo=a= c)',{},function(err,html){
  a.ok(err);
  a.ok(!html);
  testsCalled.testAttributeNoVarnameErrorInAttrVal=true;
});


process.addListener('exit', function() {
  var allPassed=true;
  for(var testname in testsCalled) {
    allPassed=allPassed&&testsCalled[testname];
    if(!testsCalled[testname]) {
      console.log(' ✗ '+testname+' failed! ');
    }
  }
  a.ok(allPassed);  console.log(' ✓ Mustaml error tests passed. ');
});

