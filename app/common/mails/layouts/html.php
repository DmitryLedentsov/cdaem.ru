<?php

use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$domain = Yii::$app->params['siteDomain'];

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <style type="text/css">

            body {
                margin: 0px;
                padding: 0px;
                background-color: #ffffff;
                color: #777777;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                width: 100% !important;
            }

            a, a:link, a:visited {
                color: #2c8fd6;
                text-decoration: underline;
            }

            a:hover, a:active {
                text-decoration: none;
                color: #125f96 !important;
            }

            h1, h2, h3, h1 a, h2 a, h3 a {
                color: #2c8fd6 !important;
            }

            h2 {
                padding: 0px 0px 10px 0px;
                margin: 0px 0px 10px 0px;
            }

            h2.name {
                padding: 0px 0px 7px 0px;
                margin: 0px 0px 7px 0px;
            }

            h3 {
                padding: 0px 0px 5px 0px;
                margin: 0px 0px 5px 0px;
            }

            p {
                margin: 0 0 14px 0;
                padding: 0;
            }

            img {
                border: 0;
                -ms-interpolation-mode: bicubic;
                max-width: 100%;
            }

            a img {
                border: none;
            }

            table td {
                border-collapse: collapse;
            }

            td.quote {
                font-family: Georgia, 'Times New Roman', Times, serif;
                font-size: 18px;
                line-height: 20pt;
                color: #2c8fd6;
            }

            span.phone a, span.noLink a {
                color: 2 c8fd6;
                text-decoration: none;
            }

            /* Hotmail */
            .ReadMsgBody {
                width: 100%;
            }

            .ExternalClass {
                width: 100%;
            }

            /* / Hotmail */

            /* Media queries */
            @media (max-width: 767px) {
                td[class=shareContainer], td[class=topContainer], td[class=container] {
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }

                table[class=row] {
                    width: 100% !important;
                    max-width: 600px !important;
                }

                img[class=wideImage], img[class=banner] {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100%;
                }
            }

            @media (max-width: 560px) {
                td[class=twoFromThree] {
                    display: block;
                    width: 100% !important;
                }

                td[class=inner2], td[class=authorInfo] {
                    padding-right: 30px !important;
                }

                td[class=socialIconsContainer] {
                    display: block;
                    width: 100% !important;
                    border-top: 0px !important;
                }

                td[class=socialIcons], td[class=socialIcons2] {
                    padding-top: 0px !important;
                    text-align: left !important;
                    padding-left: 30px !important;
                    padding-bottom: 20px !important;
                }
            }

            @media (max-width: 480px) {
                html, body {
                    margin-right: auto;
                    margin-left: auto;
                }

                td[class=oneFromTwo] {
                    display: block;
                    width: 100% !important;
                }

                td[class=inner] {
                    padding-left: 30px !important;
                    padding-right: 30px !important;
                }

                td[class=inner_image] {
                    padding-left: 30px !important;
                    padding-right: 30px !important;
                    padding-bottom: 25px !important;
                }

                img[class=wideImage] {
                    width: auto !important;
                    margin: 0 auto;
                }

                td[class=viewOnline] {
                    display: none !important;
                }

                td[class=date] {
                    font-size: 14px !important;
                    padding: 10px 30px !important;
                    background-color: #f4f4f4;
                    text-align: left !important;
                }

                td[class=title] {
                    font-size: 24px !important;
                    line-height: 32px !important;
                }

                table[class=quoteContainer] {
                    width: 100% !important;
                    float: none;
                }

                td[class=quote] {
                    padding-right: 0px !important;
                }

                td[class=spacer] {
                    padding-top: 18px !important;
                }
            }

            @media (max-width: 380px) {
                td[class=shareContainer] {
                    padding: 0px 10px !important;
                }

                td[class=topContainer] {
                    padding: 10px 10px 0px 10px !important;
                    background-color: #e9e9e9 !important;
                }

                td[class=container] {
                    padding: 0px 10px 10px 10px !important;
                }

                table[class=row] {
                    min-width: 240px !important;
                }

                img[class=wideImage] {
                    width: 100% !important;
                    max-width: 255px;
                }

                td[class=authorInfo], td[class=socialIcons2] {
                    text-align: center !important;
                }

                td[class=spacer2] {
                    display: none !important;
                }

                td[class=spacer3] {
                    padding-top: 23px !important;
                }

                table[class=iconContainer], table[class=iconContainer_right] {
                    width: 100% !important;
                    float: none !important;
                }

                table[class=authorPicture] {
                    float: none !important;
                    margin: 0px auto !important;
                    width: 80px !important;
                }

                td[class=icon] {
                    padding: 5px 0px 25px 0px !important;
                    text-align: center !important;
                }

                td[class=icon] img {
                    display: inline !important;
                }

                img[class=buttonRight] {
                    float: none !important;
                }

                img[class=bigButton] {
                    width: 100% !important;
                    max-width: 224px;
                    height: auto !important;
                }

                h2[class=website] {
                    font-size: 22px !important;
                }
            }

            /* / Media queries */

        </style>

        <!-- Internet Explorer fix -->
        <!--[if IE]>
        <style type="text/css">
            @media (max-width: 560px) {
                td[class=twoFromThree], td[class=socialIconsContainer] {float:left; padding:0px;}
            }
            @media only screen and (max-width: 480px) {
                td[class=oneFromTwo] {float:left; padding:0px;}
            }
            @media (max-width: 380px) {
                span[class=phone] {display:block !important;}
            }
        </style>
        <![endif]-->
        <!-- / Internet Explorer fix -->

        <!-- Windows Mobile 7 -->
        <!--[if IEMobile 7]>
        <style type="text/css">
            td[class=shareContainer], td[class=topContainer], td[class=container] {padding-left:10px !important; padding-right:10px !important;}
            table[class=row] {width:100% !important; max-width:600px !important;}
            td[class=oneFromTwo], td[class=twoFromThree] {float:left; padding:0px; display:block; width:100% !important;}
            td[class=socialIconsContainer] {float:left; padding:0px; display:block; width:100% !important; border-top:0px !important;}
            td[class=socialIcons], td[class=socialIcons2] {padding-top:0px !important; text-align:left !important; padding-left:30px !important; padding-bottom:20px !important;}
            td[class=inner], td[class=inner2], td[class=authorInfo] {padding-left:30px !important; padding-right:30px !important;}
            td[class=inner_image] {padding-left:30px !important; padding-right:30px !important; padding-bottom:25px !important;}
            td[class=viewOnline] {display:none !important;}
            td[class=date] {font-size:14px !important; padding:10px 30px !important; background-color:#f4f4f4; text-align:left !important;}
            td[class=title] {font-size:24px !important; line-height:32px !important;}
            table[class=quoteContainer] {width:100% !important; float:none;}
            td[class=quote] {padding-right:0px !important;}
            td[class=spacer] {padding-top:18px !important;}
            span[class=phone] {display:block !important;}
            img[class=banner] {width:100% !important; height:auto !important; max-width:100%;}
            img[class=wideImage] {width:auto !important; margin:0 auto;}
        </style>
        <![endif]-->
        <!-- / Windows Mobile 7 -->

        <!-- Outlook -->
        <!--[if gte mso 15]>
        <style type="text/css">
            .iconContainer, .quoteContainer {
                mso-table-rspace: 0px;
                border-right: 1px solid #ffffff;
            }

            .iconContainer_right {
                mso-table-rspace: 0px;
                border-right: 1px solid #ffffff;
                padding-right: 1px;
            }

            .authorPicture {
                mso-table-rspace: 0px;
                border-right: 1px solid #f4f4f4;
            }
        </style>
        <![endif]-->
        <!-- / Outlook -->

    </head>

    <body mc:edit>
    <?php $this->beginBody() ?>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">

        <tr>
            <td class="container"
                style="padding-left:5px; padding-right:5px; padding-bottom:20px; background-color:#e9e9e9;">

                <table class="row" width="800" height="150" bgcolor="#ffffff" align="center" cellpadding="0"
                       cellspacing="0" border="0"
                       style="border-bottom: solid 7px #8DD9E2; background-image: url('<?= $domain ?>/email-images/header.png'); background-repeat: no-repeat; border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
                    <tr>
                        <td style="padding: 15px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; text-align:center; color:#2c8fd6;"
                            valign="top">
                            <div style="font-size:12px; line-height:100%;">
                                <a href="<?= $domain ?>"><img class="banner" alt="header"
                                                              src="<?= $domain ?>/basic-images/logo.png" vspace="0"
                                                              hspace="0" border="0" style="display:block;"/></a>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="row" width="800" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0"
                       border="0" style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
                    <tr>
                        <td style="padding-top: 30px; padding-left:30px; padding-right:30px; padding-bottom:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
                            <?= $content ?>
                        </td>
                    </tr>
                </table>

                <table class="row" width="800" bgcolor="#f4f4f4" align="center" cellpadding="0" cellspacing="0"
                       border="0" style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
                    <tr>
                        <td colspan="2"
                            style="padding-top:25px; padding-left:30px; padding-right:30px; padding-bottom:25px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777; border-top:1px #dddddd dotted;">
                            <?= date('Y') ?> <img alt="©" src="<?= $domain ?>/email-images/copyright.png" border="0"
                                                  height="12" width="11" style="vertical-align:-1px;"/> <a
                                    style="text-decoration:none; color:#2c8fd6;"
                                    href="<?= $domain ?>"><?= $domain ?></a>. Все права защищены.
                            <br/> Это письмо было сгенерировано автоматически, пожалуйста не отвечайте на него.
                        </td>
                        <td class="socialIconsContainer" width="35%" valign="bottom"
                            style="border-top:1px #dddddd dotted;">
                            <?php /*
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; border-spacing:0; min-width:210px;">
                            <tr>
                                <td class="socialIcons2" style="padding-top:25px; padding-left:15px; padding-right:30px; padding-bottom:25px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777; text-align:right;">
                                    <a href="#"><img alt="Facebook" src="<?=$domain?>/email-images/facebookIcon.png" border="0" vspace="0" hspace="0" /></a>&nbsp;&nbsp;
                                    <a href="#"><img alt="Google Plus" src="<?=$domain?>/email-images/googlePlusIcon.png" border="0" vspace="0" hspace="0" /></a>&nbsp;&nbsp;
                                </td>
                            </tr>
                        </table>
                        */ ?>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

    </table>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>