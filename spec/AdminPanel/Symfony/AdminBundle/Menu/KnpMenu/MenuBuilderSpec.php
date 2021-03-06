<?php

declare(strict_types=1);

namespace spec\AdminPanel\Symfony\AdminBundle\Menu\KnpMenu;

use AdminPanel\Symfony\AdminBundle\Menu\Builder\Builder;
use AdminPanel\Symfony\AdminBundle\Menu\Item\Item;
use AdminPanel\Symfony\AdminBundle\Menu\KnpMenu\ItemDecorator;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;

class MenuBuilderSpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory, ItemDecorator $itemDecorator)
    {
        $this->beConstructedWith($factory, $itemDecorator);
    }

    public function it_builds_knp_menu_and_decorates_items(
        FactoryInterface $factory,
        ItemInterface $knpRootItem,
        ItemInterface $knpFirstItem,
        ItemInterface $knpSecondItem,
        ItemInterface $knpChildOfSecondItem,
        ItemDecorator $itemDecorator,
        Builder $builder,
        Item $rootItem,
        Item $firstItem,
        Item $secondItem,
        Item $childOfSecondItem
    ) {
        $builder->buildMenu()->willReturn($rootItem);
        $firstItem->getName()->willReturn('first item');
        $firstItem->hasChildren()->willReturn(false);
        $firstItem->isSafeLabel()->willReturn(true);
        $secondItem->getName()->willReturn('second item');
        $secondItem->hasChildren()->willReturn(true);
        $secondItem->isSafeLabel()->willReturn(false);
        $childOfSecondItem->getName()->willReturn('child of second item');
        $childOfSecondItem->hasChildren()->willReturn(false);
        $childOfSecondItem->isSafeLabel()->willReturn(false);
        $rootItem->getChildren()->willReturn([$firstItem, $secondItem]);
        $secondItem->getChildren()->willReturn([$childOfSecondItem]);
        $rootItem->getOption('attr')->willReturn(['id' => null, 'class' => 'some class']);

        $factory->createItem('root')->willReturn($knpRootItem);
        $knpRootItem->addChild('first item', [])->willReturn($knpFirstItem);
        $knpRootItem->addChild('second item', [])->willReturn($knpSecondItem);
        $knpSecondItem->addChild('child of second item', [])->willReturn($knpChildOfSecondItem);

        $knpRootItem->setChildrenAttribute('id', null)->shouldBeCalled();
        $knpRootItem->setChildrenAttribute('class', 'some class')->shouldBeCalled();
        $knpFirstItem->setExtra('safe_label', true)->shouldBeCalled();
        $itemDecorator->decorate($knpFirstItem, $firstItem)->shouldBeCalled();
        $itemDecorator->decorate($knpSecondItem, $secondItem)->shouldBeCalled();
        $itemDecorator->decorate($knpChildOfSecondItem, $childOfSecondItem)->shouldBeCalled();

        $this->createMenu($builder);
    }
}
