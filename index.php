<!DOCTYPE html>
<html lang="en-us">
<head>
<meta charset="utf-8">
<title>Term vs. Term</title>
<link rel="stylesheet" href="src/bootstrap-3.1.1-dist/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="src/bootstrap-3.1.1-dist/js/bootstrap.js"></script>
<script type="text/javascript">
$(function(){
	$('input:text').each(function(){
		var txtval = $(this).val();
		$(this).focus(function(){
			$(this).val('')
		});
		$(this).blur(function(){
			if($(this).val() == ""){
				$(this).val(txtval);
			}
		});
	});
});
</script>

<style>
.container { padding:20px; }

.title ul { list-style:none; padding-left:0px }

form { text-align:center; border:1px solid #ddd; border-bottom:none;border-top:none; padding:30px }
form .query { display:inline-block; margin:0 40px 30px 40px }
form .bar-container { height:300px; width:200px; position: relative;  }
form .bar { background:#ccc; width:200px; height:0; position: absolute; bottom: 0; }
form .info { margin:10px 0 10px 0; }
form input { width:200px; }

.timer { clear:both; padding-top:20px; text-align:center; margin:50px 0; color:#999 }
</style>
</head>
<body>
<div class="container">
<div class="row">

<?php

error_reporting(E_ALL); 				// display all php errors
ini_set('display_errors', 'on'); 		// php errors "on"
include_once('src/om_functions.php'); 	// useful functions
$start_time = time_tracker(NULL); 		// track response time

/**
 *	An example use of the Digital Public Library of America API
 *	More information: http://dp.la/info/developers/codex/api-basics/
 *	
 *	1. Get your API key
 *	   http://dp.la/info/developers/codex/policies/#get-a-key
 *	
 *	2. Setup a json viewer 
 *	   e.g. JSONView: https://chrome.google.com/webstore/detail/jsonview/chklaanhfefbnpoihckbnefhakgolnmc
 *	
 */


// include key
$key = 'YOUR_API_KEY';
include_once('src/config.php'); // replace with your key

// store results
$q1 = array('term'=>'','count'=>0,'score'=>0);
$q2 = array('term'=>'','count'=>0,'score'=>0);


if(isset($_GET['q1']) && strlen($_GET['q1']) > 0){
	$q1['term'] = $_GET['q1'];
	$q1['count'] = getCount($q1['term']);
} else {
	$q1['term'] = 'search term 1';
}
if(isset($_GET['q2']) && strlen($_GET['q2']) > 0){
	$q2['term'] = $_GET['q2'];
	$q2['count'] = getCount($q2['term']);
} else {
	$q2['term'] = 'search term 2';
}



function getCount($query){
	global $key;
	// build url and fetch with curl() function
	$url = "http://api.dp.la/v2/items?q=".str_replace(" ","+",$query)."&api_key=".$key;
	$json = curl($url);
	
	// decode json
	$data = json_decode($json,true);
	if (count($data) > 0 && isset($data['count'])){
		//print_r($data);
		return $data['count'];
	} else {
		return 0;	
	}
}
//print_r($q1);
//print_r($q2);

if(isset($_GET['q1']) && isset($_GET['q2'])){
	$compute = true;
	$high = $q1['count'] + $q2['count'];
	$q1['score'] = convertRange($q1['count'],0,$high,2,300,0);
	$q2['score'] = convertRange($q2['count'],0,$high,2,300,0);
}
$winnerbg =  'src/green.png';
$q1bg = $q2bg = 'src/grey.png';

if ($q2['score'] == $q1['score']) {
	$q1bg = $q2bg = $winnerbg;
} else if ($q2['score'] > $q1['score']) {
	$q2bg = $winnerbg;
} else {
	$q1bg = $winnerbg;
}

?>

<div class="col-md-9">

	<form action="index.php" method="get">
	
		<div class="query">
			<div class="bar-container">
			<div class="bar" style="height:<?php print $q1['score'] ?>px; background:url(<?php print $q1bg ?>)"></div></div>
			<?php if (isset($compute)){ ?>
			<div class="info"><a href="http://dp.la/search?utf8=%E2%9C%93&q=<?php print $q1['term'] ?>"><?php print $q1['count'] ?> results</a></div>
			<?php } ?>
			<input class="form-control" type="text" value="<?php print $q1['term'] ?>" name="q1"> 
		</div>
		
		vs.
		
		<div class="query">
			<div class="bar-container">
			<div class="bar" style="height:<?php print $q2['score'] ?>px; background:url(<?php print $q2bg ?>)"></div></div>
			<?php if (isset($compute)){ ?>
			<div class="info"><a href="http://dp.la/search?utf8=%E2%9C%93&q=<?php print $q2['term'] ?>"><?php print $q2['count'] ?> results</a></div>
			<?php } ?>
			<input class="form-control" type="text" value="<?php print $q2['term'] ?>" name="q2">
		</div>
		<br>
		<input type="submit" value="Compare" class="btn btn-primary">
		
		
		
		<?php if (isset($compute)) print "<div class='timer'>$high results returned in ". time_tracker($start_time) . " seconds</div>\n"; ?>
	</form>

</div>



<div class="col-md-3 title">

<h2>Term vs. Term</h2>

<p>Compare the number of search results for two phrases from the <a href="http://dp.la/">Digital Public Library of America</a>.</p>


<h4>Popular terms</h4>

<ul>
<li><a href="?q1=apples&q2=oranges">apples vs. oranges</a></li>
<li><a href="?q1=cats&q2=dogs">cats vs. dogs</a></li>
<li><a href="?q1=science&q2=religion">science vs. religion</a></li>
<li><a href="?q1=heaven&q2=hell">heaven vs. hell</a></li>
<li><a href="?q1=red&q2=green">red vs. green</a></li>
<li><a href="?q1=the+end+of&q2=the+world+as+we+know+it">the end of vs. the world as we know it</a></li>
</ul>


<h4>Code</h4>

<a href="https://github.com/omundy/term-vs-term">https://github.com/omundy/term-vs-term</a>

<p>By <a href="http://owenmundy.com">Owen Mundy</a></p>

</div>





</div>
</div>

<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=1615856; 
var sc_invisible=1; 
var sc_security="7680ed06"; 
</script>
<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script>
<noscript><div class="statcounter"><a title="site stats"
href="http://statcounter.com/free-web-stats/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/1615856/0/7680ed06/1/"
alt="site stats"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->

</body>
</html>