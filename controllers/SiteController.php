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

        $this->layout = 'second';

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
        $invoice_save_path = Yii::getAlias('@invoices');

        $propis_file_path = Yii::getAlias('@uploads/PROPIS.xla');

        $input_file_path = Yii::getAlias('@uploads/import.xlsx');
        $template_file_path = Yii::getAlias('@uploads/invoice-template.xlsx');
        $total_list_file_path = Yii::getAlias('@uploads/total-registration-list-and-invoices.xlsx');
        
        $input_file_spreadsheet = IOFactory::load($input_file_path);
        $template_file_spreadsheet = IOFactory::load($template_file_path);
        $total_file_spreadsheet = IOFactory::load($total_list_file_path);


        $input_file_spreadsheet->setActiveSheetIndex(0);
        $input_file_worksheet = $input_file_spreadsheet->getActiveSheet();

        $template_file_spreadsheet->setActiveSheetIndex(0);
        $template_worksheet = $template_file_spreadsheet->getActiveSheet();

        $total_file_spreadsheet->setActiveSheetIndex(0);
        $total_worksheet = $total_file_spreadsheet->getActiveSheet();
        
        $highestRow = $input_file_worksheet->getHighestRow();

        $invoice_date = strtotime($input_file_worksheet->getCell('A4')->getFormattedValue());
        $invoice_semester_date = $input_file_worksheet->getCell('D5')->getFormattedValue();

        for($row = 6; $row <= $highestRow; $row++){
            $invoice_info = [
                'student_num' => $input_file_worksheet->getCell('A'.$row)->getFormattedValue(),
                'fullname' => $input_file_worksheet->getCell('B'.$row)->getValue(),
                'faculty' => $input_file_worksheet->getCell('C'.$row)->getValue(),
                'contract_num' => $input_file_worksheet->getCell('E'.$row)->getFormattedValue(),
                'contract_date' => strtotime($input_file_worksheet->getCell('G'.$row)->getFormattedValue()),
                'course' => $input_file_worksheet->getCell('H'.$row)->getFormattedValue(),
                'invoice_num' => $input_file_worksheet->getCell('I'.$row)->getFormattedValue(),
                'issue_date' => strtotime($input_file_worksheet->getCell('J'.$row)->getFormattedValue()),
                'money' => $input_file_worksheet->getCell('K'.$row)->getValue(),
            ];
            print_r($invoice_info);
            die();
        }

        /*$highestColumn = $worksheet->getHighestColumn();
        $highestRow = $worksheet->getHighestRow();

        $invoice_date = $worksheet->getCell('A4')->getFormattedValue();
        $invoice_semester_date = $worksheet->getCell('D5')->getFormattedValue();
        
        for($row = 6; $row <= $highestRow; $row++){
            $invoice = [
                'student_num' => $worksheet->getCell('A'.$row)->getFormattedValue(),
                'fullname' => $worksheet->getCell('B'.$row)->getValue(),
                'faculty' => $worksheet->getCell('C'.$row)->getValue(),
                'contract_num' => $worksheet->getCell('E'.$row)->getFormattedValue(),
                'contract_date' => strtotime($worksheet->getCell('G'.$row)->getFormattedValue()),
                'course' => $worksheet->getCell('H'.$row)->getValue(),
                'invoice_num' => $worksheet->getCell('I'.$row)->getFormattedValue(),
                'issue_date' => strtotime($worksheet->getCell('J'.$row)->getFormattedValue()),
                'money' => $worksheet->getCell('K'.$row)->getValue(),
            ];

            $invoice_template_worksheet = $spreadsheet->getSheetByName('template');

            $new_worksheet = clone $invoice_template_worksheet;
            $new_worksheet->setTitle($invoice['student_num']);
            
            $new_worksheet->setCellValue('E1', "=Reg.List!I{$row}");
            $new_worksheet->setCellValue('E3', "=Reg.List!E{$row}");
            $new_worksheet->setCellValue('G1', "=Reg.List!J{$row}");
            $new_worksheet->setCellValue('G3', "=Reg.List!G{$row}");
            $new_worksheet->setCellValue('B20', "=Reg.List!B{$row}");
            $new_worksheet->setCellValue('B22', "{$invoice_semester_date}");
            $new_worksheet->setCellValue('C21', "=Reg.List!C{$row}");
            $new_worksheet->setCellValue('F19', "=Reg.List!K{$row}");
            $new_worksheet->setCellValue('C29', "=prop(F24)");
            $new_worksheet->setCellValue('B51', "прошел(ла) обучение за {$invoice_semester_date}");

            $spreadsheet->addSheet($new_worksheet);
    
        }
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="myfile.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');


        // echo $highestColumn.$highestRow;

        // $data = $spreadsheet->getActiveSheet()->toArray(null,true,false,true);

        // print_r($data);

        // echo $spreadsheet->getActiveSheet()->getCell('E3')->getCalculatedValue() . PHP_EOL;
        // echo $spreadsheet->getActiveSheet()->getCell('F19')->getCalculatedValue();

        // $spreadsheet->setActiveSheetIndex(1);

        die();*/
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
