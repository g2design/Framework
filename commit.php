<?php
$password = 'must$be@this$pass$word$';



$commands = array(
	'git status'
);

function run_commands($commands) {
	// Run the
	$output = '';
	foreach ($commands AS $command) {
		// Run it
//	$tmp = shell_exec($command);
		$handle = popen($command . ' 2>&1', 'r');
		$tmp = '';
		while ($str = fgets($handle)) {
			$tmp .= $str . '';
		}
		pclose($handle);

		// Output
		$output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
		$output .= htmlentities(trim($tmp)) . "\n";
	}

	return $output;
}

$output = run_commands($commands);

// Make it pretty for manual user access (and why not?)

if(!empty($_POST) && $_POST['password'] == $password) {
	$message = $_POST['message'];
	if(empty($message)){
		echo "Message must be filled in";
	} else {
		$message = addslashes($message);
		$commands = ['git commit -am "'.$message.'"'];

		$out = run_commands($commands);

		echo $out;
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>G2 Design Commit this change script</title>

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

		<style>
			form{
				background: #ccffcc;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row"><h2>Commit Local Changes</h2>
				<pre>
					<?php echo $output; ?>
				</pre>
			</div>
			<div class="row">
				<form action="" method="POST" class="col-md-4">
					<div class="row">
						<div class="col-md-12">
							<label for="">Message</label>
							<textarea name="message" id="" cols="30" rows="10"></textarea>
						</div>
						<div class="col-md-12">
							<label for="">Password</label>
							<input type="text" name="password">
						</div>
						<div class="col-md-12 text-right">
							<button type="submit">Commit Data</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
