<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'remove' => [
        // TODO: remove
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,

        NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class,
        SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants::class,
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff::class,

        // Allow it
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff::class,
    ],
    'config' => [
        PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff::class => [
            'exclude' => [
                'src/Command/Job.php',
                'src/Command/PortAutoChange.php',
            ],
        ],
        // Db migration should not have a class declaration
        // If they have, phinx will unable to found them
        PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff::class => [
            'exclude' => [ 'db/migrations' ],
        ],
    ],

    'exclude' => [
        'storage',
    ],
];
