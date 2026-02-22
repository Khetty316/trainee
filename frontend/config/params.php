<?php

const UPLOADPATH = '../uploads/';
return [
    'application_name' => 'Digital Management System',
    'version' => 'v0.1.1',
    'application_hostname' => 'tkserver',
    'supportEmail' => 'npleoffice@opitsolutions.com.my',
    'cronEmail' => 'tkdms@opitsolutions.com.my',
//    'adminEmail' => 'admin@example.com',
    'user_profile_file_path' => '../uploads/user/',
    'temp_folder' => '../uploads/temp/',
    'leave_file_path' => '../uploads/leave/',
    'personaldocument_file_path' => '../uploads/personaldocument/',
    'publicdocument_file_path' => '../uploads/publicdocument/',
    'monthlyappraisal_file_path' => '../uploads/monthlyappraisaldocument/',
    'quotation_pdf_path' => '../uploads/quotation-for-client/',
    //Common
    'maxSize' => 10485760,
    'tooBigMsg' => 'Limit is 10MB',
    // production design
    'project_design_file_path' => '../uploads/project/design/',
    'project_file_path' => '../uploads/project/',
    'cronValidationKey' => 'Zx2KGSvjWSaEcvSjYuMpU8',
//    'application_hostname' => 'XXXXXXXXX.npl.com.my',
    'gridViewCommonOption' => [
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - ']
    ],
    'detailViewOption28' => [
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm']
    ],
];
