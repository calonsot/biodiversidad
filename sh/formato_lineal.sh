#!/bin/bash
rm -f $2
sed -n -e '1x;1!H;${x;s-\n- -gp}' $1 > $2
sed -i -e 's/<\//</g' $2
chmod 777 $2
