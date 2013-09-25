<?php

if (isset($_POST['s'])) {

  header('Location: http://'.$_SERVER['HTTP_HOST'].'/search/'.urlencode($_POST['s']));
}

?>
