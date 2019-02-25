<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use moonland\phpexcel\Excel;

/**
 * ContactForm is the model behind the contact form.
 */
class UploadFile extends Model
{
    public $file;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx, csv'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => Yii::t('app', 'File'),
        ];
    }

    public function upload(){
        if($this->validate()){
            $path = Yii::getAlias('@uploads');
            $date = date('ymd-His');
            $file = 'import-' . $date . '.' . $this->file->extension;
            $this->file->saveAs($path . '/' . $file);

            return true;
        }else{
            return false;
        }
    }

}
