<?php
$start = microtime(true);

define('BOARD_SIZE', 8);
for ($row = 0; $row < BOARD_SIZE; $row++) {
    $layout[$row] = 1 << $row;
    //echo showBinary($layout[$row]);
}
function showBinary($val) {
    return str_pad(decbin($val), 8, '0', STR_PAD_LEFT) . '<br>';
}

// Find all permutations of an array
function pc_next_permutation($p, $size) {

    // Slide down the array looking for a value smaller than the previous one
    for ($i = $size - 1;@ $p[$i] >= $p[$i+1]; --$i) {}

    // If this doesn't occur, the array has been reversed,
    // and we've finished our permutations
    if ($i == -1) {
        return false;
    }

    // Slide down the array looking for a bigger number than before
    for ($j = $size; $p[$j] <= $p[$i]; --$j) {}

    // Swap them
    $tmp = $p[$i];
    $p[$i] = $p[$j];
    $p[$j] = $tmp;

    // Now reverse the elements in between by swapping the ends
    for (++$i, $j = $size; $i < $j; ++$i, --$j) {
        $tmp = $p[$i];
        $p[$i] = $p[$j];
        $p[$j] = $tmp;
    }
    return $p;
}

function checkDiagonals($layout) {
    $row = 0;
    while ($row < BOARD_SIZE) {
        // Initialize offset for row and column numbers
        $offset = 1;
        // Use the offset to check each row in turn against the current row
        while ($offset < BOARD_SIZE - $row) {
            // Check for diagonal attacks from left and right
            $ld = $layout[$row + $offset] << $offset;
            $rd = $layout[$row + $offset] >> $offset;
            // If the shifted value is the same as the row being checked,
            // the queen can be attacked diagonally, so return false
            if ($layout[$row] == $ld || $layout[$row] == $rd) {
                return false;
            }
            $offset++;
        }
        $row++;
    }
    // If no attacks have been detected, return true
    return true;
}

// Function to rotate a board 90 degrees
function rotateBoard($layout) {
    $row = 0;
    while ($row < BOARD_SIZE) {
        // Convert the number to binary, and get its length
        $offset = strlen(decbin($layout[$row])) - 1;
        // Add a queen to each column starting from the left
        $rotated[$offset] = 1 << (BOARD_SIZE - $row - 1);
        $row++;
    }
    // Sort the new array using the array keys in ascending order
    ksort($rotated);
    return $rotated;
}

function findRotations($layout, &$solutions) {
    $rotation = $layout;
    // Rotate the board through 90, 180 & 270 degrees
    for ($i = 0; $i < 3; $i++) {
        $rotation = rotateBoard($rotation);
        if (!in_array($rotation, $solutions)) {
            $solutions[] = $rotation;
        }
    }

    // Reflected
    $reflected = array_reverse($layout);
    if (!in_array($reflected, $solutions)) {
        $solutions[] = $reflected;
    }

    // Rotate the reflected version through 90, 180 & 270 degrees
    $rotation = $reflected;
    for ($i = 0; $i < 3; $i++) {
        $rotation = rotateBoard($rotation);
        if (!in_array($rotation, $solutions)) {
            $solutions[] = $rotation;
        }
    }
}

function renderBoard($layout) {
    echo '<table>';
    for ($row = 0; $row < BOARD_SIZE; $row++) {
        echo '<tr>';
        for ($col = 0; $col < BOARD_SIZE; $col++){
            if ($layout[$row] == 1 << $col) {
                echo '<td><img src="crown.png" width="28" height="26"></td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
        }
        echo '</tr>';
    }
    echo '</table>';
}
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Eight Queens</title>
        <link href="queens.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <h1>Solving the Eight Queens Problem</h1>
<?php
$size = BOARD_SIZE - 1;
$solutions = array();
$unique = 0;
do {
    if (checkDiagonals($layout)) {
        if (!in_array($layout, $solutions)) {
            $solutions[] = $layout;
            findRotations($layout, $solutions);
            renderBoard($layout);
            $unique++;
        }
    }
} while ($layout = pc_next_permutation($layout, $size));

echo "<p>Total solutions (including rotations and reflections): " . count
    ($solutions)
    . "<br>";
echo $unique . ' unique solutions found <br>';

$end = microtime(true);
echo 'Time taken: ' . ($end - $start) . ' seconds</p>';
?>
    </body>
</html>