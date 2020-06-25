<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics;

use Daley\Logistics\Channel\Channel;
use Daley\Logistics\Exceptions\InvalidArgumentException;

class Factory
{
    protected $channels = [];

    // 数组元素存储查询对象
    public function channel($name)
    {
        if (!isset($this->channels[$name])) {
            $className = $this->formatClassName($name);

            if (!class_exists($className)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not exists.', $className));
            }

            $instance = new $className();

            if (!($instance instanceof Channel)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not inherited from %s.', $name, Channel::class));
            }

            $this->channels[$name] = $instance;
        }

        return $this->channels[$name];
    }

    /**
     * 格式化类的名称.
     *
     * @param string $name
     *
     * @return string
     */
    protected function formatClassName($name)
    {
        if (class_exists($name)) {
            return $name;
        }

        $name = ucfirst(str_replace(['-', '_', ' '], '', $name));

        return __NAMESPACE__."\\Channel\\{$name}Channel";
    }
}
