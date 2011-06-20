#!/bin/sh
#
#   bear.sh - shell wrapper for bear.php 
#
#   $Id: bear.sh 707 2009-07-06 18:31:29Z koriyama@users.sourceforge.jp $
#
BEAR_HOME="@PEAR-DIR@/BEAR"

if (test -z "$PHP_COMMAND");
then
    export PHP_COMMAND=php
fi

if (test -z "$PHP_CLASSPATH");
then
    PHP_CLASSPATH=BEAR_HOME/class
    export PHP_CLASSPATH
fi
$PHP_COMMAND -d html_errors=off -qC $BEAR_HOME/BEAR/bin/bear.php $*