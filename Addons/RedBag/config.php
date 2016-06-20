<?php
return array(
    'mch_id' => array(
        'title' => '商户号:',
        'type' => 'text',
        'value' => '',
        'tip' => '微信支付分配的商户号'
    ),
    'partner_key' => array(
        'title' => 'API密钥:',
        'type' => 'text',
        'value' => '',
        'tip' => '务必填写正确，否则提示签名出错'
    ),
    'wxappid' => array(
        'title' => '商户appid:',
        'type' => 'text',
        'value' => '',
        'tip' => ''
    ),
    'wxappsecret' => array(
        'title' => '商户wxappsecret:',
        'type' => 'text',
        'value' => '',
        'tip' => ''
    ),
    'cert_dir' => array(
        'title' => '上传证书:',
        'type' => 'file',
        'value' => '',
        'tip' => '请上传正确的支付证书(apiclient_cert.pem)'
    ),
    'key_dir' => array(
        'title' => '上传密钥证书:',
        'type' => 'file',
        'value' => '',
        'tip' => '请上传正确的密钥证书(apiclient_key.pem)'
    )
);