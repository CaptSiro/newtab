<?php

function bezier($parameter, $a = 3.0, $b = 2.0): float {
    return $parameter * $parameter * ($a - ($b * $parameter));
}
