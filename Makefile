BUILD_NAME=mustaml
BUILD_VERSION=$$(sed -n -e "4s/Version //p" README.md)
BUILD_FILELIST=demos README.md
PHP_PATH=/usr/bin

all: docs dist demos

clean:
	@echo "----------------------------------------"
	@echo
	@echo "Cleaning up build and dist..."
	@echo
	@echo "----------------------------------------"
	rm -Rvf target
	rm -vf ${BUILD_NAME}.phar
	@echo "----------------------------------------"
	mkdir -vp target/{dist,docs/{phpuml,phpdoc,reference}}

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

test: clean
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
	phpuml -f html -o target/docs/phpuml --no-deployment-view -n "${BUILD_NAME}-${BUILD_VERSION}" php
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
	

gen:
	@echo "----------------------------------------"
	@echo
	@echo "Building various..."
	@echo
	@echo "----------------------------------------"
	php build/gen-unittests.php build/doc-unittests.json > test-php/GeneratedTest.php
	sass build/doc.sass:target/docs/reference/doc.css
	php mustaml.php build/doc-unittests.json build/doc.mustaml > target/docs/reference/index.html


