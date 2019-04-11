<?php

namespace App\Services\API;

//use Illuminate\Http\Request;
use Validator;
use App\Classes\LiveSwitch;

class LiveswitchService extends APIService
{
    public function __construct(/*User $model, SubscribeService $subscribe,*/ LiveSwitch $liveswitch)
    {
        //$this->model = $model;
        //$this->subscribe = $subscribe;
        $this->liveswitch = $liveswitch;
    }

    public function generateClientRegisterToken($request) {
      $response = ['status' => 'success', 'token' => ''];

      $fields = $this->getClientRegisterValidationFields();

      $validator = Validator::make($request->all(), $fields);

      if ($validator->fails()) {
          $response = ['status' => 'error', 'errors' => []];
          $messages = $validator->errors();

          foreach ($fields as $k => $v) {
              if ($messages->has($k)) {
                  $response['errors'][] = ['field' => $k, 'message' => str_replace('-', ' ', $messages->first($k))];
              }
          }

          return response()->json($response, 422);
      } else {
          $jsonObject = [
            'userId' => $request->json('userId', NULL),
            'deviceId' => $request->json('deviceId', NULL),
            'clientId' => $request->json('clientId', NULL),
            'roles' => $request->json('roles', NULL),
            'channels' => $request->json('channels', NULL)
          ];

          $token = $this->liveswitch->generateClientRegisterToken((object)$jsonObject);

          $response['token'] = $token;

          return response()->json($response);
      }
    }

    public function generateClientJoinToken($request) {
      $response = ['status' => 'success', 'token' => ''];

      $fields = $this->getClientJoinValidationFields();

      $validator = Validator::make($request->all(), $fields);

      if ($validator->fails()) {
          $response = ['status' => 'error', 'errors' => []];
          $messages = $validator->errors();

          foreach ($fields as $k => $v) {
              if ($messages->has($k)) {
                  $response['errors'][] = ['field' => $k, 'message' => str_replace('-', ' ', $messages->first($k))];
              }
          }

          return response()->json($response, 422);
      } else {
          $jsonObject = [
            'userId' => $request->json('userId', NULL),
            'deviceId' => $request->json('deviceId', NULL),
            'clientId' => $request->json('clientId', NULL),
            'roles' => $request->json('roles', NULL),
            'channel' => $request->json('channel', NULL)
          ];

          $token = $this->liveswitch->generateClientJoinToken((object)$jsonObject);

          $response['token'] = $token;

          return response()->json($response);
      }
    }

    /**
     * @return array
     */
    protected function getClientRegisterValidationFields()
    {
        return [
            'userId' => 'required',
            'deviceId' => 'required',
            'clientId' => 'required',
        ];
    }

    /**
     * @return array
     */
    protected function getClientJoinValidationFields()
    {
        return [
            'userId' => 'required',
            'deviceId' => 'required',
            'clientId' => 'required',
            'channel' => 'required',
        ];
    }
}
