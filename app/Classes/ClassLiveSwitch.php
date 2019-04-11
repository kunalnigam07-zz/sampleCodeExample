<?php

namespace App\Classes;

use App\Classes\LiveSwitch;


class ClassLiveSwitch extends LiveSwitch {
  protected $class;

  public function __construct($class) {
    parent::__construct();
    $this->class = $class;
  }

  public function getApplicationId() {
    if (!empty($this->class->liveswitch_appid)) {
      return trim($this->class->liveswitch_appid);
    } else {
      return parent::getApplicationId();
    }
  }
}
