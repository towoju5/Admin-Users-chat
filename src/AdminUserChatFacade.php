<?php

namespace Towoju5\AdminUserChat;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Towoju5\AdminUserChat\Skeleton\SkeletonClass
 */
class AdminUserChatFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'woju-chat';
    }
}
