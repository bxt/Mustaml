
var htmlCompiler=require('../js/htmlCompiler.js')();

var a=require('assert');

var testsCalled={};

testsCalled.testUnsetValueAttribute=false;
htmlCompiler.render(null,'%p(=lol)',{},function(err,html){
  a.ok(!err);
  a.strictEqual('<p></p>',html);
  testsCalled.testUnsetValueAttribute=true;
});

testsCalled.testAsyncSequencing=false;
htmlCompiler.render(null,'-x\n =v\n -f',{x:[
      { v:'s1', f:function(err,data,cb){process.nextTick(function(){cb(null,'f1');});} },
      { v:'s2', f:function(err,data,cb){cb(null,'f2')} }]
    },function(err,html){
  a.ok(!err);
  a.strictEqual('s1f1s2f2',html);
  testsCalled.testAsyncSequencing=true;
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

