<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentFactorySpec extends ObjectBehavior
{
    /**
     * @param Sylius\Component\Resource\Repository\RepositoryInterface $shipmentRepository
     */
    function let($shipmentRepository)
    {
        $this->beConstructedWith($shipmentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactory');
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactoryInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface              $order
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface      $inventoryUnit
     * @param Sylius\Bundle\CoreBundle\Model\ShipmentInterface           $shipment
     * @param Doctrine\Common\Collections\ArrayCollection                $shipments
     */
    function it_creates_a_single_shipment_and_assigns_all_inventory_units_to_it($shipmentRepository, $order, $shipment, $shipments, $inventoryUnit)
    {

        $shipmentRepository
            ->createNew()
            ->willReturn($shipment)
        ;

        $order
            ->getShipments()
            ->willReturn($shipments)
            ->shouldBeCalled()
        ;

        $shipments
            ->first()
            ->willReturn(null)
            ->shouldBeCalled()
        ;

        $order
            ->getInventoryUnits()
            ->willReturn(array($inventoryUnit))
        ;

        $shipment
            ->addItem($inventoryUnit)
            ->shouldBeCalled()
        ;

        $order
            ->addShipment($shipment)
            ->shouldBeCalled()
        ;

        $this->createShipment($order);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface              $order
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface      $inventoryUnit
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface      $inventoryUnitWithoutShipment
     * @param Sylius\Bundle\CoreBundle\Model\ShipmentInterface           $shipment
     * @param Doctrine\Common\Collections\ArrayCollection                $shipments
     */
    function it_adds_new_inventory_units_to_existing_shipment($order, $shipment, $shipments, $inventoryUnit, $inventoryUnitWithoutShipment)
    {
        $shipments
            ->first()
            ->willReturn($shipment)
            ->shouldBeCalled()
        ;

        $inventoryUnit
            ->getShipment()
            ->willReturn($shipment)
        ;

        $order
            ->getInventoryUnits()
            ->willReturn(array(
                $inventoryUnit,
                $inventoryUnitWithoutShipment
            ))
        ;

        $order
            ->getShipments()
            ->willReturn($shipments)
            ->shouldBeCalled()
        ;

        $shipment
            ->addItem($inventoryUnitWithoutShipment)
            ->shouldBeCalled()
        ;

        $shipment
            ->addItem($inventoryUnit)
            ->shouldNotBeCalled()
        ;

        $this->createShipment($order);
    }
}
