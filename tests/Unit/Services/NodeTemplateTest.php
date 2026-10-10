<?php

declare(strict_types=1);

use Smarty\Smarty;

it('renders Hysteria2 defaults without interpreting JavaScript objects as Smarty tags', function (string $page) {
    $templatePath = dirname(__DIR__, 3) . '/resources/views/tabler/admin/node/' . $page . '.tpl';
    expect(is_readable($templatePath))->toBeTrue("Template is not readable: {$templatePath}");
    $source = file_get_contents($templatePath);
    expect($source)->toBeString();
    // Isolate the page body from shared layouts and their application/database dependencies.
    $source = preg_replace('/\{include file=\'admin\/(?:header|footer)\.tpl\'\}/', '', $source);
    $compileDir = sys_get_temp_dir() . '/sspanel-node-template-' . bin2hex(random_bytes(8));
    mkdir($compileDir, 0700);

    try {
        $smarty = new Smarty();
        $smarty->setCompileDir($compileDir);
        $smarty->assign('config', ['jsdelivr_url' => 'example.invalid', 'jump_delay' => 1234]);
        $smarty->assign('update_field', ['name']);
        $node = new stdClass();
        preg_match_all('/\$node->([a-z_][a-z0-9_]*)/', $source, $properties);
        foreach (array_unique($properties[1]) as $property) {
            $node->{$property} = 0;
        }
        $node->id = 42;
        $node->custom_config = '{"existing":true}';
        $smarty->assign('node', $node);
        $rendered = $smarty->fetch('string:' . $source);

        expect($rendered)
            ->toContain("masquerade: {type: '404'}")
            ->toContain("quicParams: {congestion: 'bbr'}")
            ->toContain("portHopping: {enabled: false, autoConfigureFirewall: false, ports: ''}")
            ->toContain("name: $('#name').val()")
            ->toContain('1234);')
            ->not->toContain('{literal}')
            ->not->toContain('{$node');
        if ($page === 'edit') {
            expect($rendered)->toContain('editor.set({"existing":true})')->toContain('/admin/node/42');
        }
    } finally {
        foreach (glob($compileDir . '/*') as $file) {
            unlink($file);
        }
        rmdir($compileDir);
    }
})->with(['create', 'edit']);
