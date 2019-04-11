<?php

namespace App\Classes;

use AuthHelper;
use OpenTok\Role;
use OpenTok\Session;
use App\Models\Setting;

class TokBox
{
    protected $opentok;
    protected $sessionId;

    public function __construct($api_choice = 1)
    {
        $settings = Setting::find(1);
        $this->my_id = AuthHelper::id();
    }

    public function createSession($record = 0)
    {
      $this->sessionId = date('U');
      return $this->sessionId;
    }

    public function generateToken($sid, $hours = 12)
    {
      return $this->sessionId;
    }

    public function startBroadcast($sid)
    {
      $object = new stdClass();
      $object->hlsUrl = 'Dummy URL';
      $object->id = 'SomeID';
      return $object;
    }

    public function stopBroadcast($sid)
    {

    }
}
