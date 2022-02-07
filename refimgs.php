<?php
$home = "./";
$cmd = "python ./refimgs.py";
exec($cmd, $output);
var_dump($output);