<?php

$url = 'https://javah2h.com/api/connect/';
$header = array(
'h2h-userid: H0893',
'h2h-key: 1a2d895ab765a69427acf4386569a7ee', // lihat hasil autogenerate di member area
'h2h-secret: e9550030e598c60f5f0a662143edb3098848d08550d01d52908dff6cf6f21d91', // lihat hasil autogenerate di member area
);

$data = array( 
'inquiry' => 'D', // konstan
'bank' => 'bca', // bank tersedia: bca, bni, mandiri, bri, muamalat
'nominal' => 100000, // jumlah request
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);

echo $result; // ini berupa data json
?>