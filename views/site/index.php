<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

?>

<div class="site-index">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 text-center">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
                <?php echo $form->field($model, 'file')->label(false)->fileInput(['class' => 'btn btn-default mh-auto']); ?>
                <div class="form-group">
                    <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-default']); ?>
                </div>
            <?php ActiveForm::end();?>
        </div>
    </div>
</div>
