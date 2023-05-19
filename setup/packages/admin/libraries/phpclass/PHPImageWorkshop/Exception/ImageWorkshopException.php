<?php

namespace PHPImageWorkshop\Exception;

use PHPImageWorkshop\Exception\ImageWorkshopBaseException as ImageWorkshopBaseException;

/**
 * ImageWorkshopException
 *
 * Manage ImageWorkshop exceptions
 *
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshopException extends ImageWorkshopBaseException
{
    public static function invalidUnitArgument(): self
    {
        return new self("Invalid unit value: should be ImageWorkshopLayer::UNIT_PIXEL or ImageWorkshopLayer::UNIT_PERCENT");
    }
}
