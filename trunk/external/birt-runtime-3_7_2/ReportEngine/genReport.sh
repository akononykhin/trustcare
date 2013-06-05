#!/bin/sh

BIRT_HOME=`dirname $0`

unset BIRTCLASSPATH
for i in `ls $BIRT_HOME/lib/*.jar`;do export BIRTCLASSPATH=$i:$BIRTCLASSPATH;done

JAVACMD='java';
$JAVACMD -cp "$BIRTCLASSPATH" -DBIRT_HOME="$BIRT_HOME" ${1+"$@"}

exit $?
