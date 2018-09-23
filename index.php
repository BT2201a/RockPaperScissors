<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	index.php
	Author:		Mike Blackmore
	Background: Scissors, Paper, Stone Game.
	Created:	06/12/2010 - Initial script
	Modified:	11/01/2011 - Debug XHTML <tag> errors and cosmetic changes
				15/01/2011 - Failed testing on IE and Opera as do no support post method with multiple images on form
							 Replaced
				18/02/2011 - Added Google Analytics
	
-->

<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">

	<head>
		<title>Assigment 2 - Scissors-Paper-Stone</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Author" content="Mike Blackmore"/>

		<style type="text/css">
		
		body{font-family: Verdana, Arial, sans-serif; font-size: 100%; color: #990099; background-color: #eeeeee; width: 520px;}
			
		h1 { font-size: 150%; color: black; background-color: inherit; text-align:center} 
		h2 { font-size: 130%; color: #000066; background-color: inherit; text-align:center}
		
		p  { color:black; width:520px;}
		p.indent  {font-size: 80%; margin-left:20px; width:480px; background-color:#b0e0e6;text-align:left }
		span.i{margin-left:20px}
		span.ii{margin-left:40px}
		p.left {text-align: left; margin-left:20px;}
		p.centered{text-align:center;margin-top:0px;margin-bottom:0px;padding:0px;}
		
		</style>
		<!-- Google Analytics -->
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-20775469-2']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>		
	</head>
	
	<body>
		<!-- Display header information -->
		
		<h1>Mike Blackmore - Assignment 2</h1>
		<h2>Scissors-Paper-Stone</h2>
		
		<?php

		session_start();							// Using sessions to retain values between posts
		
		//echo date("l, F d, Y h:i" ,time());		//used during debugging to confirm server time
				
		function win($user, $action, $comp)  														// Player hand beats Computer this round
		{
			printf("<p class=\"centered\">You Win! - %s %s %s</p>\n", $user, $action, $comp);
			$_SESSION['PlayScore']++;																// Add 1 to Player score
			return;
		}
		
		function lose($comp, $action, $user)														// Computer hand beats Player this round
		{
			printf("<p class=\"centered\">You Lose! - %s %s %s</p>\n", $comp, $action, $user);
			$_SESSION['CompScore']++;																// Add 1 to Computer score
			return;
		}
		
		function draw($match)																		// As it is a draw, need to replay the round
		{
			printf("<p class=\"centered\">Redraw - Both picked %s</p>\n", $match);
			$_SESSION['Draw']=TRUE;
			$_SESSION['Roundn']--;																	// Add 1 to Round number
			return;
		}
		
		function allreset()
		{
			printf("<p class=\"indent\">Best Of: <strong>%d</strong><br />\n", $_SESSION['Target']);
			printf("Round: <strong>%d</strong><br />\n", $_SESSION['Roundn']);
			printf("Computer: <strong>%d</strong><br />\n",$_SESSION['CompScore']);					// Display Computer Score
			printf("%s: <strong>%d</strong></p>\n", $_SESSION['Player'],$_SESSION['PlayScore']);	// Display Players  Score
			
																									// Now reset counters so initial screen is re-displayed on button press
			$_SESSION['Target']=0;																	// Reset number of Rounds
			unset($_SESSION['Roundn']);																// unset the round number
			$_SESSION['Player']=0;																	// Reset player name
			$_SESSION['CompScore']=0;																// Reset Computer Score
			$_SESSION['PlayScore']=0;																// Reset Players Score
			$_SESSION['Draw']=FALSE;

			echo '<p class="centered"><img src="scissors.png" alt="scissors"/><img src="paper.png" alt="paper" /><img src="stone.png" alt="stone" /></p>';

			echo '	<form action="index.php" method="post" >';
			echo '		<p class="centered"><input name="replay" type="submit" value="Play Again" /></p>';
			echo '	</form>';
			
			return;		
		}
		
		//---Main-----------------------------------------------------------------------------------------------------------------------------------------
		
		$choice=array("Stone","Paper","Scissors");													// Array to store possible choices
		
		$_SESSION['Target']=(int)$_SESSION['Target'];												// Number of Rounds in Game - If not set, set to 0
		$_SESSION['Roundn']=(int)$_SESSION['Roundn'];												// Round Number - If not set, set to 0
				
		if ( $_POST['player'] )																		// If $_POST['player'] has been posted, must have been on start page.
			{
			$_SESSION['Player']=trim($_POST['player']);												// Store Players name passed from form (use trim to remove blank spaces at start or end of name
			if ( $_SESSION['Player'] == "" || $_SESSION['Player'] == " " || $_SESSION['Player'] == "Enter name" ) $_SESSION['Player']="You";
																									// If a blank name is posted, replace with "You"
			//$_SESSION['Roundn']++;																						
			$_SESSION['Target']=$_POST['rounds'];													// Desired number of rounds 
			}
		
		//-----------------------------------------------------------------------------------------------------------------------------------------------
		
		if ( !$_SESSION['Target'] )																	// If Target Rounds does not have a value, need to start a new game.
			{
			// Display Game Rules
			
			echo '<p class="indent"><strong>How to Play:</strong><br /><br />
			        <span class="i">1. Select number of rounds to play, enter name and click <strong>Play!</strong></span><br />
				    <span class="i">2. Click on one of the three objects displayed.</span><br />
				    <span class="i">3. The computer will choose an object at random.</span><br />
				    <span class="i">4. If the objects match, the round is a draw and is <strong>re-run</strong></span><br />
				    <span class="i">5. Winning hands:</span><br />
				    <span class="ii">- Stone blunts Scissors</span><br />
				    <span class="ii">- Scissors cut Paper</span><br />
				    <span class="ii">- Paper covers stone</span><br />
				    <span class="i">6. Game is won when one player wins over half the rounds.</span><br /><br /></p>
								
					<form method="post" action="index.php" >										
					<p class="indent"><strong>Number of Rounds:</strong><br /><br />
					<span class="ii">
					<input name="rounds" type="radio" value="3" checked="checked"/>3		<!--default-->
					<input name="rounds" type="radio" value="5" />5 
					<input name="rounds" type="radio" value="7" />7 
					<input name="rounds" type="radio" value="9" />9 
					<input name="rounds" type="radio" value="11" />11 
					<input name="rounds" type="radio" value="13" />13 
					<input name="rounds" type="radio" value="15" />15 
					<input name="rounds" type="radio" value="17" />17 
					<input name="rounds" type="radio" value="19" />19 
					<input name="rounds" type="radio" value="21" />21</span><br /><br />
					<span class="ii">
					Name: <input type="text" value="Enter name" name="player" /><input type="submit" value="Play!" /></span></p>
					</form>';

			// Display Running Scores
			
			$comp_score = file("computer.txt");
			$play_score = file("players.txt");
			
			echo '<p class="indent"><strong>Running Score:</strong><br /><br />
				<span class="i">Computer: '.$comp_score[0].'</span><br />
				<span class="i">Players : '.$play_score[0].'</span><br /><br /></p>';
			}
		else																				// Must be in-game as variable "$_SESSION['Target'] has a value
			{
			if 		( $_POST["Scissors_x"] ) $player="Scissors";							// As IE does not support using multiple images on a form
			else if ( $_POST["Paper_x"] ) $player="Paper";									// need to use unique names= for the images and check for
			else if ( $_POST["Stone_x"] ) $player="Stone";									// name of one of the x,y co-ordinates to confirm selection

//			echo '<p>'.var_dump($_POST).'</p>';												// Debug - used to find out why IE was not working
				
			if ( $player )																	// Player has chosen an object
				{
				$computer=$choice[rand(0,2)];												// Computer Selection using rand
				
				if ( $player == $computer ) draw($player);									// Both picked same object
				
				else if ( $player == "Stone" )
					{
					if ( $computer == "Paper" ) lose($computer, "covers",   $player);
					else 						 win($player,   "blunts", $computer);		// Computer must be Scissors
					}
				else if ( $player == "Paper" )
					{
					if ( $computer == "Scissors" ) lose($computer, "cut",   $player);
					else 							win($player, "covers", $computer);		// Computer must be Stone
					}
				else	// $player must be Scissors
					{
					if ( $computer == "Stone" ) lose($computer, "blunts", $player);
					else 						win($player,   "cut", $computer);
					}
				}
				
			//Fix in case first round is a draw
			if ( $_SESSION['Roundn'] == 0 && $_SESSION['Draw'] == FALSE )  printf("<p class=\"centered\">Click on an image to play</p>\n");
			
			// Check for a winner
			if ( $_SESSION['PlayScore'] >  ($_SESSION['Target'] / 2) )								
				{
				// Increment the overall number of games won by players
				$games = file("players.txt");
				$games[0]++;
				$f_ptr = fopen("players.txt" , "w");
				fputs($f_ptr , "$games[0]");
				fclose($f_ptr);

				printf("<p class=\"centered\"><strong>Game Over: Well Done %s - You beat me!</strong></p>\n",$_SESSION['Player']);				
				allreset();
				}
			else if ( $_SESSION['CompScore'] > ($_SESSION['Target'] / 2) )
				{
				// Increment the overall number of games won by the computer
				$games = file("computer.txt");
				$games[0]++;
				$f_ptr = fopen("computer.txt" , "w");
				fputs($f_ptr , "$games[0]");
				fclose($f_ptr);
				
				printf("<p class=\"centered\"><strong>Game Over: Ha! Ha! - You Lost %s!</strong></p>\n",$_SESSION['Player']);
				allreset();
				}
			else																								
				{
				// Next Round				
				$_SESSION['Roundn']++;																	// Add 1 to Round number
				
				printf("<p class=\"indent\">Best Of: <strong>%d</strong><br />\n",$_SESSION['Target']);		
				printf("Round: <strong>%d</strong><br />\n",$_SESSION['Roundn']);
				printf("Computer: <strong>%d</strong><br />\n",$_SESSION['CompScore']);
				printf("%s: <strong>%d</strong><br /></p>\n",$_SESSION['Player'], $_SESSION['PlayScore']);
				
				// Create Form for user to select the target number of rounds. 
				printf("<form method=\"post\" action=\"index.php\" >\n");										
				printf("<p class=\"centered\"><input type=\"image\" alt=\"Scissors\" src=\"scissors.png\" name=\"Scissors\" value=\"Scissors\" />\n");
				printf("<input type=\"image\" alt=\"Paper\" src=\"paper.png\" name=\"Paper\" value=\"Paper\" />\n");
				printf("<input type=\"image\" alt=\"Stone\" src=\"stone.png\" name=\"Stone\" value=\"Stone\" />\n");
				printf("</p></form>\n");				
				printf("<p class=\"centered\"><br />Click on an image to play</p>\n");
				}					
	
			//----------------------------------------------------------------------------------------------------------------------------------------		
			}
			?>
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>	
	</body>
</html>
