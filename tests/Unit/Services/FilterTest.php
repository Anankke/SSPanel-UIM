<?php

declare(strict_types=1);

use App\Services\Filter;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

it('allows email when filter is set to whitelist mode and email domain is in list', function () {
    $_ENV['mail_filter'] = 1;
    $_ENV['mail_filter_list'] = ['example.com'];
    
    expect(Filter::checkEmailFilter('test@example.com'))->toBeTrue();
});

it('blocks email when filter is set to blacklist mode and email domain is in list', function () {
    $_ENV['mail_filter'] = 2;
    $_ENV['mail_filter_list'] = ['example.com'];
    
    expect(Filter::checkEmailFilter('test@example.com'))->toBeFalse();
});

it('blocks invalid email addresses', function () {
    $_ENV['mail_filter'] = 2;
    $_ENV['mail_filter_list'] = ['example.com'];
    
    expect(Filter::checkEmailFilter('invalid_email'))->toBeFalse();
});

it('allows any email when filter is disabled', function () {
    $_ENV['mail_filter'] = 0;
    
    expect(Filter::checkEmailFilter('test@example.com'))->toBeTrue();
});

it('blocks email when filter is set to whitelist mode and email domain is not in list', function () {
    $_ENV['mail_filter'] = 1;
    $_ENV['mail_filter_list'] = ['example.com'];
    
    expect(Filter::checkEmailFilter('test@notexample.com'))->toBeFalse();
});

it('allows email when filter is set to blacklist mode and email domain is not in list', function () {
    $_ENV['mail_filter'] = 2;
    $_ENV['mail_filter_list'] = ['example.com'];
    
    expect(Filter::checkEmailFilter('test@notexample.com'))->toBeTrue();
});