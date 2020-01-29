<?php
namespace Test\GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator;

use GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\PropertyDecorator;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyDecoratorTest extends TestCase {
	
	public function testDecorateModel() {
		$nameConverter        = $this->getMockBuilder(NameConverterInterface::class)       ->getMockForAbstractClass();
		$classMetadataFactory = $this->getMockBuilder(ClassMetadataFactoryInterface::class)->getMockForAbstractClass();
		$typeDiscover         = $this->getMockBuilder(TypeDiscoverInterface::class)        ->getMockForAbstractClass();

		$model = new ObjectType(\stdClass::class);

		$type1 = $this->getMockBuilder(TypeInterface::class)->getMockForAbstractClass();
		$type2 = $this->getMockBuilder(TypeInterface::class)->getMockForAbstractClass();
		
		$attribute1 = $this->getMockBuilder(AttributeMetadataInterface::class)->getMockForAbstractClass();
		$attribute2 = $this->getMockBuilder(AttributeMetadataInterface::class)->getMockForAbstractClass();

		$attribute1->expects($this->at(0))->method('getName')->willReturn('propName1');
		$attribute2->expects($this->at(0))->method('getName')->willReturn('propName2');

		$attribute1->expects($this->at(1))->method('getSerializedName')->willReturn('prop_name1');
		$attribute2->expects($this->at(1))->method('getSerializedName')->willReturn(null);
		
		$attribute1->expects($this->at(2))->method('getGroups')->willReturn([ 'group1' ]);
		$attribute2->expects($this->at(2))->method('getGroups')->willReturn([]);
		
		$metadata = $this->getMockBuilder(ClassMetadataInterface::class)->getMockForAbstractClass();
		$metadata
			->expects($this->once())
			->method('getAttributesMetadata')
			->willReturn([
				$attribute1,
				$attribute2
			])
		;
		
		$classMetadataFactory
			->expects($this->once())
			->method('getMetadataFor')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$typeDiscover
			->expects($this->at(0))
			->method('getType')
			->with(\stdClass::class, 'propName1')
			->willReturn($type1)
		;
		$typeDiscover
			->expects($this->at(1))
			->method('getType')
			->with(\stdClass::class, 'propName2')
			->willReturn($type2)
		;
		
		$nameConverter
			->expects($this->once())
			->method('normalize')
			->with('propName2')
			->willReturn('prop_name2')
		;
		
		$propertyDecorator = new PropertyDecorator(
			$nameConverter,
			$classMetadataFactory,
			$typeDiscover
		);

		$this->assertEquals($propertyDecorator->decorateModel($model), $model);

		$properties = $model->getProperties();

		$this->assertEquals($properties['prop_name1']->getName(), 'propName1');
		$this->assertEquals($properties['prop_name1']->getSerializeName(), 'prop_name1');
		$this->assertEquals($properties['prop_name1']->getType(), $type1);
		$this->assertEquals($properties['prop_name1']->getGroups(), [ 'group1' ]);

		$this->assertEquals($properties['prop_name2']->getName(), 'propName2');
		$this->assertEquals($properties['prop_name2']->getSerializeName(), 'prop_name2');
		$this->assertEquals($properties['prop_name2']->getType(), $type2);
		$this->assertEquals($properties['prop_name2']->getGroups(), []);
	}
	
}