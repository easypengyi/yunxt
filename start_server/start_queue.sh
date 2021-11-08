#!/usr/bin/env bash

PHP_URL=
basepath=$(cd `dirname $0`; pwd)

source ${basepath}/constants.sh

think=${basepath}/../think

${PHP_URL} ${think} queue:work --sleep 1
