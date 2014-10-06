#!/bin/bash
var1=`grep -n $1 $3 | awk -F ':' '{print $1}'`
var2=`grep -n $2 $3 | tail -1 | awk -F ':' '{print $1}'`
v1=$var1
v2=`expr $var2 - 1`
sed -n $v1','$v2'p' $3 > $4