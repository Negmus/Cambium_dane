<?php
$ip=($_GET["ip"]);
$login="admin";
$haslo="";
$url="";

$connection = ssh2_connect($ip, 22);
ssh2_auth_password($connection, $login, $haslo);
$stream = ssh2_exec($connection, 'show dashboard');
stream_set_blocking($stream, true);

$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$str=stream_get_contents($stream_out);
$tab = [];
foreach (explode("\n", $str) as $linia) {
  list($key, $value) = preg_split('/ +/', trim($linia), 2);
  $tab[$key] = $value;
}
$urzadzenie=$tab['cambiumCurrentSWInfo'];
$mac=$tab['cambiumLANMACAddress'];
$sn=$tab['cambiumEPMPMSN'];
$uptime=$tab['cambiumSystemUptime'];
$ether=$tab['cambiumLANSpeedStatus'];
$ssid=$tab['cambiumEffectiveSSID'];
$freq=$tab['cambiumSTAConnectedRFFrequency'];
$moc=$tab['cambiumSTAConductedTXPower'];
$tx=$tab['cambiumSTADLRSSI'];
$txccq=$tab['staTxQuality'];
echo("SN ".$sn."<br>");
echo("Mac ".$mac."<br>");
echo("Uptime ".$uptime."<br>");
echo("Ether ".$ether."<br>");
echo("Ssid ".$ssid."<br>");
echo("Freq ".$freq."<br>");
echo("Moc ".$moc."<br>");
echo("Tx ".$tx."<br>");
echo("TxCCQ ".$txccq."<br>");
cambium_urządzenie($mac);
function cambium_urządzenie($mac){
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => '$url'.$mac,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: '
    ),
  ));
$blad=curl_error($curl);
$response = curl_exec($curl);
if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
curl_close($curl);
$response=json_decode($response, true);
$response=$response[data][0];
$urzadzenie=$response[product];
$wersja=$response[software_version];
echo("Urządzenie: ".$urzadzenie." ".$wersja."<br>");
}
?>
