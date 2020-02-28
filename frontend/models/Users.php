<?php

namespace app\models;

use Yii;

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
class Users extends \yii\mongodb\ActiveRecord
{
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'bio', 'avatar_url', 'email', 'password_hash', 'auth_token'], 'safe']
        ];
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
}
