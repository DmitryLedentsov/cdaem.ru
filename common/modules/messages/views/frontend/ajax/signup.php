<?php
/*
    Регистрация
    @var \yii\web\View this
    @var common\modules\users\models\User $user
    @var common\modules\users\models\Profile $profile
    @var int $interlocutorId
*/
use common\modules\partners\models\Service;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div id="modal-message" class="modal fade" data-title="Написать сообщение">
    <div class="office">
        <div class="load">
            <div class="alert alert-info">
                <b>Внимание:</b>
                Оставлять сообщения могут только зарегистрированные пользователи, поэтому Вам необходимо
                <?= Html::a('войти', ['/users/guest/login'], ['target' => '_blank'])?> в свой аккаунт или <?= Html::a('зарегистрироваться', ['/users/guest/signup'], ['target' => '_blank'])?>.
            </div>

            <?php
            /*
            $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/messages/ajax/signup'],
                'enableClientScript' => false,
                'id' => 'form-signup'
            ]);
            echo $form->field($user, 'email', [
                'template' => '{label}<div style="width: 200px;">{input}</div>{error}'
            ])->textInput();
            echo $form->field($user, 'phone', [
                'template' => '{label}<div style="width: 200px;">{input}</div>{error}'
            ])->textInput();
            echo $form->field($user, 'password', [
                'template' => '{label}<div style="width: 200px;">{input}</div>{error}'
            ])->passwordInput();

            echo $form->field($profile, 'name', [
                'template' => '{label}<div style="width: 200px;">{input}</div>{error}'
            ])->textInput();
            echo $form->field($profile, 'surname', [
                'template' => '{label}<div style="width: 200px;">{input}</div>{error}'
            ])->textInput();

            echo Html::tag('div', Html::submitButton('Отправить', ['class' => 'btn btn-primary']), ['class' => 'text-center']);
            ActiveForm::end();*/
            ?>
        </div>
    </div>
</div>


<script>
    /**
     * Отправка формы "Сообщение"
     */
    /*$('#form-signup').formApi({

        // Все поля
        fields: [
            "_csrf",
            "User[email]",
            "User[phone]",
            "User[password]",
            "Profile[name]",
            "Profile[surname]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "user-email",
            "user-phone",
            "user-password",
            "profile-name",
            "profile-surname"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {
            var $target = formApi.targetForm.parent();
            var $targetForm = formApi.targetForm;

            if ($.isPlainObject(response) && 'status' in response) {
                $targetForm.hide();
                if (response.status == 1) {
                    $target.append('<div class="result"><div class="alert alert-info">'+response.message+'</div> <div class="text-center"><span class="link" id="again">Написать еще одно сообщение</span></div></div>');
                } else {
                    $target.append('<div class="result"><div class="alert alert-danger">'+response.message+'</div> <div class="text-center"><span class="link" id="again">Попробовать еще раз</span></div></div>');
                }
            }
        }
    });*/
</script>