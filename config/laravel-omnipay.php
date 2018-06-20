<?php

return [

	// The default gateway to use
	'default' => 'alipay',

	// Add in each gateway here
	'gateways' => [
		'paypal' => [
			'driver'  => 'PayPal_Express',
			'options' => [
				'solutionType'   => '',
				'landingPage'    => '',
				'headerImageUrl' => ''
			]
		],

        'alipay' => [
            'driver' => 'Alipay_Express',
            'options' => [
                'partner' => '2088031893487502',
                'key' => ' 2018041802578801',
                'sellerEmail' =>'shichen.liu@zoomov.com',
                'returnUrl' => 'http://www.zoomov.com/alipay/pay',
                'notifyUrl' => ' http://www.zoomov.com/alipay/paid'
            ]
        ]
	]
];