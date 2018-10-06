<?php
/*
copyright @ medantechno.com
Modified @ Farzain - zFz
2017

*/

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelAccessToken = 'ixnDeb4zwA+7BVBKIPgjp8GWJalb4GvzpNTp4DNu8GwtBzzjkpVzmHmACRzj5lJEXcoBzZwSICQDWJuRyfR58DTfUpMQDbumEXZ7o0xlMoesHVAo0FlB2hbK3/sGO2PPCrPM1KP+oS2D+WqLht0TpQdB04t89/1O/w1cDnyilFU='; //sesuaikan 
$channelSecret = '9c53f6b4937f59306ac976e7a2fa3d16';//sesuaikan

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

$userId 	= $client->parseEvents()[0]['source']['userId'];
$groupId 	= $client->parseEvents()[0]['source']['groupId'];
$replyToken = $client->parseEvents()[0]['replyToken'];
$timestamp	= $client->parseEvents()[0]['timestamp'];
$type 		= $client->parseEvents()[0]['type'];

$message 	= $client->parseEvents()[0]['message'];
$messageid 	= $client->parseEvents()[0]['message']['id'];

$profil = $client->profil($userId);

$pesan_datang = explode(" ", $message['text']);

$command = $pesan_datang[0];
$options = $pesan_datang[1];
if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

#-------------------------[Function]-------------------------#
function cuaca($keyword) {
    $uri = "http://api.openweathermap.org/data/2.5/weather?q=" . $keyword . ",ID&units=metric&appid=e172c2f3a3c620591582ab5242e0e6c4";

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
    $result = "This is the weather forecast for : ";
	$result .= $json['name'];
	$result .= " ";
	$result .= "\n\nWeather : ";
	$result .= $json['weather']['0']['main'];
	$result .= "\nDescription : ";
	$result .= $json['weather']['0']['description'];
    return $result;
}
#-------------------------[Function]-------------------------#

# require_once('./src/function/search-1.php');
# require_once('./src/function/download.php');
# require_once('./src/function/random.php');
# require_once('./src/function/search-2.php');
# require_once('./src/function/hard.php');

//show menu, saat join dan command /menu
if ($type == 'join' || $command == '/greet') {
    $text = "Hello there, why are you here again?";
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $text
            )
        )
    );
}

//others
if($message['type']=='text') {
	    if ($command == '/name') {
		    
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => 'You are '.$profil->displayName.'. If your name did not appear, please add the bot first.'
                )
            )
        );
    }



//pesan bergambar
if($message['type']=='text') {
	    if ($command == '/weather') {

        $result = cuaca($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    }

}else if($message['type']=='sticker')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'Hello '.$profil->displayName.', How are you?'										
									
									)
							)
						);
						
}else if($command == '/time')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'Time : '. date('Y-m-d H:i:s')									
									
									)
							)
						);
						
}else if($command == '/name')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'Hello '.$profil->displayName.'.'									
									
									)
							)
						);
						
}
if (isset($balas)) {
    $result = json_encode($balas);
//$result = ob_get_clean();

    file_put_contents('./balasan.json', $result);


    $client->replyMessage($balas);
}
?>
