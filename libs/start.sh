cd $1
screen -dmS $3 java -Xmx1024M -Xms1024M -jar $2 nogui
