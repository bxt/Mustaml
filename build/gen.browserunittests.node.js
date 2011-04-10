var fs=require('fs');



if(!process.argv[2]) throw "Specify json input file!";

fs.readFile(process.argv[2], function (err, filedata) {
	if (err) throw err;
	var testdata=JSON.parse(filedata);
	var testJs='';
	testJs+='<!DOCTYPE html>\n';
	testJs+='<html>\n';
	testJs+='<head>\n';
	testJs+='<title>Test '+testdata.pagetitle+'</title>\n';
	if(process.argv[3]&&process.argv[3]=='dist') {
		testJs+='<script src="../mustaml.min.js"></script>\n';
	} else {
		testJs+='<script>var mustaml={};</script>\n';
		testJs+='<script src="../js/ast.js"></script>\n';
		testJs+='<script src="../js/attrParser.js"></script>\n';
		testJs+='<script src="../js/htmlCompiler.js"></script>\n';
		testJs+='<script src="../js/htmlCompilerAttrs.js"></script>\n';
		testJs+='<script src="../js/scanner.js"></script>\n';
		testJs+='<script src="../js/parser.js"></script>\n';
	}
	testJs+='</head>\n';
	testJs+='<body>\n';
	testJs+='<h1>Test '+testdata.pagetitle+'</h1>\n';
	testJs+='<p>'+testdata.desc+'</p>\n';
	testJs+='<p>Result: <span id="result">Waiting for tests to finish...</span></p>\n';
	testJs+='<script>\n';
	
	testJs+='var htmlCompiler=mustaml.htmlCompiler();\n\n';
	testJs+='var a={};\n\n';
	testJs+='a.strictEqual=function (a,b) {if(!a===b) throw ["Assertion error","strictEqual",a,b];};\n\n';
	testJs+='a.ok=function (a) {if(!a) throw ["Assertion error","ok",a];};\n\n';
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
	testJs+='window.setTimeout(function() {\n';
	testJs+='  var allPassed=true;\n';
	testJs+='  for(var testname in testsCalled) {\n';
	testJs+='    allPassed=allPassed&&testsCalled[testname];\n';
	testJs+='    if(!testsCalled[testname]) {\n';
	testJs+='      document.getElementById("result").firstChild.nodeValue=(\' ✗ \'+testname+\' failed! \');\n';
	testJs+='    }\n';
	testJs+='  }\n';
	testJs+='  a.ok(allPassed);';
	testJs+='  document.getElementById("result").firstChild.nodeValue=(\' ✓ All Mustaml tests passed. \');\n';
	testJs+='},100);\n';
	
	testJs+='</script>\n';
	testJs+='</body>\n';
	testJs+='</html>\n';
	require('sys').puts(testJs);
});

function escapeJsString(str) {
	return '\''+str.replace(new RegExp('\\\\','g'),'\\\\').replace(new RegExp('\n','g'),'\\n').replace(new RegExp('\'','g'),'\\\'')+'\'';
}
