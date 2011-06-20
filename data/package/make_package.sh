#!/bin/bash
# ex) ./make.sh pear.example.com
cd ../../
rm package.xml
rm package.log
rm BEAR-*.tgz
cd data/package
echo 'make package.xml...'
php make.php make > /dev/null
cd ../../
echo 'packaging...'
pear package > package.log
echo 'pacakge.log was created.done.'

