<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'remove' => [
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff::class,
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff::class,
        PhpCsFixer\Fixer\Import\OrderedImportsFixer::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
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
            'exclude' => ['db/migrations'],
        ],
    ],

    'exclude' => [
        'storage',

        // TODO: mute legacy contents error
        'src/Services/Gateway/CoinPay/CoinPayDataBase.php',
        'src/Services/Gateway/Epay',
        'src/Services/Mail.php',
        'src/Services/Password.php',
        'src/Utils/GA.php',
        'src/Utils/GeetestLib.php',
    ],
];
