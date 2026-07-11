<?php

namespace App\Console\Commands;

use App\Models\Attraction;
use App\Models\QrCode;
use Illuminate\Console\Command;

class GenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all attractions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attractions = Attraction::all();
        $this->info('Generating QR codes for ' . $attractions->count() . ' attractions...');

        $bar = $this->output->createProgressBar($attractions->count());
        $bar->start();

        foreach ($attractions as $attraction) {
            QrCode::generateForEntity('Attraction', $attraction->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('QR codes generated successfully!');
    }
}