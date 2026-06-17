<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Templates;

use Sendportal\Base\Services\Templates\BrandingVariableProvider;
use Tests\TestCase;

class BrandingVariableProviderTest extends TestCase
{
    private function provider(): BrandingVariableProvider
    {
        return new BrandingVariableProvider();
    }

    private function fullBrand(): array
    {
        return [
            'brand_name'          => 'talenthunter.asia',
            'logo_url'            => 'https://cdn.example.com/logo.png',
            'brand_address'       => 'Q1, HCMC',
            'brand_support_email' => 'support@th.asia',
            'brand_hotline'       => '1900 1234',
            'brand_website'       => 'https://th.asia',
            'brand_linkedin'      => 'https://linkedin.com/company/th',
            'brand_facebook'      => 'https://fb.com/th',
            'brand_zalo'          => '',
            'brand_youtube'       => '',
            'brand_tiktok'        => '',
            'brand_instagram'     => '',
        ];
    }

    public function test_header_uses_logo_when_present(): void
    {
        $html = $this->provider()->headerHtml($this->fullBrand());
        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('https://cdn.example.com/logo.png', $html);
    }

    public function test_header_falls_back_to_brand_name_text_without_logo(): void
    {
        $brand = array_merge($this->fullBrand(), ['logo_url' => '']);
        $html = $this->provider()->headerHtml($brand);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('talenthunter.asia', $html);
    }

    public function test_social_renders_only_set_platforms(): void
    {
        $html = $this->provider()->socialHtml($this->fullBrand());
        $this->assertStringContainsString('https://fb.com/th', $html);
        $this->assertStringContainsString('https://linkedin.com/company/th', $html);
        $this->assertStringNotContainsString('Zalo', $html);
    }

    public function test_social_is_empty_when_no_platforms(): void
    {
        $brand = $this->fullBrand();
        foreach (['brand_website','brand_linkedin','brand_facebook','brand_zalo','brand_youtube','brand_tiktok','brand_instagram'] as $k) {
            $brand[$k] = '';
        }
        $this->assertSame('', $this->provider()->socialHtml($brand));
    }

    public function test_contact_omitted_when_all_empty(): void
    {
        $brand = array_merge($this->fullBrand(), [
            'brand_address' => '', 'brand_support_email' => '', 'brand_hotline' => '',
        ]);
        $this->assertSame('', $this->provider()->contactHtml($brand));
    }

    public function test_caller_overrides_brand_scalar(): void
    {
        $out = $this->provider()->composeVariables($this->fullBrand(), [
            'brand_facebook' => 'https://fb.com/override',
            'candidate_name' => 'Minh Anh',
        ]);
        $this->assertStringContainsString('https://fb.com/override', $out['brand_social_html']);
        $this->assertSame('Minh Anh', $out['candidate_name']);
    }

    public function test_employer_company_var_is_not_overwritten_by_branding(): void
    {
        $out = $this->provider()->composeVariables($this->fullBrand(), ['company' => 'ACME Corp']);
        $this->assertSame('ACME Corp', $out['company']);
        $this->assertSame('talenthunter.asia', $out['brand_name']);
    }

    public function test_brand_name_is_html_escaped_in_header(): void
    {
        $brand = array_merge($this->fullBrand(), ['logo_url' => '', 'brand_name' => '<Acme & Sons>']);
        $html = $this->provider()->headerHtml($brand);
        $this->assertStringContainsString('&lt;Acme &amp; Sons&gt;', $html);
        $this->assertStringNotContainsString('<Acme', $html);
    }

    public function test_javascript_url_is_dropped_from_social(): void
    {
        $brand = array_merge($this->fullBrand(), ['brand_facebook' => 'javascript:alert(1)']);
        $html = $this->provider()->socialHtml($brand);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('>f<', $html); // the facebook badge is dropped
    }

    public function test_unsafe_logo_url_falls_back_to_text(): void
    {
        $brand = array_merge($this->fullBrand(), ['logo_url' => 'javascript:alert(1)', 'brand_name' => 'Acme']);
        $html = $this->provider()->headerHtml($brand);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('Acme', $html);
    }

    public function test_caller_cannot_inject_raw_composite_html(): void
    {
        $out = $this->provider()->composeVariables($this->fullBrand(), [
            'brand_social_html' => '<script>alert(1)</script>',
        ]);
        $this->assertStringNotContainsString('<script>', $out['brand_social_html']);
    }
}
