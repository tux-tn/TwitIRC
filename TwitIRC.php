<?php
/*

 TwitIRC V1.0 , a simple Twitter IRC bot 

*/
set_time_limit(0);
$channel = "#TwitIRC"; // Channel where the bot can connect , you can use "#channel1,#channel2,#channel3"
$pseudo  = "TwitIRC"; // Bot Nickname
$server  = "irc.freenode.net"; // IRC server 
$port    = "6667"; // The port of the IRC server
$account = "support"; // Twitter username
$minute  = 15; // Interval in minutes of checking for new tweets
$pattern = '$\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i'; // Awesome brain fuck :D
$socket  = fsockopen($server,$port);
$minute  = 15; // Interval in minutes of checking for new tweets
$socket  = fsockopen($server,$port);
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
function unshortener($url){
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$yy = curl_exec($ch);
curl_close($ch);
$w = explode("\n",$yy);
$real_url = substr($w[3],10);
return $real_url;
} // Function to get original URL from a shortened one
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
			$string = html_entity_decode($last_tweet[1][1], ENT_QUOTES, "UTF-8");
			preg_match_all($pattern, $string, $result, PREG_PATTERN_ORDER); // putting all shortened URLs in the tweet into an array
			$A = $result[0];
			$count = count($A);
			for ($i = 0; $i < $count; $i++) {
				$A[$i] = unshortener($A[$i]);
				}
			$R = str_replace($result[0],$A,$string);
			fputs($socket, "PRIVMSG $channel :[Twitter] $R \r\n");
			$last = $last_tweet[1][1];
			}
		$RSS= 0;
		}
	}
usleep(100000); // Sleep 0.1 second to economize proc usage ,you can customize this value
$RSS++;
}
?>
