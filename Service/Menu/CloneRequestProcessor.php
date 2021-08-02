<?php

declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Exception;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Service\Menu\Cloner as MenuCloner;

class CloneRequestProcessor
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var MenuCloner
     */
    private $logger;

    /**
     * @var MenuCloner
     */
    private $menuCloner;

    public function __construct(
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        MenuCloner $menuCloner
    ) {
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->menuCloner = $menuCloner;
    }

    public function clone(MenuInterface $menu): MenuInterface
    {
        try {
            $menuClone = $this->menuCloner->clone($menu);

            $successMessage = __(
                'Menu "%1" has been successfully duplicated as "%2".',
                $menu->getIdentifier(),
                $menuClone->getIdentifier()
            );

            $this->messageManager->addSuccessMessage($successMessage);

            return $menuClone;
        } catch (Exception $exception) {
            $this->logger->critical($exception, ['origin' => 'snowdog-menu-cloner']);
            $this->messageManager->addErrorMessage(
                __(
                    'An error occurred while duplicating menu "%1". [REASON: %2]',
                    $menu->getIdentifier(),
                    $exception->getMessage()
                )
            );
        }

        return $menu;
    }
}
