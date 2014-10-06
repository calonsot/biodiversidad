#!/bin/bash
#Encuentra la primer ocurrencia de la palabra indicada y la almacena
var1=`grep -n $1 $3 | awk -F ':' '{print $1}'`
#Encuentra la primer ocurrencia de la segunda palabra indicada y la almacena
var2=`grep -n $2 $3 | tail -1 | awk -F ':' '{print $1}'`
v1=$var1
v2=`expr $var2 - 1`
#Envia al archivo lo que se encuentra entre las 2 palabras indicadas
sed -n $v1','$v2'p' $3 > $4
