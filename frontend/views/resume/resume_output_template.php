<meta http-equiv="content-type" content="charset=utf-8">
<?php

use common\models\myTools\MyFormatter;
?>
<style>
    body    {
        /*font-size: 1em !important;*/
        line-height: 1!important;
        font-family: Calibri !important;
    }
    td{
        padding:3px 5px 3px 5px;
    }

    .text-center{
        text-align: center;
    }
    .p-0{
        padding: 0px !important;
        margin: 0px !important;
    }
    .headerTitle{
        font-style: italic;
        font-size: 16pt;
        color: #7d7d7d;
        padding-top: 15px;
    }
    .isHr{
        <?php if ($fileType == "doc") { ?>
            padding:0px;
            margin:0px;
            <?php
        } else if ($fileType == "pdf") {
            ?> 
            padding:0px;
            margin:10px;
            <?php
        }
        ?>
    }
    .isHrSub{
        padding-bottom: 30px;
        color: #d0d0d0;
    }
    .b{
        font-weight: bold;
    }

    .f13{
        font-size: 13pt;
    }

    .isRed{
        background-color: red;
    }

    br{
        padding: 0px !important;
        margin: 0px !important;
    }
</style>

<body>
    <div>
        <div class="text-center" style="font-size: 13pt">
            <span >
                <?= strtoupper($user->fullname) ?><br/>
                <?php
                $address = $user->address;
                if ($user->address_line_2) {
                    $address .= "<br/>" . $user->address_line_2;
                }
                echo $address . "<br/>" . $user->postcode . ", " . (is_null($user->area) ? "" : $user->area->area_name);
                ?><br/>
                <?= $user->contact_no ?><br/>
                <?= $user->email ?></span>
        </div>
        <br/>
        <div class="headerTitle">
            Personal Information
        </div>
        <div>
            <hr class="isHr">
        </div>
        <br/>
        <table style="width: 100%">
            <tr>
                <td style="width: 30%" class="b">Nationality</td><td>Malaysian</td>
            </tr>
            <tr>
                <td class="b">Sex</td><td><?= $user['sex0']['sex_name'] ?></td>
            </tr>
            <tr>
                <td  class="b">Date Of Birth</td><td><?= MyFormatter::asDate_Read($user['dob']) ?></td>
            </tr>
        </table>
        <br/>
        <!--<div style="text-align:center;  font-family: Calibri; font-size: 8pt">-->
        <div class="headerTitle">
            Academic Qualifications
        </div>
        <hr class="isHr">
        <br/>

        <?php
        foreach ($academicList as $key => $academic) {
            if ($key > 0) {
                ?>
                <hr class="isHrSub">
                <?php
            }
            ?>
            <table style="width: 100%;page-break-inside: avoid">
                <tr>
                    <td style="width: 30%" class="b"><?= $academic->academic_level ?></td>
                    <td>
                        <p class="b">
                            <?= $academic->academic_course . ' (' . $academic->academic_period . ')' ?>
                        </p>
                    </td>
                </tr>
                <?php
                if ($academic->academic_honour != '') {
                    ?>
                    <tr>
                        <td class="t">Honour</td>
                        <td><?= $academic->academic_honour ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="font-weight-bold br text-right ">Institution</td>
                    <td class=""><?= $academic->academic_institution ?></td>
                </tr>
            </table>

            <?php
        }
        ?>

        <br/>

        <div>
            <?php
            foreach ($employList as $key => $employ) {
                if ($key > 0) {
                    ?>
                    <hr class="isHrSub">
                    <?php
                }
                ?>
                <div style="page-break-inside: avoid;">
                    <?php if ($key == 0) { ?>
                        <div class="headerTitle">
                            Employment History
                        </div>
                        <hr class="isHr"><br/>
                    <?php } ?>
                    <table style="width: 100%;page-break-inside: avoid;">
                        <?php if ($key == 0) {
                            ?> 

                        <?php }
                        ?>
                        <tr>
                            <td style="width: 30%" class="b"><?= $employ->employ_period ?></td>
                            <td>
                                <p class="b">
                                    <?= $employ->employ_role . ' - <br/>' . $employ->employ_company ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p><?= nl2br($employ->employ_detail) ?></p></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
        <br/>

        <div>
            <div class="headerTitle">
                Project Ref's:
            </div>
            <hr class="isHr"><br/>
            <table>
                <?php
                foreach ($projectList as $key => $project) {
                    ?>
                    <tr>
                        <td style="width: 30%" class="b"></td>
                        <td>
                            <p class="b">
                                <?= ' - ' . $project->project_detail ?>
                            </p>
                        </td>
                    </tr>
                    <?php
                }
                foreach ($nplProjectList as $key => $project) {
                    ?>
                    <tr>
                        <td style="width: 30%" class="b"></td>
                        <td>
                            <p class="b">
                                <?= ' - ' . $project->title_long ?>
                            </p>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>



</body>


