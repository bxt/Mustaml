var fs=require('fs');



if(!process.argv[2]) throw "Specify json input file!";

fs.readFile(process.argv[2], function (err, filedata) {
	if (err) throw err;
	var testdata=JSON.parse(filedata);
	var testJs='';
	testJs+='// '+testdata.pagetitle+'\n';
	testJs+='//\n';
	testJs+='// '+testdata.desc+'\n\n';
	testJs+='var htmlCompiler=require(\'../js/htmlCompiler.js\')();\n\n';
	testJs+='var a=require(\'assert\');\n\n';
	testJs+='var testsCalled={};\n\n';
	for (var i=0;i<testdata.unittests.length;i++) {
		var curtest=testdata.unittests[i];
		testJs+='// '+curtest.title+'\n';
		testJs+='//\n';
		testJs+='// '+curtest.desc+'\n';
		testJs+='testsCalled.test'+curtest.testname+'=false;\n';
		testJs+='htmlCompiler.render(null,'+escapeJsString(curtest.mustaml)+','+(curtest.data?curtest.data:'{}')+',function(err,html){\n';
		testJs+='  a.ok(!err);\n';
		testJs+='  a.strictEqual('+escapeJsString(curtest.html)+',html);\n';
		testJs+='  testsCalled.test'+curtest.testname+'=true;\n';
		testJs+='});\n\n\n';
	}
	testJs+='process.addListener(\'exit\', function() {\n';
	testJs+='  var allPassed=true;\n';
	testJs+='  for(var testname in testsCalled) {\n';
	testJs+='    allPassed=allPassed&&testsCalled[testname];\n';
	testJs+='    if(!testsCalled[testname]) {\n';
	testJs+='      console.log(\' ✗ \'+testname+\' failed! \');\n';
	testJs+='    }\n';
	testJs+='  }\n';
	testJs+='  a.ok(allPassed);';
	testJs+='  console.log(\' ✓ Mustaml reference tests passed. \');\n';
	testJs+='});\n';
	require('sys').puts(testJs);
});

function escapeJsString(str) {
	return '\''+str.replace(new RegExp('\\\\','g'),'\\\\').replace(new RegExp('\n','g'),'\\n').replace(new RegExp('\'','g'),'\\\'')+'\'';
}
