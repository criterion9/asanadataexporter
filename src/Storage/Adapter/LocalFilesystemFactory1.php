<?php

/**
 * Description of LocalFilesystemFactory
 * 
 * PHP version 7
 * 
 * * * License * * * 
 * PORTOSPIRE ("COMPANY") CONFIDENTIAL
 * Unpublished Copyright (c) 2016-2018 PORTOSPIRE, All Rights Reserved.
 * 
 * NOTICE: All information contained herein is, and remains the property of 
 * COMPANY. The intellectual and technical concepts contained herein are
 * proprietary to COMPANY and may be covered by U.S. and Foreign Patents, 
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material is 
 * strictly forbidden unless prior written permission is obtained from COMPANY.
 * Access to the source code contained herein is hereby forbidden to anyone 
 * except current COMPANY employees, managers or contractors who have executed 
 * Confidentiality and Non-disclosure agreements explicitly covering such access.
 * 
 * The copyright notice above does not evidence any actual or intended publication
 * or disclosure of this source code, which includes information that is 
 * confidential and/or proprietary, and is a trade secret, of COMPANY. 
 * ANY REPRODUCTION, MODIFICATION, DISTRIBUTION, PUBLIC  PERFORMANCE, OR
 * PUBLIC DISPLAY OF OR THROUGH USE OF THIS SOURCE CODE WITHOUT THE EXPRESS WRITTEN
 * CONSENT OF COMPANY IS STRICTLY PROHIBITED, AND IN VIOLATION OF APPLICABLE 
 * LAWS AND INTERNATIONAL TREATIES. THE RECEIPT OR POSSESSION OF THIS SOURCE CODE
 * AND/OR RELATED INFORMATION DOES NOT CONVEY OR IMPLY ANY RIGHTS TO REPRODUCE,
 * DISCLOSE OR DISTRIBUTE ITS CONTENTS, OR TO MANUFACTURE, USE, OR SELL ANYTHING 
 * THAT IT MAY DESCRIBE, IN WHOLE OR IN PART.
 * * * End License * * * 
 * 
 * @category  Storage
 * @package   PortoSpireCMS
 * @author    Andrew Wallace <andrew.wallace@portospire.com>
 * @copyright 2017 PORTOSPIRE
 * @license   https://portospire.com/policies Proprietary, Confidential
 * @version   GIT: $ID$
 * @link      https://portospire.com 
 */

namespace PortoSpireCMS\Storage\Factory;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface,
    \PortoSpireCMS\Storage\LocalFilesystem,
                PortoSpireConfig\Service\ConfigurationManager,
        PortoSpireConfig\Service\SiteManager;
use Laminas\Stdlib\ArrayUtils;

/**
 * Description of LocalFilesystemFactory
 *
 * @category  Storage
 * @package   PortoSpireCMS
 * @author    Andrew Wallace <andrew.wallace@portospire.com>
 * @copyright 2017 PORTOSPIRE
 * @license   https://portospire.com/policies Proprietary
 * @version   Release: @package_version@
 * @link      https://coderepo.portospire.com/#portospirecms
 * @since     Class available since Release 0.7.14
 */
class LocalFilesystemFactory implements AbstractFactoryInterface
{

    /**
     * Constructor for LocalFilesystemFactory
     * 
     * @param \Interop\Container\ContainerInterface $container     Container object
     * @param type                                  $requestedName Object creating
     * @param array                                 $options       Array of options
     * 
     * @return LocalFilesystem
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, 
        $requestedName, array $options = null
    ) {

        $config = [];
        $config1 = $container->get('Config');
        if (isset($config1['ImageManager']['storage'])) {
            $config = $config1['ImageManager']['storage'];
        }
        $configManager = $container->get(ConfigurationManager::class);
        $siteArr = $configManager->buildArray(0, $container->get(SiteManager::class)->getAdminSiteID());
        if(isset($siteArr['root']['ImageManager']['storage'])){
            $config = ArrayUtils::merge($config, $siteArr['root']['ImageManager']['storage']);
        }
        return new LocalFilesystem($config);
    }

    /**
     * Provides v2 way to init. not making compatible.
     * 
     * @param \Interop\Container\ContainerInterface $container     Container object
     * @param type                                  $requestedName Requested object
     * 
     * @return void
     */
    public function canCreate(\Interop\Container\ContainerInterface $container, 
        $requestedName
    ) {
        
    }

}
