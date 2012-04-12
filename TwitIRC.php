<?php
/*

 TwitIRC V1.0 , a simple Twitter IRC bot 

*/
set_time_limit(0);
$channel = "#TwitIRC"; // Channel where the bot can connect , you can use "#channel1,#channel2,#channel3"
$pseudo = "TwitIRC"; // Bot Nickname
$server = "irc.freenode.net"; // IRC server 
$port = "6667"; // The port of the IRC server
$account = "support"; // Twitter username
$minute = 15; // Interval in minutes of checking for new tweets
$socket = fsockopen($server,$port);
if(!$socket)
{
        echo "Can't connect to the server";
        exit;
}
fputs($socket, "USER $pseudo $pseudo $pseudo $pseudo\r\n");
fputs($socket, "NICK $pseudo\r\n");
fputs($socket, "JOIN $channel\r\n");
$RSS =0;
$last = "";

while(1)
{
$donnees =  fgets($socket, 1024);
if($donnees){
	$commande = explode(' ', $donnees);
	$message = explode(':', $donnees);
	if($commande[0] == 'PING'){
		fputs($socket, "PONG :" . $commande[1] . "\r\n");
		} // Playing a ping pong game with the server to stay connected
	if($RSS=$minute*600){
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, 'https://twitter.com/statuses/user_timeline/' . $account .'.rss');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HEADER, true);
		$output = curl_exec($c);
		if(preg_match_all("#<description>(.+)</description>#S", $output, $last_tweet) && $last != $last_tweet[1][1]){
			fputs($socket, "PRIVMSG $channel :New Tweet: @" . html_entity_decode($last_tweet[1][1], null, "UTF-8") . "\r\n");
			$last = $last_tweet[1][1];
			}
		$RSS= 0;
		}
	}
usleep(100000); // Sleep 0.1 second to economize proc usage ,you can customize this value
$RSS++;
}
?>
