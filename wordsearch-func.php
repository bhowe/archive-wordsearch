<?

/**
 ** wordsearch-func.php - make_word function that puts a word in a puzzle...it's all about string manipulation
 **         - show_word_bank function prints the wordbank
 **         - strtrimarraytoupper function that changes all of the strings in an array to upper case, and takes off leading/trailing spaces, and strips the slashes
 **
 ** Author: Dan Schaub
 ** Date: 9/25/02
 **
 **/

function make_word($word) {
    global $rows, $bad_words, $post_rows, $post_cols, $highlight;

    if ($word == '') {
        $bad_words++;
        return;
    }

    $type = ceil(rand(0, 2));           // generate a type: 1 for horizontal, 2 for vertical, 3 for diagonal (coming soon)

    $len = strlen($word);               // length of the string (easy reference)
    $xs = floor(rand(1, (integer)$post_cols)) - 1;  // x position of first letter (xs = x start)
    $ys = floor(rand(1, (integer)$post_rows)) - 1;  // y position of first letter (ys = y start)

    if ($rows[$ys][$xs] != '') {
        make_word($word);           // if the position is taken, start over
        return;
    }

    if (($xs + $len > $post_cols) || ($ys + $len > $post_rows)) {
        make_word($word);
        return;
    }

    // this whole thing checks to make sure the word will fit entirely in the puzzle...if it doesn't, it adds the word to $bad_words
    $willfit = 0;
    for ($i = 0; $i < count($rows); $i++) {         // loop through the rows
        for ($j = 0; $j < count($rows[$i]); $j++) { // loop through the columns
            $xfree = 0;             // initiate the amount of free x and y spots
            $yfree = 0;
            if ($rows[$i][$j] == '') {      // if the current position contains a blank
                for ($k = $j; $k < count($rows[$i]); $k++) {    // initiate the variable to the current xpos; go until the end of the row; increment
                    if ($rows[$i][$k] == '') $xfree++;  // if the letter is free, increment
                }

                for ($k = $i; $k < count($rows); $k++) {    // initiate the variable to the current ypos; go until the end of the column; increment
                    if ($rows[$k][$j] == '') $yfree++;  // if the spot it free, increment
                }
            }

            if ($xfree >= $len or $yfree >= $len) $willfit = 1; // if enough spaces, it'll fit
        }
    }

    if (!$willfit) {
        $bad_words++;
        return;
    }

    switch($type) { // perform different actions for different types
        case 1: // type 1 is horizontal
            // check to make sure all of the places are free
            for ($i = $xs; $i < $len; $i++) {
                if ($rows[$ys][$i] != '') {
                    make_word($word);
                    return;
                }
            }

            $cstrpos = 0;           // initiate string position
            $ccolpos = $xs;         // initiate column position
            while ($cstrpos < $len) {   // loop while the string is under its length
                if ($rows[$ys][$ccolpos] != '') {           // if the space is taken by another word
                    for ($i = $ccolpos - 1; $i >= $xs; $i--) {  // start at the letter before it, loop backwords until the first letter
                        $rows[$ys][$i] = '';            // make that spot blank again
                    }
                    make_word($word);               // try again
                    return;
                }
                $rows[$ys][$ccolpos] = ($highlight ? '<b><font color=red>' . substr($word, $cstrpos, 1) . '</font></b>' : substr($word, $cstrpos, 1));  // take one letter and stick it in the grid
                $ccolpos++; $cstrpos++; // increment both thingies
            }

            break;
        case 2: // type 2 is vertical - essentially the same operation, we're just working up and down
            // check to make sure all of the places are free
            for ($i = $ys; $i < $len; $i++) {
                if ($rows[$i][$xs] != '') {
                    make_word($word);
                    return;
                }
            }

            $cstrpos = 0;
            $crowpos = $ys;
            while ($cstrpos < $len) {
                if ($rows[$crowpos][$xs] != '') {
                    for ($i = $crowpos - 1; $i >= $ys; $i--) {
                        $rows[$i][$xs] = '';
                    }
                    make_word($word);
                    return;
                }
                $rows[$crowpos][$xs] = ($highlight ? '<b><font color=red>' . substr($word, $cstrpos, 1) . '</font></b>' : substr($word, $cstrpos, 1));  // take one letter and stick it in the grid
                $crowpos++; $cstrpos++;
            }

            break;
        default:
            make_word($word);
            return;
            break;
    }
} // end function make_word($word)

function show_word_bank($words, $perline = 5) {
    $wordbank = '<center><b>Word Bank</b></center>';
    $wordbank .= '<table align="center" border="0" cellspacing="0" cellpadding="10">';
    $wordstable = '<tr>';
    for ($i = 0; $i < count($words); $i++) {
        $wordstable .= "<td>$words[$i]</td>\n";
        if (($i + 1) % $perline == 0) {
            $wordstable .= "</tr>\n";
            $wordstable .= '<tr>';
        }
    }
    $wordbank .= $wordstable . '</table><br><br>';

    echo $wordbank;
}

function striptrimarraytoupper($array) {
    @reset($array);
    while (list($key, $val) = each($array)) {
        if (is_array($array[$key])) {
            $array[$key] = striptrimarraytoupper($array[$key]);
        } else {
            $array[$key] = strtoupper($val);
            $array[$key] = trim($array[$key]);
            $array[$key] = stripslashes($array[$key]);
        }
    }

    return $array;
} // end function arraytoupper($array)

?>