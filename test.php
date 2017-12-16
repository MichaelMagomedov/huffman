<?php

use Utils\BinaryOpertions;

include "src/Utils/Entr.php";
include "src/Utils/Math.php";
include "src/Utils/BinaryOpertions.php";
include "src/Nodes/Node.php";
include "src/Nodes/HuffmanNode.php";
include "src/HuffmanCoder.php";

$huffCodes = new HuffmanCoder("/var/www/tik/huffman/test.txt");
$bytes = $huffCodes->encode(2);
echo $huffCodes->decodeFile("/var/www/tik/huffman/test.txt.huf");

