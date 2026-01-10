<?php

namespace App\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;

/**
 * Filament action to create a new document via LetsPeppol API
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint::create
 */
class CreateDocumentAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'create_document';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Create Document')
            ->icon('heroicon-o-document-plus')
            ->form([
                Textarea::make('ubl_xml')
                    ->label('UBL XML Content')
                    ->required()
                    ->rows(10)
                    ->hint('Paste the UBL XML content for the document'),
                    
                Toggle::make('draft')
                    ->label('Save as Draft')
                    ->default(false)
                    ->hint('If enabled, the document will be saved as a draft'),
                    
                DateTimePicker::make('schedule')
                    ->label('Schedule Send')
                    ->hint('Optional: Schedule document to be sent at a specific time')
                    ->native(false),
            ])
            ->action(function (array $data): void {
                try {
                    $schedule = $data['schedule'] ?? null;
                    
                    $result = $this->getClient()
                        ->app()
                        ->documents()
                        ->create(
                            $data['ubl_xml'],
                            $data['draft'] ?? false,
                            $schedule
                        );

                    $this->notifySuccess(
                        'Document Created',
                        'Document ' . ($result['id'] ?? 'unknown') . ' has been created successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Create Document',
                        $e->getMessage()
                    );
                }
            });
    }
}
