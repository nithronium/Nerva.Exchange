# Nerva.Exchange
php based cryptocurrency exchange script

In order to setup Nerva.Exchange's script, your server must have PHP and MySQL installed. You do not have to run any installation scripts for the front end. However, the backend requires daemons running on different screens.

Initially clone the source files to your desired machine. Then edit the files according to your login information. Almost all the pages contain places where you have to insert your database username and password. 

You must also insert the login information for the coin daemons. This script requires you running a pruned (or full) bitcoin core node, and Nerva node and Nerva RPC wallet. 

After doing all these edits on the source codes, you must fix the codes related to Google re-captcha. Once all the settings are done, the script must be working as intended. There are no explanations on the source code as it was designed only by me and I did not need explanations. However, you can fork the project and make changes or add explanations as you wish. Nerva.Exchange was not meant to be an open source project but now it is, so be careful while running it, in terms of security. 

Also you might want to change the location of /order path in order to secure your PHP scripts connecting with your coin daemons. You can set up another folder for your SRC files and make the PHP get the info from there, so web end users will not be able to reach them. 

At last, in order to process orders, you must assign a cron job for the file /order/hiddensrc_exchange/nervaprocessor.php and it must be running every 5-10-60 (or however seconds you want) to check the order status and update the database and process the orders. I used to have 10 seconds cron job that would be checking if the transactions got enough confirmations every 10 seconds.

For any other questions, you can DM me via Discord. Nithronium#8851
