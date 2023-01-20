<!DOCTYPE html>
<html>
<head>

<title>Processing request</title>

<meta charset="UTF-8"/>
<meta name="format-detection" content="telephone=no"/>
<meta name="msapplication-tap-highlight" content="no"/>
<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width"/>
<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://maxcdn.bootstrapcdn.com https://fonts.googleapis.com https://use.fontawesome.com; script-src 'self' 'unsafe-inline' https://maxcdn.bootstrapcdn.com https://cdnjs.cloudflare.com; child-src 'self'; media-src *; img-src * data:; object-src 'self'; font-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://use.fontawesome.com;form-action 'self';"/>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
<link rel="stylesheet" href="../css/frameworks/normalize.css"></link>
<link rel="stylesheet" href="../css/frameworks/animate.css"></link>
<link rel="stylesheet" href="../css/frameworks/app.css"></link>

<script src="../js/frameworks/jquery-3.3.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

<link rel="stylesheet" href="../css/style.css"></link>
<link rel="stylesheet" href="../css/stylexxxxxxlarge.css"></link>
<link rel="stylesheet" href="../css/stylexxxxxlarge.css"></link>
<link rel="stylesheet" href="../css/stylexxxxlarge.css"></link>
<link rel="stylesheet" href="../css/stylexxxlarge.css"></link>
<link rel="stylesheet" href="../css/stylexxlarge.css"></link>
<link rel="stylesheet" href="../css/stylexlarge.css"></link>
<link rel="stylesheet" href="../css/stylelarge.css"></link>
<link rel="stylesheet" href="../css/stylemedium.css"></link>
<link rel="stylesheet" href="../css/stylesmall.css"></link>

</head>
<body id="main" style="height:100% !important;background-image: linear-gradient(to bottom,rgba(250,250,250,0.57),rgba(255,255,255,1.0) 100%)">

<div class="container-fluid" style="height:0em;margin-bottom: -10px;background: rgba(255,255,255,0.3)">

<header id="collapsedtop">

<div id="rowholder" style="margin-top:-6.2em">
<div id="toposigned" class="centering row">
<div id="login_bot" style="margin-top: 10px;clear: left;float: right" class="on">
<div class="dropdown">
<button class="buttons" id="login" name="" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo htmlspecialchars(utf8_encode($resposta['ACTOR']));?></button>
<br>
<div class="dropdown-menu pull-right" aria-labelledby="login" style="min-width: 0px !important;text-align: center;background-image: linear-gradient(to bottom,rgba(145,0,0,0.7),#617c58 300%);">
</div>
</div>
<br>
<br>
</div>
<br>
<button type="button" id="back" style="display:block" onclick="window.location.replace('../');" class="backbutton"><i class="fas fa-angle-left"></i></button>
<br>
</div>
</div>

<div id="logo" style="margin-left:-0.5em">
<br>
<br>
<br>
<br>
<br>
<br>
<span id="backgroundContainer" style="display:none">
<div id="backgroundtop" style="background-image: url(../img/background.png);background-size: 100% 90%;background-repeat: no-repeat;">
</div>
<div>
<img class="logo" id="logotipo" src="../img/marca.png"/>
</div>
<span class="centering">
</span>
</span>
<br>
</div>

</header>

<div class="col-lg-12" id="precontent" style="min-height:100%;position:absolute;z-index:1;">
<div id="msg" class="off">
<div class="alert alert-info" role="alert">
<label id="msgtext"><?php echo htmlspecialchars(utf8_encode($resposta['RESPONSE']));?></label>
</div>
</div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<article class="contentContainerfix" style="min-height:25em" id="contentContainer">

<div class="modelo">
<br>
<br>

<div class="app centering" style="display:block;margin-top:15em">
<div id="pageload" class="blink centering">
<b class="event listening">Loading</b>
</div>
</div>

</div>

</article>

<footer style="bottom:0;position:relative;">

<div id="corpinfo" style="width: 100%;float:left;clear:right;display:none">
<div id="company" class="centering_2" style="text-align: center;margin-top:3.0em !important;margin-left: 1.0em !important;">
<sup><a href="./privacy" class="companyjobs" style="display:inline-block;text-decoration:none">Terms of use and privacy.</a></sup>
<br>
<sup>Developed by Bruno Henrique Ferreira de Oliveira, Copyright 2019.</sup>
<br>
<a href="https://github.com/brunonimos" class="companyicons" style="display:inline-block;text-decoration:none"><i class="companyicons fab fa-github"></i></a>
<!--
<a href="https://br.linkedin.com/in/brunonimos" class="companyicons" style="display:inline-block;text-decoration:none"><i class="companyicons fab fa-linkedin"></i></a>
<a href="https://www.instagram.com/brunonimos_works/" class="companyicons" style="display:inline-block;text-decoration:none"><i class="fab fa-instagram"></i></a>
<a href="https://www.youtube.com/channel/UCst81omPyq4uae3Sm2bJvlw" class="companyicons" style="display:inline-block;text-decoration:none"><i class="fab fa-youtube"></i></a>
<button onclick="alert('mail\@mail.com');" class="companyicons" style="display:inline-block;background: rgba(255,255,255,0.0);border:0em"><i class="companyicons fas fa-envelope"></i></button>
-->
</div>
<br>
<br>
</div>

<!--
<div class="centering">
<b style="text-align: center">Copyright 2019 Bruno Henrique Ferreira de Oliveira.</b>
<em style="font-size:0.5em !important;text-align: center">Copyright material available on this website is licensed under the MIT or GNU License. To view a copy of this licence, visit https://opensource.org/licenses</em><br><br>
</div>
-->

</footer>

<script src="../js/filter.js"></script>
<script src="../js/errorview.js"></script>

</body>

</html>