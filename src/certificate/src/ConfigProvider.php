<?php

namespace Certificate;

use Certificate\Action;
use Rbac\Role;
use Zend\ServiceManager;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'routes' => $this->getRoutes(),
            'dependencies' => $this->getDependencies(),
            'view_helpers' => $this->getViewHelpers(),
            'shared' => $this->getShared(),
            'rbac' => $this->getRbac()
        ];
    }

    /**
     * Returns the routes array
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            [
                'name' => 'certificate/post/payment',
                'path' => '/certificate/payment',
                'middleware' => Action\Post\CertificatePayment::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/form/payment',
                'path' => '/certificate/payment/form/{id:\d+}[/{coupon:\w+}]',
                'middleware' => Action\Form\CertificatePayment::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'certificate/view/resultset',
                'path' => '/certificate/resultset',
                'middleware' => Action\View\ResultSet::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'certificate/form/address',
                'path' => '/certificate/address/form/{examid:\d+}[/{coupon:\w+}[/{id:\d+}]]',
                'middleware' => Action\Form\CertificateAddress::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'certificate/json/state',
                'path' => '/certificate/json/state',
                'middleware' => Action\Json\State::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/address',
                'path' => '/certificate/json/address',
                'middleware' => Action\Json\CertificateAddress::class,
                'allowed_methods' => ['POST']
            ],
			[
                'name' => 'certificate/view/certificate',
                'path' => '/certificate/certificate/{id:\d+}',
                'middleware' => Action\View\Certificate::class,
                'allowed_methods' => ['GET']
            ],
			[
                'name' => 'certificate/view/multicertificate',
                'path' => '/certificate/multicertificate',
                'middleware' => Action\View\MultiCertificate::class,
                'allowed_methods' => ['POST']
            ],
			[
                'name' => 'certificate/view/selectcertificate',
                'path' => '/certificate/select/{coupon:\w+}',
                'middleware' => Action\View\SelectCertificate::class,
                'allowed_methods' => ['GET']
            ],
			[
                'name' => 'certificate/view/mergeaddress',
                'path' => '/certificate/mergeaddress',
                'middleware' => Action\View\MergeAddress::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/offer',
                'path' => '/certificate/json/offer',
                'middleware' => Action\Json\CertificateStopFirstOffer::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/showoffer',
                'path' => '/certificate/json/showoffer',
                'middleware' => Action\Json\CertificateStopShowOffer::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/sent',
                'path' => '/certificate/json/sent',
                'middleware' => Action\Json\CertificateDeliverySent::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/unsent',
                'path' => '/certificate/json/unsent',
                'middleware' => Action\Json\CertificateDeliveryNotSent::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'certificate/json/delivery',
                'path' => '/certificate/json/delivery/{id:\d+}',
                'middleware' => Action\Json\CertificateDeliveryUpdate::class,
                'allowed_methods' => ['GET']
            ]
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
        ];
    }


    public function getViewHelpers(): array
    {
        return [
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        9000 => [
                            'routeName' => 'certificate/view/resultset',
                            'active' => '/certificate',
                            'label' => 'Print Certificates'
                        ]
                    ],
					Role\Student::class => [
                        9000 => [
                            'routeName' => 'certificate/view/resultset',
                            'active' => '/certificate',
                            'label' => 'Print Certificates'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns the rbac array
     *
     * @return array
     */
    public function getRbac(): array
    {
        return [
            'permissions' => [
                Role\Administrator::class => [
                    'certificate/view/resultset',
					'certificate/view/certificate',
					'certificate/view/multicertificate',
					'certificate/view/mergeaddress',
					'certificate/json/sent',
					'certificate/json/unsent',
					'certificate/json/delivery',
                ],
                Role\Student::class => [
					'certificate/post/payment',
                    'certificate/form/payment',
                    'certificate/view/resultset',
					'certificate/form/address',
					'certificate/json/state',
					'certificate/json/address',
					'certificate/view/certificate',
					'certificate/view/multicertificate',
					'certificate/view/selectcertificate',
					'certificate/view/mergeaddress',
					'certificate/json/offer',
					'certificate/json/showoffer',
                ]
            ]
        ];
    }
}
