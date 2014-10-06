#!/bin/bash
#Elimina el archivo indicado en el segundo parametro
rm -f $2
#Funcion para quitar saltos de linea
sed -n -e '1x;1!H;${x;s-\n- -gp}' $1 > $2
#Cambia los tags finales por apertura de tags
sed -i -e 's/<\//</g' $2
#Cambia permisos
chmod 777 $2
