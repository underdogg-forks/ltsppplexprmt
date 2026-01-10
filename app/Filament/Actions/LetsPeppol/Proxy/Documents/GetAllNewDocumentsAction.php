<?php

namespace App\Filament\Actions\LetsPeppol\Proxy\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to get all new documents
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Proxy\DocumentsEndpoint::getAllNew
 */
class GetAllNewDocumentsAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'get_all_new_documents';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Get All New Documents')
            ->icon('heroicon-o-document-arrow-down')
            ->form([
                TextInput::make('size')
                    ->label('Batch Size')
                    ->numeric()
                    ->default(100)
                    ->minValue(1)
                    ->maxValue(1000)
                    ->hint('Number of documents to retrieve'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->proxy()
                        ->documents()
                        ->getAllNew($data['size'] ?? 100);

                    $count = count($result);
                    
                    $this->notifySuccess(
                        'New Documents Retrieved',
                        "Retrieved {$count} new document(s)."
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Get Documents',
                        $e->getMessage()
                    );
                }
            });
    }
}
