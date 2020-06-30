<?php

use common\modules\partners\models\AdvertReservation;

if ($confirm == 3 and AdvertReservation::checkDateToFail($date) and !AdvertReservation::checkAlreadyFailed($id)) :

    ?>
    <div class="clearfix">
        <br/>
        <button class="btn btn-danger" style="width: 155px;" id="button-reservation-failure" data-id="<?= $id ?>">
            Незаезд
        </button>
    </div>

<?php
endif;
?>