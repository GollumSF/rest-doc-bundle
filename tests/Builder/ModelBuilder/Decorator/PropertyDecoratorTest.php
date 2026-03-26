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
		$nameConverter        = $this->createMock(NameConverterInterface::class);
		$classMetadataFactory = $this->createMock(ClassMetadataFactoryInterface::class);
		$typeDiscover         = $this->createMock(TypeDiscoverInterface::class);

		$model = new ObjectType(\stdClass::class);

		$type1 = $this->createMock(TypeInterface::class);
		$type2 = $this->createMock(TypeInterface::class);
		
		$attribute1 = $this->createMock(AttributeMetadataInterface::class);
		$attribute2 = $this->createMock(AttributeMetadataInterface::class);

		$attribute1->expects($this->once())->method('getName')->willReturn('propName1');
		$attribute2->expects($this->once())->method('getName')->willReturn('propName2');

		$attribute1->expects($this->once())->method('getSerializedName')->willReturn('prop_name1');
		$attribute2->expects($this->once())->method('getSerializedName')->willReturn(null);
		
		$attribute1->expects($this->once())->method('getGroups')->willReturn([ 'group1' ]);
		$attribute2->expects($this->once())->method('getGroups')->willReturn([]);
		
		$metadata = $this->createMock(ClassMetadataInterface::class);
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

		$typeCallCount = 0;
		$typeDiscover
			->expects($this->exactly(2))
			->method('getType')
			->willReturnCallback(function ($class, $propName) use (&$typeCallCount, $type1, $type2) {
				$expected = [
					[\stdClass::class, 'propName1'],
					[\stdClass::class, 'propName2'],
				];
				$returns = [$type1, $type2];
				$this->assertSame($expected[$typeCallCount][0], $class);
				$this->assertSame($expected[$typeCallCount][1], $propName);
				return $returns[$typeCallCount++];
			})
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
