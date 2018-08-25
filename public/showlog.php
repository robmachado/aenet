<?php

$tipo = filter_input(INPUT_GET, 't', FILTER_SANITIZE_SPECIAL_CHARS);

$path = __DIR__ . '/../storage/job_'.$tipo.'.log';

if (!is_file($path)) {
    header("location:./index.html");
}

$logs = file($path);
$c = '';
foreach($logs as $l) {
    $l = str_replace(["\n","\r", "[] []", "[ ]"], "", $l);
    if (!empty($l)) {
        $c .= "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span><span class=\"sr-only\">Error:</span>$l</div>";
    }
}


$template = "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <title>Aenet NFe Server</title>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\">
  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js\"></script>
  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\"></script>
</head>
<body>
<div class=\"jumbotron\">
  <center>
  <img src=\"Aenet_Logo_AR.png\" class=\"img-rounded\" alt=\"Aenet\">  
  <h1>Aenet Sistemas Ltda</h1>      
  <a href=\"http://aenet.com.br/\">Visite nossa homepage</a>
  </center>  
</div>

<div class=\"container\">
  {content}
</div>
</body>
</html>
";

$template = str_replace('{content}', $c, $template);

echo $template;