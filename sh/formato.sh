#!/bin/bash
rm -f $2
echo $1 > $2
chmod 777 $2
sed -i -e 's/</\n</g' $2
sed -i '1d' $2