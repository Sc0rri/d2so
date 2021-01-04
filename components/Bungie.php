<?php
namespace app\components;

use yii\authclient\OAuth2;

class Bungie extends OAuth2
{
    public $authUrl = 'https://www.bungie.net/ru/OAuth/Authorize';

    public $tokenUrl = 'https://www.bungie.net/Platform/App/OAuth/token/';

    public $apiBaseUrl = 'https://www.bungie.net/Platform';

    protected function initUserAttributes()
    {
        $user_id = $this->accessToken->params['membership_id'];

        return [
            'id' => $user_id,
            'email' => $user_id,
            'login' => $user_id
        ];
    }
}