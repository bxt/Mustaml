
var htmlCompiler=require('../js/htmlCompiler.js')();

var a=require('assert');

var testsCalled={};

testsCalled.testUnsetValueAttribute=false;
htmlCompiler.render(null,'%p(=lol)',{},function(err,html){
  a.ok(!err);
  a.strictEqual('<p></p>',html);
  testsCalled.testUnsetValueAttribute=true;
});

process.addListener('exit', function() {
  var allPassed=true;
  for(var testname in testsCalled) {
    allPassed=allPassed&&testsCalled[testname];
    if(!testsCalled[testname]) {
      console.log(' ✗ '+testname+' failed! ');
    }
  }
  a.ok(allPassed);  console.log(' ✓ Mustaml special tests passed. ');
});

