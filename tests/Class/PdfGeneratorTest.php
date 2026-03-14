<?php

namespace App\Tests\Class;

use App\Class\PdfGenerator;
use PHPUnit\Framework\TestCase;

class PdfGeneratorTest extends TestCase
{
    private PdfGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new PdfGenerator();
    }

    public function testGenerateReturnsPdfResponse(): void
    {
        $response = $this->generator->generate('<h1>Facture</h1>', 'facture-test.pdf');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function testGenerateContentDispositionContainsFilename(): void
    {
        $filename = 'facture-LU000042.pdf';
        $response = $this->generator->generate('<p>Contenu</p>', $filename);

        $this->assertStringContainsString(
            'attachment; filename="' . $filename . '"',
            $response->headers->get('Content-Disposition')
        );
    }

    public function testGenerateOutputIsNotEmpty(): void
    {
        $response = $this->generator->generate('<h1>Test PDF</h1>', 'test.pdf');

        $this->assertNotEmpty($response->getContent());
    }

    public function testGenerateStartsWithPdfMagicBytes(): void
    {
        $response = $this->generator->generate('<h1>Test</h1>', 'test.pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}
