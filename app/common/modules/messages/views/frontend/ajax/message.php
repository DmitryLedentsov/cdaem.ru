<?php
/*
    Написать сообщение
    @var \yii\web\View this
    @var common\modules\messages\models\Message $message
    @var int $interlocutorId

*/

use common\modules\partners\models\Service;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div id="modal-message" class="modal fade" data-title="Написать сообщение">
    <div class="office">
        <div class="load">
            <?php
            $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/messages/default/create', 'interlocutorId' => $interlocutorId],
                'enableClientScript' => false,
                'id' => 'form-message'
            ]);
            echo $form->field($message, 'text')->textarea();
            echo Html::tag('div', Html::submitButton('Отправить', ['class' => 'btn btn-primary']), ['class' => 'text-center']);
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>


<script>

    var $targetForm = null;

    /**
     * Отправка формы "Сообщение"
     */
    $('#form-message').formApi({

        // Все поля
        fields: [
            "_csrf",
            "Message[text]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "message-text"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {
            var $target = formApi.targetForm.parent();
            $targetForm = formApi.targetForm;

            if ($.isPlainObject(response) && 'status' in response) {
                $targetForm.hide();
                if (response.status == 1) {
                    $target.append('<div class="result"><div class="alert alert-info">' + response.message + '</div> <div class="text-center"><span class="link" id="again">Написать еще одно сообщение</span></div></div>');
                } else {
                    $target.append('<div class="result"><div class="alert alert-danger">' + response.message + '</div> <div class="text-center"><span class="link" id="again">Попробовать еще раз</span></div></div>');
                }
            }
        }
    });


    /**
     * Отправить еще одно сообщение
     */
    $(document).on('click', '#again', function () {
        $targetForm.show();
        $targetForm.data('formApi').reset();
        $targetForm.parent().find('.result').remove();
    });

</script>