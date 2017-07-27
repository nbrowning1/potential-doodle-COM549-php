<!DOCTYPE html>
<html>
<head>
 <title>Book-O-Rama Search Results</title>
</head>
<body>
 <h1>Book-O-Rama Search Results</h1>
 <?php
  include('../connect-db.php');
  
  $searchType = $_POST['searchtype'];
  $searchTerm = $_POST['searchterm'];
  
  if (empty($searchType) || empty($searchTerm)) {
    echo "Enter a search type and search term";
    exit();
  }
  
  $type;
  switch ($searchType) {
    case 'Author':
      $type = 'Author';
      break;
    case 'Title':
      $type = 'Title';
      break;
    case 'ISBN':
      $type = 'ISBN';
      break;
    default:
      echo 'Invalid search type';
      exit();
  }
  
  $query = "SELECT * FROM books WHERE $type = ?";
  
  $stmt = $db->prepare($query);
  $stmt->bind_param('s', $searchTerm);
  $stmt->execute();
  $stmt->store_result();
  
  echo "<p>Number of books found: $stmt->num_rows</p>";
  
  $stmt->bind_result($isbn, $author, $title, $price);
  while ($stmt->fetch()) {
    echo "$isbn, $author, $title, $price";
  }
  
  $stmt->free_result();
  $db->close();
 ?>
</body>
</html>