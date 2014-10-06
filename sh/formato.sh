#!/bin/bash
rm -f $2
echo $1 > $2
chmod 777 $2
sed -i -e 's/</\n</g' $2
sed -i -e 's/<a/---a/g' $2
sed -i -e 's/<\/a>/|a|/g' $2
sed -i -e 's/<strong/---strong/g' $2
sed -i -e 's/<\/strong>/|strong|/g' $2
sed -i -e 's/<em/---em/g' $2
sed -i -e 's/<\/em>/|em|/g' $2
#Opcional
sed -i -e 's/<ul/---ul/g' $2
sed -i -e 's/<\/ul>/|ul|/g' $2
sed -i -e 's/<li/---li/g' $2
sed -i -e 's/<\/li>/|li|/g' $2
sed -i '1d' $2