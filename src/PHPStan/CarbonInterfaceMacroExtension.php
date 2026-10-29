<?php

namespace Phattarachai\Thaidate\PHPStan;

use Carbon\CarbonInterface;
use Carbon\PHPStan\MacroExtension;
use Phattarachai\Thaidate\CarbonMacros;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ClosureTypeFactory;

/**
 * @internal
 *
 * nesbot/carbon's MacroExtension resolves macros on Carbon classes but not on CarbonInterface,
 * because it asks the class itself whether the macro exists and an interface can't answer. A value
 * typed CarbonInterface (a CarbonInterface return, or a Carbon|CarbonImmutable union) is always a
 * concrete Carbon at runtime, so this package's macros resolve on it with the same types.
 */
final class CarbonInterfaceMacroExtension implements MethodsClassReflectionExtension
{
    private MacroExtension $carbonMacros;

    public function __construct(ReflectionProvider $reflectionProvider, ClosureTypeFactory $closureTypeFactory)
    {
        $this->carbonMacros = new MacroExtension($reflectionProvider, $closureTypeFactory);
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->isInterface()
            && ($classReflection->getName() === CarbonInterface::class || $classReflection->isSubclassOf(CarbonInterface::class))
            && in_array($methodName, CarbonMacros::NAMES, true);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->carbonMacros->getMethod($classReflection, $methodName);
    }
}
