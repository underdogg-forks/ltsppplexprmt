<?php

namespace App\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to get a specific document by ID
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint::get
 */
class GetDocumentAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'get_document';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Get Document')
            ->icon('heroicon-o-document-magnifying-glass')
            ->form([
                TextInput::make('id')
                    ->label('Document ID')
                    ->required()
                    ->hint('Enter the document ID to retrieve'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->documents()
                        ->get($data['id']);

                    $this->notifySuccess(
                        'Document Retrieved',
                        'Document ' . ($result['id'] ?? 'unknown') . ' retrieved successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Get Document',
                        $e->getMessage()
                    );
                }
            });
    }
}
