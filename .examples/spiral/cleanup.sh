rm -rf app
rm -rf tests
rm -rf vendor
rm -rf runtime
rm -rf public
rm -rf proto
rm -rf generated

rm .env
rm .env.sample
rm .rr.yaml
rm rr
rm protoc-gen-php-grpc
rm app.php
rm composer.lock
rm deptrac.yaml
rm phpunit.xml

git checkout psalm.xml
git checkout composer.json
git checkout README.md
git checkout LICENSE