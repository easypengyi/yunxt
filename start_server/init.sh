#!/usr/bin/env bash

PHP_URL=
basepath=$(cd `dirname $0`; pwd)

source ${basepath}/constants.sh

think=${basepath}/../think

${PHP_URL} ${think} clear
${PHP_URL} ${think} optimize:autoload
${PHP_URL} ${think} optimize:schema
${PHP_URL} ${think} optimize:route
${PHP_URL} ${think} optimize:config
${PHP_URL} ${think} optimize:config admin
${PHP_URL} ${think} optimize:config api
${PHP_URL} ${think} optimize:config callback
${PHP_URL} ${think} optimize:config upload
${PHP_URL} ${think} optimize:config home
${PHP_URL} ${think} optimize:config index
${PHP_URL} ${think} optimize:config member
${PHP_URL} ${think} optimize:config report
${PHP_URL} ${think} optimize:config tool
${PHP_URL} ${think} optimize:config mobile
