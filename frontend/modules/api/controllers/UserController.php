<?php
namespace app\modules\api\controllers;

use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\Users';
    public $serializer = [
        'class' => 'app\components\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function verbs()
    {
        return array_merge(parent::verbs(), [
            'update' => ['POST'],
        ]);
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        if ($this->action->id === 'update') {
//            $behaviors['authenticator'] = [
//                'class' => HttpBasicAuth::class,
//                'auth' => [$this, 'auth']
//            ];
//        }
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['create'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function actionCreate()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');
        $validatePassword = Yii::$app->request->post('validate_password');

        if (!$email || !$password || !$validatePassword) {
            throw new BadRequestHttpException('Parameters are invalid');
        }

        if ($password !== $validatePassword) {
            throw new BadRequestHttpException('Password doesn\'t match Verify password!');
        }

        $model = new Users();
        $model->setScenario(Users::SCENARIO_CREATE);

        $model->email = $email;
        $model->password_hash = base64_encode($password);
        $model->auth_token = Yii::$app->security->generateRandomString();

        $model->save();

        return $model;
    }

    public function actionLogin()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        if (!$email || !$password) {
            throw new BadRequestHttpException('Parameters are invalid');
        }

        $model = Users::findOne(['email' => $email]);
        if (!$model) {
            throw new BadRequestHttpException('User not found');
        }

        if ($model->password_hash !== base64_encode($password)) {
            throw new UnauthorizedHttpException('Wrong password');
        }

        $model->setScenario(Users::SCENARIO_LOGIN);
        return $model;
    }

    public function actionUpdate()
    {
        $headers = Yii::$app->request->getHeaders();
        $data = base64_decode(str_replace('Basic ', '', $headers->get('authorization')));

        $data = explode(':', $data);
        $email = $data[0];
        $token = $data[1];

        $title = Yii::$app->request->post('title');
        $bio = Yii::$app->request->post('bio');

        if (!$title && !$bio) {
            throw new BadRequestHttpException('Wrong parameters');
        }

        if (strlen($title) > 200) {
            throw new BadRequestHttpException('Title length should be less than 200');
        }

        $model = Users::findOne(['email' => $email, 'auth_token' => $token]);
        $model->avatar = UploadedFile::getInstanceByName('avatar');

        if (!$model->avatar) {
            throw new BadRequestHttpException('Avatar file not found.');
        }

        $model->title = $title;
        $model->bio = $bio;

        $fileName = $model->upload();
        if ($fileName !== false) {
            $model->avatar = null;

            $model->avatar_url = $model->getAvatarURL($fileName);
            $model->save();

            return $model;
        } else {
            throw new BadRequestHttpException('Can not upload the avatar file');
        }
    }

    public function auth($email, $token)
    {
        return Users::findOne(['email' => $email, 'auth_token' => $token]);
    }

    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Users::find(),
            'pagination' => ['pageSize' => Yii::$app->params['api']['pageSize']],
        ]);
    }
}