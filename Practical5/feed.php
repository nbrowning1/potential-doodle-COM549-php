<?php

require_once __DIR__ . '/../lib/vendor/autoload.php';
use GuzzleHttp\Client;

session_start();

// access token we gained in complete-oauth.php (hopefully)
if(!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
  header("Location: index.php");
  exit;
}

// URI to get media from the Instagram API - by default is the most recent media
$requestUri = "https://api.instagram.com/v1/users/self/media/recent";
// will build up media from the request
$recentPhotos = [];
// tag we are retrieving media for
$tag = '';

// allows to search by a specific tag, overwriting the most recent media from above
if (isset($_GET['tagQuery']) && !empty($_GET['tagQuery'])) {
  $tag = urlencode($_GET['tagQuery']);
  $requestUri = "https://api.instagram.com/v1/tags/$tag/media/recent";
}

// perform GET request to fetch the desired media using our access token and limiting the number of results to 50
$client = new Client();
$response = $client->get($requestUri, [
  'query' => [
    'access_token' => $_SESSION['access_token']['access_token'],
    'count' => 50
  ]
]);

/* response will be JSON, so we decode into a PHP array
    and break the array into chunks of 4 to aid with
    rendering the results in neat rows */
$results = json_decode($response->getBody(), true);
if (is_array($results)) {
  $recentPhotos = array_chunk($results['data'], 4);
}

?>

<html>
   <head>
      <title>PMWD - Chapter 30 - Instagram Demo</title>
      <link rel="stylesheet" href="bootstrap.min.css">
      <script src="jquery.min.js"></script>
      <script src="bootstrap.min.js"></script>
      <script>
         $(document).ready(function() {
           $('.like-button').on('click', function(e) {
             e.preventDefault();
             var media_id = $(e.target).data('media-id');
             $.get('like.php?media_id=' + media_id, function(data) {
               if(data.success) {
                 $(e.target).remove();
               }
             });
           });
         });
      </script>
   </head>
   <body>
      <div class="container">
         <h1>Instagram Recent Photos</h1>
         <div class="row">
            <div class="col-md-12">
               <form class="form-horizontal" method="GET"
                  action="feed.php">
                  <fieldset class="form-group">
                     <div class="col-xs-9 input-group">
                        <input type="text" class="form-control" id="tagQuery" name="tagQuery" placeholder="Search for a tag...."
                           value="<?=$tag?>"/>
                        <span class="input-group-btn">
                        <button type="submit" class="btn btnprimary"><i class="glyphicon glyphicon-search"></i> Search</button>
                        </span>
                     </div>
                  </fieldset>
               </form>
            </div>
         </div>
         <div class="row">
            <?php foreach($recentPhotos as $photoRow): ?>
            <div class="row">
               <?php foreach($photoRow as $photo): ?>
               <div class="col-md-3">
                  <div class="card">
                     <div class="card-block">
                        <h4 class="cardtitle"><?=substr($photo['caption']['text'],
                           0, 30)?></h4>
                        <h6 class="card-subtitle textmuted"><?=substr($photo['caption']['text'],
                           30, 30)?></h6>
                     </div>
                     <img class="card-img-top"
                        src="<?=$photo['images']['thumbnail']['url']?>"
                        alt="<?=$photo['caption']['text']?>">
                     <div class="card-block">
                        <?php foreach($photo['tags'] as $tag): ?>
                        <a href="feed.php?tagQuery=<?=$tag?>"
                           class="card-link">#<?=$tag?></a>
                        <?php endforeach?>
                     </div>
                     <div class="card-footer text-right">
                        <?php if(!$photo['user_has_liked']): ?>
                        <a data-media-id="<?=$photo['id']?>"
                           href="#" class="btn btn-xs btn-primary like-button"><i class="glyphicon
                           glyphicon-thumbs-up"></i> Like</a>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
               <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
         </div>
      </div>
   </body>
</html>