<?php

namespace App\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;

/**
 * Filament action to list documents with filtering
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint::list
 */
class ListDocumentsAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'list_documents';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('List Documents')
            ->icon('heroicon-o-document-text')
            ->form([
                Select::make('type')
                    ->label('Document Type')
                    ->options([
                        'INVOICE' => 'Invoice',
                        'CREDIT_NOTE' => 'Credit Note',
                        'ORDER' => 'Order',
                        'DESPATCH_ADVICE' => 'Despatch Advice',
                    ])
                    ->hint('Filter by document type'),
                    
                Select::make('direction')
                    ->label('Direction')
                    ->options([
                        'INCOMING' => 'Incoming',
                        'OUTGOING' => 'Outgoing',
                    ])
                    ->hint('Filter by document direction'),
                    
                TextInput::make('page')
                    ->label('Page')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                    
                TextInput::make('size')
                    ->label('Page Size')
                    ->numeric()
                    ->default(20)
                    ->minValue(1)
                    ->maxValue(100),
            ])
            ->action(function (array $data): void {
                try {
                    $filters = array_filter([
                        'type' => $data['type'] ?? null,
                        'direction' => $data['direction'] ?? null,
                    ]);
                    
                    $result = $this->getClient()
                        ->app()
                        ->documents()
                        ->list(
                            $filters,
                            $data['page'] ?? 0,
                            $data['size'] ?? 20
                        );

                    $totalElements = $result['totalElements'] ?? 0;
                    $contentCount = count($result['content'] ?? []);
                    
                    $this->notifySuccess(
                        'Documents Retrieved',
                        "Found {$totalElements} total documents. Showing {$contentCount} on this page."
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to List Documents',
                        $e->getMessage()
                    );
                }
            });
    }
}
