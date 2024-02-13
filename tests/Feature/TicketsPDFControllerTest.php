<?php

namespace Tests\Feature;

use Tests\TestCase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketsPDFControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function it_generates_a_pdf_with_qr_code()
    {
        Session::start();
        Session::put('datosCompra', [
            'buyerDNI' => '12345678A',
            'sessionId' => 'ABC123'
        ]);

        // Generate QR code
        $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/');
        $base64QrCode = base64_encode($qrCodeImage);

        // Mock PDF facade output
        PDF::shouldReceive('loadView')
            ->once()
            ->andReturnSelf();

        PDF::shouldReceive('output')
            ->once()
            ->andReturn('PDF Contents');

        // Expect storage to be called with correct parameters
        Storage::fake('public');

        // Hit the controller
        $response = $this->get(route('generate-pdf'));

        // Assert the response
        $response->assertStatus(200)
            ->assertSee('PDF guardado en la ruta: public/pdfs/12345678AABC123.pdf');

        // Assert PDF has been stored
        Storage::disk('public')->assertExists('pdfs/12345678AABC123.pdf');
    }

    /** @test */
    public function it_generates_a_nominal_pdf_with_qr_code()
    {
        Session::start();
        Session::put('datosCompra', [
            'buyerDNI' => '12345678A',
            'sessionId' => 'ABC123'
        ]);

        // Generate QR code
        $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/');
        $base64QrCode = base64_encode($qrCodeImage);

        // Mock PDF facade output
        PDF::shouldReceive('loadView')
            ->once()
            ->andReturnSelf();

        PDF::shouldReceive('output')
            ->once()
            ->andReturn('PDF Contents');

        // Expect storage to be called with correct parameters
        Storage::fake('public');

        // Hit the controller
        $response = $this->get(route('generate-pdf-nominal'));

        // Assert the response
        $response->assertStatus(200)
            ->assertSee('PDF guardado en la ruta: public/pdfs/12345678AABC123.pdf');

        // Assert PDF has been stored
        Storage::disk('public')->assertExists('pdfs/12345678AABC123.pdf');
    }

    /** @test */
    public function it_downloads_a_pdf()
    {
        // Expect storage to be called with correct parameters
        Storage::fake('public');
        Storage::put('pdfs/12345678AABC123.pdf', 'PDF Contents');

        // Hit the controller
        $response = $this->get(route('download-pdf', '12345678AABC123.pdf'));

        // Assert the response
        $response->assertStatus(200);
    }
}