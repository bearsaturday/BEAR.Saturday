#!/bin/bash
# ex) ./make_package.sh __uri
cd ../../
rm package.xml
rm package.log
rm App-*.tgz
cd data/package
echo 'make package.xml...'
php make_package.php $1 channel > /dev/null
cd ../../
echo 'packaging...'
pear package > package.log
echo 'pacakge.log was created.'
