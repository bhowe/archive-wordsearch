<?

// set time limit to inifinity
set_time_limit(0);

// limit error reporting to everything by notices
error_reporting(E_ALL ^ E_NOTICE);

// extract URL get vars, post vars, and server vars into variables $get_varname, $post_varname, and $server_varname
@extract($_GET, EXTR_PREFIX_ALL, 'get');
@extract($_POST, EXTR_PREFIX_ALL, 'post');
@extract($_SERVER, EXTR_PREFIX_ALL, 'server');
?>

<html>
<head>
<title> Create-a-Puzzle </title>
<style type="text/css">
BODY, TABLE	{ font-family: verdana; font-size: 10pt }
TABLE.puzzle	{ font-family: courier new; font-size: 10pt }
</style>
</head>
<body>
<center><font size="+3"><b>Build a Word Search</b></font></center><hr>
<? if ($get_act == 'build') {	// build if they click submit
	// get functions
	require('wordsearch-func.php');

	// split up the input words
	$post_words = explode("\n", $post_words_text);

	// use the custom function to change all of the strings to upper case
	$post_words = striptrimarraytoupper($post_words);

	// highlight the words in the puzzle?
	$highlight = isset($post_highlight);

	// initiate puzzle HTML
	$puzzle = '<table class="puzzle" align="center" border="1" cellpadding="10"><tr><td>';

	// keeps track of the number of words that couldn't be put in the puzzle
	$bad_words = 0;

	// $letters contains letters that can be used to fill in the blank area
	$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	// $rows will contain ordered pairs pointing to characters, for example $rows[0][0] refers to the top left character
	$rows = array();

	// $output will contain the output text
	$output = '';

	// loop through, make as many rows as the user specified
	for ($i = 0; $i < $post_rows; $i++) {
		$rows[$i] = array();	// each row is an array of columns

		for ($j = 0; $j < $post_cols; $j++) {
			$rows[$i][$j] = '';	// this will eventually be assigned a letter
		}
	}

	// seed the random number generator
	srand((double)microtime() * 100000);

	// loops through the words provided by the user and generates the puzzle places
	for ($i = 0; $i < count($post_words); $i++) {
		make_word($post_words[$i]);
	}

	// fills in the empty spaces in the puzzle
	for ($i = 0; $i < count($rows); $i++) {
		for ($j = 0; $j < count($rows[$i]); $j++) {
			if ($rows[$i][$j] == '') {		// continue if the position is blank
				$letterpos = rand(0, 25);	// get a random letter
				$rows[$i][$j] = substr($letters, $letterpos, 1);
			}
		}
	}

	// finally, make the rows and columns that are to be outputted
	for ($i = 0; $i < count($rows); $i++) {
		for ($j = 0; $j < count($rows[$i]); $j++) {
			if ($j == (count($rows[$i]) - 1)) {
				$output .= $rows[$i][$j] . '<br>';
			} else {
				$output .= $rows[$i][$j] . ' ';
			}
		}
	}

	// close off HTML
	$puzzle .= ($output . '</td></tr></table>');
?>
<?=$puzzle?><br><br>
<?
	// generate a wordbank if they so choose
	if (isset($_POST['showwords'])) {
		show_word_bank($post_words, $post_wordsperline);
	}
?>
<center><b><?=$bad_words?></b> out of <b><?=count($post_words)?></b> word(s) didn't fit.  To make them fit, you can try making the puzzle bigger.</center>
<center><a href="<?=$server_PHP_SELF?>">Create another puzzle</a><hr></center>
</body>
</html>
<?

	exit;
} // end if ($get_act == 'build')

?>
Fill out this form to make our very own Word Search Puzzle:<br><br>
<form action="<?=$server_PHP_SELF?>?act=build" method="post">
How many rows should there be in the puzzle? <input type="text" name="rows" value="20"><br>
How many columns should there be in the puzzle? <input type="text" name="cols" value="20"><br>
Enter your words, <b>each one on a seperate line</b>:<br>
<textarea name="words_text" rows="10" cols="50"></textarea><br><br>
<input type="checkbox" name="showwords" checked> Display Word Bank - Number of words per line: <input type="text" name="wordsperline" size="1" maxlength="1" value="5"><br>
<input type="checkbox" name="highlight"> Hilight Words<br>
<input type="submit" value="Create Puzzle">
</form>
<hr>
</body>
</html>

<!-- Default size:

X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X
X X X X X X X X X X X X X X X X X X X X

-->