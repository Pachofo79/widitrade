<?php

namespace ContainerJilke9M;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getValidator_BuilderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'validator.builder' shared service.
     *
     * @return \Symfony\Component\Validator\ValidatorBuilder
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/ValidatorBuilder.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/Validation.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/ConstraintValidatorFactoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/ContainerConstraintValidatorFactory.php';

        $container->privates['validator.builder'] = $instance = \Symfony\Component\Validator\Validation::createValidatorBuilder();

        $instance->setConstraintValidatorFactory(new \Symfony\Component\Validator\ContainerConstraintValidatorFactory(new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'Symfony\\Component\\Validator\\Constraints\\EmailValidator' => ['privates', 'validator.email', 'getValidator_EmailService', true],
            'Symfony\\Component\\Validator\\Constraints\\ExpressionValidator' => ['privates', 'validator.expression', 'getValidator_ExpressionService', true],
            'Symfony\\Component\\Validator\\Constraints\\NotCompromisedPasswordValidator' => ['privates', 'validator.not_compromised_password', 'getValidator_NotCompromisedPasswordService', true],
            'validator.expression' => ['privates', 'validator.expression', 'getValidator_ExpressionService', true],
        ], [
            'Symfony\\Component\\Validator\\Constraints\\EmailValidator' => '?',
            'Symfony\\Component\\Validator\\Constraints\\ExpressionValidator' => '?',
            'Symfony\\Component\\Validator\\Constraints\\NotCompromisedPasswordValidator' => '?',
            'validator.expression' => '?',
        ])));
        $instance->setTranslationDomain('validators');
        $instance->enableAnnotationMapping(true);
        $instance->setDoctrineAnnotationReader(($container->privates['annotations.cached_reader'] ?? $container->load('getAnnotations_CachedReaderService')));
        $instance->addMethodMapping('loadValidatorMetadata');
        $instance->addObjectInitializers([]);

        return $instance;
    }
}
