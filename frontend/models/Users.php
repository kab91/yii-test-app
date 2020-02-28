<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for collection "users".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $title
 * @property mixed $bio
 * @property mixed $avatar_url
 * @property mixed $email
 * @property mixed $password_hash
 * @property mixed $auth_token
 */
class Users extends \yii\mongodb\ActiveRecord implements IdentityInterface
{
    /**
     * @var UploadedFile $avatar
     */
    public $avatar;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_LOGIN = 'login';

    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return ['test_task', 'users'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'title',
            'bio',
            'avatar_url',
            'email',
            'password_hash',
            'auth_token',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        if (!in_array($this->scenario, [self::SCENARIO_CREATE, self::SCENARIO_LOGIN])) {
            unset($fields['auth_token']);
        }
        unset($fields['email'], $fields['password_hash']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 200],
            [['title', 'bio', 'avatar_url', 'email', 'password_hash', 'auth_token'], 'safe'],
            ['auth_token', 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_LOGIN]],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg']
        ];
    }

    public function beforeSave($insert)
    {
        $attributes = array_diff($this->attributes(), ['_id']);
        foreach ($attributes as $attribute) {
            if (!isset($this->$attribute)) {
                $this->$attribute = '';
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'title' => 'Title',
            'bio' => 'Bio',
            'avatar_url' => 'Avatar Url',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_token' => 'Auth Token',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $fileName = $this->avatar->baseName . '_' . time() . '.' . $this->avatar->extension;
            $this->avatar->saveAs(Yii::$app->basePath . '/../public_html/images/' . $fileName);
            return $fileName;
        } else {
            return false;
        }
    }

    public function getAvatarUrl($fileName) {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/images/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
