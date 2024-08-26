<?php

function prizeCounter($s) {

  $score = 0;
  if (empty($s)) {
    return $score;
  }

  $prev = $s[0];
  $reward = [
    'R' => 500,
    'G' => 200,
    'B' => 300,
  ];
  $prevCount = 1;
  foreach($s as $sym) {
    if($prevCount === 3) {
      $score += $reward[$sym];
    } else {
      $score += 100;
    }

    if($sym === $prev) {
      $prevCount += 1; 
    } else {
      $prevCount = 1;
    }

    $prev = $sym;
  }
  return $score;
}
