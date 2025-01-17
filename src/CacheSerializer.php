<?php
/**
* CacheSerializer
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled
* with this package in the file LICENSE.
* It is also available through the world-wide-web at this URL:
* http://krsteski.de/new-bsd-license/
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to gjero@krsteski.de so we can send you a copy immediately.
*
* @category CacheDba
* @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
* @license http://krsteski.de/new-bsd-license New BSD License
*/

/**
 * CacheSerializer
 *
 * @category  CacheDba
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */
class CacheSerializer
{
    public function __construct()
    {
    }

    private function mask($item)
    {
        return (object) $item;
    }

    private function unmask($item)
    {
        if (isset($item->scalar))
        {
            return $item->scalar;
        }

        return (array) $item;
    }

    /**
     * Serialize the object as stdClass.
     * @return string containing a byte-stream representation.
     */
    public function serialize($object)
    {
        $masked = false;

        if (false === is_object($object))
        {
            $object = $this->mask($object);
            $masked   = true;
        }

        $objectInformation         = new stdClass();
        $objectInformation->type   = get_class($object);
        $objectInformation->object = $object;
        $objectInformation->fake   = $masked;
        $objectInformation->time   = time();

        if ($object instanceof SimpleXMLElement)
        {
            $objectInformation->object = $object->asXml();
        }

        return serialize($objectInformation);
    }

    /**
     * Unserialize the object.
     * @return stdClass
     */
    public function unserialize($object)
    {
        $objectInformation = unserialize($object);

        if (true === $objectInformation->fake)
        {
            $objectInformation->object = $this->unmask($objectInformation->object);
        }

        if ($objectInformation->type == 'SimpleXMLElement')
        {
            $objectInformation->object = simplexml_load_string($objectInformation->object);
        }

        return $objectInformation;
    }
}