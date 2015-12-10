<?php 

# 'Expires' in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

# Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

# HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

# HTTP/1.0
header("Pragma: no-cache");

$lines = array(); 

function showScores($inf="high-scores.txt", $separator='$-$')
{
  $lines = file($inf) or die("<table id='high_scores'><tr><td>Could not open $inf</td></tr></table>\n");
  
  echo "<table id='high_scores'>\n<caption>High Scores:</caption>\n<tbody>\n";
  foreach($lines as $line_num => $line) {
    if($line=="") next($lines);
    elseif($line=="\n") next($lines);
    else {
      $line = rtrim($line);
      $line = ltrim($line);
      list($sc,$na) = explode($separator,$line);
      echo "<tr><th>$sc</th><td>$na</td></tr>\n";
    }
  }
  echo "</tbody>\n</table>\n";
}

function checkScore($score, $inf="high-scores.txt", $separator='$-$')
{
  $lines = file($inf) or die('0');
  
  foreach($lines as $line_num => $line) {
    if($line=="") next($lines);
    elseif($line=="\n") next($lines);
    else {
      $line = rtrim($line);
      $line = ltrim($line);    
      list($sc,$na) = explode($separator,$line);
      if($score > $sc) return 1;
    }
  }
  
  return 0;
}

function addScore($name, $score, $inf="high-scores.txt", $separator='$-$')
{

  $newScore = $score;
  
  $check = true;

  $scores = array();

  $lines = file($inf);
  
  foreach($lines as $line_num => $line) {
    if($line=="") next($lines);
    elseif($line=="\n") next($lines);
    else {
      $line = rtrim($line);
      $line = ltrim($line);    
      list($sc,$na) = explode($separator,$line);
      if($sc=='') next($lines);
      else {
        if($newScore>$sc && $check) {
          $scores[] = "$newScore$separator$name";
          $check = false;
        }
        $scores[] = "$sc$separator$na";
      }
    }
  }
  
  if(count($scores)>10) {
    $junk = array_pop($scores);
  }
  
  $fh = fopen($inf, 'w') or die("0");
  
  if(flock($fh, LOCK_EX)) { // do an exclusive lock
    foreach($scores as $item) {
      fwrite($fh,"$item\n");
    }
    flock($fh, LOCK_UN); // release the lock
  } else {
    die("2");
  }  

  fclose($fh);

}

if(isset($_POST['ts']) && isset($_COOKIE['token']) && $_COOKIE['token']==md5('j c tunnel'.$_POST['ts']) && isset($_POST['name']))
{
  $playerName = htmlspecialchars($_POST['name']);
  $playerScore = htmlspecialchars($_POST['highscore']);
  addScore($playerName, $playerScore);
  echo "1";
}
else if(isset($_POST['score']))
{
  $playerScore = htmlspecialchars($_POST['score']);
  $result = checkScore($playerScore);
  echo "$result";
}
else
{
  showScores();
}

?>