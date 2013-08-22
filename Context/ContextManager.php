<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminBundle\Context;

use FSi\Bundle\AdminBundle\Admin\ElementInterface;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class ContextManager
{
    /**
     * @var \FSi\Bundle\AdminBundle\Context\ContextBuilderInterface[]
     */
    protected $builders;

    /**
     * @param \FSi\Bundle\AdminBundle\Context\ContextBuilderInterface[] $builders
     */
    public function __construct($builders = array())
    {
        $this->builders = array();

        foreach($builders as $builder) {
            $this->addContextBuilder($builder);
        }
    }

    public function addContextBuilder(ContextBuilderInterface $builder)
    {
        $this->builders[] = $builder;
    }

    /**
     * @param string $route
     * @param \FSi\Bundle\AdminBundle\Admin\ElementInterface $element
     * @return \FSi\Bundle\AdminBundle\Context\ContextInterface|null
     */
    public function createContext($route, ElementInterface $element)
    {
        foreach ($this->builders as $builder) {
            if ($builder->supports($route, $element)) {
                return $builder->buildContext($element);
            }
        }

        return null;
    }
}