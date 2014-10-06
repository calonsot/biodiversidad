#!/bin/bash
#Elimina el archivo del segundo parametro
rm -f $2
#Escribe cadena en archivo
echo $1 > $2
#Cambia permisos a archivo
chmod 777 $2
#Se agregan todos los cambios que tendra el archivo
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
