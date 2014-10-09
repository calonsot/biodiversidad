#!/bin/bash
chmod 777 $1
sed -i -e 's/a>/a><br>/g' $1
sed -i -e 's/m>/m><br>/g' $1
sed -i -e 's/g>/g><br>/g' $1
sed -i -e 's/l>/l><br>/g' $1
sed -i -e 's/i>/i><br>/g' $1
cat $1