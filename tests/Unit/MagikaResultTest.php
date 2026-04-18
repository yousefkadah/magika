<?php

namespace YousefKadah\LaravelMagika\Tests\Unit;

use PHPUnit\Framework\TestCase;
use YousefKadah\LaravelMagika\MagikaResult;

class MagikaResultTest extends TestCase
{
    public function test_it_creates_from_json(): void
    {
        $data = [
            'path' => '/tmp/test.pdf',
            'result' => [
                'status' => 'ok',
                'value' => [
                    'score' => 0.99,
                    'dl' => [
                        'label' => 'pdf',
                        'mime_type' => 'application/pdf',
                        'group' => 'document',
                        'description' => 'PDF document',
                        'is_text' => false,
                        'extensions' => ['pdf'],
                    ],
                    'output' => [
                        'label' => 'pdf',
                        'mime_type' => 'application/pdf',
                        'group' => 'document',
                        'description' => 'PDF document',
                        'is_text' => false,
                        'extensions' => ['pdf'],
                    ],
                ],
            ],
        ];

        $result = MagikaResult::fromJson($data);

        $this->assertSame('/tmp/test.pdf', $result->path);
        $this->assertSame('pdf', $result->label);
        $this->assertSame('application/pdf', $result->mimeType);
        $this->assertSame('document', $result->group);
        $this->assertSame('PDF document', $result->description);
        $this->assertSame(0.99, $result->score);
        $this->assertFalse($result->isText);
        $this->assertSame(['pdf'], $result->extensions);
        $this->assertSame('ok', $result->status);
    }

    public function test_is_ok(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test.pdf',
            label: 'pdf',
            mimeType: 'application/pdf',
            group: 'document',
            description: 'PDF document',
            score: 0.99,
            isText: false,
            extensions: ['pdf'],
            status: 'ok',
        );

        $this->assertTrue($result->isOk());
    }

    public function test_is_not_ok(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test',
            label: 'unknown',
            mimeType: 'application/octet-stream',
            group: 'unknown',
            description: 'Unknown',
            score: 0.0,
            isText: false,
            extensions: [],
            status: 'error',
        );

        $this->assertFalse($result->isOk());
    }

    public function test_matches_label_string(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test.pdf',
            label: 'pdf',
            mimeType: 'application/pdf',
            group: 'document',
            description: 'PDF document',
            score: 0.99,
            isText: false,
            extensions: ['pdf'],
            status: 'ok',
        );

        $this->assertTrue($result->matchesLabel('pdf'));
        $this->assertFalse($result->matchesLabel('docx'));
    }

    public function test_matches_label_array(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test.pdf',
            label: 'pdf',
            mimeType: 'application/pdf',
            group: 'document',
            description: 'PDF document',
            score: 0.99,
            isText: false,
            extensions: ['pdf'],
            status: 'ok',
        );

        $this->assertTrue($result->matchesLabel(['pdf', 'docx']));
        $this->assertFalse($result->matchesLabel(['docx', 'xlsx']));
    }

    public function test_matches_mime_type(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test.pdf',
            label: 'pdf',
            mimeType: 'application/pdf',
            group: 'document',
            description: 'PDF document',
            score: 0.99,
            isText: false,
            extensions: ['pdf'],
            status: 'ok',
        );

        $this->assertTrue($result->matchesMimeType('application/pdf'));
        $this->assertTrue($result->matchesMimeType(['application/pdf', 'image/png']));
        $this->assertFalse($result->matchesMimeType('image/png'));
    }

    public function test_matches_group(): void
    {
        $result = new MagikaResult(
            path: '/tmp/test.pdf',
            label: 'pdf',
            mimeType: 'application/pdf',
            group: 'document',
            description: 'PDF document',
            score: 0.99,
            isText: false,
            extensions: ['pdf'],
            status: 'ok',
        );

        $this->assertTrue($result->matchesGroup('document'));
        $this->assertTrue($result->matchesGroup(['document', 'code']));
        $this->assertFalse($result->matchesGroup('image'));
    }
}
