<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Term vs. Term <?php

if (isset($_GET['q1']) && isset($_GET['q2'])) {
    $compute = true;
    echo '- '.$_GET['q1'].' vs. '.$_GET['q2'];
}
?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

<link href="assets/css/styles.css" rel="stylesheet">

</head>
<body>



<?php

error_reporting(E_ALL); 				// display all php errors
ini_set('display_errors', 'on'); 		// php errors "on"
include_once('src/om_functions.php'); 	// useful functions
$start_time = time_tracker(null); 		// track response time

/**
 *	An example use of the Digital Public Library of America API
 *	More information: http://dp.la/info/developers/codex/api-basics/ *
 */


// include key
$key = 'YOUR_API_KEY';			// replace with your key
include_once('src/config.php'); // http://dp.la/info/developers/codex/policies/#get-a-key

// store results
$q1 = array('term'=>'','count'=>0,'score'=>0);
$q2 = array('term'=>'','count'=>0,'score'=>0);


if (isset($_GET['q1']) && strlen($_GET['q1']) > 0) {
	$q1['term'] = $_GET['q1'];
	$q1['count'] = getCount($q1['term']);
} else {
	//$q1['term'] = 'search term 1';
}
if (isset($_GET['q2']) && strlen($_GET['q2']) > 0) {
	$q2['term'] = $_GET['q2'];
	$q2['count'] = getCount($q2['term']);
} else {
	//$q2['term'] = 'search term 2';
}

function getCount($query)
{
	global $key;
	// build url and fetch with curl() function
	$url = "https://api.dp.la/v2/items?q=".str_replace(" ", "+", $query)."&api_key=".$key;
	$json = curl($url);
	// print_r($json);
	// decode json
	$data = json_decode($json, true);

	if (isset($data) && count($data) > 0 && isset($data['count'])) {
		return $data['count'];
	} else {
		return 0;
	}
}
//print_r($q1);
//print_r($q2);

if (isset($compute)) {
	$high = $q1['count'] + $q2['count'];
	$q1['score'] = convertRange($q1['count'], 0, $high, 2, 300, 0);
	$q2['score'] = convertRange($q2['count'], 0, $high, 2, 300, 0);
}
$winbg =  'green.png';
$q1bg = $q2bg = 'grey.png';

if ($q2['score'] == $q1['score']) {
	$q1bg = $q2bg = $winbg;
} elseif ($q2['score'] > $q1['score']) {
	$q2bg = $winbg;
} else {
	$q1bg = $winbg;
}

?>


<div class="container-fluid page pb-4">

	<div class="row">
		<div class="col-12 text-center mt-3 title">

			<h2><a href="./">Term vs. Term</a></h2>

			<small>Compare search results for phrases from the <a href="https://pro.dp.la/developers/api-codex">Digital Public Library of America API</a>.</small>

		</div>
	</div>


	<div class="row">
		<div class="col-12 col-md-10 offset-md-1 text-center results mt-5 mb-3">



				<div class="query">
					<div class="bar-container">
						<div class="bar" style="height:<?php print $q1['score'] ?>px; background:url('assets/img/<?php print $q1bg ?>');">
						</div>
					</div>
					<?php if (isset($compute)) { ?>
						<div class="my-2">
							<small><a href="http://dp.la/search?utf8=%E2%9C%93&q=<?php print $q1['term'] ?>">
								<?php print number_format($q1['count']); ?> results</a></small>
						</div>
					<?php } ?>

					<input class="form-control q1" type="text" value="<?php print $q1['term'] ?>" name="q1">
				</div>


				<div class="vs-container text-center">
					<?php if (!isset($compute)) {
					    print '<div class="questionmark text-center">?</div>';
					} ?>
					vs.
				</div>


				<div class="query">
					<div class="bar-container">
						<div class="bar" style="height:<?php print $q2['score'] ?>px; background:url('assets/img/<?php print $q2bg ?>');">
						</div>
					</div>
					<?php if (isset($compute)) { ?>
						<div class="my-2">
							<small><a href="http://dp.la/search?utf8=%E2%9C%93&q=<?php print $q2['term'] ?>">
								<?php print number_format($q2['count']); ?> results</a></small>
						</div>
					<?php } ?>

					<input class="form-control q2" type="text" value="<?php print $q2['term'] ?>" name="q2">
				</div>


				<div class="my-2 mt-4">
					<button type="button" class="btn btn-primary compare-btn">Compare</button>
				</div>


				<?php if (isset($compute)) { ?>
				    <div class='timer mt-2'>
						<small><?php print number_format($high); ?> results returned in <?php print time_tracker($start_time); ?> seconds</small>
					</div>
				<?php } ?>


		</div>
	</div>

</div>
<div class="container-fluid footer">

	<div class="row">
		<div class="col-12 col-md-10 offset-md-1 text-center">

			<h5 class="mt-5 mb-2">Examples</h5>

			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=apples&q2=oranges">apples vs. oranges</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=cats&q2=dogs">cats vs. dogs</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=science&q2=religion">science vs. religion</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=heaven&q2=hell">heaven vs. hell</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=red&q2=green">red vs. green</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=the+end+of&q2=the+world+as+we+know+it">the end of vs. the world as we know it</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=coffee&q2=tea">coffee vs. tea</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=cake&q2=death">cake vs. death</a>
			<a class="btn btn-sm btn-outline-secondary m-1" role="button" href="?q1=USSR&q2=Russia">USSR vs Russia</a>

		</div>
	</div>



	<div class="row">
		<div class="col-12 col-md-10 offset-md-1 text-center">

			<h5 class="mt-4 mb-2">Credits</h5>

			<small>
				<p>Created by <a href="http://owenmundy.com">Owen Mundy</a> for the Digital Humanities DPLA Hackathon @ Florida State University, 2014. </p>

				<p><a href="http://owenmundy.com/work/term-vs-term/">App</a> | <a href="https://github.com/omundy/term-vs-term">Github</a></p>
			</small>

		</div>
	</div>



</div>





<script src="assets/libs/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<script src="assets/js/main.js"></script>

<?php include("../../_site/partials/stats.php"); ?>

</body>
</html>
