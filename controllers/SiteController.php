<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use yii\web\UploadedFile;
use app\models\ContactForm;
use yii\filters\VerbFilter;
use moonland\phpexcel\Excel;
use yii\filters\AccessControl;
use yidas\phpSpreadsheet\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new \app\models\forms\UploadFile;

        if(Yii::$app->request->isPost){
            $model->file = UploadedFile::getInstance($model, 'file');
            if($model->upload()){
                Yii::$app->session->setFlash('success', Yii::t('app', 'File has been uploaded successfully'));
                $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('warning', Yii::t('app', 'Something happened while uploading the file'));
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionExcel(){

        $file = Yii::getAlias('@uploads/test-input-data.xlsx');

        $spreadsheet = IOFactory::load($file);

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        print_r($data);

        $invoice_period = $data[5][3];
        $period = $data[4]['A'];

        /*for($i = 5; $i <= count($data) - 1; $i++){
            $whitelist[] = [
                'serial_num' => $data[$i][0],
                'fullname' => $data[$i][1],
                'department' => $data[$i][2],
                'contract_num' => $data[$i][4],
                'date_from' => $data[$i][6],
                'course' => $data[$i][7],
                'invoice_num' => $data[$i][8],
                'given_date' => $data[$i][9],
                'amount' => (int)implode('', explode(',', $data[$i][10]))
            ];
        }
        echo $invoice_period;
        print_r($whitelist);*/

        die();
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
