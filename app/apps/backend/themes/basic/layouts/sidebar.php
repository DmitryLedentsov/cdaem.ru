<?php
/**
 * Sidebar
 * @var yii\base\View $this Представление
 */

use yii\helpers\Url;
use yii\helpers\Html;

?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-content">

        <p><br/></p>

        <!-- Main navigation -->
        <ul class="navigation">
            <?php echo Yii::$app->navigation->render() ?>
        </ul>
        <!-- /main navigation -->

    </div>
</div>
<!-- /sidebar -->
