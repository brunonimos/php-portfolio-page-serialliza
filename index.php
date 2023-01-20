<?php

//symlink(__DIR__."/public/home.html","home.html");
//exec('mklink /j "'.str_replace('/','\\',__DIR__.'/public/home.html').'" "'.str_replace('/','\\',__DIR__.'/home.html').'"');

require realpath('./vendor/autoload.php');

use Google\Cloud\Firestore\FirestoreClient;

if($_SERVER['SERVER_NAME']!=="localhost"){
if(!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on' || $_SERVER['HTTPS']==1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https')){
$redirect='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$redirect);
exit();
}
}

if(isset($_GET['id'])){
$id=$_GET['id'];
$searchengine=explode("Search",$id);
if($id=="orders" || $id=="billing" || $id=="banking" || $id=="profile" || $id=="messages" || $id=="myorders" || $id=="mybilling" || $id=="contact" || $id=="allusers" || $id=="system"){
include_once(__DIR__.'/admin.html');
}else if($searchengine[sizeof($searchengine)-1]=="Do"){
include_once(__DIR__.'/results.html');
}else{
include_once(__DIR__.'/home.html');
}
}else{
include_once(__DIR__.'/home.html');
}

try{
$db = new FirestoreClient(['projectId'=>'brunonimos-s-guitar','keyFile' => json_decode(file_get_contents('./resources/api/brunonimos-s-guitar-firebase-adminsdk-8psw1-cd8f85540d.json'), true)]);
$docRef = $db->collection('notifications')->document('1iJJKnbDXLsYN7m1wXmb');
/*
$docRef->set([
    'dispatchDate' => '30-04-2021',
    'dispatchTime' => '09:00',
    'notifyId' => 11000,
    'notifyMessage' => 'Test 2',
    'product' => 'S-Guitar Pro App.',
    'notifyAuthor' => 'brunonimos'
]);
printf('Added data to the lovelace document in the users collection.'.PHP_EOL);
*/
}catch (\Throwable $t) {
print_r("<br><br>Error in google<br><br>");
print_r($t);
}

$notifications = $db->collection('notifications');
$snapshot = $notifications->documents();
//print_r($snapshot);

foreach ($snapshot as $notification) {
    if (!empty($notification['notifyMessage'])) {
        printf('<br><br><br>Note: %s' . PHP_EOL, $notification['notifyMessage']);
    }
}

printf('Retrieved and printed out all documents from the users collection.' . PHP_EOL);

/*echo("<script>routerbridge('".htmlspecialchars($id)."');</script>");*/

?>