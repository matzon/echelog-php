<?php
	require_once("channels.inc.php");
	
	// Check for shortcut
	// ====================================================
	$channel = array_keys($_GET);
	$channel = isset($channel[0]) ? $channel[0] : '';

	// some channels have weird names, check for these
	if(strstr($channel, 'jnode_org')) {
		$channel = 'jnode.org';
	}

	if($channel != '' && validate_channel($channel)) {
		header("Location: /logs/browse/$channel/" . time());
		return;
	}
	// ----------------------------------------------------
	
  function write_channels() {
  	global $channels;
    foreach($channels as $key => $value) {
    	if(empty($channels[$key]['archived'])) {
    		write_channel($key, $value, '');
    	}
    }

    foreach($channels as $key => $value) {
    	if(!empty($channels[$key]['archived'])) {
    		write_channel($key, $value, ' [ARCHIVED]');
    	}
    }
  }
  
  function write_channel($key, $value, $archived) {
  	global $channels;
    echo "<tr>";
 		echo "  <td colspan=\"3\" align=\"center\"><font size=\"4\"><b>" . strtoupper($key) . $archived ."</b></font></td>";
  	echo "</tr>";
  	echo "<tr>";
  	echo "  <td align=\"left\"><i>" . $channels[$key]['description'] . "</i></td>";
//  	echo "  <td width=\"50\" align=\"center\">&nbsp;<a href=\"stats/$key.html\">stats</a></td>";
  	echo "  <td width=\"50\" align=\"center\"><a href=\"logs/browse/$key/\">logs</a></td>";
  	if($channels[$key]['url']) {
  		echo "  <td width=\"50\" align=\"center\"><a href=\"" . $channels[$key]['url'] . "\" target=\"blank\">www</a></td>";
  	}
  	echo "</tr>";
    echo "<tr>";
  	echo "  <td colspan=\"4\"><hr></td>";
  	echo "</tr>";  	
	}
?>
<html>
	<head>
		<title>echelog</title>
		<style>
			body {
				margin: 	10px;
				padding: 	10px;
				background-color: #ffffff;
				font: 12px/16px Verdana, Arial, Helvetica;
				}
		</style>
		<link rel="shortcut icon" href="favicon.ico">
	</head>
<body>
  <!-- shutdown banner -->
  <div style="padding: 10px 15px 0px;border: 1px solid #e1e1e1;background-color: #f9f9f9;border-radius: 4px;">
    <h2 style="font-size: 1.5em;letter-spacing: 0.04em;margin: 0;font-weight: normal;color: #ff5252;">Echelog is shutting down</h2>
    <p>We had a good run, but all good things must come to an end.</p>
    <p>Orginally, Echelog was just a simple setup to be able to read messages on IRC, when not connected to my BNC, but quickly grew to include a lot of other channels than the original #lwjgl and #haiku-os. Since then a lot of other channels were added. Almost 100!</p>
    <p>However, I am no longer working on or taking an active part of <a href="https://lwjgl.org">https://lwjgl.org</a> nor <a href="https://haiku-os.org">https://haiku-os.org</a> and I am not actively using IRC anymore. Furthermore, the cost (financially, mentally and legally (GDPR)) of running the site, no longer makes sense for me.</p>
    <p>Therefore, I have decided to no longer renew the domain, and I will shutdown hosting once the domain expires (May 29, 2020).</p>
    <p>I plan to post some of the setup/code to github, once it's offline. Mostly for historical reasons. There is <strong>nothing</strong> worthwhile in the code itself, it's a true testament to <i>If it ain't broke, don't fix it</i>!</p>
    <p><i>I have renewed the domain and I am currently looking into handing over the project or archiving the content</i></p>
    <p>April 6th, 2020<br>Brian Matzon<br>brian@matzon.dk</p>
  </div>

  <p align="center">
    Please choose:
    <br/>
    <br/>
    <table border="0" align="center" width="600">
			<?php write_channels(); ?>
    </table>
  </p>
  <p align="center">If you wish to have your channel on Freenode logged or have any other inquiries, please mail me at: <a href="mailto:brian@matzon.dk">brian@matzon.dk</a>.</p>
  <p align="center"><i>Please note that this service is free, but does contain some very unobtrusive google ads. It is however possible to get the logs exported should you wish to show them integrated into your own site.</p>
  <p align="center">The name of the bot is echelog. If you wish write a line that should be ignored, prefix it with a dash (-).</p>
  <p align="center"><b>NOTICE</b> Due to a lot of hit'n'miss, you need to avg at least 10 people on a channel before requesting logging.</p>
</body>
</html>
