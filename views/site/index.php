<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

?>

<!-- <div class="site-index">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 text-center">
            <?php //$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
                <?php //echo $form->field($model, 'file')->label(false)->fileInput(['class' => 'btn btn-default mh-auto']); ?>
                <div class="form-group">
                    <?php //echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-default']); ?>
                </div>
            <?php //ActiveForm::end();?>
        </div>
    </div>
</div> -->

<div class="block">
    <?php $form = ActiveForm::begin( ['options' => [ 'enctype' => 'multipart/form-data' ] ] ); ?>
    <?php echo $form->field($model, 'file',[
        'template' => '<label class="upload" id="upload">{labelTitle}<div class="file" id="file"><h1>XLSX</h1></div><h1>Upload a file</h1>{input}</label>'
    ])->label(false)->fileInput(['hidden' => true])?>
    <?php echo Html::submitButton('Upload', ['class' => 'submit', 'id' => 'submit']) ?>
    <?php echo Html::button('Download', ['class' => 'download', 'id' => 'download']) ?>
    <?php ActiveForm::end(); ?>
</div>