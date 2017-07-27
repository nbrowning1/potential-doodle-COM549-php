<html>
  <head></head>
  <body>
    <?php
      define("DS", DIRECTORY_SEPARATOR);
      define('ABSPATH', dirname(__FILE__) . DS);
    
      $content = $_POST['content'];
      $append = isset($_POST['append']) ? true : false;
      $filename = $_POST['filename'];
      
      if (isset($content) && !empty($content) && isset($filename) && !empty($filename)) {
        $writeMode = $append ? 'a' : 'w';
        writeToFile($filename, $writeMode, $content);
        $fileContent = readFromFile($filename);
        echo "<p>Successfully wrote to file. File contents are now: '$fileContent'";
        
      } else {
        echo '<p>Enter a filename and content to write to the file</p>';
        return;
      }
    
      function writeToFile($filename, $writeMode, $content) {
        $fileHandle = fopen(ABSPATH . $filename, $writeMode);
        if (!$fileHandle) {
          echo "Failed to write to file";
          return;
        }
        fwrite($fileHandle, $content);
        fclose($fileHandle);
      }
    
      function readFromFile($filename) {
        $fileHandle = fopen(ABSPATH . $filename, 'r');
        // second param is length of file
        $fileContent = fread($fileHandle, filesize($filename));
        fclose($fileHandle);
        
        return $fileContent;
      }
    ?>
  </body>
</html>