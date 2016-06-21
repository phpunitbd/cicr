<?php

function prep_raise_memory_limit($request) {
  $res = ini_set('memory_limit', round($request) . 'M');
  if ($res === false) {
    prep_raise_memory_limit_try($request, $request * 0.6, 128);
    // If this warning shows, consider raising suhosin.memory_limit
    error_log('Warning: cannot set memory_limit, memory stays at ' . ini_get('memory_limit'));
  }
}

function prep_raise_memory_limit_try($overload, $test, $min) {
  $res = ini_set('memory_limit', round($test) . 'M');
  if ($res === false) {
    $diff = $test - $min;
    if ($diff < 3) return;
    prep_raise_memory_limit_try($test, $min + ($diff / 2), $min);
  }
  else {
    $diff = $overload -	$test;
    if ($diff < 3) return;
    prep_raise_memory_limit_try($overload, $test + ($diff / 2), $test);
  }
}

if (preg_match('/^80\.94\.14[67]\.[0-9]{1,3}$/', $_SERVER['REMOTE_ADDR'])
    || $_SERVER['REMOTE_ADDR'] == '88.190.14.150' /* aka icrc.data.fr */
    || $_SERVER['REMOTE_ADDR'] == '94.23.33.75' /* aka corrin.geekwu.org */) {
  prep_raise_memory_limit(2048);
}

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
    strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') == 0) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}
