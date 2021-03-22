<?php

namespace Leon\BswBundle\Module\Entity;

class Enum
{
    /**
     * @const array 状态
     */
    const STATE = [
        Abs::CLOSE  => 'Encase',
        Abs::NORMAL => 'Normal',
    ];

    /**
     * @const array 对立面
     */
    const OPPOSE = [
        Abs::NO  => 'No',
        Abs::YES => 'Yes',
    ];

    /**
     * @const array 验证码类型
     */
    const BSW_CAPTCHA_TYPE = [
        Abs::CAPTCHA_SMS   => 'SMS code',
        Abs::CAPTCHA_EMAIL => 'Email code',
    ];

    /**
     * @const array 验证码场景
     */
    const BSW_CAPTCHA_SCENE = [
        Abs::SNS_SCENE_SIGN_IN        => 'Sign in',
        Abs::SNS_SCENE_SIGN_UP        => 'Sign up',
        Abs::SNS_SCENE_PASSWORD       => 'Forget password',
        Abs::SNS_SCENE_BIND           => 'Account bind',
        Abs::SNS_SCENE_AGENT_SIGN_IN  => 'Agent sign in',
        Abs::SNS_SCENE_AGENT_SIGN_UP  => 'Agent sign up',
        Abs::SNS_SCENE_AGENT_PASSWORD => 'Agent forget password',
        Abs::SNS_SCENE_AGENT_WITHDRAW => 'Agent forget withdraw',
    ];

    /**
     * @const array 附件上传平台
     */
    const BSW_ATTACHMENT_PLATFORM = [
        0 => Abs::UNKNOWN,
        1 => 'Frontend system',
        2 => 'Backend system',
    ];

    /**
     * @const array 性别
     */
    const GENDER = [
        0 => 'Secret',
        1 => 'Male',
        2 => 'Female',
    ];

    /**
     * @const array 性别
     */
    const BSW_ADMIN_USER_SEX = self::GENDER;

    /**
     * @const array 操作类型
     */
    const BSW_ADMIN_PERSISTENCE_LOG_TYPE = [
        1 => 'CURD create one',
        2 => 'CURD create any',
        3 => 'CURD update one',
        4 => 'CURD update any',
        5 => 'CURD delete one',
        6 => 'CURD delete any',
        7 => 'CURD retrieve one',
        8 => 'CURD retrieve any',
    ];

    /**
     * @const array 日志级别
     */
    const LOGGER_LEVEL = [
        0 => 'Unknown',
        1 => 'DEBUG',
        2 => 'INFO',
        3 => 'NOTICE',
        4 => 'WARNING',
        5 => 'ERROR',
        6 => 'CRITICAL',
        7 => 'ALERT',
        8 => 'EMERGENCY',
    ];

    /**
     * @const array 设备操作系统
     */
    const DEVICE_OS = [
        Abs::OS_FULL         => 'Full Platform',
        Abs::OS_ANDROID      => 'Android',
        Abs::OS_IOS          => 'iOS',
        Abs::OS_WINDOWS      => 'Windows',
        Abs::OS_MAC          => 'Mac',
        Abs::OS_WEB          => 'Web',
        Abs::OS_ANDROID_TV   => 'AndroidTV',
        Abs::OS_MAC_OFFICIAL => 'MacOfficial',
    ];

    /**
     * @const array 域名状态
     */
    const DOMAIN_STATE = [
        0 => 'Unavailable',
        1 => 'Available',
    ];

    /**
     * @const array URL跳转类型
     */
    const URL_TYPE = [
        1 => 'Inner scheme chain',
        2 => 'Outer link chain',
        3 => 'Outer scheme chain',
    ];

    /**
     * @const array 云平台
     */
    const CLOUD_PLATFORM = [
        Abs::CLD_ALI => 'Ali cloud',
        Abs::CLD_TX  => 'Tx cloud',
        Abs::CLD_AWS => 'Aws cloud',
        Abs::CLD_GLE => 'Gle cloud',
        Abs::CLD_HW  => 'Hw cloud',
    ];

    /**
     * @const array 云平台简称
     */
    const CLOUD_PLATFORM_AB = [
        Abs::CLD_ALI => Abs::CLOUD_ALI,
        Abs::CLD_TX  => Abs::CLOUD_TX,
        Abs::CLD_AWS => Abs::CLOUD_AWS,
        Abs::CLD_GLE => Abs::CLOUD_GLE,
        Abs::CLD_HW  => Abs::CLOUD_HW,
    ];

    /**
     * @const array 是否上报日志
     */
    const USER_OPERATE_LOGGER = [
        Abs::NO  => 'Close',
        Abs::YES => 'Open',
    ];

    /**
     * @const array 支付平台
     */
    const PAY_THIRD_PLATFORM = [
        Abs::PAY_PLATFORM_SIN    => 'Platform SinF pay',
        Abs::PAY_PLATFORM_WALL   => 'Platform Paymentwall',
        Abs::PAY_PLATFORM_PSN    => 'Platform Payssion',
        Abs::PAY_PLATFORM_APPLE  => 'Platform Apple pay',
        Abs::PAY_PLATFORM_WX     => 'Platform WeChat pay',
        Abs::PAY_PLATFORM_ALI    => 'Platform Ali pay',
        Abs::PAY_PLATFORM_GOOGLE => 'Platform Google pay',
    ];

    /**
     * @const array 支付方式 - 信付
     */
    const PAY_METHOD__SIN = [
        1 => 'WeChat pay',
        2 => 'Ali pay',
    ];

    /**
     * @const array 支付方式 - PaymentWall
     */
    const PAY_METHOD__WALL = [
        0   => 'Unknown',
        1   => 'WeChat pay',
        2   => 'Ali pay',
        100 => 'Test Method',
        101 => 'Credit Cards',
        102 => 'Credit Cards for Korea (Republic of)',
        103 => 'UnionPay',
        104 => 'Hipercard Brazil',
        105 => 'Bancontact',
        106 => 'FasterPay',
        107 => 'PayPal',
        108 => 'Webmoney',
        109 => 'CherryCredits',
        110 => 'Webcash',
        111 => 'MercadoPago',
        112 => 'Yandex Money',
        113 => 'MyCard Wallet',
        114 => 'PagSeguro',
        115 => 'Qiwi Wallet',
        116 => 'Bitcoin',
        117 => 'VTC Wallet',
        118 => 'Redcompra',
        119 => 'UniPin Wallet',
        120 => 'DOKU Wallet',
        121 => 'Toss Pay',
        122 => 'EPS',
        123 => 'Bank Transfer Finland',
        124 => 'Bank Transfer Korea',
        125 => 'Bank Transfer Malaysia',
        126 => 'Bank Transfer Philippines',
        127 => 'Klarna(Sofort)',
        128 => 'GiroPay',
        129 => 'Przelewy24',
        130 => 'DotPay',
        131 => 'Mybank',
        132 => 'KBC',
        133 => 'Bank Transfer Colombia',
        134 => 'PSE',
        135 => 'Bank Transfer Peru',
        136 => 'iDeal',
        137 => 'Transferência bancária',
        138 => 'Safetypay',
        139 => 'Multibanco',
        140 => 'Belfius',
        141 => 'Bank Transfer Mexico',
        142 => 'Redpagos',
        143 => 'Poli',
        144 => 'Interac',
        145 => 'CBC',
        146 => 'Bank Transfer Estonia',
        147 => 'Bank Transfer Latvia',
        148 => 'Bank Transfer Lithuania',
        149 => 'Bank Transfer Argentina',
        150 => 'Bank Transfer Poland',
        151 => 'MINT',
        152 => 'Todito Cash',
        153 => 'Gudang Voucher',
        154 => 'Happy Voucher',
        155 => 'Wavegame',
        156 => 'MOL',
        157 => 'Teencash',
        158 => 'T-money',
        159 => 'Oxxo',
        160 => 'OneCard',
        161 => 'Book Gift Voucher',
        162 => 'Oncash',
        163 => 'Eggmoney',
        164 => 'NeoSurf',
        165 => 'MyCard Card',
        166 => 'Ticket Premium',
        167 => 'Baloto',
        168 => 'Gana',
        169 => 'Game-ON',
        170 => 'Boleto',
        171 => 'Openbucks',
        172 => 'Efectivo',
        173 => 'Unipin Express',
        174 => 'Maxima',
        175 => 'Paypost',
        176 => 'Perlas',
        177 => 'Narvesen',
        178 => 'Pagofacil',
        179 => 'RapiPago',
        180 => 'Efecty',
        181 => 'Davivienda',
        182 => 'Minimart',
        183 => 'ATM Transfer Indonesia',
        184 => 'Culture Voucher',
        185 => 'Cashbee',
        186 => 'Mobiamo',
    ];

    /**
     * @const array 支付方式 - PaymentWall (Payment System)
     */
    const PAY_METHOD__WALL_PS = [
        0   => 'all',
        1   => 'wechatpayments',
        2   => 'alipay',
        100 => 'test',
        101 => 'cc',
        102 => 'allthegate',
        103 => 'unionpay',
        104 => 'ccbrazilhipercard',
        105 => 'bancontact',
        106 => 'fasterpay',
        107 => 'paypal',
        108 => 'webmoney',
        109 => 'cherrycredits',
        111 => 'mercadopago',
        112 => 'yamoney',
        113 => 'mycardwallet',
        114 => 'pagseguro',
        115 => 'qiwiwallet',
        116 => 'coinbasebitcoin',
        117 => 'vtc',
        118 => 'redcompra',
        119 => 'unipinwallet',
        120 => 'dokuwallet',
        122 => 'epspayments',
        123 => 'btfinland',
        124 => 'kftc',
        125 => 'ipay88',
        126 => 'dragonpay',
        127 => 'sofortbanktransfer',
        128 => 'giropay',
        129 => 'przelewy24',
        130 => 'dotpay',
        131 => 'mybank',
        132 => 'kbc',
        133 => 'btcolombia',
        134 => 'pse',
        135 => 'btperu',
        136 => 'idealpayments',
        137 => 'ebanxtransfer',
        138 => 'safetypay',
        139 => 'multibanco',
        140 => 'belfius',
        141 => 'banktransfermexico',
        142 => 'redpagos',
        143 => 'polipayments',
        144 => 'interac',
        145 => 'cbc',
        146 => 'btestonia',
        147 => 'btlatvia',
        148 => 'btlithuania',
        149 => 'banktransferargentina',
        150 => 'btpoland',
        151 => 'mint',
        152 => 'todito',
        153 => 'gudangvoucher',
        154 => 'happyvoucher',
        155 => 'wavegame',
        156 => 'mol',
        157 => 'teencash',
        158 => 'tmoney',
        159 => 'oxxomexico',
        160 => 'onecard',
        161 => 'bookculture',
        162 => 'oncash',
        163 => 'eggmoney',
        164 => 'neosurf',
        165 => 'mycardcard',
        166 => 'ticketsurf',
        167 => 'baloto',
        168 => 'gana',
        169 => 'gameon',
        170 => 'boletobr',
        171 => 'openbucks',
        172 => 'safetypaycash',
        173 => 'unipinexpress',
        174 => 'maxima',
        175 => 'paypost',
        176 => 'perlas',
        177 => 'narvesen',
        178 => 'pagofacil',
        179 => 'rapipago',
        180 => 'efectycolombia',
        181 => 'davivienda',
        182 => 'minimart',
        183 => 'atmtransfer',
        184 => 'culturevoucherkr',
        185 => 'cashbee',
        186 => 'mobilegateway',
    ];

    /**
     * @const array 支付方式 - Payssion
     */
    const PAY_METHOD__PSN = [
        300 => 'E-banking',
        301 => 'eNets',
        302 => 'E-banking',
        303 => 'Doku',
        304 => 'ATM',
        305 => 'Alfamart',
        306 => 'Dragonpay',
        307 => 'Globe Gcash',
        109 => 'CherryCredits',
        309 => 'MOLPoints',
        310 => 'MOLPoints card',
        2   => 'Ali pay',
        1   => 'WeChat pay',
        313 => 'Unionpay',
        314 => 'Gash',
        315 => 'UPI',
        316 => 'Paytm',
        317 => 'India Netbanking',
        318 => 'India Credit/Debit Card',
        319 => 'South Korea Credit Card',
        320 => 'South Korea Internet Banking',
        160 => 'onecard',
        322 => 'Fawry',
        323 => 'Santander Rio',
        324 => 'Pago Fácil',
        179 => 'Rapi Pago',
        326 => 'bancodobrasil',
        327 => 'itau',
        170 => 'Boleto',
        329 => 'bradesco',
        330 => 'caixa',
        331 => 'Santander',
        332 => 'BBVA Bancomer',
        333 => 'Santander',
        334 => 'oxxo',
        335 => 'SPEI',
        142 => 'redpagos',
        337 => 'Abitab',
        338 => 'Banco de Chile',
        339 => 'RedCompra',
        340 => 'WebPay plus',
        341 => 'Servipag',
        342 => 'Santander',
        180 => 'Efecty',
        134 => 'PSE',
        345 => 'BCP',
        346 => 'Interbank',
        347 => 'BBVA',
        348 => 'Pago Efectivo',
        349 => 'BoaCompra',
        350 => 'QIWI',
        112 => 'Yandex.Money',
        108 => 'Webmoney',
        353 => 'Bank Card (Yandex.Money)',
        354 => 'Cash (Yandex.Money)',
        355 => 'Moneta',
        356 => 'Alfa-Click',
        357 => 'Promsvyazbank',
        358 => 'Faktura',
        359 => 'Russia Bank transfer',
        360 => 'Turkish Credit/Bank Card',
        361 => 'ininal',
        362 => 'bkmexpress',
        363 => 'Turkish Bank Transfer',
        364 => 'Paysafecard',
        365 => 'Sofort',
        366 => 'Giropay',
        122 => 'EPS',
        368 => 'Bancontact/Mistercash',
        369 => 'Dotpay',
        370 => 'P24',
        371 => 'PayU',
        372 => 'PayU',
        136 => 'iDeal',
        139 => 'Multibanco',
        375 => 'Neosurf',
        376 => 'Polipayment',
    ];

    /**
     * @const array 支付方式 - Payssion (Payment Method ID)
     */
    const PAY_METHOD__PSN_PM = [
        300 => 'ebanking_my',
        301 => 'enets_sg',
        302 => 'ebanking_th',
        303 => 'doku_id',
        304 => 'atm_id',
        305 => 'alfamart_id',
        306 => 'dragonpay_ph',
        307 => 'gcash_ph',
        109 => 'cherrycredits',
        309 => 'molpoints',
        310 => 'molpointscard',
        2   => 'alipay_cn',
        1   => 'tenpay_cn',
        313 => 'unionpay_cn',
        314 => 'gash_tw',
        315 => 'upi_in',
        316 => 'paytm_in',
        317 => 'ebanking_in',
        318 => 'bankcard_in',
        319 => 'creditcard_kr',
        320 => 'ebanking_kr',
        160 => 'onecard',
        322 => 'fawry_eg',
        323 => 'santander_ar',
        324 => 'pagofacil_ar',
        179 => 'rapipago_ar',
        326 => 'bancodobrasil_br',
        327 => 'itau_br',
        170 => 'boleto_br',
        329 => 'bradesco_br',
        330 => 'caixa_br',
        331 => 'santander_br',
        332 => 'bancomer_mx',
        333 => 'santander_mx',
        334 => 'oxxo_mx',
        335 => 'spei_mx',
        142 => 'redpagos_uy',
        337 => 'abitab_uy',
        338 => 'bancochile_cl',
        339 => 'redcompra_cl',
        340 => 'webpay_cl',
        341 => 'servipag_cl',
        342 => 'santander_cl',
        180 => 'efecty_co',
        134 => 'pse_co',
        345 => 'bcp_pe',
        346 => 'interbank_pe',
        347 => 'bbva_pe',
        348 => 'pagoefectivo_pe',
        349 => 'boacompra',
        350 => 'qiwi',
        112 => 'yamoney',
        108 => 'webmoney',
        353 => 'yamoneyac',
        354 => 'yamoneygp',
        355 => 'moneta_ru',
        356 => 'alfaclick_ru',
        357 => 'promsvyazbank_ru',
        358 => 'faktura_ru',
        359 => 'banktransfer_ru',
        360 => 'bankcard_tr',
        361 => 'ininal_tr',
        362 => 'bkmexpress_tr',
        363 => 'banktransfer_tr',
        364 => 'paysafecard',
        365 => 'sofort',
        366 => 'giropay_de',
        122 => 'eps_at',
        368 => 'bancontact_be',
        369 => 'dotpay_pl',
        370 => 'p24_pl',
        371 => 'payu_pl',
        372 => 'payu_cz',
        136 => 'ideal_nl',
        139 => 'multibanco_pt',
        375 => 'neosurf',
        376 => 'polipayment',
    ];

    /**
     * @const array 支付方式 - WeChatPay
     */
    const PAY_METHOD__WX = [
        1 => 'WeChat pay',
    ];

    /**
     * @const array 支付方式 - AliPay
     */
    const PAY_METHOD__ALI = [
        2 => 'Ali pay',
    ];

    /**
     * @const array 支付方式 - ApplePay
     */
    const PAY_METHOD__APPLE = [
        3 => 'Apple pay',
    ];

    /**
     * @const array 支付方式 - GooglePay
     */
    const PAY_METHOD__GOOGLE = [
        6 => 'Google pay',
    ];

    /**
     * @const array 订单状态
     */
    const PURCHASE_STATE = [
        Abs::PAY_STATE_CLOSE     => 'Order closure',
        Abs::PAY_STATE_WAIT_USER => 'Waiting payment',
        Abs::PAY_STATE_WAIT_CALL => 'Waiting callback',
        Abs::PAY_STATE_ERROR     => 'Pay error',
        Abs::PAY_STATE_FAIL      => 'Pay fail',
        Abs::PAY_STATE_DONE      => 'Pay success',
        Abs::PAY_STATE_COMPLETE  => 'Purchase complete',
        Abs::PAY_STATE_REFUND    => 'Order refund',
    ];

    /**
     * @const array 用户状态
     */
    const USER_BASE_STATE = [
        Abs::CLOSE  => 'Frozen',
        Abs::NORMAL => 'Normal',
    ];

    /**
     * @const array 快递平台
     */
    const EXPRESS_PLATFORM = [
        1  => 'Express SF',
        2  => 'Express SF fast',
        3  => 'Express EMS normal',
        4  => 'Express YuanTong',
        5  => 'Express ZhongTong',
        6  => 'Express ShenTong',
        7  => 'Express ZaiJiSong',
        8  => 'Express DeBang',
        9  => 'Express YunDa',
        10 => 'Express JingDong',
        11 => 'Express BaiShi',
        12 => 'Express BaishiHuiTong',
        13 => 'Express HuiTong',
        14 => 'Express TianTian',
        15 => 'Express LianHao',
        16 => 'Express QuanFeng',
        17 => 'Express QuanYi',
        18 => 'Express SuEr',
        19 => 'Express GuoTong',
        20 => 'Express HuaQiang',
        21 => 'Express ZhongTie',
        22 => 'Express ZhongTie fast',
        23 => 'Express HuaYu',
        24 => 'Express UPS',
        25 => 'Express FedEx',
        26 => 'Express Panalpina',
        27 => 'Express DHL',
        28 => 'Express TNT',
        29 => 'Express EMS',
    ];

    /**
     * @const array 绑定类型
     */
    const BIND_TYPE = [
        Abs::BIND_THIRD_TO_PHONE  => 'Third to phone',
        Abs::BIND_THIRD_TO_EMAIL  => 'Third to email',
        Abs::BIND_DEVICE_TO_PHONE => 'Device to phone',
        Abs::BIND_DEVICE_TO_EMAIL => 'Device to email',
        Abs::BIND_PHONE_TO_THIRD  => 'Phone to third',
        Abs::BIND_PHONE_TO_DEVICE => 'Phone to device',
        Abs::BIND_EMAIL_TO_THIRD  => 'Email to third',
        Abs::BIND_EMAIL_TO_DEVICE => 'Email to device',
    ];

    /**
     * @const array 自然周期
     */
    const NATURAL_PERIOD = [
        Abs::PERIOD_YEAR    => 'Every year',
        Abs::PERIOD_QUARTER => 'Every quarter',
        Abs::PERIOD_MONTH   => 'Every month',
        Abs::PERIOD_WEEK    => 'Every week',
        Abs::PERIOD_DAY     => 'Every day',
        Abs::PERIOD_HOUR    => 'Every hour',
        Abs::PERIOD_MINUTE  => 'Every minute',
    ];

    /**
     * @const array 国内银行
     */
    const BANK_TYPE = [
        0  => 'Unknown',
        1  => 'Industrial and Commercial Bank of China',
        2  => 'Construction Bank of China',
        3  => 'Bank of China',
        4  => 'Agricultural Bank of China',
        5  => 'Bank of Communications',
        6  => 'China Merchants Bank',
        7  => 'Industrial Bank',
        8  => 'Pudong Development Bank',
        9  => 'Postal Savings Bank of China',
        10 => 'Minsheng Bank of China',
        12 => 'China CITIC Bank',
        13 => 'Everbright Bank of China',
        14 => 'Shenzhen Ping An Bank',
        15 => 'Bank of Beijing',
        16 => 'Huaxia Bank',
        17 => 'Bank of Shanghai',
        18 => 'China Guangfa Bank',
        19 => 'Hongkong and Shanghai Banking Corporation',
    ];

    /**
     * @const array 语言包映射 locale
     */
    const LANG_TO_LOCALE = [
        'en_us'      => 'en_us',
        'ar'         => 'ar',
        'ar_ae'      => 'ar_ae',
        'ar_bh'      => 'ar_bh',
        'ar_dz'      => 'ar_dz',
        'ar_eg'      => 'ar_eg',
        'ar_iq'      => 'ar_iq',
        'ar_jo'      => 'ar_jo',
        'ar_kw'      => 'ar_kw',
        'ar_lb'      => 'ar_lb',
        'ar_ly'      => 'ar_ly',
        'ar_ma'      => 'ar_ma',
        'ar_om'      => 'ar_om',
        'ar_qa'      => 'ar_qa',
        'ar_sa'      => 'ar_sa',
        'ar_sd'      => 'ar_sd',
        'ar_sy'      => 'ar_sy',
        'ar_tn'      => 'ar_tn',
        'ar_ye'      => 'ar_ye',
        'be'         => 'be',
        'be_by'      => 'be_by',
        'bg'         => 'bg',
        'bg_bg'      => 'bg_bg',
        'ca'         => 'ca',
        'ca_es'      => 'ca_es',
        'ca_es_euro' => 'ca_es_euro',
        'cs'         => 'cs',
        'cs_cz'      => 'cs_cz',
        'da'         => 'da',
        'da_dk'      => 'da_dk',
        'de'         => 'de',
        'de_at'      => 'de_at',
        'de_at_euro' => 'de_at_euro',
        'de_ch'      => 'de_ch',
        'de_de'      => 'de_de',
        'de_de_euro' => 'de_de_euro',
        'de_lu'      => 'de_lu',
        'de_lu_euro' => 'de_lu_euro',
        'el'         => 'el',
        'el_gr'      => 'el_gr',
        'en_au'      => 'en_au',
        'en_ca'      => 'en_ca',
        'en_gb'      => 'en_gb',
        'en_ie'      => 'en_ie',
        'en_ie_euro' => 'en_ie_euro',
        'en_nz'      => 'en_nz',
        'en_za'      => 'en_za',
        'es'         => 'es',
        'es_bo'      => 'es_bo',
        'es_ar'      => 'es_ar',
        'es_cl'      => 'es_cl',
        'es_co'      => 'es_co',
        'es_cr'      => 'es_cr',
        'es_do'      => 'es_do',
        'es_ec'      => 'es_ec',
        'es_es'      => 'es_es',
        'es_es_euro' => 'es_es_euro',
        'es_gt'      => 'es_gt',
        'es_hn'      => 'es_hn',
        'es_mx'      => 'es_mx',
        'es_ni'      => 'es_ni',
        'et'         => 'et',
        'es_pa'      => 'es_pa',
        'es_pe'      => 'es_pe',
        'es_pr'      => 'es_pr',
        'es_py'      => 'es_py',
        'es_sv'      => 'es_sv',
        'es_uy'      => 'es_uy',
        'es_ve'      => 'es_ve',
        'et_ee'      => 'et_ee',
        'fi'         => 'fi',
        'fi_fi'      => 'fi_fi',
        'fi_fi_euro' => 'fi_fi_euro',
        'fr'         => 'fr',
        'fr_be'      => 'fr_be',
        'fr_be_euro' => 'fr_be_euro',
        'fr_ca'      => 'fr_ca',
        'fr_ch'      => 'fr_ch',
        'fr_fr'      => 'fr_fr',
        'fr_fr_euro' => 'fr_fr_euro',
        'fr_lu'      => 'fr_lu',
        'fr_lu_euro' => 'fr_lu_euro',
        'hr'         => 'hr',
        'hr_hr'      => 'hr_hr',
        'hu'         => 'hu',
        'hu_hu'      => 'hu_hu',
        'is'         => 'is',
        'is_is'      => 'is_is',
        'it'         => 'it',
        'it_ch'      => 'it_ch',
        'it_it'      => 'it_it',
        'it_it_euro' => 'it_it_euro',
        'iw'         => 'iw',
        'iw_il'      => 'iw_il',
        'ja'         => 'ja',
        'ja_jp'      => 'ja_jp',
        'ko'         => 'ko',
        'ko_kr'      => 'ko_kr',
        'lt'         => 'lt',
        'lt_lt'      => 'lt_lt',
        'lv'         => 'lv',
        'lv_lv'      => 'lv_lv',
        'mk'         => 'mk',
        'mk_mk'      => 'mk_mk',
        'nl'         => 'nl',
        'nl_be'      => 'nl_be',
        'nl_be_euro' => 'nl_be_euro',
        'nl_nl'      => 'nl_nl',
        'nl_nl_euro' => 'nl_nl_euro',
        'no'         => 'no',
        'no_no'      => 'no_no',
        'no_no_ny'   => 'no_no_ny',
        'pl'         => 'pl',
        'pl_pl'      => 'pl_pl',
        'pt'         => 'pt',
        'pt_br'      => 'pt_br',
        'pt_pt'      => 'pt_pt',
        'pt_pt_euro' => 'pt_pt_euro',
        'ro'         => 'ro',
        'ro_ro'      => 'ro_ro',
        'ru'         => 'ru',
        'ru_ru'      => 'ru_ru',
        'sh'         => 'sh',
        'sh_yu'      => 'sh_yu',
        'sk'         => 'sk',
        'sk_sk'      => 'sk_sk',
        'sl'         => 'sl',
        'sl_si'      => 'sl_si',
        'sq'         => 'sq',
        'sq_al'      => 'sq_al',
        'sr'         => 'sr',
        'sr_yu'      => 'sr_yu',
        'sv'         => 'sv',
        'sv_se'      => 'sv_se',
        'th'         => 'th',
        'th_th'      => 'th_th',
        'tr'         => 'tr',
        'tr_tr'      => 'tr_tr',
        'uk'         => 'uk',
        'uk_ua'      => 'uk_ua',
        'zh_cn'      => 'cn',
        'cn'         => 'cn',
        'zh_hk'      => 'zh_hk',
        'zh_tw'      => 'zh_tw',
        'hk'         => 'hk',
        'tc'         => 'hk',
        'en'         => 'en',
        'id'         => 'id',
        'id_id'      => 'id_id',
        'jp'         => 'jp',
    ];

    /**
     * @const array 语言包
     */
    const LANG = [
        'Compatible lang' => 0,
        'en_us'           => 1,
        'ar'              => 2,
        'ar_ae'           => 3,
        'ar_bh'           => 4,
        'ar_dz'           => 5,
        'ar_eg'           => 6,
        'ar_iq'           => 7,
        'ar_jo'           => 8,
        'ar_kw'           => 9,
        'ar_lb'           => 10,
        'ar_ly'           => 11,
        'ar_ma'           => 12,
        'ar_om'           => 13,
        'ar_qa'           => 14,
        'ar_sa'           => 15,
        'ar_sd'           => 16,
        'ar_sy'           => 17,
        'ar_tn'           => 18,
        'ar_ye'           => 19,
        'be'              => 20,
        'be_by'           => 21,
        'bg'              => 22,
        'bg_bg'           => 23,
        'ca'              => 24,
        'ca_es'           => 25,
        'ca_es_euro'      => 26,
        'cs'              => 27,
        'cs_cz'           => 28,
        'da'              => 29,
        'da_dk'           => 30,
        'de'              => 31,
        'de_at'           => 32,
        'de_at_euro'      => 33,
        'de_ch'           => 34,
        'de_de'           => 35,
        'de_de_euro'      => 36,
        'de_lu'           => 37,
        'de_lu_euro'      => 38,
        'el'              => 39,
        'el_gr'           => 40,
        'en_au'           => 41,
        'en_ca'           => 42,
        'en_gb'           => 43,
        'en_ie'           => 44,
        'en_ie_euro'      => 45,
        'en_nz'           => 46,
        'en_za'           => 47,
        'es'              => 48,
        'es_bo'           => 49,
        'es_ar'           => 50,
        'es_cl'           => 51,
        'es_co'           => 52,
        'es_cr'           => 53,
        'es_do'           => 54,
        'es_ec'           => 55,
        'es_es'           => 56,
        'es_es_euro'      => 57,
        'es_gt'           => 58,
        'es_hn'           => 59,
        'es_mx'           => 60,
        'es_ni'           => 61,
        'et'              => 62,
        'es_pa'           => 63,
        'es_pe'           => 64,
        'es_pr'           => 65,
        'es_py'           => 66,
        'es_sv'           => 67,
        'es_uy'           => 68,
        'es_ve'           => 69,
        'et_ee'           => 70,
        'fi'              => 71,
        'fi_fi'           => 72,
        'fi_fi_euro'      => 73,
        'fr'              => 74,
        'fr_be'           => 75,
        'fr_be_euro'      => 76,
        'fr_ca'           => 77,
        'fr_ch'           => 78,
        'fr_fr'           => 79,
        'fr_fr_euro'      => 80,
        'fr_lu'           => 81,
        'fr_lu_euro'      => 82,
        'hr'              => 83,
        'hr_hr'           => 84,
        'hu'              => 85,
        'hu_hu'           => 86,
        'is'              => 87,
        'is_is'           => 88,
        'it'              => 89,
        'it_ch'           => 90,
        'it_it'           => 91,
        'it_it_euro'      => 92,
        'iw'              => 93,
        'iw_il'           => 94,
        'jp'              => 95,
        'ja'              => 95,
        'ja_jp'           => 96,
        'ko'              => 97,
        'ko_kr'           => 98,
        'lt'              => 99,
        'lt_lt'           => 100,
        'lv'              => 101,
        'lv_lv'           => 102,
        'mk'              => 103,
        'mk_mk'           => 104,
        'nl'              => 105,
        'nl_be'           => 106,
        'nl_be_euro'      => 107,
        'nl_nl'           => 108,
        'nl_nl_euro'      => 109,
        'no'              => 110,
        'no_no'           => 111,
        'no_no_ny'        => 112,
        'pl'              => 113,
        'pl_pl'           => 114,
        'pt'              => 115,
        'pt_br'           => 116,
        'pt_pt'           => 117,
        'pt_pt_euro'      => 118,
        'ro'              => 119,
        'ro_ro'           => 120,
        'ru'              => 121,
        'ru_ru'           => 122,
        'sh'              => 123,
        'sh_yu'           => 124,
        'sk'              => 125,
        'sk_sk'           => 126,
        'sl'              => 127,
        'sl_si'           => 128,
        'sq'              => 129,
        'sq_al'           => 130,
        'sr'              => 131,
        'sr_yu'           => 132,
        'sv'              => 133,
        'sv_se'           => 134,
        'th'              => 135,
        'th_th'           => 136,
        'tr'              => 137,
        'tr_tr'           => 138,
        'uk'              => 139,
        'uk_ua'           => 140,
        'zh_cn'           => 141,
        'cn'              => 141,
        'zh_hk'           => 143,
        'zh_tw'           => 144,
        'hk'              => 145,
        'tc'              => 145,
        'en'              => 146,
        'id_id'           => 147,
        'id'              => 147,
    ];

    /**
     * @const array 消息类型
     */
    const MESSAGE_TYPE = [
        Abs::MESSAGE_BOOTSTRAP => 'Bootstrap message',
        Abs::MESSAGE_CAROUSEL  => 'Carousel message',
        Abs::MESSAGE_NOTICE    => 'Notice message',
        Abs::MESSAGE_POPUP     => 'Popup message',
    ];

    /**
     * @const array 队列任务状态
     */
    const BSW_COMMAND_QUEUE_STATE = [
        1 => 'Mission ready',
        2 => 'Mission in progress',
        3 => 'Mission success',
        4 => 'Mission failed',
    ];

    /**
     * @const array 微信吊起支付方式
     */
    const WX_PAY_CLASSIFY = [
        Abs::WX_PAY_INSIDE,
        Abs::WX_PAY_QR,
        Abs::WX_PAY_H5,
        Abs::WX_PAY_APP,
    ];

    /**
     * @const array 任务执行类型
     */
    const CRON_TYPE = [
        1 => 'Execute now',
        2 => 'Execute manual',
    ];

    /**
     * @const array 任务执行风格
     */
    const CRON_REUSE = [
        0 => 'Away after execute',
        1 => 'Reuse after execute',
    ];

    /**
     * @const array 任务类型
     */
    const BSW_WORK_TASK_TYPE = [
        1 => 'Member task',
        2 => 'Team task',
    ];

    /**
     * @const array 工作任务状态
     */
    const BSW_WORK_TASK_STATE = [
        0 => 'Task closed',
        1 => 'Task unopened',
        2 => 'Task in progress',
        3 => 'Task done in contract',
        4 => 'Task done out contract',
    ];

    /**
     * @const array TOKEN场景
     */
    const BSW_TOKEN_SCENE = [
        0 => Abs::COMMON,
        1 => 'Login backend',
    ];

    /**
     * @const array 配合类型
     */
    const BSW_CONFIG_TYPE = [
        1 => 'Type string',
        2 => 'Type int',
        3 => 'Type float',
        4 => 'Type boolean',
    ];
}