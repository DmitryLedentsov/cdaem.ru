#!/bin/sh

# set these paths to match your environment
YII_PATH=/home/cdaem.com/prod
APIDOC_PATH=/home/cdaem.com/prod/vendor/yiisoft/yii2-apidoc/
OUTPUT=/home/cdaem.com/prod/docs

cd $APIDOC_PATH
./apidoc api $YII_PATH $OUTPUT --interactive=0
