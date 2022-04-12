#!/bin/sh
VERSION=$1

# Update version number in readme.txt if one exists
[ -e 'readme.txt' ] && perl -i -pe 's/Stable tag:*.+/Stable tag: '${VERSION}'/' readme.txt

# Update version in main plugin file
perl -i -pe 's/Version:*.+/Version: '${VERSION}'/' charitable-campaign-currencies.php

# Update version in package.json
perl -i -pe 's/"version":*.+/"version": "'${VERSION}'",/' package.json

# Update version in includes/class-charitable-scaffolder.php
perl -i -pe "s/const VERSION \= '*.+';/const VERSION = '${VERSION}';/" includes/class-charitable-campaign-currencies.php
