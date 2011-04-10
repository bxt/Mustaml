BUILD_NAME=mustaml
BUILD_VERSION=$$(sed -n -e "4s/Version //p" README.md)
BUILD_FILELIST=demos README.md
PHP_PATH=/usr/bin

default: docs gen dist demos

clean:
	@echo "----------------------------------------"
	@echo
	@echo "Cleaning up build and dist..."
	@echo
	@echo "----------------------------------------"
	rm -Rvf target
	rm -vf ${BUILD_NAME}.phar
	@echo "----------------------------------------"
	mkdir -vp target/{dist,docs/{phpuml,phpdoc}}

phar: clean
	@echo "----------------------------------------"
	@echo
	@echo "Creating ${BUILD_NAME}.phar..."
	@echo
	@echo "----------------------------------------"
	find php -type f -iname "*.php" | xargs -n 1 ${PHP_PATH}/php -l
	@echo "----------------------------------------"
	find lib -type f -iname "*.php" | xargs ${PHP_PATH}/php -d phar.readonly=0 ${PHP_PATH}/phar pack -f "${BUILD_NAME}.phar"
	find php -type f -iname "*.php" | xargs ${PHP_PATH}/php -d phar.readonly=0 ${PHP_PATH}/phar pack -f "${BUILD_NAME}.phar" -s "${BUILD_NAME}.php" 
	chmod a+x "${BUILD_NAME}.phar"

test: clean test-php/GeneratedTest.php
	@echo "----------------------------------------"
	@echo
	@echo "Running tests..."
	@echo
	@echo "----------------------------------------"
	phpmd php text design
	@echo "----------------------------------------"
	- phpmd php text codesize
	@echo "----------------------------------------"
	phpunit --coverage-html target/docs/test-coverage/ test-php/

test-js: test-js-node/generated.test.js
	@echo "----------------------------------------"
	@echo
	@echo "Running JS tests..."
	@echo
	@echo "----------------------------------------"
	node test-js-node/generated.test.js

dist: phar test cleandemos
	@echo "----------------------------------------"
	@echo
	@echo "Packing into dist..."
	@echo
	@echo "----------------------------------------"
	find lib -type f ! -iname "*.php" | xargs zip -r "target/dist/${BUILD_NAME}-${BUILD_VERSION}.zip" ${BUILD_FILELIST} "${BUILD_NAME}.phar"

docs: clean
	@echo "----------------------------------------"
	@echo
	@echo "Gathering docs..."
	@echo
	@echo "----------------------------------------"
	phpuml -f htmlnew -o target/docs/phpuml --no-deployment-view -n "${BUILD_NAME}-${BUILD_VERSION}" php
	(echo "STATS FOR ${BUILD_NAME}-${BUILD_VERSION}" && echo "------" && phploc php) > target/docs/phploc.txt
	#phpdoc --defaultpackagename "${BUILD_NAME}" --defaultcategoryname "${BUILD_NAME}-${BUILD_VERSION}" -ric README.md -d php -t target/docs/phpdoc

cleandemos:
	@echo "----------------------------------------"
	@echo
	@echo "Cleaning up demo-out..."
	@echo
	@echo "----------------------------------------"
	rm -Rvf demos/out
	mkdir -vp demos/out

demos: phar cleandemos
	@echo "----------------------------------------"
	@echo
	@echo "Running some demos..."
	@echo
	@echo "----------------------------------------"
	php mustaml.phar demos/test.json demos/test.mustaml > demos/out/test.html

gen: test-php/GeneratedTest.php test-js-node/generated.test.js target/docs/ref.html target/docs/index.html target/docs/php.html
	@echo "----------------------------------------"
	@echo
	@echo "Building various..."
	@echo
	@echo "----------------------------------------"



test-php/GeneratedTest.php: build/gen-unittests.php build/ref-unittests.json
	php build/gen-unittests.php build/ref-unittests.json > test-php/GeneratedTest.php

test-js-node/generated.test.js: build/gen.unittests.node.js build/ref-unittests.json
	node build/gen.unittests.node.js build/ref-unittests.json > test-js-node/generated.test.js

target/docs/doc.css: build/doc.sass
	sass build/doc.sass:target/docs/doc.css

target/docs/ref.html: target/docs/doc.css build/ref-unittests.json build/ref.mustaml build/htmlintro.mustaml build/htmlintro.json
	php mustaml.php build/ref-unittests.json build/ref.mustaml > target/docs/ref.html

target/docs/index.html: target/docs/doc.css build/index.json build/index.mustaml build/index.json build/htmlintro.mustaml build/htmlintro.json
	php mustaml.php build/index.json build/index.mustaml > target/docs/index.html

target/docs/php.html: target/docs/doc.css build/php.json build/php.mustaml build/php.json build/htmlintro.mustaml build/htmlintro.json
	php mustaml.php build/php.json build/php.mustaml > target/docs/php.html

install: 
	ln -s $(PWD)/bin/mustaml ~/bin/mustaml
