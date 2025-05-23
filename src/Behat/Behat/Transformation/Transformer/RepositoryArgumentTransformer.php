<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Transformation\Transformer;

use Behat\Behat\Definition\Call\DefinitionCall;
use Behat\Behat\Definition\Pattern\PatternTransformer;
use Behat\Behat\Definition\Translator\TranslatorInterface;
use Behat\Behat\Transformation\RegexGenerator;
use Behat\Behat\Transformation\SimpleArgumentTransformation;
use Behat\Behat\Transformation\Transformation;
use Behat\Behat\Transformation\Transformation\PatternTransformation;
use Behat\Behat\Transformation\TransformationRepository;
use Behat\Gherkin\Node\ArgumentInterface;
use Behat\Testwork\Call\CallCenter;

/**
 * Argument transformer based on transformations repository.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class RepositoryArgumentTransformer implements ArgumentTransformer, RegexGenerator
{
    /**
     * @var TransformationRepository
     */
    private $repository;
    /**
     * @var CallCenter
     */
    private $callCenter;
    /**
     * @var PatternTransformer
     */
    private $patternTransformer;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Initializes transformer.
     */
    public function __construct(
        TransformationRepository $repository,
        CallCenter $callCenter,
        PatternTransformer $patternTransformer,
        TranslatorInterface $translator,
    ) {
        $this->repository = $repository;
        $this->callCenter = $callCenter;
        $this->patternTransformer = $patternTransformer;
        $this->translator = $translator;
    }

    public function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue)
    {
        return count($this->repository->getEnvironmentTransformations($definitionCall->getEnvironment())) > 0;
    }

    public function transformArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue)
    {
        $environment = $definitionCall->getEnvironment();
        list($simpleTransformations, $normalTransformations) = $this->splitSimpleAndNormalTransformations(
            $this->repository->getEnvironmentTransformations($environment)
        );

        $newValue = $this->applySimpleTransformations($simpleTransformations, $definitionCall, $argumentIndex, $argumentValue);
        $newValue = $this->applyNormalTransformations($normalTransformations, $definitionCall, $argumentIndex, $newValue);

        return $newValue;
    }

    public function generateRegex($suiteName, $pattern, $language)
    {
        $translatedPattern = $this->translator->trans($pattern, [], $suiteName, $language);
        if ($pattern == $translatedPattern) {
            return $this->patternTransformer->transformPatternToRegex($pattern);
        }

        return $this->patternTransformer->transformPatternToRegex($translatedPattern);
    }

    /**
     * Apply simple argument transformations in priority order.
     *
     * @param SimpleArgumentTransformation[] $transformations
     * @param int|string                     $index
     */
    private function applySimpleTransformations(array $transformations, DefinitionCall $definitionCall, $index, $value)
    {
        usort($transformations, function (SimpleArgumentTransformation $t1, SimpleArgumentTransformation $t2) {
            return $t2->getPriority() <=> $t1->getPriority();
        });

        $newValue = $value;
        foreach ($transformations as $transformation) {
            $newValue = $this->transform($definitionCall, $transformation, $index, $newValue);
        }

        return $newValue;
    }

    /**
     * Apply normal (non-simple) argument transformations.
     *
     * @param Transformation[] $transformations
     * @param int|string       $index
     */
    private function applyNormalTransformations(array $transformations, DefinitionCall $definitionCall, $index, $value)
    {
        $newValue = $value;
        foreach ($transformations as $transformation) {
            $newValue = $this->transform($definitionCall, $transformation, $index, $newValue);
        }

        return $newValue;
    }

    /**
     * Transforms argument value using registered transformers.
     *
     * @param int|string $index
     */
    private function transform(DefinitionCall $definitionCall, Transformation $transformation, $index, $value)
    {
        if (is_object($value) && !$value instanceof ArgumentInterface) {
            return $value;
        }

        if ($transformation instanceof SimpleArgumentTransformation
            && $transformation->supportsDefinitionAndArgument($definitionCall, $index, $value)) {
            return $transformation->transformArgument($this->callCenter, $definitionCall, $index, $value);
        }

        if ($transformation instanceof PatternTransformation
            && $transformation->supportsDefinitionAndArgument($this, $definitionCall, $value)) {
            return $transformation->transformArgument($this, $this->callCenter, $definitionCall, $value);
        }

        return $value;
    }

    /**
     * Splits transformations into simple and normal ones.
     *
     * @param Transformation[] $transformations
     *
     * @return array
     */
    private function splitSimpleAndNormalTransformations(array $transformations)
    {
        return array_reduce($transformations, function ($acc, $t) {
            return [
                $t instanceof SimpleArgumentTransformation ? array_merge($acc[0], [$t]) : $acc[0],
                !$t instanceof SimpleArgumentTransformation ? array_merge($acc[1], [$t]) : $acc[1],
            ];
        }, [[], []]);
    }
}
