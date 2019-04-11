<?php

namespace App\Classes;

use AuthHelper;
use OpenTok\Role;
use OpenTok\Session;
use App\Models\Setting;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parsing\Encoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class LiveSwitch
{
    protected $url;
    protected $key;
    protected $secret;
    public $xirsys;

    public function __construct($api_choice = 1)
    {
        $settings = Setting::find(1);
        $this->xirsys = new Xirsys();
        $this->coturn = new Coturn();

        $this->processSettings($settings);

        $this->my_id = AuthHelper::id();
    }

    public function getGatewayUrl() {
        return $this->url;
    }

    public function getApplicationId() {
        return $this->key;
    }

    public function getApplicationSecret() {
        return $this->secret;
    }

    public function getIceServers() {
      if ($this->xirsys->isEnabled()) {
          return $this->xirsys->getIceServers();
      } else if ($this->coturn->isEnabled()) {
          return $this->coturn->getIceServers();
      }

      return "[]";
    }

    public function generateClientRegisterToken($jsonObject) {
      $token = new Token();
      $token->setApplicationId($this->key);
      $token->setType(Token::TYPE_REGISTER);
      $token->setUserId($jsonObject->userId);
      $token->setDeviceId($jsonObject->deviceId);
      $token->setClientId($jsonObject->clientId);
      $token->setClientRoles($jsonObject->roles);
      $token->setClientChannels($jsonObject->channels);
      $signedToken = $token->sign($this->secret);

      return (string)$signedToken;
    }

    public function generateClientJoinToken($jsonObject) {
      $token = new Token();
      $token->setApplicationId($this->key);
      $token->setType(Token::TYPE_JOIN);
      $token->setUserId($jsonObject->userId);
      $token->setDeviceId($jsonObject->deviceId);
      $token->setClientId($jsonObject->clientId);
      $token->setClientRoles($jsonObject->roles);
      $token->setClientChannels((is_null($jsonObject->channel) ? null : array($jsonObject->channel)));
      $signedToken = $token->sign($this->secret);

      return (string)$signedToken;
    }

    public function createSession()
    {

    }

    public function generateToken($sid, $hours = 12)
    {

    }

    public function startBroadcast($sid)
    {

    }

    public function stopBroadcast($sid)
    {

    }

    /**
     * @param Setting $settings
     */
    protected function processSettings(Setting $settings)
    {
        $this->url = $settings->liveswitch_url;
        $this->key = $settings->liveswitch_key;
        $this->secret = $settings->liveswitch_secret;
    }
}

class Token {
    const DEFAULT_EXPIRY = 120;
    const TYPE_REGISTER = "register";
    const TYPE_JOIN = "join";

    protected $_token;

    public function __construct() {
        $this->signer = new Sha256();
        $this->_token = new Builder();
        $this->_token->setIssuedAt(time());
        $this->_token->setExpiration(time() + (self::DEFAULT_EXPIRY));

    }

    protected function setClaim($claimName, $value) {
        // $this->_token->with($claimName, $value);
        $this->_token->set($claimName, $value);
    }

    public function setApplicationId($applicationId) {
        if (!is_null($applicationId)) {
            $this->setClaim('applicationId', $applicationId);
        }
    }

    public function setClientId($clientId) {
        if (!is_null($clientId)) {
            $this->setClaim('clientId', $clientId);
        }
    }

    public function setClientRoles($clientRoles) {
        if (is_string($clientRoles)) {
          $clientRoles = [$clientRoles];
        }

        if (is_array($clientRoles) && count($clientRoles)) {

          $this->setClaim('clientRoles', $clientRoles);
        } else {
          //$this->setClaim('clientRoles', null);
        }
    }

    public function setClientChannels($clientChannels) {
        if (is_array($clientChannels) && count($clientChannels)) {
          $jsonChannels = array();

          foreach($clientChannels as $clientChannel) {
            if (!is_null($clientChannel)) {
              if (is_string($clientChannel)) {
                try {
                  $channel = json_decode($clientChannel);
                  
                  if (is_object($channel) && property_exists($channel, "id")) {
                    $channel = ChannelClaim::fromJSON((object)$channel);
                  } else {
                    throw new Exception('String is not a JSON object');
                  }
                } catch(Exception $e) {
                  $channel = new ChannelClaim($clientChannel);
                }
                //$channel = new ChannelClaim($clientChannel);
              } else {
                $channel = ChannelClaim::fromJSON((object)$clientChannel);
              }

              $jsonChannels[] = $channel->toJSON();
            }
          }

          if (count($jsonChannels)) {
            $this->setClaim('channels', $jsonChannels);
          } else {
            //$this->setClaim('channels', null);
          }
        }
    }

    public function setDeviceId($deviceId) {
        if (!is_null($deviceId)) {
            $this->setClaim('deviceId', $deviceId);
        }
    }

    public function setType($type) {
        if (!is_null($type)) {
            $this->setClaim('type', $type);
        }
    }

    public function setUserId($userId) {
        if (!is_null($userId)) {
            $this->setClaim('userId', $userId);
        }
    }

    public function sign($secret) {
        //$tt = $this->_token->getToken();
        //$encoder = new Encoder();
        //return $encoder->jsonEncode($tt->getClaims());

        $signer = new Sha256();
        return $this->_token->sign($signer, $secret)->getToken();
    }
}

function enableBooleanField($arr, $field) {
    return array_merge($arr, array($field => true));
}

class ChannelClaim {
  protected $id = null;
  protected $dmsg = false;
  protected $dpeer = false;
  protected $dmcu = false;
  protected $dsfu = false;
  protected $dsa = false;
  protected $dsv = false;
  protected $dsd = false;
  protected $arw = NULL;
  protected $drw = NULL;
  protected $vrw = NULL;

  public function __construct($channelId) {
    $this->setId($channelId);
  }

  public function toJSON() {
    $obj = array("id" => $this->id);

    if ($this->getDisableMcu()) {
      $obj = enableBooleanField($obj, "dmcu");
    }

    if ($this->getDisablePeer()) {
      $obj = enableBooleanField($obj, "dpeer");
    }

    if ($this->getDisableSfu()) {
      $obj = enableBooleanField($obj, "dsfu");
    }

    if ($this->getDisableSendAudio()) {
      $obj = enableBooleanField($obj, "dsa");
    }

    if ($this->getDisableSendData()) {
      $obj = enableBooleanField($obj, "dsd");
    }

    if ($this->getDisableSendVideo()) {
      $obj = enableBooleanField($obj, "dsv");
    }

    if ($this->getDisableSendMessage()) {
      $obj = enableBooleanField($obj, "dmsg");
    }

    if (!is_null($this->getAudioReceiveWhitelist())) {
      $obj = array_merge($obj, array("arw" => $this->arw));
    }

    if (!is_null($this->getDataReceiveWhitelist())) {
      $obj = array_merge($obj, array("arw" => $this->drw));
    }

    if (!is_null($this->getVideoReceiveWhitelist())) {
      $obj = array_merge($obj, array("arw" => $this->vrw));
    }

    return $obj;
  }

  public function getId() { return $this->id; }
  public function getAudioReceiveWhitelist() { return $this->arw; }
  public function getDataReceiveWhitelist() { return $this->drw; }
  public function getVideoReceiveWhitelist() { return $this->vrw; }
  public function getDisableMcu() { return $this->dmcu; }
  public function getDisablePeer() { return $this->dpeer; }
  public function getDisableSfu() { return $this->dsfu; }
  public function getDisableSendAudio() { return $this->dsa; }
  public function getDisableSendData() { return $this->dsd; }
  public function getDisableSendVideo() { return $this->dsv; }
  public function getDisableSendMessage() { return $this->dmsg; }

  public function setId($value) { $this->id = $value; }
  public function setAudioReceiveWhitelist($value) { $this->arw = $value; }
  public function setDataReceiveWhitelist($value) { $this->drw = $value; }
  public function setVideoReceiveWhitelist($value) { $this->vrw = $value; }
  public function setDisableMcu($value) { $this->dmcu = $value; }
  public function setDisablePeer($value) { $this->dpeer = $value; }
  public function setDisableSfu($value) { $this->dsfu = $value; }
  public function setDisableSendAudio($value) { $this->dsa = $value; }
  public function setDisableSendData($value) { $this->dsd = $value; }
  public function setDisableSendVideo($value) { $this->dsv = $value; }
  public function setDisableSendMessage($value) { $this->dmsg = $value; }

  public static function fromJSON($obj) {
    $claim = new ChannelClaim($obj->id);

    if (property_exists($obj, 'dmcu') && $obj->dmcu) { $claim->setDisableMcu(true); }
    if (property_exists($obj, 'dpeer') && $obj->dpeer) { $claim->setDisablePeer(true); }
    if (property_exists($obj, 'dsfu') && $obj->dsfu) { $claim->setDisableSfu(true); }
    if (property_exists($obj, 'dsa') && $obj->dsa) { $claim->setDisableSendAudio(true); }
    if (property_exists($obj, 'dsd') && $obj->dsd) { $claim->setDisableSendData(true); }
    if (property_exists($obj, 'dsv') && $obj->dsv) { $claim->setDisableSendVideo(true); }
    if (property_exists($obj, 'dmsg') && $obj->dmsg) { $claim->setDisableSendMessage(true); }
    if (property_exists($obj, 'arw') && $obj->arw) { $claim->setAudioReceiveWhitelist($obj->arw); }
    if (property_exists($obj, 'drw') && $obj->drw) { $claim->setDataReceiveWhitelist($obj->drw); }
    if (property_exists($obj, 'vrw') && $obj->vrw) { $claim->setVideoReceiveWhitelist($obj->vrw); }

    return $claim;
  }
}

class Coturn {
    protected $ident = 'fitswarm';
    protected $secret;
    protected $secure = true;
    protected $host = '';

    public function __construct($api_choice = 1)
    {
        $settings = Setting::find(1);
        
        $this->host = $settings->coturn_host;
        $coturn_name = trim($settings->coturn_name);
        if (!empty($coturn_name)) {
          $this->ident = $coturn_name;
        }
        $this->secret = $settings->coturn_secret;
        $this->my_id = AuthHelper::id();
    }

    public function isEnabled() {
        return ($this->host && $this->secret);
    }

    public function generateKeys() {
      $keys = array();
      $timestamp = time() + 24 * 3600;
      $username = $timestamp.':'.$this->ident;
      
      $key = base64_encode(hash_hmac('sha1', $username, $this->secret, true));
      $keys[$username] = $key;

      return $keys;
    }

    public function getIceServers() {
        if ($this->isEnabled()) {
          $servers = array();

          $stunServer = array();
          $stunServer['url'] = 'stun:'.$this->host.':80';

          $servers[] = $stunServer;

          $stunServer = array();
          $stunServer['url'] = 'stun:'.$this->host.':80?transport=tcp';

          $servers[] = $stunServer;

          $keys = $this->generateKeys();
          foreach($keys as $username => $password) {
              // TURN
              $server = array();
              $server['url'] = 'turn:'.$this->host.':80';
              $server['username'] = $username;
              $server['credential'] = $password;

              $servers[] = $server;

              // TURN TCP
              $server = array();
              $server['url'] = 'turn:'.$this->host.':80?transport=tcp';
              $server['username'] = $username;
              $server['credential'] = $password;

              $servers[] = $server;

              // TURNS
              $server = array();
              $server['url'] = 'turns:'.$this->host.':443';
              $server['username'] = $username;
              $server['credential'] = $password;

              $servers[] = $server;

              // TURNS TCP
              $server = array();
              $server['url'] = 'turns:'.$this->host.':443?transport=tcp';
              $server['username'] = $username;
              $server['credential'] = $password;

              $servers[] = $server;
          }
          return json_encode($servers);
        } else {
          return "[]";
        }
    }
}

class Xirsys {
    protected $ident;
    protected $secret;
    protected $channel;
    protected $secure = true;
    protected $host = 'global.xirsys.net';
    protected $path = '/_turn/';

    public function __construct($api_choice = 1)
    {
        $settings = Setting::find(1);
        
        $this->ident = $settings->liveswitch_xirsys_ident;
        $this->secret = $settings->liveswitch_xirsys_secret;
        $this->channel = $settings->liveswitch_xirsys_channel;
        $this->my_id = AuthHelper::id();
    }

    public function isEnabled() {
        return ($this->channel && $this->ident && $this->secret);
    }

    public function getIdent() {
        return $this->ident;
    }

    public function getSecret() {
        return $this->secret;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function getSecure() {
        return $this->secure;
    }

    public function getHost() {
        return $this->host;
    }

    public function getPath() {
        return $this->path;
    }

    public function getUrl() {
        return ($this->secure ? 'https' : 'http') . '://' . $this->getHost() . $this->getPath() . $this->getChannel();
    }

    public function getIceServers() {
        if ($this->isEnabled()) {
          $curl = curl_init();
          curl_setopt_array( $curl, array (
                CURLOPT_URL => $this->getUrl(),
                CURLOPT_USERPWD => $this->ident.":".$this->secret,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_RETURNTRANSFER => 1
          ));

          $resp = curl_exec($curl);

          if(curl_errno($curl)){
              //echo "Curl error: " . curl_error($curl);
              return "[]";
          };

          curl_close($curl);

          try {
              $resp = json_decode($resp);
          } catch(Exception $e) {
              return "[]";
          }

          if (is_object($resp) && property_exists($resp, 'v')) {
            if ($resp->v && $resp->v->iceServers) {
                if (is_array($resp->v->iceServers)) {
                    if (sizeof($resp->v->iceServers) > 0) {
                        return json_encode($resp->v->iceServers);
                    }
                }
            }
          }

          return "[]";
        } else {
          return "[]";
        }
    }
}